<?php declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\EntityExists;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\EntityExistsValidator;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

covers(EntityExistsValidator::class, EntityExists::class);

beforeEach(function () {
    $this->entityManagerMock = Mockery::mock(EntityManagerInterface::class);
    $this->contextMock       = Mockery::mock(ExecutionContextInterface::class);

    $this->entityExistsValidator = new EntityExistsValidator($this->entityManagerMock);
    $this->entityExistsValidator->initialize($this->contextMock);
});

test('validate when constraint is not of the right type', function () {
    $this->entityManagerMock->expects('getRepository')->never();

    $this->entityExistsValidator->validate(2, new Length(1));
})->throws(UnexpectedTypeException::class);

test('validate when value is empty', function (?string $value) {
    $this->entityManagerMock->expects('getRepository')->never();

    $this->entityExistsValidator->validate($value, new EntityExists('class'));
})->with([
    '',
    '    ',
    null,
]);

test('validate when entity exists', function () {
    $repositoryMock = Mockery::mock(EntityRepository::class);

    $entityClass = 'class';
    $constraint  = new EntityExists($entityClass);

    $this->entityManagerMock->expects('getRepository')
                            ->once()
                            ->with($entityClass)
                            ->andReturn($repositoryMock);

    $entityId = 2;

    $entity = new class {
    };

    $repositoryMock->expects('findOneBy')
                   ->once()
                   ->with([
                       'id' => $entityId,
                   ])
                   ->andReturn($entity);

    $this->entityExistsValidator->validate($entityId, $constraint);
});

test('validate when entity does not exist', function (string $entityClass, string $entityProperty, ?string $entityName, int | string $value) {
    $repositoryMock = Mockery::mock(EntityRepository::class);

    $constraint = new EntityExists($entityClass, $entityProperty, $entityName);

    $this->entityManagerMock->expects('getRepository')
                            ->once()
                            ->with($entityClass)
                            ->andReturn($repositoryMock);

    $repositoryMock->expects('findOneBy')
                   ->once()
                   ->with([
                       $entityProperty => $value,
                   ])
                   ->andReturn(null);

    $name = $entityName ?? 'Entity';
    $this->contextMock->expects('addViolation')
                      ->once()
                      ->with("The requested `$name` does not exist.");

    $this->entityExistsValidator->validate($value, $constraint);
})->with([
    [Entity::class, 'value', 'entity', 2],
    [Entity::class, 'id', 'entity', 'p'],
    [Entity::class, 'id', null, 'p'],
]);
