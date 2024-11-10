<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle\Service;

use LogicException;
use Symfony\Component\HttpFoundation\Request;
use function is_array;
use function is_string;
use function json_encode;
use function trim;

class RequestPayloadTrimmer
{
    public function trim(Request $request): string
    {
        $trimmedPayload = json_encode(
            $this->trimParameters($request->getPayload()->all())
        );

        if ($trimmedPayload === false) {
            throw new LogicException(
                sprintf('Unable to trim payload for request with payload %s', $request->getContent())
            );
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
