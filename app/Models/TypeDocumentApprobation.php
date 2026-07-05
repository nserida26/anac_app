<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeDocumentApprobation extends Model
{
    use HasFactory;

    protected $table = 'type_document_approbations';

    protected $fillable = [
        'nom_fr',
        'nom_en',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function documents()
    {
        return $this->hasMany(DocumentApprobation::class, 'type_document_id');
    }
}
