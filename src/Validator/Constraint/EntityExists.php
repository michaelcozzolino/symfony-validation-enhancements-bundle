<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint;

use Attribute;
use ReflectionException;
use Symfony\Component\Validator\Constraint;

/**
 * @psalm-api
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class EntityExists extends Constraint
{
    /**
     * @param class-string          $entityClass
     * @param bool                  $validateExistence true if you want to validate the existence or false if you want
     *                                                 to validate the non-existence of the entity
     * @param non-empty-string      $entityProperty    The property for which the entity must be retrieved
     * @param non-empty-string|null $entityName        An identifier to show in the validation message, if null the
     *                                                 entity class short name will be used
     * @param mixed|null            $options
     * @param list<string>|null     $groups
     * @param mixed|null            $payload
     */
    public function __construct(
        protected readonly string  $entityClass,
        protected readonly bool    $validateExistence = true,
        protected readonly string  $entityProperty = 'id',
        protected readonly ?string $entityName = null,
        mixed                      $options = null,
        ?array                     $groups = null,
        mixed                      $payload = null
    ) {
        parent::__construct($options, $groups, $payload); // @pest-mutate-ignore
    }

    /**
     * @return class-string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    public function validateExistence(): bool
    {
        return $this->validateExistence;
    }

    /**
     * @return non-empty-string
     */
    public function getEntityProperty(): string
    {
        return $this->entityProperty;
    }

    /**
     * @throws ReflectionException
     *
     * @return string|null
     */
    public function getEntityName(): ?string
    {
        return $this->entityName ?? (new \ReflectionClass($this->entityClass))->getShortName();
    }
}
