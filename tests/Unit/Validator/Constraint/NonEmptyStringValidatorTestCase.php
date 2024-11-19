<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Tests\Unit\Validator\Constraint;

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Tests\Unit\Validator\ConstraintValidatorTestCase;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyStringValidator;
use Random\RandomException;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use function random_int;
use function strlen;

class NonEmptyStringValidatorTestCase extends ConstraintValidatorTestCase
{
    public function createValidator(): ConstraintValidatorInterface
    {
        return new NonEmptyStringValidator();
    }

    /**
     * @param int $length
     *
     * @throws RandomException
     * @return string
     */
    public function generateRandomString(int $length): string
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $index        = random_int(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}
