<?php

namespace App\Events;

use App\Models\Discriminator;
use Illuminate\Foundation\Events\Dispatchable;

class DiscriminatorEvent
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private Discriminator $model
    ){
    }

    public function getModel(): Discriminator
    {
        return $this->model;
    }
}
