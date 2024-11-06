<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Tests\Validator\Constraint;

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyString;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyStringValidator;
use PHPUnit\Framework\TestCase;

class NonEmptyStringTest extends TestCase
{
    protected NonEmptyString $nonEmptyString;

    protected function setUp(): void
    {
        parent::setUp();

        $this->nonEmptyString = new NonEmptyString();
    }

    public function testValidatedBy(): void
    {
        self::assertSame(NonEmptyStringValidator::class, $this->nonEmptyString->validatedBy());
    }
}
