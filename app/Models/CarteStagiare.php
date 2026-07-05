<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\CarteExpiryNotification;

class CarteStagiare extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_carte',
        'np',
        'date_naissance',
        'adresse',
        'nationalite',
        'photo',
        'signature',
        'date_deliverance',
        'date_expiration',
        'signature_dg',
        'signature_dsv',
        'signature_pel',
        'cachet',
        'demande_id',
        'demandeur_id'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_deliverance' => 'date',
        'date_expiration' => 'date'
    ];

    // Relationships
    public function demande()
    {
        return $this->belongsTo(Demande::class, 'demande_id');
    }

    public function demandeur()
    {
        return $this->belongsTo(Demandeur::class, 'demandeur_id');
    }
}
