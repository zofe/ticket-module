<?php


namespace App\Modules\Ticket\Database\Seeders;

use App\Modules\Ticket\Models\TicketCategory;
use App\Modules\Ticket\Models\TicketClosingCategory;
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
        $items = config('ticket.ticket_categories');

        foreach ($items as $item) {
            TicketCategory::create($item);
        }

        TicketClosingCategory::insert(config('ticket.ticket_closing_categories'));


    }
}
