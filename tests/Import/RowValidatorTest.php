<?php

declare(strict_types=1);

namespace Tests\Import;

use App\Import\RowValidator;
use PHPUnit\Framework\TestCase;

class RowValidatorTest extends TestCase
{
    public function testValidRowPassesValidation(): void
    {
        $validator = new RowValidator();
        
        $testRow = ['john', 'smith', 'john.smith@example.com'];

        $errors = $validator->validateRow($testRow);

        $this->assertEmpty($errors);
    }

    public function testInvalidShapeFailsValidation(): void
    {
        $validator = new RowValidator();
        
        $testRow = ['john', 'smith'];

        $errors = $validator->validateRow($testRow);

        
        $this->assertNotEmpty($errors);
        $this->assertContains("Invalid field shape", $errors);  
    }

    public function testEmptyFieldsFailValidation(): void
    {
        $validator = new RowValidator();
        
        $testRow = ['', 'smith', 'john@example.com'];

        $errors = $validator->validateRow($testRow);

        $this->assertNotEmpty($errors);
        $this->assertContains("Invalid value", $errors);
    }

    public function testInvalidEmailFormatFailsValidation(): void
    {
        $validator = new RowValidator();
        
        $testRow = ['john', 'smith', 'invalid-email'];

        $errors = $validator->validateRow($testRow);

        $this->assertNotEmpty($errors);
        $this->assertContains("Invalid email", $errors);
        
    }
}
