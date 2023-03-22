<?php

namespace App\Modules\Ticket\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    protected $table = 'ticket_categories';

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'ticket_category_id')
            ->orderBy('created_at','asc');
    }
}
