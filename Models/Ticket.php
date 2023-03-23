<?php

namespace App\Modules\Ticket\Models;

use App\Models\User;
use App\Modules\Ticket\Notifications\NewTicket;
use App\Modules\Ticket\Notifications\NewTicketAssigned;
use Zofe\Rapyd\Traits\ShortId;
use Zofe\Rapyd\Traits\SSearch;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

use Mews\Purifier\Facades\Purifier;
use ZeroDaHero\LaravelWorkflow\Traits\WorkflowTrait;

/**
 * App\Modules\Tickets\Models\Ticket
 *
 * @property string $id
 * @property string $origin
 * @property string $subject
 * @property string $content
 * @property string|null $html
 * @property string $status
 * @property int|null $ticket_category_id
 * @property int $user_id
 * @property int $company_id
 * @property int|null $agent_id
 * @property string|null $screenshot
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $agent
 * @property-read \App\Modules\Tickets\Models\TicketCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Tickets\Models\TicketComment[] $comments
 * @property-read int|null $comments_count
 * @property-read mixed $last_message
 * @property-read mixed $short_id
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket active()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket complete()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket userTickets($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereScreenshot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereTicketCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereUserId($value)
 * @mixin \Eloquent
 */
class Ticket extends Model
{
    use HasUuids;
    use ShortId;
    use WorkflowTrait;
    use SSearch;


    protected $table = 'tickets';
    protected $dates = ['completed_at', 'sla_expiring', 'sla_charge_expiring', 'last_commented_at', 'last_opened_at', 'last_closed_at'];

//    protected $keyType = 'string';
//    public $incrementing = false;


    public static function ssearchFallback($search)
    {
        return empty($search) ? static::query()
            : static::query()->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                ->orWhere('subject', 'like', '%' . $search . '%')
                ;
            });
    }

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = Purifier::clean($value);
    }

//    public function setClosingNoteAttribute($value)
//    {
//        if($value) {
//            $this->attributes['closing_note'] = Purifier::clean($value);
//        }
//    }

    public function getLastMessageAttribute()
    {
        if($this->hasComments()) {
            $comment = $this->hasMany(TicketComment::class, 'ticket_id')
                ->orderBy('created_at','desc')->get()->first();

            $user = $comment->isOperator() ?  'operatore' : 'partner';
            $time = Carbon::parse($this->last_commented_at)->diffForHumans();

            return "<em>$time</em><br><strong>$user</strong>: ".Str::limit(html_entity_decode(strip_tags($comment->content)),80, '...');
        }
        return '';
    }

    public function getScreenshot(Ticket $ticket, $screenid = 1)
    {
        $extension = substr(strrchr($ticket->{"screenshot".$screenid}, '.'), 1);
        $asset = asset("storage/uploads/ticket_screenshot/{$ticket->{"screenshot".$screenid}}");
        if($extension == "txt"|| $extension == "pdf"){
            return "<a href='{$asset}' download='allegato_{$ticket->{"screenshot".$screenid}}'><i class='fas fa-file-pdf'></i>Scarica allegato</a>";
        }else{
            return "<img class='img-thumbnail' src='{$asset}'>";
        }
    }

    /**
     * List of completed tickets.
     *
     * @return bool
     */
    public function hasComments()
    {
        return (bool) count($this->comments);
    }

    public function isComplete()
    {
        return (bool) $this->completed_at;
    }

    /**
     * List of completed tickets.
     *
     * @return Collection
     */
    public function scopeComplete($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * List of active tickets.
     *
     * @return Collection
     */
    public function scopeActive($query)
    {
        return $query->whereNull('completed_at');
    }


    /**
     * Get Ticket category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id');
    }

    public function closing()
    {
        return $this->belongsTo(TicketClosingCategory::class, 'closing_category');
    }

    /**
     * Get Ticket owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get Ticket agent.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }


    /**
     * Get Ticket comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(TicketComment::class, 'ticket_id')
            ->orderBy('created_at','asc');
    }

    /**
     * Get Ticket comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lastComment()
    {
        return $this->hasOne(TicketComment::class, 'ticket_id')
            ->where('created_at', '=', $this->updated_at)
            ->orderBy('created_at', 'desc')
            ->whereHas('user', function ($q2){
                $q2->whereIn('user_type_id', [2,3,4,5,9]);
            });
    }

    /**
     * Get all user tickets.
     *
     * @param $query
     * @param $id
     *
     * @return mixed
     */
    public function scopeUserTickets($query, $id)
    {
        return $query->where('user_id', $id);
    }

    public function getReadableStatusAttribute()
    {
        $workflow = $this->workflow_get();
        return $workflow->getMetadataStore()->getMetadata('label',$this->status);
    }

    public function getAdminUrlAttribute()
    {
        return route_lang('tickets.view',$this);
    }


    public function scopeExpiredForAgent($query, $agent_id=null)
    {
        $query = $query->whereDate('sla_expiring','<',Carbon::now()->format('Y-m-d'))
            ->whereIn('status',['awaiting','assigned']);
        if($agent_id) {
            $query = $query->where('agent_id','=', $agent_id);
        }
        return $query;
    }

    public function scopeAwaitingForAgent($query, $agent_id=null)
    {
        $query = $query->whereDate('last_commented_at','<',Carbon::now()->subDays(4)->format('Y-m-d'))
            ->whereIn('status',['awaiting']);

        if($agent_id) {
            $query = $query->where('agent_id','=', $agent_id);
        }
        return $query;
    }

    public function scopePendingForAgent($query, $agent_id=null)
    {
        //awaiting
        $query = $query->where(function($q) use ($agent_id) {
            $q
                ->whereDate('last_commented_at','<',Carbon::now()->subDays(4)->format('Y-m-d'))
                ->whereIn('status',['awaiting']);

        //expired
        })->orWhere(function($q2) use ($agent_id) {
            $q2->whereDate('sla_expiring','<',Carbon::now()->format('Y-m-d'))
                ->whereIn('status',['awaiting','assigned']);
        });
        if($agent_id) {
            $query = $query->where('agent_id','=', $agent_id);
        }
        return $query;
    }

    public function scopePendingForCharge($query)
    {
        return $query->whereDate('sla_charge_expiring','<',Carbon::now()->format('Y-m-d'))->orderBy('sla_charge_expiring','asc');
    }

    public function getCompanyNameAttribute()
    {
//        dd(config('ticket.company_relation'),
//            config('ticket.company_field'),
//           $this->user,
//           $this->user->company,
//        );

        return $this->user->{config('ticket.company_relation')}->{config('ticket.company_field')};
    }

}
