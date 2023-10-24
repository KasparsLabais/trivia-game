<?php

namespace PartyGames\TriviaGame\database\seeders;

use Illuminate\Database\Seeder;
use PartyGames\TriviaGame\Models\Categories;

class TriviaCategorySeeder extends Seeder
{

    public function run()
    {
        Categories::createMany([
            [
                'name' => 'General Knowledge',
                'is_active' => 1
            ],
            [
                'name' => 'Entertainment: Books',
                'is_active' => 1
            ],
            [
                'name' => 'Entertainment: Film',
                'is_active' => 1
            ],
            [
                'name' => 'Entertainment: Music',
                'is_active' => 1
            ],
            [
                'name' => 'Entertainment: Musicals & Theatres',
                'is_active' => 1
            ],
            [
                'name' => 'Entertainment: Television',
                'is_active' => 1
            ],
            [
                'name' => 'Entertainment: Video Games',
                'is_active' => 1
            ],
            [
                'name' => 'Entertainment: Anime & Manga',
                'is_active' => 1
            ],
            [
                'name' => 'Entertainment: Cartoon & Animations & Comics',
                'is_active' => 1
            ],
            [
                'name' => 'Entertainment: Celebrities',
                'is_active' => 1
            ],
            [
                'name' => 'Nature: Animals',
                'is_active' => 1
            ],
            [
                'name' => 'Places',
                'is_active' => 1
            ],
            [
                'name' => 'History',
                'is_active' => 1
            ],
            [
                'name' => 'Science: Computers & Gadgets',
                'is_active' => 1
            ],
            [
                'name' => 'Science: Mathematics',
                'is_active' => 1
            ],
            [
                'name' => 'Engineering',
                'is_active' => 1
            ]
        ]);
    }

}