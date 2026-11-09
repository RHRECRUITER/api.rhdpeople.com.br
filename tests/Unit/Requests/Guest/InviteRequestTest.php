<?php

namespace Tests\Unit\Requests\Guest;

use App\Http\Requests\Guest\InviteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class InviteRequestTest extends TestCase
{
    public function test_regra_de_expiracao_eh_array_e_nao_string_unica(): void
    {
        $request = new InviteRequest(Request::create('/invite/PreRegister'));
        $rules = $request->rules();

        $this->assertIsArray($rules['expiration_date']);
        $this->assertNotSame('required, date', $rules['expiration_date']);
        $this->assertContains('required', $rules['expiration_date']);
        $this->assertContains('date', $rules['expiration_date']);
    }

    public function test_validacao_de_data(): void
    {
        $request = new InviteRequest(Request::create('/'));
        $rule = $request->rules()['expiration_date'];

        $this->assertTrue(
            Validator::make(['expiration_date' => '2026-12-31'], ['expiration_date' => $rule])->passes()
        );
        $this->assertTrue(
            Validator::make(['expiration_date' => 'nao-e-uma-data'], ['expiration_date' => $rule])->fails()
        );
    }
}
