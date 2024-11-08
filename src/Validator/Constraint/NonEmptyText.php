<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint;

use Attribute;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Enum\MySqlDatabaseStringLength;

/**
 * @psalm-api
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class NonEmptyText extends NonEmptyString
{
    public function __construct()
    {
        parent::__construct(max: MySqlDatabaseStringLength::Text->value);
    }
}
