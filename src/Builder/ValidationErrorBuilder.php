<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Builder;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraints\DivisibleBy;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use function explode;
use function implode;
use function strpos;
use function substr;

/**
 * @psalm-api
 */
class ValidationErrorBuilder
{
    /**
     * Builds an array of errors whose structure has a request-like shape depending on the request that has been
     * validated. If at least one validation fails the returned array will have the keys of the object parameters for
     * which the validation failed and will contain an array of errors.
     * Example:
     * The validation fails on c with the following constraints: {@see DivisibleBy} and {@see PositiveOrZero}
     * Request: { a: { b: 1, c: [-1, 0, 2] } }
     * Errors: { a: { c: ["DivisibleBy error message", "PositiveOrZero error message"] } }
     *
     * @param ConstraintViolationListInterface $violations
     *
     * @return array<array-key, mixed>
     */
    public function build(ConstraintViolationListInterface $violations): array
    {
        $errors = [];

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $errorMessages = $this->getErrorMessages($violations);

        foreach ($errorMessages as $propertyPath => $errorMessage) {
            $propertyAccessor->setValue(
                $errors,
                $this->convertPropertyPathToArrayProperty($propertyPath),
                $errorMessage
            );
        }

        /**
         * @noinspection PhpCastIsUnnecessaryInspection
         *
         * The return value will never be an object as an array is always passed to {@see PropertyAccessor::setValue},
         * but the cast is needed for both static analysis.
         */
        return (array) $errors;
    }

    /**
     * @param ConstraintViolationListInterface $violations
     *
     * @return array<string, list<string>>
     */
    protected function getErrorMessages(ConstraintViolationListInterface $violations): array
    {
        $errorMessages = [];

        foreach ($violations as $violation) {
            $errorMessages[$violation->getPropertyPath()][] = (string) $violation->getMessage();
        }

        return $errorMessages;
    }

    /**
     * The property path of each violation is a dot notation string containing the path of the object property
     * whose validation failed. In order to be able to automatically set it to an array we have to add the
     * brackets to each sub path.
     * E.G:
     * a.b.name => [a][b][name]
     * a.b[1].name => [a][b][1][name]
     *
     * @param string $propertyPath
     *
     * @return string
     */
    protected function convertPropertyPathToArrayProperty(string $propertyPath): string
    {
        $paths = explode('.', $propertyPath);

        $properties = [];
        foreach ($paths as $path) {
            $arrayElementBracketIndex = strpos($path, '[');

            if ($arrayElementBracketIndex !== false) {
                $property = '[' . substr($path, 0, $arrayElementBracketIndex) . ']' . substr($path, $arrayElementBracketIndex);
            } else {
                $property = '[' . $path . ']';
            }

            $properties[] = $property;
        }

        return implode('', $properties);
    }
}
