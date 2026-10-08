<?php

namespace App\DTOs\Admin;

use Illuminate\Http\Request;

class PlanTierDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?float $basePrice,         // Aqui está o nome que será usado no constructor
        public readonly ?int $maxEmployees,
        public readonly ?float $annualDiscountPct,
        public readonly ?bool $isActive,
        public readonly ?array $modules,
        public readonly ?array $addOns,
    ) {}

    /**
     * Mapeia os dados do Request para o DTO.
     */
    public static function fromRequest(Request $request): self
    {
        // Aqui garantimos que usamos exatamente os mesmos nomes definidos no __construct
        return new self(
            name: $request->validated('name'),
            basePrice: $request->validated('base_price') ? (float) $request->validated('base_price') : null,
            maxEmployees: $request->validated('max_employees') ? (int) $request->validated('max_employees') : null,
            annualDiscountPct: $request->validated('annual_discount_pct') ? (float) $request->validated('annual_discount_pct') : 0.00,
            isActive: $request->validated('is_active') ?? true,
            modules: $request->validated('modules') ?? [],
            addOns: $request->validated('add_ons') ?? [],
        );
    }
}
