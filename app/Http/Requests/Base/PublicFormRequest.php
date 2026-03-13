<?php
namespace App\Http\Requests\Base;

//classe abstrata par reciclar regra de autorização para formrequest de endpoint publico
abstract class PublicFormRequest extends BaseFormRequest
{
    final protected function authorizeRequest(): bool
    {
        return true;
    }
}
