<?php

declare(strict_types=1);

namespace App\Import;

use App\Db\UserRepository;

class ImportService
{
    private RowValidator $validator;
    private RowNormalizer $normalizer;
    private Deduplicator $deduplicator;
    private ?UserRepository $userRepository;

    public function __construct(
        ?RowValidator $validator = null,
        ?RowNormalizer $normalizer = null,
        ?Deduplicator $deduplicator = null,
        ?UserRepository $userRepository = null
    ) {
        $this->validator = $validator;
        $this->normalizer = $normalizer;
        $this->deduplicator = $deduplicator;
        $this->userRepository = $userRepository;
    }

    /**
     * Processes an array of raw CSV rows through the full import pipeline.
     *
     * @param array<int, array<int, string>> $rows The raw rows from CsvReader
     * @param bool $isDryRun If true, validate and deduplicate without inserting to DB
     * @return ImportResult The aggregated results and statistics
     */
    public function process(array $rows, bool $isDryRun = false): ImportResult
    {
        $result = new ImportResult();

        // reset the Deduplicator to ensure a clean state for this import batch
        $this->deduplicator->reset();

        foreach($rows as $index => $row) {
            // validate the row 
            $errors = $this->validator->validateRow($row);
            if (count($errors) > 0) {
                $result->addInvalid($index, $row, $errors);
                continue;
            } 

            // normalize the row if valid
            [$name, $surname, $email] = $this->normalizer->normalize($row); 
            
            // check if the normalized email is a duplicate in this CSV
            $isDuplicate = $this->deduplicator->isDuplicate($email);
            if ($isDuplicate) {
                $result->addDuplicate($index, [$name, $surname, $email], $email);
                continue;
            }

            // database deduplication
            $isDuplicateDb = $this->userRepository->emailExists($email);
            if ($isDuplicateDb) {
                $result->addDuplicate($index, [$name, $surname, $email], $email);
                continue;
            } 
            
            // database persistence if not dry run
            if (!$isDryRun && $userRepository) {
                $userRepository->insertUser($name, $surname, $email);
            }
            
            // record the validated, normalized row as successfully imported in ImportResult
            $result->addImported([$name, $surname, $email]);

            // return the completed ImportResult
            return $result.toArray();
        }
    }
}
