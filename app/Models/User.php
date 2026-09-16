<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'username',
        'whatsapp',
        'email',
        'photo',
        'status',
        'user_type',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'user_type'         => 'string',
    ];

    // ── Relationships ──

    public function demandeur(): HasOne
    {
        return $this->hasOne(Demandeur::class, 'user_id');
    }

    public function evaluateur(): HasOne
    {
        return $this->hasOne(Evaluateur::class, 'user_id');
    }

    public function examinateur(): HasOne
    {
        return $this->hasOne(Examinateur::class, 'user_id');
    }

    public function signature(): HasOne
    {
        return $this->hasOne(Signature::class, 'user_id');
    }

    public function cachet(): HasOne
    {
        return $this->hasOne(Cachet::class, 'user_id');
    }

    public function compagnie(): HasOne
    {
        return $this->hasOne(Compagnie::class, 'user_id');
    }

    public function compagnies(): HasMany
    {
        return $this->hasMany(Compagnie::class, 'user_id');
    }

    public function centreFormation(): HasOne
    {
        return $this->hasOne(CentreFormation::class, 'user_id');
    }

    public function demandeAutorisations(): HasMany
    {
        return $this->hasMany(DemandeAutorisation::class, 'user_id');
    }

    public function demandeApprobations(): HasMany
    {
        return $this->hasMany(DemandeApprobation::class, 'user_id');
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(PaiementAutorisation::class, 'user_id');
    }

    // ── Notifications ──

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }
}
