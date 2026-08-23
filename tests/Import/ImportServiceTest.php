<?php

declare(strict_types=1);

namespace Tests\Import;

use App\Import\ImportService;
use PHPUnit\Framework\TestCase;

class ImportServiceTest extends TestCase
{
    public function testProcessesValidRowsSuccessfully(): void
    {
        $importService = new ImportService();

        $testRows = [
            ['john', 'smith', 'john.smith@example.com'],
            ['jane', 'doe', 'jane.doe@example.com']
        ];

        $result = $importService->process($testRows, isDryRun: true);

        $this->assertTrue($result->getImportedCount() === 2);
        $this->assertTrue($result->getInvalidCount() === 0);
        $this->assertTrue($result->getDuplicateCount() === 0);
    }

    public function testSeparatesValidAndInvalidRows(): void
    {
        $importService = new ImportService();
    
        $testRows = [
            ['john', 'smith', 'john.smith@example.com'],
            ['bad', 'user', 'invalid-email']
        ];

        $result = $importService->process($testRows, isDryRun: true);

        $this->assertTrue($result->getImportedCount() === 1); 
        $this->assertTrue($result->getInvalidCount() === 1);
    }

    public function testFiltersDuplicateEmailsInSameBatch(): void
    {
        $importService = new ImportService();
    
        $testRows = [
            ['john', 'smith', 'john@example.com'],
            ['duplicate', 'user', 'JOHN@EXAMPLE.COM']
        ];

        $result = $importService->process($testRows, isDryRun: true);

        $this->assertTrue($result->getImportedCount() === 1); 
        $this->assertTrue($result->getDuplicateCount() === 1);
    }
}
