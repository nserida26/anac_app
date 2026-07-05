<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentreLicence extends Model
{
    protected $table = 'centre_licences';
    
    protected $fillable = [
        'centre_formation_id',
        'type_licence_id',
        'date_obtention',
        'date_expiration',
        'document_justificatif',
        'statut'
    ];
    
    protected $casts = [
        'date_obtention' => 'date',
        'date_expiration' => 'date'
    ];
    
    public function centreFormation()
    {
        return $this->belongsTo(CentreFormation::class);
    }
    
    public function typeLicence()
    {
        return $this->belongsTo(TypeLicence::class);
    }
}