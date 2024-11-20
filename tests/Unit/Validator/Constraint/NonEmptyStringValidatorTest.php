<?php declare(strict_types=1);

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Enum\MySqlStringLength;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyMySqlText;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyMySqlVarcharDefault;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyString;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyStringValidator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

covers(
    NonEmptyStringValidator::class,
    NonEmptyString::class,
    NonEmptyMySqlVarcharDefault::class,
    NonEmptyMySqlText::class
);

dataset('expected lengths', function () {
    return [
        [1],
        [2],
        [900],
        [null],
    ];
});

test('NonEmptyString must have min and max lengths', function (?int $max) {
    $constraint = new NonEmptyString($max);

    expect($constraint->min)->toBe(1);
    expect($constraint->max)->toBe($max);
})->with('expected lengths');

dataset('unexpected types', function () {
    return [
        [2, new NonEmptyString()],
        ['hi', new Email()],
    ];
});

test('validate when an unexpected type exception occurs', function (string | int $value, NonEmptyString | Email $constraint) {
    $this->validator->validate($value, $constraint);
})->with('unexpected types')
  ->throws(UnexpectedTypeException::class);

dataset('non empty strings or null', function () {
    return [
        [null, 'hello'],
        [null, null],
        [10, 'value'],
        [100, '          value with spaces    '],
        [4, '     four     ',],
    ];
});

test('non empty string or null is valid', function (?int $max, ?string $value) {
    $constraint = new NonEmptyString($max);

    $this->validator->validate($value, $constraint);

    $this->assertNoViolation();
})->with('non empty strings or null');

dataset('non empty string is not valid', function () {
    return [
        [50, '          ',],
        [502, '',],
    ];
});

test('non empty string is not valid', function (?int $max, string $value) {
    $constraint = new NonEmptyString($max);

    $this->validator->validate($value, $constraint);

    $this->assertViolations(1);
})->with('non empty string is not valid');

test('non empty varchar default is valid', function () {
    $this->validator->validate(
        $this->generateRandomString(MySqlStringLength::VarcharDefault->value),
        new NonEmptyMySqlVarcharDefault()
    );

    $this->assertNoViolation();
});

test('non empty text is valid', function () {
    $this->validator->validate(
        $this->generateRandomString(MySqlStringLength::Text->value),
        new NonEmptyMySqlText()
    );

    $this->assertNoViolation();
});

test('non empty varchar default is not valid', function () {
    $this->validator->validate(
        $this->generateRandomString(1000 + MySqlStringLength::VarcharDefault->value),
        new NonEmptyMySqlVarcharDefault()
    );

    $this->assertViolations(1);
});

test('non empty text is not valid', function () {
    $this->validator->validate(
        $this->generateRandomString(MySqlStringLength::Text->value + 9382),
        new NonEmptyMySqlText()
    );

    $this->assertViolations(1);
});
