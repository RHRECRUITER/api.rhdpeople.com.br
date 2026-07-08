<?php
namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticationService
{
    public function authenticate(array $credentials): void
    {
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas.'],
            ]);
        }
    }

    public function buildLoginResponse(User $user): array
    {
        //Load dos relacionamentos de licenças para evitar queries desnecessarias
        $user->load('licenses.plan.tenant');
        //Extração de helpers disponibilizados no model users
        $isSuperAdmin     = $user->isSuperAdmin();
        $isCompanyAdmin   = $user->isCompanyAdmin();
        $hasActiveLicense = $user->licenses()->where('is_active', true)->exists();

        //Deinição de role
        $primaryRole = 'user';
        if ($isSuperAdmin) {
            $primaryRole = 'super_admin';
        } elseif ($isCompanyAdmin) {
            $primaryRole = 'company_admin';
        } elseif ($hasActiveLicense) {
            $primaryRole = 'company_user';
        }
        //Regra de renovação de licensa
        $needRenewal = (! $isSuperAdmin && ! $hasActiveLicense);
        // Licensa com status atual
        $currentLicense = $user->licenses()->with('plan.tenant')->where('is_active', true)->first();
        $companyName    = null;
        if ($currentLicense && $currentLicense->plan && $currentLicense->plan->tenant) {
            $companyName = $currentLicense->plan->tenant->name;
        }
        return [
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'access'  => [
                'role'               => $primaryRole,
                'has_active_license' => $hasActiveLicense,
                'need_renewal'       => $needRenewal,
            ],
            'context' => $currentLicense ? [
                'license_id'   => $currentLicense->id,
                'type_license' => $currentLicense->type_license,
                'company'      => $companyName,
            ] : null,
        ];
    }

}
