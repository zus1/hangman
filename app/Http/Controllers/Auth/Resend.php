<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\UserRequest;
use App\Services\Mailer;
use Illuminate\Http\JsonResponse;

class Resend
{
    public function __construct(
        private Mailer $mailer
    ){
    }

    public function __invoke(UserRequest $request): JsonResponse
    {
        $this->mailer->resend(
            identifier: $request->input('identifier'),
            type: $request->input('type')
        );

        return new JsonResponse([], JsonResponse::HTTP_OK);
    }
}
