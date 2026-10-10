<?php

namespace App\Http\Controllers\Api\Auth;

use App\DTOs\Auth\RegisterOwnerDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\RegisterAndContractService;
use Illuminate\Support\Facades\Response;

class RegisterController extends Controller
{
    public function __construct(
        private RegisterAndContractService $registerService
    ) {}

    public function store(RegisterRequest $request)
    {
        // Transforma o array validado em um objeto DTO tipado
        $dto = new RegisterOwnerDTO(
            userData: $request->only(['name', 'email', 'password']),
            companyData: $request->input('company'),
            planData: $request->input('plan')
        );

        $result = $this->registerService->registerOwner($dto);

        return Response::api($result, 'Empresa e usuário criados com sucesso.', 201);
    }
}
