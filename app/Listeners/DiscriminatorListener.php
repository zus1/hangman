<?php

namespace App\Listeners;

use App\Events\DiscriminatorEvent;
use App\Models\Discriminator;

class DiscriminatorListener
{
    /**
     * Handle the event.
     */
    public function handle(DiscriminatorEvent $event): void
    {
        $model = $event->getModel();

        /** @var ?Discriminator $parent */
        $parent = $model->parent()->first() ?? $model->parent;

        if($parent === null) {
            return;
        }

        $parent->setHidden();

        foreach ($parent->toArray() as $key => $attribute) {
            if($key === 'id') {
                continue;
            }
            $model->setAttribute($key, $attribute);
        }

        unset($model['parent']);
    }
}
