<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispositifFormation extends Model
{
    protected $table = 'dispositifs_formation';
    
    protected $fillable = [
        'centre_formation_id',
        'simulateur_id',
        'date_acquisition',
        'date_derniere_certification',
        'date_expiration_certification',
        'certificat_document',
        'statut',
        'notes'
    ];
    
    protected $casts = [
        'date_acquisition' => 'date',
        'date_derniere_certification' => 'date',
        'date_expiration_certification' => 'date'
    ];
    
    public function centreFormation()
    {
        return $this->belongsTo(CentreFormation::class);
    }
    
    public function simulateur()
    {
        return $this->belongsTo(Simulateur::class);
    }
    
    public function formations()
    {
        return $this->hasMany(Formation::class);
    }
    
    public function scopeOperationnel($query)
    {
        return $query->where('statut', 'operationnel');
    }
}