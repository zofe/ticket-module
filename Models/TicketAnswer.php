<?php

namespace App\Modules\Ticket\Models;

use Zofe\Rapyd\Traits\SSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class TicketAnswer extends Model
{
    use SSearch;

    protected $table = 'ticket_answers';

    protected $guarded = [];

    public function getFullAnswerAttribute(){
        return $this->title ." - ". Str::limit($this->answer, 50);
    }


    public static function ssearchFallback($query)
    {
        return empty($query) ? static::query()
            : static::query()->where(function ($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%');
            });
    }

}
