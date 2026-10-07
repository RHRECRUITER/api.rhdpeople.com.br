<?php
namespace App\Http\Controllers\Api\Guest\V1;


use App\Services\Guest\InviteService;

use Illuminate\Http\Request;
use App\Http\Requests\Guest\InviteRequest;

class InviteController{
    function __construct(
        protected InviteService $service,
        ){}
    public function createrPreRegistration(InviteRequest $request){
        $dateInput = $request->validated();
        $result = $this->service->createrPreRegistration(
            $dateInput['email'],
            $dateInput['plan_id'],
            $dateInput['type_license'],
            $dateInput['expiration_date']
        );
        return response()->api(
            $result['name'],
            $result['email'],
            $result['status']
        );

    }
    public function createrInviteRegister(Request $request){
        $dateInput = $request->validate([
            'name' => ['required','string','max:100'],
            'password' =>['required', 'confirmed', 'min:8'],
            'token' => ['required','string'],
            ]);
            $result = $this->service->createrInviteRegister(
                $dateInput['name'],
                $dateInput['password'],
                $dateInput['token']

            );
            return response()->api(
                $result['data'],
                $result['message'],
                $result['status']
            );

    }

    public function validateToken(InviteRequest $request){
        $token = $request->validate([
            'token' => ['required','string'],
            ]);
        $result = $this->service->validateToken($token['token']);
        return response()->api(
            $result['data'],
            $result['message'],
            $result['status']
        );

    }
}
?>
