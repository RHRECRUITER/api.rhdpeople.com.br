<?php

namespace App\Domain\Analytics;

final class EventSchema
{
    /**
     * Notas: classe responavel por definir o esquema de eventos, incluindo chaves permitidas, propriedades e valores para cada tipo de evento.
     * Os métodos allowedEventKeys, allowedProps, requiredProps e allowedValues fornecem as regras para validação dos eventos antes de serem inseridos no EventStore.
     */

    /**
     * @return array<int, string>
     */
    public static function allowedEventKeys(): array
    {
        return ['event_id', 'type', 'occurred_at', 'page', 'props'];
    }

    /**
     * @return array<int, string>
     */
    public static function allowedProps(EventType $type): array
    {
        return match ($type) {
            EventType::FormStart => ['form_id'],
            EventType::FormSubmitSuccess => ['form_id'],
            EventType::PageView => ['section'],
            EventType::Heartbeat => [],
            EventType::RestrictedClick => ['section'],
        };
    }

    /**
     * @return array<int, string>
     */
    public static function requiredProps(EventType $type): array
    {
        return match ($type) {
            EventType::FormStart, EventType::FormSubmitSuccess => ['form_id'],
            default => [],
        };
    }

    /**
     * @return array<int, string>|null
     */
    public static function allowedValues(EventType $type, string $prop): ?array
    {
        return match ($type) {
            default => null,
        };
    }

    /**
     * @return array<int, string>
     */
    public static function allowedPropsForType(?string $type): array
    {
        if (! is_string($type) || $type === '') {
            return [];
        }

        $enum = EventType::tryFrom($type);

        if (! $enum) {
            return [];
        }

        return self::allowedProps($enum);
    }

    /**
     * @return array<int, string>
     */
    public static function requiredPropsForType(?string $type): array
    {
        if (! is_string($type) || $type === '') {
            return [];
        }

        $enum = EventType::tryFrom($type);

        if (! $enum) {
            return [];
        }

        return self::requiredProps($enum);
    }

    /**
     * @return array<int, string>|null
     */
    public static function allowedValuesForTypeProp(?string $type, string $prop): ?array
    {
        if (! is_string($type) || $type === '') {
            return null;
        }

        $enum = EventType::tryFrom($type);

        if (! $enum) {
            return null;
        }

        return self::allowedValues($enum, $prop);
    }
}
