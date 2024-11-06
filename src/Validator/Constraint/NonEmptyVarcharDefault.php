<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint;

use Attribute;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Enum\MySqlDatabaseStringLength;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class NonEmptyVarcharDefault extends NonEmptyString
{
    public function __construct()
    {
        parent::__construct(max: MySqlDatabaseStringLength::VarcharDefault->value);
    }
}
