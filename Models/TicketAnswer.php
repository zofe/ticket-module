<?php

namespace App\Modules\Tickets\Models;

use App\Traits\SSearch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;


class TicketAnswer extends Model
{
    use Searchable, SSearch;

    protected $table = 'ticket_answers';

    protected $guarded = [];

    public function getFullAnswerAttribute(){
        return $this->title ." - ". Str::limit($this->answer, 50);
    }

    /**
     * @codeCoverageIgnore
     */
    public function toSearchableArray()
    {
        $fields = $this->toArray();

        $allowed  = ['id','title','created_at'];
        $filtered = array_filter(
            $fields,
            fn ($key) => in_array($key, $allowed),
            ARRAY_FILTER_USE_KEY
        );

        return $filtered;
    }

    public static function ssearchFallback($query)
    {
        return empty($query) ? static::query()
            : static::query()->where(function ($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%');
            });
    }

}
