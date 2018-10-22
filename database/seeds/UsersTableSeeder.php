<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\User::class)->create([
            'email' => 'user1@example.com',
            'password' => app('hash')->make('123@123a_')
        ]);

        factory(App\Models\User::class)->create([
            'email' => 'user2@example.com',
            'password' => app('hash')->make('123@123a_')
        ]);

        factory(App\Models\User::class)->create([
            'email' => 'user3@example.com',
            'password' => app('hash')->make('123@123a_')
        ]);
    }
}