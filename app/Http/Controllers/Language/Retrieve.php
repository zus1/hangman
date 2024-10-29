<?php

namespace App\Http\Controllers\Language;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Retrieve
{
    public function __invoke(Request $request): Response
    {
        $lang = $request->input('language', 'en');

        if(!file_exists(lang_path(sprintf('/%s.json', $lang)))) {
            throw new HttpException(400, 'Language is not supported');
        }

        return new Response(file_get_contents(lang_path(sprintf('/%s.json', $lang))));
    }
}
