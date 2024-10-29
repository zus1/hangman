<?php

namespace App\Http\Requests;

use App\Constant\AuthEmailType;
use App\Constant\RouteName;
use App\Constant\TokenType;
use App\Helper\TokenGenerator;
use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserRequest extends FormRequest
{
    public function __construct(
        private TokenGenerator $tokenGenerator,
        private UserRepository $repository,
    ){
        parent::__construct();
    }

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
        if($this->route()->action['as'] === RouteName::AUTH_REGISTER) {
            return $this->registerRules();
        }
        if($this->route()->action['as'] === RouteName::AUTH_LOGIN) {
            return $this->loginRules();
        }
        if($this->route()->action['as'] === RouteName::AUTH_RESET_PASSWORD_SEND) {
            return $this->emailRule(checkType: 'exists');
        }
        if($this->route()->action['as'] === RouteName::AUTH_RESET_PASSWORD) {
            return $this->passwordWithConfirmationRule();
        }
        if($this->route()->action['as'] === RouteName::AUTH_EMAIL_RESEND) {
            return $this->emailResendRules();
        }

        throw new HttpException(500, 'Unsupported route');
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if($this->route()->action['as'] === RouteName::AUTH_RESET_PASSWORD_SEND) {
                    $this->additionalResetPasswordSendRules($validator);
                }
            }
        ];
    }

    private function emailResendRules(): array
    {
        return [
            'type' => [
                'required',
                Rule::in(AuthEmailType::values()),
            ],
            ...$this->addIdentifierRules(),
        ];

    }

    private function addIdentifierRules(): array
    {
        $type = $this->input('type');
        if($type === null) {
            return [];
        }

        if($type === AuthEmailType::VERIFY) {
            return  [
                'identifier' => [
                    'required',
                    'string',
                    function (string $attribute, string $value, \Closure $failed) {
                        if($this->tokenGenerator->isToken($value, TokenType::VERIFY_EMAIL) === false) {
                            $failed(sprintf('%s must be a valid token', $attribute));
                        }
                    }
                ]
            ];
        }
        if($type === AuthEmailType::RESET_PASSWORD) {
            return [
                'identifier' => $this->emailRule(checkType: 'exists')['email'],
            ];
        }

        throw new HttpException(400, 'Invalid email type '.$type);
    }

    private function additionalResetPasswordSendRules(Validator $validator): void
    {
        /** @var ?User $user */
        $user = $this->repository->findOneBy(['email' => $this->input('email')]);

        if($user === null) {
            return; //let if fail in previous step
        }

        if($user->email_verified_at === null) {
            $validator->errors()->add('verified', 'Email not verified');
        }
    }

    private function registerRules(): array
    {
        return [
            ...$this->emailRule(),
            ...$this->passwordWithConfirmationRule(),
            'nickname' => 'required|string|max:20',
        ];
    }

    private function loginRules(): array
    {
        return [
            ...$this->emailRule(checkType: 'exists'),
            ...$this->passwordRule(),
        ];
    }

    private function passwordWithConfirmationRule(): array
    {
        return [
            ...$this->passwordRule(),
            'confirm_password' => 'same:password',
        ];
    }

    private function passwordRule(): array
    {
        return [
            'password' => [
                'required',
                Password::min(8)
                    ->letters()
                    ->numbers()
                    ->mixedCase()
                    ->symbols()
                    ->uncompromised(),
            ],
        ];
    }

    private function emailRule(string $checkType = 'unique'): array
    {
        return [
            'email' => sprintf('required|email|%s:users,email', $checkType),
        ];
    }
}
