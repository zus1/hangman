<?php

namespace App\Repository;

use App\Constant\Pagination;
use App\Constant\Result;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class GameRepository extends BaseRepository
{
    protected const MODEL = Game::class;

    protected function baseCreate(Game $game): Game
    {
        $game->is_finished = false;

        $this->associateUser($game);

        return $game;
    }

    public function findForUser(array $data, User $user): LengthAwarePaginator
    {
        $builder = $this->getBuilder();

        $query =  $builder->whereRelation('user', 'id', $user->id);

        $this->applyFilters($query, $data);

        return $query->orderBy($data['order_by'] ?? Pagination::DEFAULT_COLUMN, $data['order_direction'] ?? 'DESC')
            ->paginate($data['per_page'] ?? Pagination::DEFAULT_PER_PAGE)
            ->withQueryString();
    }

    protected function updateFinished(Game $game, string $result): void
    {
        $game->is_finished = true;
        $game->result = $result;
    }

    private function applyFilters(Builder $query, array $data): void
    {
        $wheres = $this->sanitizeWheres($data);
        $filters = $this->getFilters($wheres);
        $rangeFilters = $this->getRangeFilters($wheres);

        foreach ($filters as $column => $value) {
            $query->where($column, $value);
        }
        foreach($rangeFilters as $column => $range) {
            $query->whereBetween($column, $range);
        }

    }

    private function getFilters(array $wheres): array
    {
        return array_filter($wheres, function (string $key) {
            return str_starts_with($key, 'from') === false && str_starts_with($key, 'to') === false;
        }, ARRAY_FILTER_USE_KEY);
    }

    private function getRangeFilters(array $wheres): array
    {
        $rangeFilters = [];

        foreach($wheres as $key => $value) {
            if(str_starts_with($key, 'from')) {
                $prefixKey = explode(':', $key);
                $to = $wheres['to:'.$prefixKey[1]];
                $rangeFilters[$prefixKey[1]] = [$value, $to];
            }
        }

        return $rangeFilters;
    }

    private function sanitizeWheres(array $data): array
    {
        $excluded = ['order_by', 'order_direction', 'per_page', 'page'];

        return array_filter($data, function (string $key) use ($excluded) {
            return !in_array($key, $excluded);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function associateUser(Game $game): void
    {
        /** @var ?User $user */
        $user = Auth::user();

        if($user === null) {
            return;
        }

        $game->user()->associate($user);
    }
}
