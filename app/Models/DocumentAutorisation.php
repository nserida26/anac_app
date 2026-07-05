<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentAutorisation extends Model
{
    use HasFactory;

    protected $table = 'document_autorisations';

    protected $fillable = [
        'demande_autorisation_id',
        'url',
        'type_document_id',
        'valider',
        'motif'
    ];

    /**
     * Get the demande autorisation that owns the document
     */
    public function demandeAutorisation()
    {
        return $this->belongsTo(DemandeAutorisation::class);
    }
    public function typeDocument()
    {
        return $this->belongsTo(TypeDocumentAutorisation::class, 'type_document_id');
    }
}
