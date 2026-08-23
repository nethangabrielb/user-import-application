<?php

declare(strict_types=1);

namespace App\Import;

class ImportResult
{
    private array $imported = [];
    private array $invalid = [];
    private array $duplicates = [];

    public function addImported(array $row): void
    {
        $this->imported[] = $row;
    }

    /**
     * Record a row that failed validation with reasons.
     *
     * @param int $rowNumber The CSV line number (for clear error reporting)
     * @param array $row The raw row data
     * @param array<string> $errors List of error descriptions from RowValidator
     */
    public function addInvalid(int $rowNumber, array $row, array $errors): void
    {
        $this->invalid[] = [
            'rowNumber' => $rowNumber,
            'data' => $row,
            'errors' => $errors
        ];
    }

    /**
     * Record a row that was skipped because of a duplicate email.
     *
     * @param int $rowNumber The CSV line number
     * @param array $row The raw/normalized row data
     * @param string $email The duplicate email
     */
    public function addDuplicate(int $rowNumber, array $row, string $email): void
    {
        $this->duplicates[] = [
            'rowNumber' => $rowNumber,
            'data' => $row,
            'email' => $email,
        ];
    }

    // getters & counts

    public function getImported(): array
    {
        return $this->imported;
    }

    public function getInvalid(): array
    {
        return $this->invalid;
    }

    public function getDuplicates(): array
    {
        return $this->duplicates;
    }

    public function getImportedCount(): int
    {
        return count($this->imported);
    }

    public function getInvalidCount(): int
    {
        return count($this->invalid);
    }

    public function getDuplicateCount(): int
    {
        return count($this->duplicates);
    }

    public function getTotalProcessed(): int
    {
        return count($this->imported) + count($this->invalid) + count($this->duplicates);
    }

    // Export all results as an associative array
    public function toArray(): array
    {
        return [
            'imported' => $this->imported,
            'invalid' => $this->invalid,
            'duplicates' => $this->duplicates,
            'counts' => [
                'total' => $this->getTotalProcessed(),
                'imported' => $this->getImportedCount(),
                'invalid' => $this->getInvalidCount(),
                'duplicates' => $this->getDuplicateCount(),
            ],
        ];
    }
}
