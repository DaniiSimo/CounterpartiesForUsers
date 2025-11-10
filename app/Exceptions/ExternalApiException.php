<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ExternalApiException extends Exception
{
    public function __construct(
        public readonly string $service,
        public readonly string $endpoint,
        public readonly int $status = 0,
        public readonly array $payload = [],
        public readonly ?string $logChannel = null,
        public readonly ?string $responseBody = null,
        ?string $message = null
    ) {
        parent::__construct(message: $message ?? "External API '$service' failed ($status)");
    }

    public function context(): array
    {
        $context = [
            'service' => $this->service,
            'endpoint' => $this->endpoint,
            'status' => $this->status,
            'payload' => $this->payload,
        ];

        if (!is_null($this->responseBody)) {
            $context['responseBody'] = $this->responseBody;
        }

        return $context;
    }

    public function report(): void
    {
        $channel = config(key: "logging.channels.{$this->logChannel}")
            ? $this->logChannel
            : 'external'
        ;

        Log::channel(channel: $channel)->error(message: $this->getMessage(), context: $this->context());
    }
}
