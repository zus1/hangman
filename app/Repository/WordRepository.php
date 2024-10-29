<?php

namespace App\Repository;

use App\Constant\DateTime;
use App\Models\Language;
use App\Models\Word;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WordRepository extends BaseRepository
{
    protected const MODEL = Word::class;

    public function make(string $wordStr, Language $language): Word
    {
        $word = new Word();
        $word->created_at = Carbon::now()->format(DateTime::FORMAT);
        $word->word = $wordStr;
        $word->active = true;
        $word->language_id = $language->id;

        return $word;
    }

    public function insertIfNotExists(Collection $words): void
    {
        $wordsArr = $words->pluck('word')->all();

        $existing = $this->getBuilder()->whereIn('word', $wordsArr)->get();
        $existingArr = $existing->isEmpty() ? [] : $existing->pluck('word')->all();

        $notExisting = $words->filter(function (Word $word) use ($existingArr) {
            return !in_array($word->word, $existingArr);
        });

        if($notExisting->isNotEmpty()) {
            $this->getBuilder()->insert($notExisting->toArray());
        }

    }

    public function findRandom(string $language): Word
    {
        $builder = $this->getBuilder();

        return $builder->whereRelation('language', 'short', $language)
            ->orderBy(DB::raw('RAND()'))
            ->first();
    }
}
