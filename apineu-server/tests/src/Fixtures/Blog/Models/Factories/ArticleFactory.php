<?php

namespace Neuedev\Apineu\Test\Fixtures\Blog\Models\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use function Neuedev\Apineu\Test\fake;

class ArticleFactory extends Factory
{
    public function definition()
    {
        return [
            'title' => fake()->sentence(),
            'date' => fake()->date()
        ];
    }
}
