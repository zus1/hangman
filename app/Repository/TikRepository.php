<?php

namespace App\Repository;

use App\Constant\Result;
use App\Models\Tik;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TikRepository extends GameRepository
{
    protected const MODEL = Tik::class;

    public function create(int $fieldsNum): Tik
    {
        $tik = new Tik();

        $this->baseCreate($tik);

        $tik->fields_num = $fieldsNum;
        $tik->streak_length = (int) sqrt($fieldsNum);
        $tik->grid = $this->makeInitialGrid($fieldsNum);
        $tik->starts = random_int(0, 1);

        $tik->save();

        return $tik;
    }

    public function updateGrid(array $data, Tik $tik, int $marker): Tik
    {
        if(!array_key_exists($data['position'], $tik->grid)) {
            throw new HttpException(400, 'Unknown position');
        }

        /*if($tik->grid[$data['position']] !== null) {
            throw new HttpException(400, 'Position already taken');
        }*/

        $this->updateGridValue($tik, $data, $marker);

        $tik->save();

        return $tik;
    }

    private function updateGridValue(Tik $tik, array $data, int $marker): void
    {
        $grid = $tik->grid;

        $grid[$data['position']] = $marker;

        $tik->grid = $grid;
    }

    public function updateResult(Tik $tik, string $result): Tik
    {
        $this->updateFinished($tik, $result);

        $tik->save();

        return $tik;
    }

    private function makeInitialGrid(int $fieldsNum): array
    {
        $grid = [];
        for($i = 1; $i <= $fieldsNum; $i++) {
            $grid[$i] = null;
        }

        return $grid;
    }
}
