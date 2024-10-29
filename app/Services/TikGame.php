<?php

namespace App\Services;

use App\Constant\Result;
use App\Models\Tik;
use App\Models\TikBlueprint;
use App\Repository\TikBlueprintRepository;
use App\Repository\TikRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TikGame
{
    public function __construct(
        private TikRepository $repository,
        private TikBlueprintRepository $blueprintRepository,
    ){
    }

    private int $marker;
    private array $positionalBlueprint = [];
    private array $directionalBlueprint = [];
    private ?array $workingBlueprint = null;

    //blocking players streak
    private int $allowedSkips = 0;
    private int $allowedSkipsInitial = 0;
    private ?int $skippedField = null;

    public function play(array $data, Tik $tik): Tik
    {
        $this->loadBlueprints($tik->fields_num);
        $this->setMarker($tik, $data);

        if($data['player'] === 'player') {
            return $this->handlePlayer($tik, $data);
        }
        if($data['player'] === 'opponent') {
            return $this->handleOpponent($tik);
        }

        throw new HttpException(400, 'Unknown player type '. $data['player']);
    }

    private function loadBlueprints(int $fieldsNum): void
    {
        $blueprints = $this->blueprintRepository->findByFieldsNum($fieldsNum)->all();

        array_walk($blueprints, function (TikBlueprint $blueprint) {
            if($blueprint->type === \App\Constant\TikBlueprint::TYPE_DIRECTIONAL) {
                $this->directionalBlueprint = $blueprint->blueprint;
            }
            if($blueprint->type === \App\Constant\TikBlueprint::TYPE_POSITIONAL) {
                $this->positionalBlueprint = $blueprint->blueprint;
            }
        });
    }

    private function setMarker(Tik $tik, array $data): void
    {
        $this->marker = $data['player'] === 'player' ? $tik->starts : (int) !$tik->starts;
    }

    private function handlePlayer(Tik $tik, array $data): Tik
    {
        $this->repository->updateGrid($data, $tik, $this->marker);

        $this->determineStreak($tik, $data['position']);

        $this->finishIfPossible($tik,Result::GUESSED);

        return $tik;
    }

    private function handleOpponent(Tik $tik): Tik
    {
        $position = $this->determineOpponentsMove($tik);

        if($position === null) {
            $this->repository->updateResult($tik, Result::TIE);

            return $tik;
        }

        $this->repository->updateGrid(['position' => $position], $tik, $this->marker);

        $this->determineStreak($tik, $position);

        $this->finishIfPossible($tik, Result::NOT_GUESSED);

        return $tik;
    }

    private function finishIfPossible(Tik $tik, string $result): void
    {
        if($tik->isStreakOnce()) {
            $this->repository->updateResult($tik, $result);
        }
        if(!in_array(null, $tik->grid, strict: true)) {
            $this->repository->updateResult($tik, Result::TIE);
        }
    }

    private function determineOpponentsMove(Tik $tik): ?int
    {
        $opponentsFields = array_filter($tik->grid, function (mixed $value) {
            return $value === $this->marker;
        });

        if(($position = $this->opponentFinishStreak($tik, $opponentsFields)) !== null) {
            return $position;
        }

        if(($position = $this->blockPlayersStreak($tik, allowedSkips: 1)) !== null) {
            return $position;
        }

        if($opponentsFields === [] && ($randomPosition = $this->opponentRandomPosition($tik)) !== null) {
            return $randomPosition;
        }

        if(($position = $this->opponentContinueStreak($tik)) !== null) {
            return $position;
        }

        if(
            $tik->fields_num > \App\Constant\TikBlueprint::DEFAULT_FIELDS_NUM &&
            ($position = $this->blockPlayersStreak($tik, allowedSkips: 2)) !== null
        ) {
            return $position;
        }

        return $randomPosition ?? $this->opponentRandomPosition($tik);
    }

    private function opponentFinishStreak(Tik $tik, array $opponentFields): ?int
    {
        $skippedField = $this->getSkippedField($tik, $opponentFields, allowedSkips: 1);

        $this->resetSkips();

        return $skippedField;
    }

    private function resetSkips(): void
    {
        $this->setAllowedSkips(0);
        $this->skippedField = null;
    }

    private function opponentRandomPosition(Tik $tik): ?int
    {
        $actions = [
            'opponentCornerPosition',
            'opponentCentralPosition',
            'opponentAnyPosition',
        ];

        foreach ($actions as $action) {
            if(($position = $this->$action($tik)) !== null) {
                return $position;
            }
        }

        return null;
    }

    private function opponentCornerPosition(Tik $tik): ?int
    {
        $corners = [
            $tik->fields_num,
            $tik->fields_num - $tik->streak_length + 1,
            $tik->fields_num - ($tik->streak_length - 1) * $tik->streak_length,
            $tik->fields_num - $tik->streak_length * $tik->streak_length + 1,
        ];

        while($corners !== []) {
            $key = array_rand($corners);
            $position = $corners[$key];

            if($this->isPositionEmpty($tik, $position) === true) {
                return $position;
            }

            unset($corners[$key]);
        }

        return null;
    }

    private function opponentCentralPosition(Tik $tik): ?int
    {
        $position = (int) ceil($tik->fields_num / $tik->streak_length);

        return $this->isPositionEmpty($tik, $position) ? $position : null;
    }

    private function opponentAnyPosition(Tik $tik): ?int
    {
        $grid = $tik->grid;

        $this->kshuffle($grid);

        foreach (array_keys($grid) as $position) {
            if($this->isPositionEmpty($tik, $position)) {
                return $position;
            }
        }

        return null;
    }

    private function isPositionEmpty(Tik $tik, ?int $position): bool
    {
        return $tik->grid[$position] === null;
    }

    private function kshuffle(array &$array): void
    {
        $keys = array_keys($array);
        shuffle($keys);

        $shuffled = [];
        array_walk($keys, function (int $key) use (&$shuffled, $array) {
            $shuffled[$key] = $array[$key];
        });

        $array = $shuffled;
    }

    private function opponentContinueStreak(Tik $tik): ?int
    {
        $streaks = $this->getStreaks($tik);

        foreach ($streaks as $matches) {
            if(count($matches) === 1) {
                if(($position = $this->randomDetermineNextField($tik, $matches[0])) === null) {
                    continue;
                }

                return $position;
            }

            if(($position = $this->determineNextStreakField($tik, $matches)) !== null) {
                return $position;
            }
        }

        return null;
    }

    private function getStreaks(Tik $tik): array
    {
        $streaks = [];

        $opponentFields = array_filter($tik->grid, function (mixed $value) {
            return $value === $this->marker;
        });

        foreach(array_keys($opponentFields) as $opponentField) {
            $matches = [];
            $this->determineStreak($tik, $opponentField, $matches);
            $streaks[] = $matches;
        }

        return $this->sortStreaks($streaks);
    }

    private function sortStreaks(array $streaks): array
    {
        usort($streaks, function (array $one, array $two) {
            return count($one) > count($two);
        });

        return $streaks;
    }

    private function determineNextStreakField(Tik $tik, array $matches): ?int
    {
        $previous = $matches[count($matches) - 2];
        $current = $matches[count($matches) - 1];

        return $this->doDetermineNextStreakField($tik, $previous, $current);
    }


    private function doDetermineNextStreakField(Tik $tik, int $previous, int $current): ?int
    {
        $position = null;

        while(array_key_exists($previous, ($directionalBlueprint = $this->directionalBlueprint[$current]))) {
            $next = $directionalBlueprint[$previous];

            if($position === null && is_null($tik->grid[$next])) {
                $position = $next;
            }

            if($tik->grid[$next] === (int) !$this->marker) {
                $position = null;

                break;
            }

            $previous = $current;
            $current = $next;
        }

        return $position;
    }

    private function randomDetermineNextField(Tik $tik, int $current): ?int
    {
        $positionalBlueprint = $this->positionalBlueprint[$current];

        while ($positionalBlueprint !== []) {
            $key = array_rand($positionalBlueprint);
            $next = $positionalBlueprint[$key];
            unset($positionalBlueprint[$key]);

            if(is_null($tik->grid[$next]) && $this->doDetermineNextStreakField($tik, $current, $next) !== null) {
                return $next;
            }
        }

        return null;
    }


    private function blockPlayersStreak(Tik $tik, int $allowedSkips): ?int
    {
        $this->reverseMarker();

        $position = $this->doBlockPlayersStreak($tik, $allowedSkips);

        $this->reverseMarker();
        $this->resetSkips();

        return $position;
    }

    private function doBlockPlayersStreak(Tik $tik, $allowedSkips): ?int
    {
        $playersFields = array_filter($tik->grid, function (mixed $value) {
            return $value === $this->marker;
        });

        if($playersFields === []) {
            return null;
        }

        $this->kshuffle($playersFields);

        return $this->getSkippedField($tik, $playersFields, $allowedSkips);
    }

    private function getSkippedField(Tik $tik, array $fields, int $allowedSkips): ?int
    {
        foreach(array_keys($fields) as $field) {
            $this->setAllowedSkips($allowedSkips);
            $matches = [];
            $this->determineStreak($tik, $field, $matches);

            if($tik->isStreakOnce()) {
                return $this->skippedField;
            }
        }

        return null;
    }

    private function setAllowedSkips(int $allowedSkips): void
    {
        $this->allowedSkips = $this->allowedSkipsInitial = $allowedSkips;
    }

    private function resetAllowedSkips(): void
    {
        $this->allowedSkips = $this->allowedSkipsInitial;
    }

    private function determineStreak(Tik $tik, int $initial, array &$matches = []): void
    {
        $this->setWorkingBlueprint($initial);

        $this->doDetermineStreak($tik, $initial,$matches);

        $this->resetWorkingBlueprint();
    }

    private function doDetermineStreak(Tik $tik, int $initial, array &$matches = []): void
    {
        $streak = 1;

        $matches = $this->addStreakMatch($matches, $initial);

        while ($this->workingBlueprint !== [] && $tik->isStreak() === false) {
            $next = $this->workingBlueprint[0];
            $this->unsetFromWorkingBlueprint();

            if($this->isFieldAllowed($tik, $next) === false) {
                $this->doDetermineStreak($tik, $initial, $matches);

                continue;
            }

            $matches = $this->increaseStreak($streak, $matches, $next);

            $this->streakDirection($tik, $initial, $initial, $next, $streak, $matches);
        }
    }

    private bool $reverse = false;
    private function reverse(bool $reverse = true): self
    {
        $this->reverse = $reverse;

        return $this;
    }

    private function isReverse(): bool
    {
        return $this->reverse;
    }


    private function streakDirection(Tik $tik, int $initial, int $previous, int $next, int &$streak, array &$matches): void
    {
        if($streak >= $tik->streak_length) {
            $tik->setIsStreak();

            return;
        }

        $directionalGrid = $this->directionalBlueprint[$next];

        if(!array_key_exists($previous, $directionalGrid) || $this->isFieldAllowed($tik, $directionalGrid[$previous]) === false) {
            if($this->isReverse() === false) {
                $this->reverse()->streakDirection($tik, $initial, $matches[count($matches) - 1], $initial, $streak, $matches);
            }
            if($this->isReverse() === true) {
                $this->resetAllowedSkips();
                $this->reverse(false)->doDetermineStreak($tik, $initial, $matches);
            }
        } else {
            $matches = $this->increaseStreak($streak, $matches, $directionalGrid[$previous]);

            $this->streakDirection($tik, $initial, $next, $directionalGrid[$previous], $streak, $matches);
        }
    }

    private function increaseStreak(&$streak, array $matches, int $entry): array
    {
        $streak++;

        return $this->addStreakMatch($matches, $entry);
    }

    private function addStreakMatch(array $matches, int $field): array
    {
        if(!in_array($field, $matches)) {
            $matches[] = $field;
        }

        return $matches;
    }

    private function setWorkingBlueprint(int $initial): void
    {
        if($this->workingBlueprint === null) {
            $this->workingBlueprint = $this->positionalBlueprint[$initial];
        }
    }

    private function resetWorkingBlueprint(): void
    {
        $this->workingBlueprint = null;
    }

    private function unsetFromWorkingBlueprint(): void
    {
        unset($this->workingBlueprint[0]);
        $this->workingBlueprint = array_values($this->workingBlueprint);
    }

    private function isFieldAllowed(Tik $tik, int $field): bool
    {
        $allowed = $tik->grid[$field] === $this->marker;

        if($allowed === false && $this->allowedSkips > 0 && $this->isPositionEmpty($tik, $field)) {
            $this->allowedSkips--;
            $allowed = true;
            $this->skippedField = $field;
        }

        return $allowed;
    }

    private function reverseMarker(): void
    {
        $this->marker = (int) !$this->marker;
    }
}
