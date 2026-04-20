<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;


class DatabaseSeeder extends Seeder
{

    public function run()
    {
        FakerFactory::create()->Unique(true);
        $this->call([
            CategorySeeder::class,
            AuthorPublisherBookSeeder::class,
        ]);
    }
}
