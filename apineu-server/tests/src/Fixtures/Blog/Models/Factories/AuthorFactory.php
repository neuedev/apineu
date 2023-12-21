<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Models\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use function Neuedev\Apineu\Test\fake;

class AuthorFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->email()
        ];
    }
}
