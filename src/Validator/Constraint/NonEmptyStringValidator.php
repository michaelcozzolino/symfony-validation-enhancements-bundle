<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\LengthValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function is_string;
use function trim;

class NonEmptyStringValidator extends LengthValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($value === null) {
            return;
        }

        if (is_string($value) === false) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ($constraint instanceof NonEmptyString === false) {
            throw new UnexpectedTypeException($constraint, NonEmptyString::class);
        }

        parent::validate(trim($value), $constraint);
    }
}
