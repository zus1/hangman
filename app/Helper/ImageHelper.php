<?php

namespace App\Helper;

use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ImageHelper
{
    private array $images = [];

    public function __construct()
    {
        $this->load();
    }

    public function get(int $numOfMistakes)
    {
        return $this->images[$numOfMistakes] ?? throw new HttpException(500,
            'Num of mistakes mismatch, can not find image for '.$numOfMistakes.' mistakes' );
    }

    public function getImage(string $image): string
    {
        return URL::to('/images/'.$image);
    }

    private function load(): void
    {
        $images = array_filter(scandir(public_path('/images/hangman')), function (string $image) {
            return $image !== '.' && $image !== '..';
        });

        array_walk($images, function (string $image) {
            $nameExtension = explode('.', $image);
            $this->images[$nameExtension[0]] = URL::to('/images/hangman/'.$image);
        });
    }
}
