<?php
namespace App\DTOs\Admin;

use Illuminate\Http\Request;

class AddonDTO
{
    public function __construct(
        public readonly ?string $key,
        public readonly ?string $label,
        public readonly ?string $description,
        public readonly ?float $price,
        public readonly ?bool $isActive,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            key: $request->validated('key'),
            label: $request->validated('label'),
            description: $request->validated('description'),
            price: $request->validated('price') !== null ? (float) $request->validated('price') : null,
            isActive: $request->validated('is_active') ?? true,
        );
    }
}
