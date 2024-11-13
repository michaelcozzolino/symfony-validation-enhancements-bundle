<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Service;

use MichaelCozzolino\PhpRedefinitions\JsonRedefinition;
use Symfony\Component\HttpFoundation\Request;
use function is_array;
use function is_string;
use function trim;

/**
 * @psalm-api
 */
class RequestPayloadTrimmer
{
    public function __construct(protected readonly JsonRedefinition $jsonRedefinition)
    {
    }

    public function trim(Request $request): string
    {
        $trimmedPayload = $this->jsonRedefinition->jsonEncode(
            $this->trimParameters($request->getPayload()->all())
        );

        if ($trimmedPayload === false) {
            return $request->getContent();
        }

        return $trimmedPayload;
    }

    /**
     * @param array<array-key, mixed> $parameters
     *
     * @return array<array-key, mixed>
     *
     * @psalm-suppress MixedAssignment https://github.com/vimeo/psalm/issues/4442
     */
    protected function trimParameters(array $parameters): array
    {
        $trimmedParameters = [];

        foreach ($parameters as $key => $parameter) {
            $trimmedParameters[$key] = is_array($parameter)
                ? $this->trimParameters($parameter)
                : (is_string($parameter) ? trim($parameter) : $parameter);
        }

        return $trimmedParameters;
    }
}
