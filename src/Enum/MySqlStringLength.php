<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Enum;

enum MySqlStringLength: int
{
    case VarcharDefault = 255;

    case Text           = 65535;

    case MediumText     = 16777215;

    case LongText       = 4294967295;
}
