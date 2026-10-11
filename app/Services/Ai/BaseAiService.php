<?php

namespace App\Services\Ai;

use App\Exceptions\AiServiceException;
use Illuminate\Validation\ValidationException;

class BaseAiService
{
    public function __construct(protected FastApiAiClient $aiClient) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function postJson(string $endpoint, array $payload): array
    {
        try {
            return $this->aiClient->postJson($endpoint, $payload);
        } catch (AiServiceException $exception) {
            $this->throwClientException($exception);
        }
    }

    /**
     * @param  array<string, string>  $fields
     * @return array<string, mixed>
     */
    protected function postMultipart(
        string $endpoint,
        string $field,
        string $contents,
        string $filename,
        ?string $mimeType = null,
        array $fields = [],
    ): array {
        try {
            return $this->aiClient->postMultipart($endpoint, $field, $contents, $filename, $mimeType, $fields);
        } catch (AiServiceException $exception) {
            $this->throwClientException($exception);
        }
    }

    protected function invalidResponse(): never
    {
        throw ValidationException::withMessages([
            'ai' => __('ai.invalid_response'),
        ]);
    }

    private function throwClientException(AiServiceException $exception): never
    {
        throw ValidationException::withMessages([
            'ai' => $exception->isClientError()
                ? __('ai.service_error')
                : __('ai.connection_failed'),
        ]);
    }
}
