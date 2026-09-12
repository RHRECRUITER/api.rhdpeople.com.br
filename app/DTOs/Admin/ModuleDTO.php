<?php
namespace App\DTOs\Admin;

use Illuminate\Http\Request;

class ModuleDTO
{
    public function __construct(
        public readonly ?string $key,
        public readonly ?string $label,
        public readonly ?string $description,
        public readonly ?float $extraPrice,
        public readonly ?bool $isActive,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            key: $request->validated('key'),
            label: $request->validated('label'),
            description: $request->validated('description'),
            extraPrice: $request->validated('extra_price') !== null ? (float) $request->validated('extra_price') : null,
            isActive: $request->validated('is_active') ?? true,
        );
    }
}
