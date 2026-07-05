<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class TypeLicence extends Model
{
    use HasFactory;

    protected $table = 'type_licences';

    protected $fillable = ['nom', 'machine', 'categorie'];

    public function demandes()
    {
        return $this->hasMany(Demande::class, 'type_licence_id');
    }
    public function qualifications(): BelongsToMany
    {
        return $this->belongsToMany(Qualification::class, 'type_licence_qualification', 'type_licence_id', 'qualification_id');
    }
    public function checklists()
    {
        return $this->hasMany(Checklist::class);
    }
}
