<?php

namespace App\Console\Commands;

use App\Constant\TikBlueprint;
use App\Repository\TikBlueprintRepository;
use Illuminate\Console\Command;

class CreateTikBlueprints extends Command
{
    protected const DIAGONAL_DOWN_LEFT = 'down-left';
    protected const DIAGONAL_DOWN_RIGHT = 'down-right';
    protected const DIAGONAL_UP_LEFT = 'up-left';
    protected const DIAGONAL_UP_RIGHT = 'up-right';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-tik-blueprints {width} {height}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crate blueprint matrixes for tik tak toe game';

    private int $width;
    private int $height;

    private array $positionalMatrix;

    public function __construct(
        private TikBlueprintRepository $repository,
    ){
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->width = $width = (int) $this->argument('width');
        $this->height = $height =  (int) $this->argument('height');

        if($width !== $height) {
            $this->error('Width and height must be same');

            return 1;
        }

        $this->createPositionalMatrix($width * $height);
        $this->createDirectionalMatrix();

        return 0;
    }

    private function createPositionalMatrix(int $fields): void
    {
        $this->positionalMatrix = $matrix = $this->makePositionalMatrix($fields);

        $this->repository->create(TikBlueprint::TYPE_POSITIONAL, $matrix);

        $this->line(ucfirst(TikBlueprint::TYPE_POSITIONAL). ' blueprint created');
    }

    private function createDirectionalMatrix(): void
    {
        $matrix = $this->makeDirectionalMatrix();

        $this->repository->create(TikBlueprint::TYPE_DIRECTIONAL, $matrix);

        $this->line(ucfirst(TikBlueprint::TYPE_DIRECTIONAL). ' blueprint created');
    }

    private function makeDirectionalMatrix(): array
    {
        $matrix = [];
        foreach ($this->positionalMatrix as $field => $sub) {
            $directions = [];
            foreach ($sub as $allowedField) {
                if($field > $allowedField) {
                    $previous = $field + ($field - $allowedField);
                } else {
                    $previous = $field - ($allowedField - $field);
                }

                if($this->isOutOfGrid($previous) === true) {
                    continue;
                }
                if(!in_array($previous, $sub)) {
                    continue;
                }

                $directions[$previous] = $allowedField;
            }

            $matrix[$field] = $directions;
        }

        return $matrix;
    }

    private function isOutOfGrid(int $field): bool
    {
        return $field < 1 || $field > $this->width * $this->height;
    }

    private function makePositionalMatrix(int $fields): array
    {
        $matrix = [];
        for($i = 1; $i <= $fields; $i++) {
            $sub = [];
            if ($this->isLastRow($i) === false) {
                $sub[] = $i + $this->width;
            }
            if($this->isFirstRow($i) === false) {
                $sub[] = $i - $this->width;
            }
            if ($this->canGoPlus($i)) {
                $sub[] = $i + 1;
            }
            if ($this->canGoMinus($i)) {
                $sub[] = $i - 1;
            }
            if ($this->canGoDiagonal($i, self::DIAGONAL_DOWN_LEFT)) {
                $sub[] = $i + $this->width - 1;
            }
            if ($this->canGoDiagonal($i, self::DIAGONAL_DOWN_RIGHT)) {
                $sub[] = $i + $this->width + 1;
            }
            if ($this->canGoDiagonal($i, self::DIAGONAL_UP_RIGHT)) {
                $sub[] = $i - $this->width + 1;
            }
            if ($this->canGoDiagonal($i, self::DIAGONAL_UP_LEFT)) {
                $sub[] = $i - $this->width - 1;
            }

            $matrix[$i] = $sub;
            /*if ($i === 4) {
                dd([
                    'matrix' => $matrix,
                    'first' => $this->isFirstRow($i),
                    'last' => $this->isLastRow($i),
                    'minus' => $this->canGoMinus($i),
                    'plus' => $this->canGoPlus($i),
                    'down-right' => $this->canGoDiagonal($i, self::DIAGONAL_DOWN_RIGHT),
                    'down-left' => $this->canGoDiagonal($i, self::DIAGONAL_DOWN_LEFT),
                    'up-right' => $this->canGoDiagonal($i, self::DIAGONAL_UP_RIGHT),
                    'up-left' => $this->canGoDiagonal($i, self::DIAGONAL_UP_LEFT),
                ]);
            }*/
        }
        return $matrix;
    }

    private function canGoPlus(int $field): bool
    {
        $row = (int) ceil($field / $this->width);

        return $field + 1 <= $row * $this->width;
    }

    private function canGoMinus(int $field): bool
    {
        $row = ceil($field / $this->width);

        return $field - 1 > $row * $this->width - $this->width;
    }

    private function isLastRow(int $field): bool
    {
        return (int)ceil($field / $this->width) === $this->height;
    }

    private function isFirstRow(int $field): bool
    {
        return (int)ceil($field / $this->width) === 1;
    }

    private function canGoDiagonal(int $field, string $direction): bool
    {
        if(str_starts_with($direction, 'up') && $this->isFirstRow($field)) {
            return false;
        }

        if(str_starts_with($direction, 'down') && $this->isLastRow($field)) {
            return false;
        }

        return $this->canGoDirection($field, $direction);
    }

    private function canGoDirection(int $field, string $direction): bool
    {
        return match ($direction) {
            self::DIAGONAL_DOWN_LEFT,
            self::DIAGONAL_UP_RIGHT => $this->isRightDiagonal($field),
            self::DIAGONAL_DOWN_RIGHT,
            self::DIAGONAL_UP_LEFT => $this->isLeftDiagonal($field),
            default => false,
        };
    }

    private function isLeftDiagonal($field): bool
    {
        $diagonal = $this->leftDiagonalFields();
//dd($diagonal);
        return in_array($field, $diagonal);
    }

    private function isRightDiagonal($field): bool
    {
        $diagonal = $this->rightDiagonalFields();
//dd($diagonal);
        return in_array($field, $diagonal);
    }

    private function leftDiagonalFields(): array
    {
        $start = 1;
        $leftDiagonal = [$start];

        while($start < $this->width * $this->height) {
            $start = $start + $this->width + 1;
            $leftDiagonal[] = $start;
        }

        return $leftDiagonal;
    }

    private function rightDiagonalFields(): array
    {
        $start = $this->width;
        $rightDiagonal = [$start];

        while($start < ($this->width * $this->height - ($this->width - 1))) {
            $start = $start + $this->width - 1;
            $rightDiagonal[] = $start;
        }

        return $rightDiagonal;
    }
}
