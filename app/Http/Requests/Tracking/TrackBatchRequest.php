<?php

namespace App\Http\Requests\Tracking;

use App\Domain\Analytics\EventSchema;
use App\Domain\Analytics\EventType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TrackBatchRequest extends FormRequest
{
    /**
     * Notas: Classe de request para validação do payload de batch da aplicação de tracking. Ela defini as regras de validação incluindo a estrutura esperada do payload.
     * Valição realizada em duas etapas: regras basicas definidas pelo rules() e regras mais elaboradas em withValidator().
     */

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Perimitir acesso  sem autenticação, ate que seja definida a estrategia de autenticação.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Verifica se o session_id enviado no payload condiz com a sessão real do request
            // Isso impede que um cliente envie eventos "fake" para sessões de terceiros.
            if ($this->input('session_id') !== session()->getId() && ! config('app.debug')) {
                $validator->errors()->add('session_id', 'Security session mismatch.');

                return;
            }

            $allowed = EventSchema::allowedEventKeys();
            $isDebug = config('app.debug');
            $genericError = 'Invalid request structure.';

            foreach ((array) $this->input('events', []) as $i => $event) {
                if (! is_array($event)) {
                    continue;
                }

                $extra = array_diff(array_keys($event), $allowed);

                foreach ($extra as $key) {
                    $validator->errors()->add("events.$i.$key", $isDebug ? 'Field not allowed.' : $genericError);
                }

                $type = $event['type'] ?? null;
                if (! is_string($type) || $type === '') {
                    continue;
                }

                $props = $event['props'] ?? [];
                if (! is_array($props)) {
                    continue;
                }

                $allowedProps = EventSchema::allowedPropsForType($type);
                $requiredProps = EventSchema::requiredPropsForType($type);

                $extraProps = array_diff(array_keys($props), $allowedProps);
                foreach ($extraProps as $key) {
                    $validator->errors()->add("events.$i.props.$key", $isDebug ? 'Prop not allowed.' : $genericError);
                }

                foreach ($requiredProps as $prop) {
                    if (! array_key_exists($prop, $props)) {
                        $validator->errors()->add("events.$i.props.$prop", $isDebug ? 'Prop is required.' : $genericError);
                    }
                }

                foreach ($props as $prop => $value) {
                    if (! is_string($prop) || $prop === '') {
                        continue;
                    }

                    $allowedValues = EventSchema::allowedValuesForTypeProp($type, $prop);
                    if ($allowedValues === null) {
                        continue;
                    }

                    if (! is_string($value) || ! in_array($value, $allowedValues, true)) {
                        $validator->errors()->add("events.$i.props.$prop", $isDebug ? 'Invalid value.' : $genericError);
                    }
                }
            }
        });
    }

    public function rules(): array
    {
        $role = (string) $this->attributes->get('tracking_role', 'public');
        $configuredTypes = (array) config("analytics.tracking.roles.$role.event_types", EventType::values());
        $allowedTypes = array_values(array_intersect($configuredTypes, EventType::values()));

        return [
            // Regras de validação para o payload do batch de tracking.
            'client_id' => ['required', 'uuid'],
            'session_id' => ['required', 'uuid'],
            'events' => ['required', 'array', 'min:1', 'max:10'],
            // Regras para cada evento dentro do array de eventos.
            'events.*.event_id' => ['required', 'uuid'],
            'events.*.type' => ['required', 'string', Rule::in($allowedTypes)],
            'events.*.occurred_at' => ['required', 'date_format:Y-m-d\TH:i:sP'], // ISO 8601
            'events.*.page' => ['required', 'string', 'max:255'],
            // Propriedades adicionais do evento, como props do form ou outras informações.
            'events.*.props' => ['sometimes', 'array', 'max:10'],
            'events.*.props.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}
