<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aeroport extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'codeIATA',
        'codeICAO',
        'pays_id',
        'ville',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6'
    ];

    // Relation avec le pays
    public function pays()
    {
        return $this->belongsTo(Pays::class);
    }

    // Relation avec les vols (départ)
    public function volsDepart()
    {
        return $this->hasMany(Vol::class, 'aeroport_depart_id');
    }

    // Relation avec les vols (arrivée)
    public function volsArrivee()
    {
        return $this->hasMany(Vol::class, 'aeroport_arrivee_id');
    }

    // Relation avec les escales
    public function escales()
    {
        return $this->hasMany(Escale::class);
    }

    // Scopes pour faciliter les recherches
    public function scopeSearch($query, $search)
    {
        return $query->where('nom', 'LIKE', "%{$search}%")
            ->orWhere('codeIATA', 'LIKE', "%{$search}%")
            ->orWhere('codeICAO', 'LIKE', "%{$search}%")
            ->orWhere('ville', 'LIKE', "%{$search}%");
    }

    public function scopeByCountry($query, $paysId)
    {
        return $query->where('pays_id', $paysId);
    }

    // Accessor pour le nom complet
    public function getNomCompletAttribute()
    {
        return "{$this->nom} ({$this->codeIATA}/{$this->codeICAO}) - {$this->ville}";
    }

    // Méthode pour obtenir les coordonnées sous forme de tableau
    public function getCoordonneesAttribute()
    {
        return [
            'lat' => $this->latitude,
            'lng' => $this->longitude
        ];
    }

    // Validation des données
    public static function rules($id = null)
    {
        return [
            'nom' => 'required|string|max:100',
            'codeIATA' => 'required|string|size:3|unique:aeroports,codeIATA,' . $id,
            'codeICAO' => 'required|string|size:4|unique:aeroports,codeICAO,' . $id,
            'pays_id' => 'required|exists:pays,id',
            'ville' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ];
    }

    public static function messages()
    {
        return [
            'codeIATA.unique' => 'Ce code IATA est déjà utilisé',
            'codeICAO.unique' => 'Ce code ICAO est déjà utilisé',
            'pays_id.exists' => 'Le pays sélectionné n\'existe pas',
            'latitude.between' => 'La latitude doit être entre -90 et 90',
            'longitude.between' => 'La longitude doit être entre -180 et 180'
        ];
    }
}