<?php

namespace Uania\Ticket\Traits;

use Illuminate\Support\Facades\App;
use MeiliSearch\Endpoints\Indexes;


/**
 * Trait SSearch
 * ricerca fulltext con meilisearch se disponibile (non in ambiente di test)
 * todo skip se sono in ambiente di test
 *
 * @package App\Traits
 */
trait SSearch {


    public static function ssearch($search, $limit=null)
    {

        //non c'è una ricerca fulltext restituisco il query builder del model
        if(empty($search)) {
            return static::query();
        }

        //sono in modalità testing, non c'è meilisearch, uso una like di fallback
        if(App::environment(['testing']) || !config('features.use_meilisearch')) {

            return static::ssearchFallback($search);
        }

        //uso meilisearch


        if (property_exists(static::class, 'search_fiter')) {
            $matching = static::search($search, function (Indexes $meilisearch, $query, $searchParams) {
                $searchParams['filters'] = static::$search_fiter.'="'.htmlspecialchars(json_encode($query), ENT_QUOTES, 'UTF-8').'"';
                $searchParams['limit'] = 200;
                return $meilisearch->search($query, $searchParams);
            });
        } else {
            $matching = static::search($search, function (Indexes $meilisearch, $query, $searchParams ){
                $searchParams['limit'] = 200;
                return $meilisearch->search($query, $searchParams);
            });
        }

        if($limit) {
            $matching = $matching->take($limit);
        }
        $matching = $matching->get()->pluck('id');

        //restituisco mailisearch o una fallback
        if(count($matching)) {
            return static::query()->whereIn('id', $matching);
        } else {
            return static::ssearchFallback($search);
        }

    }

    //da overridare per prevedere una query usando like
    public static function ssearchFallback($search)
    {
        return static::query();
    }

    protected static function searchRulesDefault()
    {
        return [
            "typo",
            "words",
            "proximity",
            "attribute",
            "wordsPosition",
            "exactness"
        ];
    }


}
