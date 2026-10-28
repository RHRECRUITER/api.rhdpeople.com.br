<?php

namespace App\Http\Requests\Landing\Leads\V1;

use App\Http\Requests\Base\PublicFormRequest;
use Illuminate\Validation\Validator;

class LeadsRequest extends PublicFormRequest
{
    // parametros fixo aceitos do objective que são aceitos para os modulos de demonstração.
    private const MODULES = [
        'time_tracking',
        'vacation',
        'payroll',
        'documents',
        'reports',
    ];

    // sanitização e normalização dos dados antes de validação.
    protected function prepareForValidation(): void
    {
        $modules = $this->input('objective', []);
        if (is_array($modules)) {
            $defaults = array_fill_keys(self::MODULES, false);
            $modules = array_replace($defaults, $modules);
        }

        $corporateEmail = $this->input('corporate_email', $this->input('email'));
        $employeeRange = $this->input('employee_range', $this->input('number_employees'));

        $this->merge([
            'name' => is_string($this->input('name')) ? preg_replace('/\s+/', ' ', trim($this->input('name'))) : $this->input('name'),
            'company_name' => is_string($this->input('company_name')) ? preg_replace('/\s+/', ' ', trim($this->input('company_name'))) : $this->input('company_name'),
            'corporate_email' => is_string($corporateEmail) ? mb_strtolower(trim($corporateEmail)) : $corporateEmail,
            'email' => is_string($corporateEmail) ? mb_strtolower(trim($corporateEmail)) : $corporateEmail,
            'phone' => is_string($this->input('phone')) ? preg_replace('/\D+/', '', $this->input('phone')) : $this->input('phone'),
            'employee_range' => is_scalar($employeeRange) ? (string) $employeeRange : null,
            'objective' => $modules,
            'form_started_at' => $this->input('form_started_at') !== null ? (int) $this->input('form_started_at') : null,
            'website' => is_string($this->input('website')) ? trim($this->input('website')) : '',
        ]);
    }

    // verificação se todos elementos estão com o valor permitido
    public function withValidator(Validator $validator): void
    {

        $validator->after(function (Validator $validator) {
            $objective = $this->input('objective');

            if (is_array($objective)) {
                $extra = array_diff(array_keys($objective), self::MODULES);
                foreach ($extra as $key) {
                    $validator->errors()->add("objective.$key", 'Module not allowed.');
                }

                $anyTrue = false;
                foreach (self::MODULES as $key) {
                    if (($objective[$key] ?? false) === true) {
                        $anyTrue = true;
                        break;
                    }
                }

                if (! $anyTrue) {
                    $validator->errors()->add('objective', 'Select at least one module.');
                }
            } elseif (is_string($objective) && trim($objective) === '') {
                $validator->errors()->add('objective', 'The objective field is required.');
            }

            $startedAt = (int) $this->input('form_started_at', 0);
            $now = now()->timestamp;
            $elapsed = $now - $startedAt;

            if ($startedAt <= 0) {
                $validator->errors()->add('form_started_at', 'Invalid start time.');
            } elseif ($elapsed < 2) {
                $validator->errors()->add('form_started_at', 'Submitted too fast.');
            } elseif ($elapsed > 7200) { // 2h
                $validator->errors()->add('form_started_at', 'Form expired.');
            }
        });

    }

    public function rules(): array
    {

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'company_name' => ['required', 'string', 'min:2', 'max:255'],
            'corporate_email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^\d{9,13}$/'],
            'employee_range' => ['required', 'string', 'max:100'],
            'objective' => ['required'],
            'form_started_at' => ['required', 'integer'],
            'website' => ['present', 'string', 'max:0'], // honeypot: deve existir e permanecer vazio (para bots preencher).
        ];

        if (is_array($this->input('objective'))) {
            foreach (self::MODULES as $module) {
                $rules["objective.$module"] = ['required', 'boolean'];
            }
        }

        return $rules;
    }
}
