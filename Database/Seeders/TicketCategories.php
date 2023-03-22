<?php


namespace App\Modules\Tickets\Database\Seeders;

use App\Modules\Tickets\Models\TicketCategory;
use App\Modules\Tickets\Models\TicketClosingCategory;
use Illuminate\Database\Seeder;

class TicketCategories extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['name' => 'Tecnico',       'slug' =>'technical'],
            ['name' => 'Commerciale',   'slug' =>'commercial'],
            ['name' => 'Amministrativo','slug' =>'administrative'],
        ];

        foreach ($items as $item) {
            TicketCategory::create($item);
        }

        TicketClosingCategory::insert([
            ['slug' => 'config',                'name' => 'configurazione servizio'],
            ['slug' => 'commercial',            'name' => 'commerciale'],
            ['slug' => 'administrative',        'name' => 'amministrativo'],
            ['slug' => 'other',                 'name' => 'altro'],
        ]);


    }
}
