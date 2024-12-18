<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint;

use Doctrine\ORM\EntityManagerInterface;
use ReflectionException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use function is_string;
use function trim;

/**
 * @psalm-api
 * @psalm-suppress PropertyNotSetInConstructor $context is set in {@see ConstraintValidator::initialize}
 */
class EntityExistsValidator extends ConstraintValidator
{
    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     *
     * @throws ReflectionException
     *
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($constraint instanceof EntityExists === false) {
            throw new UnexpectedTypeException($constraint, EntityExists::class);
        }

        if (
            $value === null ||
            (is_string($value) && trim($value) === '')
        ) {
            return;
        }

        $repository = $this->entityManager->getRepository($constraint->getEntityClass());

        $entity = $repository->findOneBy([
            $constraint->getEntityProperty() => $value,
        ]);

        $validateExistence = $constraint->validateExistence();

        $entityName = $constraint->getEntityName();

        if ($validateExistence && $entity === null) {
            $this->context->addViolation("The requested `$entityName` does not exist.");

            return;
        }

        if ($validateExistence === false && $entity !== null) {
            $this->context->addViolation("The requested `$entityName` already exists.");
        }
    }
}
