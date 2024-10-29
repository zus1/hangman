<?php

namespace App\Http\Requests;

use App\Constant\RouteName;
use App\Models\Game;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HangmanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if($this->route()->action['as'] === RouteName::HANGMAN_PLAY) {
            return $this->playRules();
        }
        if($this->route()->action['as'] === RouteName::HANGMAN_CREATE) {
            return $this->createRules();
        }

        throw new HttpException(422, 'Unprocessable entity');


    }

    private function createRules(): array
    {
        return [
            'language' => 'string|in:en,hr'
        ];
    }

    private function playRules(): array
    {
        $this->additionalPlayRules();

        $rules = [
            'word' => 'string|nullable',
            'letter' => 'string|nullable|max:1'
        ];

        if($this->input('word') === null) {
            $rules['letter'] .= '|required';
        }
        if($this->input('letter') === null) {
            $rules['word'] .= '|required';
        }

        return $rules;
    }

    private function additionalPlayRules(): void
    {
        /** @var Game $game */
        $game = $this->route('hangman');

        if($game->is_finished === true) {
            throw new HttpException(422, 'Game is already finished');
        }
    }

    public function messages(): array
    {
        return [
            'word' => [
                'required' => 'Word or letter are required',
            ],
            'letter' => [
                'required' => 'Word or letter are required',
            ],
        ];
    }
}
