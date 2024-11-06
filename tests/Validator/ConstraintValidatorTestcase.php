<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Tests\Validator;

use Symfony\Component\Validator\Test\ConstraintValidatorTestCase as BaseConstraintValidatorTestCase;

abstract class ConstraintValidatorTestcase extends BaseConstraintValidatorTestCase
{
    protected function assertViolations(int $expectedViolationsCount): void
    {
        self::assertCount($expectedViolationsCount, $this->context->getViolations());
    }
}
