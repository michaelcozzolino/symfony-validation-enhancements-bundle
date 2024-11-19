<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Tests\Unit\Validator;

use Symfony\Component\Validator\Test\ConstraintValidatorTestCase as BaseConstraintValidatorTestCase;

abstract class ConstraintValidatorTestCase extends BaseConstraintValidatorTestCase
{
    protected function assertViolations(int $expectedViolationsCount): void
    {
        self::assertCount($expectedViolationsCount, $this->context->getViolations());
    }
}
