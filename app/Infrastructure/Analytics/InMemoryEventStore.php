<?php

namespace App\Infrastructure\Analytics;

use App\Domain\Analytics\EventStore;

/*
Notas: Esse codigo ficou responsavel por armazenas os eventos em memoria,
ele tem um array associativo onde a chave é o id do evento e o valor o valor boleano
indicando se o evento ja foi ou não visto no processo de inserção,. O insertIgnoringDuplicates
percorre o array verificando se o id do evento já existe ou não para definir so o insert é valido ou se é duplicado.
Por final o mesmo retornar um resposta com a quantidade de eventos inseridos e duplicados.
*/


class InMemoryEventStore implements EventStore
{
    /** @var array<string, bool> */
    // Array associativo para rastrabilidade de eventos já vistos ou não.
    private array $seen = [];
    public function insertIgnoringDuplicates(array $events): array
    {
        $inserted = 0;
        $duplicates = 0;

        // percorre os eventos recebidos para verificação de duplicados.
        foreach ($events as $event) {
            $id = $event['event_id'] ?? null;
            if (!$id) continue;

            if (isset($this->seen[$id])) {
                $duplicates++;
                continue;
            }

            $this->seen[$id] = true;
            $inserted++;
        }

        return compact('inserted', 'duplicates');
    }
}
