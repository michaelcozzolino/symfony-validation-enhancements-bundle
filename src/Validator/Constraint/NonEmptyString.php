<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint;

use Attribute;
use Symfony\Component\Validator\Constraints as Assert;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class NonEmptyString extends Assert\Length
{
    /**
     * @param positive-int|null $max
     */
    public function __construct(?int $max = null)
    {
        parent::__construct(min: 1, max: $max);
    }

    public function validatedBy(): string
    {
        return NonEmptyStringValidator::class;
    }
}
