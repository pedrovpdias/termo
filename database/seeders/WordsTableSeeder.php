<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $words = file(__DIR__.'/words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Insere as palavras na tabela
        collect($words)->chunk(1000)->each(function ($chunk) {
            DB::table('words')->insert($chunk->map(function ($word) {
                return [
                    'word' => mb_strtolower($word),
                    'eligible' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray());
        });
    }
}