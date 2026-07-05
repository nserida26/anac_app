<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_document_id',
        'url',
        'demande_id',
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }
    public function typeDocument()
    {
        return $this->belongsTo(TypeDocument::class, 'type_document_id');
    }
}
