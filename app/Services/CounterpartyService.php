<?php

namespace App\Services;

use App\DTO\CreateCounterpartyDTO;
use App\Exceptions\CounterpartyUniqueException;
use App\Models\Counterparty;
use App\Services\ExternalApi\DadataService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

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
     *
     * @throws CounterpartyUniqueException
     */
    public function create(CreateCounterpartyDTO $dto): Collection
    {
        $rawDataCounterparty = $this->dadataService->getOrganizations(inn: $dto->inn);

        $dataCounterparty = array_map(fn ($item) => [
            'name' =>  $item['data']['name']['short_with_opf'],
            'ogrn' => $item['data']['ogrn'],
            'address' => $item['data']['address']['unrestricted_value'],
        ], $rawDataCounterparty);

        DB::beginTransaction();

        try {
            $result = $dto->user->counterparties()->createMany(records: $dataCounterparty);
            DB::commit();
            return $result;
        } catch (QueryException $e) {
            DB::rollBack();
            if ($e->getCode() === '23505') {
                $existsOgrns = $dto->user
                    ->counterparties()
                    ->whereIn(column: 'ogrn', values: array_map(fn ($item) => $item['ogrn'], $dataCounterparty))
                    ->pluck(column: 'ogrn')
                    ->toArray();
                throw new CounterpartyUniqueException(
                    userId: $dto->user->id,
                    ogrns: $existsOgrns
                );
            }
            throw $e;
        }
    }
}
