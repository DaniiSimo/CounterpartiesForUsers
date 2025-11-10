<?php

namespace App\Services\ExternalApi;

use App\Exceptions\ExternalApiException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Сервис для работы с внешним api Dadata
 */
final readonly class DadataService
{
    /** @const string Название сервиса */
    private const string NAME_SERVICE = 'dadata';
    /** @var PendingRequest Название сервиса */
    private PendingRequest $pendingRequest;
    /** @var array Ссылки для методов */
    private array $urls;
    /** @var ?string Канал логирования */
    private ?string $logChannel;
    public function __construct()
    {
        $dataConfig = config(key: 'external_apis.'.self::NAME_SERVICE, default: []);

        $this->pendingRequest = Http::withHeaders(
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Token '.$dataConfig['token'] ?? '',
            ]
        )
            ->timeout(seconds: $dataConfig['timeout'] ?? 5)
            ->connectTimeout(seconds: $dataConfig['connect_timeout'] ?? 3)
            ->retry(
                times: $dataConfig['retry']['times'] ?? 3,
                sleepMilliseconds: $dataConfig['retry']['sleep_ms'] ?? 200,
                throw:  false
            );

        $this->urls = $dataConfig['urls'] ?? [];
        $this->logChannel = $dataConfig['log_channel'] ?? null;
    }

    /**
     * Возвращает список организаций по ИНН
     *
     * @param string $inn Инн организации
     * @param int $count Количество организаций (по умолчанию 1)
     * @param string $branchType Головная организация (MAIN) или филиал (BRANCH) (по умолчанию MAIN)
     * @param string $type Юрлицо (LEGAL) или индивидуальный предприниматель (INDIVIDUAL) (по умолчанию LEGAL)
     * @param bool $actual Признак того, что запрашиваются только действующие компании (по умолчанию true)
     *
     * @return array Найденные организации
     */
    public function getOrganizations(string $inn, int $count = 1, string $branchType = 'MAIN', string $type = 'LEGAL', bool $actual = true): array
    {
        $payload = [
            'query' => $inn,
            'count' => $count,
            'type' => $type,
            'branch_type' => $branchType,
            "status" => $actual ? ['ACTIVE'] : ['LIQUIDATING', 'LIQUIDATED']
        ];
        if (!key_exists(key: 'organization', array: $this->urls)) {
            report(exception: new ExternalApiException(
                service: self::NAME_SERVICE,
                endpoint: '',
                payload: $payload,
                logChannel: $this->logChannel,
                message: 'The organization url from the dadata configuration is not found',
            ));
            return [];
        }

        $response = $this->pendingRequest
            ->post(url: $this->urls['organization'], data: $payload)
            ->throw(
                fn ($response) => report(exception: new ExternalApiException(
                    service: self::NAME_SERVICE,
                    endpoint: $this->urls['organization'],
                    status: $response?->status() ?? 0,
                    payload: $payload,
                    logChannel: $this->logChannel,
                    responseBody: $response?->body()
                ))
            );

        return $response?->json(key: 'suggestions', default: []) ?? [];
    }
}
