<?php

namespace App\Http\Controllers\Tik;

use App\Constant\TikBlueprint;
use App\Repository\TikRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Create
{
    public function __construct(
        private TikRepository $repository,
    ){
    }

    public function __invoke(Request $request): JsonResponse
    {
        $tik = $this->repository->create($request->input('fields_num', TikBlueprint::DEFAULT_FIELDS_NUM));

        return new JsonResponse($tik);
    }
}
