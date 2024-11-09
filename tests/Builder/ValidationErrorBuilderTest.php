<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Tests\Builder;

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Builder\ValidationErrorBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationErrorBuilderTest extends TestCase
{
    protected ValidationErrorBuilder $validationErrorBuilder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validationErrorBuilder = new ValidationErrorBuilder();
    }

    public static function providerForBuild(): array
    {
        $propertyPath1     = 'name';
        $violationMessage1 = 'name is not valid';

        $propertyPath2     = 'person.name';
        $violationMessage2 = 'person name is not valid';

        $propertyPath3     = 'person.parent.name';
        $violationMessage3 = 'parent person name is not valid';

        $propertyPath4     = 'person.children[2].name';
        $violationMessage4 = 'children name is not valid';

        return [
            [
                $propertyPath1,
                $violationMessage1,
                [
                    $propertyPath1 => [$violationMessage1],
                ],
            ],
            [
                $propertyPath2,
                $violationMessage2,
                [
                    'person' => [
                        'name' => [$violationMessage2],
                    ],
                ],
            ],
            [
                $propertyPath3,
                $violationMessage3,
                [
                    'person' => [
                        'parent' => [
                            'name' => [$violationMessage3],
                        ],
                    ],
                ],
            ],
            [
                $propertyPath4,
                $violationMessage4,
                [
                    'person' => [
                        'children' => [
                            2 => [
                                'name' => [$violationMessage4],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('providerForBuild')]
    public function testBuild(string $propertyPathName, string $violationMessage, array $expectedErrors): void
    {
        $violationMock = $this->createMock(ConstraintViolation::class);

        $violationMock->expects(self::once())
                      ->method('getPropertyPath')
                      ->willReturn($propertyPathName);

        $violationMock->expects(self::once())
                      ->method('getMessage')
                      ->willReturn($violationMessage);

        $violationMocks = new ConstraintViolationList([$violationMock]);

        $actualErrors = $this->validationErrorBuilder->build($violationMocks);

        self::assertSame($expectedErrors, $actualErrors);
    }
}
