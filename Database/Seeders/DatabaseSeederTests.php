<?php


namespace App\Modules\Ticket\Database\Seeders;

use App\Models\User;
use App\Modules\Auth\Database\Seeders\AuthSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeederTests extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AuthSeeder::class);

        $admin = User::factory()->create([
            'email' => 'admin@tickets.local',
            'password' => Hash::make('12345678'),
        ]);

        //role assign
        $admin->assignRole('admin');
        $admin->save();

        $user = User::factory()->create([
            'email' => 'user@tickets.local',
            'password' => Hash::make('12345678'),
        ]);

        //role assign
        $user->assignRole('user');
        $user->save();

        $this->call(TicketCategories::class);


    }
}
