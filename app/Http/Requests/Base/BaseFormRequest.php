<?php

namespace App\Http\Requests\Base;

use Illuminate\Foundation\Http\FormRequest;

// classe abstrata par reciclar regra de autorização para formrequest de endpoint autenticado.
abstract class BaseFormRequest extends FormRequest
{
    final public function authorize(): bool
    {
        return $this->authorizeRequest();
    }

    abstract protected function authorizeRequest(): bool;

    abstract public function rules(): array;
}
