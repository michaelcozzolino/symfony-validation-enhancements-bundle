<?php declare(strict_types=1);

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Tests\Unit\Validator\Constraint\NonEmptyStringValidatorTestCase;

pest()->extend(NonEmptyStringValidatorTestCase::class)->in('Unit/Validator');
