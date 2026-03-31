<?php
namespace Database\Factories;

use App\Models\License;
use App\Models\Plan;
use App\Models\User;
use App\Enums\TypeLicense;

use Illuminate\Database\Eloquent\Factories\Factory;

class LicenseFactory extends Factory{
    protected $model = License::class;
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => fake()->uuid(),
            'type_license' => fake()->randomElement(TypeLicense::cases()),
            'plan_id' => Plan::factory(),
            'user_id' => User::factory(),
        ];
    }
}
?>
