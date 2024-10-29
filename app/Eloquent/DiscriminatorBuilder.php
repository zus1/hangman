<?php

namespace App\Eloquent;

use App\Models\Discriminator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DiscriminatorBuilder extends Builder
{
    public function get($columns = ['*']): Collection
    {
        $collection = parent::get($columns);

        if($collection->isEmpty()) {
            return $collection;
        }

        if($this->supported($collection) === false) {
            return $collection;
        }

        return $this->flattenParent($collection);
    }

    public function first($columns = ['*']): ?Model
    {
        $model = parent::first($columns);

        if($model === null) {
            return null;
        }

        if($this->supported($model) === false) {
            return $model;
        }

        return $this->flattenParent($model)->get(0);
    }

    private function flattenParent(Collection|Model $subject): Collection
    {
        $subject = $subject instanceof Model ? new Collection([$subject]) : $subject;

        $newCollection = $subject->map(function (Model $model) {
            if(!$model->hasAttribute('parent')) {
                return $model;
            }

            $parent = $model->getAttribute('parent');
            foreach ($parent as $column => $value) {
                if($column === 'id') {
                    continue;
                }
                $model->setAttribute($column, $value);
            }

            $model->setAttribute('parent', null);

            return $model;
        });

        return new Collection($newCollection->all());
    }

    private function supported(Collection|Model $subject): bool
    {
        $subject = $subject instanceof Collection ? $subject->get(0) : $subject;

        return $subject instanceof Discriminator;
    }
}
