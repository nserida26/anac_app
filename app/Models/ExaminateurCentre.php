<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminateurCentre extends Model
{
    protected $table = 'examinateurs_centre';
    
    protected $fillable = [
        'centre_formation_id',
        'nom',
        'prenom',
        'email',
        'telephone',
        'numero_licence_examinateur',
        'date_naissance',
        'nationalite',
        'adresse',
        'document_justificatif',
        'date_debut_validite',
        'date_fin_validite',
        'statut_validation',
        'motif_refus',
        'valide_par',
        'date_validation'
    ];
    
    protected $casts = [
        'date_naissance' => 'date',
        'date_debut_validite' => 'date',
        'date_fin_validite' => 'date',
        'date_validation' => 'datetime'
    ];
    
    public function centreFormation()
    {
        return $this->belongsTo(CentreFormation::class);
    }
    
    public function formations()
    {
        return $this->hasMany(Formation::class, 'examinateur_id');
    }
    
    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
    }
    
    public function getNomCompletAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }
    
    public function scopeValide($query)
    {
        return $query->where('statut_validation', 'valide')
                     ->where('date_fin_validite', '>=', now());
    }
}