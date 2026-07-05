<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instructeur extends Model
{
    protected $fillable = [
        'centre_formation_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'numero_licence',
        'date_naissance',
        'nationalite',
        'adresse',
        'document_justificatif',
        'qualifications',
        'statut'
    ];
    
    protected $casts = [
        'date_naissance' => 'date',
        'qualifications' => 'array'
    ];
    
    public function centreFormation()
    {
        return $this->belongsTo(CentreFormation::class);
    }
    
    public function formations()
    {
        return $this->hasMany(Formation::class);
    }
    
    public function getNomCompletAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }
}