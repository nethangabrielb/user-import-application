<?php

declare(strict_types=1);

namespace App\Import;

class Deduplicator
{
    private array $seenEmails = [];

    public function isDuplicate(string $email): bool
    {
        $normalizedEmail = strtolower(trim($email));

        if (in_array($normalizedEmail, $this->seenEmails)) {
            return true;
        } else {
            $this->seenEmails[] = $normalizedEmail;
            return false;
        }
    }

  
    public function reset(): void
    {
        $this->seenEmails = [];
    }
}
