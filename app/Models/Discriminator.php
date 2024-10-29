<?php

namespace App\Models;

use App\Events\DiscriminatorEvent;
use Illuminate\Database\ClassMorphViolationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Discriminator $parent
 */
abstract class Discriminator extends Model
{
    protected const PARENT = '';

    private static array $_parentAttributes = [];

    protected $dispatchesEvents = [
        'created' => DiscriminatorEvent::class,
        'updated' => DiscriminatorEvent::class,
        'retrieved' => DiscriminatorEvent::class,
    ];

    public function setHidden(array $hidden = []): self
    {
        $basic = [
            'parent',
            'child_id',
            'child_type',
        ];

        if(property_exists($this, 'hidden') && $this->hidden !== []) {
            $this->hidden = [
                ...$basic,
                ...$this->hidden,
            ];
        }

        return $this;
    }

    public function child(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): MorphOne
    {
        return $this->morphOne(static::PARENT, 'child');
    }

    private function getParentAttributes(): array
    {
        $attributes = self::$_parentAttributes;

        self::$_parentAttributes = [];

        return $attributes;
    }

    public function save(array $options = []): void
    {
        $rf = new \ReflectionClass($this);

        if(self::$_parentAttributes === []) {
            $modelProperties = $this->convertToPropertiesArray($rf);
            $modelAttributes = $this->modelAttributes($modelProperties);
            $this->setParentAttributes($modelAttributes);
        } else {
            $modelAttributes = $this->getParentAttributes();
        }

        $this->setRawAttributes($modelAttributes);

        $this->doSave($options);
    }

    public function saveQuietly(array $options = [])
    {
        return static::withoutEvents(function () use ($options) {
            if(isset($options['this'])) {
                $this->save($options);
            } else {
                parent::save($options);
            }
        });
    }

    private function doSave(array $options): void
    {
        $parent = $this->getParent() ?? $this->parent;

        if($parent === null) {
            $this->saveNew($options);

            return;
        }

        if($parent !== false) {
            $this->updateDiscriminator($parent, $options);

            return;
        }

        parent::save();
        $this->saveParent($options);
    }

    private function saveNew(array $options): void
    {
        $this->saveQuietly($options);
        $this->parent = $this->saveParent($options);

        $this->fireModelEvent('created', false);

    }

    private function updateDiscriminator(Discriminator $parent, array $options): void
    {
        foreach (self::$_parentAttributes as $attribute => $value) {
            $parent->setAttribute($attribute, $value);
        }

        $dirty = $this->isDirty() || $parent->isDirty();

        $this->saveQuietly($options);
        $this->saveParent($options, $parent);

        if($dirty === true) {
            $this->fireModelEvent('updated', $this);
        }
    }

    public function delete(): void
    {
        $this->deleteParent();

        parent::delete();
    }

    public function newModelQuery(): Builder
    {
        $newModelQuery = parent::newModelQuery();

        $newModelQuery->with('parent');

        return $newModelQuery;
    }

    private function saveParent(array $options, ?Discriminator $parent = null): ?Discriminator
    {
        $parent = $parent ?? $this->getParent();

        if($parent === false) {
            return null;
        }

        if($parent === null) {
            /** @var Discriminator $parent */
            $parent = new (static::PARENT);

            $parent->child()->associate($this);

            self::$_parentAttributes = [
                ...self::$_parentAttributes,
                ...$parent->getAttributes(),
            ];
        }

        $parent->saveQuietly([...$options, 'this' => true]);

        return $parent;
    }

    private function deleteParent(): void
    {
        $parent = $this->getParent();

        if($parent === null) {
            return;
        }

        $parent->delete();
    }


    private function getParent(): null|false|Discriminator
    {
        $parentRelationship = $this->getParentRelationship();

        if(get_class($parentRelationship->getParent()) === static::PARENT) {
            return false;
        }

        return $parentRelationship->first();
    }

    private function getParentRelationship(): ?MorphOne
    {
        try {
            $parentRelationship = $this->parent();
        } catch(ClassMorphViolationException) {
            return null;
        }

        return $parentRelationship;
    }

    private function convertToPropertiesArray(\ReflectionClass $rf): array
    {
        $doc = $rf->getDocComment();

        $arr = explode(PHP_EOL, $doc);
        array_pop($arr);
        array_shift($arr);

        $properties = [];
        array_walk($arr, function (string $value) use (&$properties) {
            if(str_contains($value, '@property')) {
                $parts = explode(' ', $value);

                $properties[] = substr($parts[count($parts) - 1], 1);
            }
        });

        return $properties;
    }

    private function modelAttributes(array $modelProperties): array
    {
        return array_filter($this->getAttributes(), function (string $key) use ($modelProperties) {
            return in_array($key, $modelProperties);
        }, ARRAY_FILTER_USE_KEY);
    }

    private function setParentAttributes(array $modelAttributes): void
    {
        self::$_parentAttributes = array_diff_key($this->getAttributes(), $modelAttributes);
    }
}
