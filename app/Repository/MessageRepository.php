<?php

namespace App\Repository;

use App\Models\Message;

class MessageRepository extends BaseRepository
{
    protected const MODEL = Message::class;

    public function findByLanguage(string $key, string $short): Message
    {
        $builder = $this->getBuilder();

        return $builder->where('key', $key)->whereRelation('language','short', $short)->first();
    }
}
