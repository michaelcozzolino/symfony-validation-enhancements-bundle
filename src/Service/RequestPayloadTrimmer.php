<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use function is_array;
use function is_string;
use function json_encode;
use function trim;

class RequestPayloadTrimmer
{
    public function trim(Request $request): string
    {
        return json_encode(
            $this->trimParameters($request->getPayload()->all())
        );
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
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
