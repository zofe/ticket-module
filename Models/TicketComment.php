<?php

namespace App\Modules\Tickets\Models;

use App\Models\User;
use App\Modules\Tickets\Notifications\NewTicketComment;
use Zofe\Rapyd\Traits\SSearch;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Mews\Purifier\Facades\Purifier;

/**
 * App\Modules\Tickets\Models\TicketComment
 *
 * @property int $id
 * @property string $ticket_id
 * @property string $origin
 * @property string $content
 * @property string|null $html
 * @property int $user_id
 * @property int $company_id
 * @property string|null $screenshot
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Tickets\Models\Ticket $ticket
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereScreenshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketComment whereUserId($value)
 * @mixin \Eloquent
 */
class TicketComment extends Model
{
    use SSearch;

    protected $table = 'ticket_comments';

    /**
     * @codeCoverageIgnore
     */
    public function toSearchableArray()
    {
        $fields = $this->toArray();

        $allowed  = ['id','subject', 'created_at','updated_at'];
        $filtered = array_filter(
            $fields,
            fn ($key) => in_array($key, $allowed),
            ARRAY_FILTER_USE_KEY
        );
        return $filtered;
    }

    public static function ssearchFallback($search)
    {
        return empty($search) ? static::query()
            : static::query()->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhere('subject', 'like', '%' . $search . '%')
                ;
            });
    }

    public function getScreenshot(TicketComment $comment, $screenid = 1)
    {
        $extension = substr(strrchr($comment->{"screenshot".$screenid}, '.'), 1);
        $asset = asset("storage/uploads/ticket_screenshot/{$comment->{"screenshot".$screenid}}");
        if($extension == "txt"|| $extension == "pdf"){
            return "<a href='{$asset}' download='allegato_{$comment->{"screenshot".$screenid}}'><i class='fas fa-file-pdf'></i>Scarica allegato</a>";
        }else{
            return "<img class='img-thumbnail' src='{$asset}'>";
        }
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = Purifier::clean($value);
    }

    /**
     * Get related ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /**
     * Get comment owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isMine(){
        if (!auth()->check()){
            return false;
        }
        return ($this->user_id === auth()->user()->id);
    }


    public function isOperator(){
        return $this->user && $this->user->hasRole(['admin','commercial','technician']);
    }
}
