<?php
/**
 * Configurações basica para de permissoes para eventos de tracking/analytics
 **/
return [
    'tracking' => [
        // Se true, exige header X-Tracking-Token válido (401 se ausente/inválido).
        'require_token' => (bool) env('TRACKING_REQUIRE_TOKEN', false),

        // Role usada quando não há token e require_token=false.
        'default_role'  => (string) env('TRACKING_DEFAULT_ROLE', 'public'),

        // Prototipo simples de token pendente de implementação de gerenciamento (hash + BD ou cache). Por enquanto, esta via .env
        'tokens'        => (static function (): array {
            $raw     = (string) env('TRACKING_TOKENS_JSON', '{}');
            $decoded = json_decode($raw, true);

            return is_array($decoded) ? $decoded : [];
        })(),

        // Política (permissões): quais eventos cada role pode enviar.
        'roles'         => [
            //Role publica padrão, para testes iniciais.
            'public' => [
                'event_types' => [
                    'page_view',
                    'section_view',
                    'scroll_depth',
                    'heartbeat',
                    'form_start',
                    'form_submit_success',
                ],
            ],

            //Exemplo de role adicional quando sistema de token estiver funcional.
            'forms'  => [
                'event_types' => [
                    'form_start',
                    'form_submit_success',
                ],
            ],
        ],
    ],
];
