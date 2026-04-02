<?php
/**
 * Configurações basica para de permissoes para eventos de tracking/analytics
 **/
return [
    'tracking' => [
        // Role padrão assumida para o tracking público da Landing Page.
        'default_role'  => 'public',

        // Política (permissões): quais eventos cada role pode enviar.
        'roles'         => [
            //Role publica padrão, para testes iniciais.
            'public' => [
                'event_types' => [
                    'page_view',
                    'heartbeat',
                    'form_start',
                    'form_submit_success',
                    'restricted_click',
                ],
            ],
        ],
    ],
];
