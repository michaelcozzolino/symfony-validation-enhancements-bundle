<?php declare(strict_types=1);

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyString;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyStringValidator;

beforeEach(function () {
    $this->nonEmptyString = new NonEmptyString();
});

test('validated by', function () {
    expect(NonEmptyStringValidator::class)->toBe($this->nonEmptyString->validatedBy());
});
