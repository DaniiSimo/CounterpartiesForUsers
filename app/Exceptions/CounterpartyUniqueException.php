<?php

namespace App\Exceptions;

use Exception;

class CounterpartyUniqueException extends Exception
{
    public function __construct(
        public readonly int $userId,
        public readonly array $ogrns,
        ?string $message = null
    ) {
        parent::__construct(message: $message ?? 'Counterparty conflict (duplicate OGRN)');
    }

    public function context(): array
    {
        return ['user_id' => $this->userId, 'ogrns' => $this->ogrns];
    }
}
