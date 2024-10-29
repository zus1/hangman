<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class BaseRepository
{
    protected const MODEL = '';

    public function getBuilder(): Builder
    {
        /** @var Model $model */
        $model = new (static::MODEL);

        return $model->newModelQuery();
    }

    public function findOneBy(array $params): ?Model
    {
        $builder = $this->getBuilder();

        $this->addWheres($builder, $params);

        return $builder->first();
    }

    public function findOneByOr404(array $params): Model
    {
        $model = $this->findOneBy($params);

        if($model === null) {
            throw new HttpException(404, 'Model not found');
        }

        return $model;
    }

    public function findBy(array $params): Collection
    {
        $builder = $this->getBuilder();

        $this->addWheres($builder, $params);

        return $builder->get();
    }

    public function findByOr404(array $params): Collection
    {
        $collection = $this->findBy($params);

        if($collection->isEmpty()) {
            throw new HttpException(404, 'Models not found');
        }

        return $collection;
    }

    public function findById(int|string $id): ?Model
    {
        /** @var Model $model */
        $model = new (static::MODEL);

        $identifier = $model->getKeyName();

        return $this->findOneBy([$identifier => $id]);
    }

    public function findByIdOr404(int|string $id): Model
    {
        $model = $this->findById($id);

        if($model === null) {
            throw new HttpException(404, 'Model not found');
        }

        return $model;
    }

    public function findAll(): Collection
    {
        return $this->getBuilder()->get();
    }

    private function addWheres(Builder $builder, array $params): void
    {
        foreach ($params as $key => $value) {
            $builder->where($key, $value);
        }
    }
}
