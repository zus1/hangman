<?php

namespace App\Repository;

use App\Models\Tik;
use App\Models\TikBlueprint;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TikBlueprintRepository extends BaseRepository
{
    protected const MODEL = TikBlueprint::class;

    public function create(string $type, array $blueprint): TikBlueprint
    {
        $tikBlueprint = new TikBlueprint();
        $tikBlueprint->type = $type;
        $tikBlueprint->blueprint = $blueprint;
        $tikBlueprint->fields_num = count($blueprint);

        $tikBlueprint->save();

        return $tikBlueprint;
    }

    public function findByFieldsNum(int $fieldsNum): Collection
    {
        $builder = $this->getBuilder();

        return $builder->where('fields_num', $fieldsNum)->get();
    }

    public function findFieldsNum(): Collection
    {
        $builder = $this->getBuilder();

        return $builder->select(DB::raw('DISTINCT fields_num'))->orderBy('fields_num', 'ASC')->get();
    }
}
