<?php

namespace App\Modules\Auth\Http\Requests;

use App\Modules\Shared\Rules\Cpf;
use App\Modules\Shared\Rules\CpfOrCnpj;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tenant' => ['required', 'array'],
            'tenant.name' => ['required', 'string', 'max:255'],
            'tenant.document' => ['required', 'string', new CpfOrCnpj],
            'tenant.email' => ['required', 'string', 'email', 'max:255', 'unique:tenants,email'],
            'tenant.phone' => ['required', 'string', 'max:20'],
            'tenant.domain' => [
                'required', 'string', 'max:255',
                'regex:/^(?=.{1,253}$)((?!-)[a-z0-9-]{1,63}(?<!-)\.)+[a-z]{2,63}$/',
                'unique:tenants,domain',
            ],

            'user' => ['required', 'array'],
            'user.name' => ['required', 'string', 'max:255'],
            'user.email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'user.phone' => ['required', 'string', 'max:20'],
            'user.document' => ['required', 'string', new Cpf],
            'user.password' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $input = $this->all();

        if (Arr::has($input, 'tenant.document')) {
            Arr::set($input, 'tenant.document', (string) preg_replace('/\D+/', '', (string) Arr::get($input, 'tenant.document')));
        }

        if (Arr::has($input, 'tenant.domain')) {
            Arr::set($input, 'tenant.domain', Str::lower(trim((string) Arr::get($input, 'tenant.domain'))));
        }

        if (Arr::has($input, 'user.document')) {
            Arr::set($input, 'user.document', (string) preg_replace('/\D+/', '', (string) Arr::get($input, 'user.document')));
        }

        $this->replace($input);
    }
}
