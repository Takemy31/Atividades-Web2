<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'author_id' => \App\Models\Author::factory(),
            'category_id' => \App\Models\Category::factory(),
            'publisher_id' => \App\Models\Publisher::factory(),
            'published_year' => $this->faker->year(),
            'pages' => $this->faker->numberBetween(100, 800),
        ];
    }
}
