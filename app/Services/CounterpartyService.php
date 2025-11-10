<?php

namespace App\Services;

use App\DTO\CreateCounterpartyDTO;
use App\Models\Counterparty;
use App\Services\ExternalApi\DadataService;
use Illuminate\Database\Eloquent\Collection;

/**
 * Сервис для работы с контрагентом
 */
final readonly class CounterpartyService
{
    public function __construct(private DadataService $dadataService)
    {
    }

    /**
     * Создание контрагента
     *
     * @param CreateCounterpartyDTO $dto Данные для создания контрагента
     *
     * @return Collection<Counterparty> Созданные контрагенты
     */
    public function create(CreateCounterpartyDTO $dto): Collection
    {
        $rawDataCounterparty = $this->dadataService->getOrganizations(inn: $dto->inn);

        $dataCounterparty = array_map(fn ($item) => [
            'name' =>  $item['data']['name']['short_with_opf'],
            'ogrn' => $item['data']['ogrn'],
            'address' => $item['data']['address']['unrestricted_value'],
        ], $rawDataCounterparty);

        return $dto->user
            ->counterparties()
            ->createMany(records: $dataCounterparty);
    }
}
