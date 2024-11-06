<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Tests\Validator\Constraint;

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Enum\MySqlDatabaseStringLength;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Tests\Validator\ConstraintValidatorTestcase;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyString;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyStringValidator;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyText;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyVarcharDefault;
use PHPUnit\Framework\Attributes\DataProvider;
use Random\RandomException;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NonEmptyStringValidatorTest extends ConstraintValidatorTestCase
{
    public static function providerForValidateWhenAnUnexpectedTypeExceptionOccurs(): array
    {
        return [
            [2, new NonEmptyString()],
            ['hi', new Email()],
        ];
    }

    #[DataProvider('providerForValidateWhenAnUnexpectedTypeExceptionOccurs')]
    public function testValidateWhenAnUnexpectedTypeExceptionOccurs(string | int $value, NonEmptyString | Email $constraint): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validator->validate($value, $constraint);
    }

    public static function providerForNonEmptyStringIsValid(): array
    {
        return [
            [null, 'hello'],
            [null, null],
            [10, 'value'],
            [100, '          value with spaces    '],
            [4, '     four     ',],
        ];
    }

    #[DataProvider('providerForNonEmptyStringIsValid')]
    public function testNonEmptyStringIsValid(?int $max, ?string $value): void
    {
        $constraint = new NonEmptyString($max);

        $this->validator->validate($value, $constraint);

        $this->assertNoViolation();
    }

    public static function providerForNonEmptyStringIsNotValid(): array
    {
        return [
            [50, '          ',],
            [502, '',],
            [2, 'abc',],
        ];
    }

    #[DataProvider('providerForNonEmptyStringIsNotValid')]
    public function testNonEmptyStringIsNotValid(?int $max, string $value): void
    {
        $constraint = new NonEmptyString($max);

        $this->validator->validate($value, $constraint);

        $this->assertViolations(1);
    }

    public function testNonEmptyVarcharDefaultIsValid(): void
    {
        $this->validator->validate(
            $this->generateRandomString(MySqlDatabaseStringLength::VarcharDefault->value),
            new NonEmptyVarcharDefault()
        );

        $this->assertNoViolation();
    }

    public function testNonEmptyTextIsValid(): void
    {
        $this->validator->validate(
            $this->generateRandomString(MySqlDatabaseStringLength::Text->value),
            new NonEmptyText()
        );

        $this->assertNoViolation();
    }

    public function testNonEmptyVarcharDefaultIsNotValid(): void
    {
        $this->validator->validate(
            $this->generateRandomString(1000 + MySqlDatabaseStringLength::VarcharDefault->value),
            new NonEmptyVarcharDefault()
        );

        $this->assertViolations(1);
    }

    public function testNonEmptyTextIsNotValid(): void
    {
        $this->validator->validate(
            $this->generateRandomString(MySqlDatabaseStringLength::Text->value + 9382),
            new NonEmptyText()
        );

        $this->assertViolations(1);
    }

    /**
     * @param int $length
     *
     * @throws RandomException
     * @return string
     */
    protected function generateRandomString(int $length): string
    {
        $characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $index        = random_int(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new NonEmptyStringValidator();
    }
}
