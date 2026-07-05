<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Activity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'action',
        'user_id',
        'demande_id',
        'type'
    ];

    /**
     * Get the user that performed the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeForUser($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    /**
     * Scope a query to filter by action.
     */
    public function scopeWithAction($query, $action)
    {
        return $query->where('action', $action);
    }


public static function log(string $action, ?int $demande_id = null, ?string $type = null): self
{
    return self::create([
        'action'     => $action,
        'user_id'    => Auth::id(),
        'demande_id' => $demande_id,
        'type'       => $type,
    ]);
}
}
