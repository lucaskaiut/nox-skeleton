<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Modules\ACL\Http\Resources\RoleResource;
use App\Modules\Auth\DTOs\AuthenticatedUser;
use App\Modules\Auth\DTOs\NewTenantData;
use App\Modules\Auth\DTOs\NewUserData;
use App\Modules\Auth\Http\Requests\LoginRequest;
use App\Modules\Auth\Http\Requests\RegisterRequest;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Shared\Http\Controllers\ApiController;
use App\Modules\Tenant\Http\Resources\TenantResource;
use App\Modules\Tenant\Support\Facades\TenantContext;
use App\Modules\User\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    public function __construct(private readonly AuthService $service) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->service->register(
            NewTenantData::fromArray($request->validated('tenant')),
            NewUserData::fromArray($request->validated('user')),
        );

        return $this->created(
            $this->authPayload($result),
            'Cadastro realizado com sucesso.',
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->service->login(
            $request->validated('email'),
            $request->validated('password'),
        );

        return $this->success($this->authPayload($result), 'Login realizado com sucesso.');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->service->logout($request->user());

        return $this->success(null, 'Logout realizado com sucesso.');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('roles.permissions');

        return $this->success([
            'user' => UserResource::make($user),
            'tenant' => TenantResource::make(TenantContext::tenant()),
            'roles' => RoleResource::collection($user->roles),
            'permissions' => $user->permissionValues(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function authPayload(AuthenticatedUser $result): array
    {
        return [
            'token' => $result->token,
            'token_type' => 'Bearer',
            'user' => UserResource::make($result->user),
            'tenant' => TenantResource::make($result->tenant),
        ];
    }
}
