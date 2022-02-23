<?php

use Illuminate\Database\Seeder;

class EmojiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $emojis = [
            '😀',
            '😁',
            '😂',
            '😄',
            '😅',
            '😇',
            '😈',
            '😉',
            '😊',
            '😋',
            '😌',
            '😍',
            '😎',
            '😐',
            '😑',
            '😓',
            '😘',
            '😡',
            '😤',
            '😥',
            '😭',
            '😬',
            '😳',
            '😷',
            '😹',
            '😻',
            '🙈',
            '🤘'
        ];

        foreach ($emojis as $emoji)
        {
            \App\Emoji::create([
               'code'=>$emoji
            ]);
        }
    }
}
