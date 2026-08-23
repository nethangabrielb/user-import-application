<?php

declare(strict_types=1);

namespace Tests\Import;

use App\Import\RowNormalizer;
use PHPUnit\Framework\TestCase;

class RowNormalizerTest extends TestCase
{
    public function testNormalizesNamesToTitleCase(): void
    {
        $normalizer = new RowNormalizer();

        $testRow = ['jOHn', 'sMIth', 'john@example.com'];

        $result = $normalizer->normalize($testRow);

        $this->assertSame(['John', 'Smith', 'john@example.com'], $result);
    }

    public function testNormalizesEmailToLowerCase(): void
    {
        $normalizer = new RowNormalizer();

        $testRow = ['john', 'smith', 'JOHN.SMITH@EXAMPLE.COM'];

        $result = $normalizer->normalize($testRow);

        $this->assertSame(['John', 'Smith', 'john.smith@example.com'], $result);
    }

    public function testTrimsWhitespaceAroundFields(): void
    {
        $normalizer = new RowNormalizer();

        $testRow =  ['  john  ', ' smith ', ' john.smith@example.com '];

        $result = $normalizer->normalize($testRow);
        
        $this->assertSame(['John', 'Smith', 'john.smith@example.com'], $result);
    }
}
