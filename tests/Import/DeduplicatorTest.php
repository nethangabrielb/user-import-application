<?php

declare(strict_types=1);

namespace Tests\Import;

use App\Import\Deduplicator;
use PHPUnit\Framework\TestCase;

class DeduplicatorTest extends TestCase
{
    public function testFirstTimeEmailIsNotDuplicate(): void
    {
        $deduplicator = new Deduplicator();

        $isDuplicate = $deduplicator->isDuplicate('john@example.com');

        $this->assertFalse($isDuplicate);
    }

    public function testSameEmailIsDuplicateOnSecondCall(): void
    {
        $deduplicator = new Deduplicator();

        $isDuplicate;

        for ($i = 0; $i < 2; $i++) {
            $isDuplicate = $deduplicator->isDuplicate('john@example.com');
        }

        $this->assertTrue($isDuplicate);
    }

    public function testCaseInsensitiveDuplicateDetection(): void
    {
        $deduplicator = new Deduplicator();

        // check lowercase email first then check uppercase email
        $testEmail = 'john@example.com';
        $isDuplicateLowerCase = $deduplicator->isDuplicate($testEmail);
        $isDuplicateUpperCase = $deduplicator->isDuplicate(strtoupper($testEmail));

        // assert that the uppercase check returns true to
        // verify that the normalization works and it is 
        // case-insensitive
        $this->assertTrue($isDuplicateUpperCase);
    }

    public function testResetClearsSeenEmails(): void
    {
        $deduplicator = new Deduplicator();

        $isDuplicateInit = $deduplicator->isDuplicate('john@example.com');

        $deduplicator->reset();

        $isDuplicateFinal = $deduplicator->isDuplicate('john@example.com');

        // should return false because we reset after the initial duplicate check
        $this->assertFalse($isDuplicateFinal);
    }
}
