<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Models\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use function Neuedev\Apineu\Test\fake;

class TagFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->unique()->word()
        ];
    }
}
