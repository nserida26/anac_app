<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentApprobation extends Model
{
    use HasFactory;

    protected $table = 'document_approbations';

    protected $fillable = [
        'url',
        'demande_approbation_id',
        'type_document_id',
        'valider',
        'motif'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function typeDocument()
    {
        return $this->belongsTo(TypeDocumentApprobation::class, 'type_document_id');
    }

    public function demandeApprobation()
    {
        return $this->belongsTo(DemandeApprobation::class);
    }
}
