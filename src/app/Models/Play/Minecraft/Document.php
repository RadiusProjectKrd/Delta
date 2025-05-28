<?php

namespace App\Models\Play\Minecraft;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    public $timestamps = false;

    protected $fillable = [
        'type',
        'name',
        'user_id',
        'data',
        'publisher'
    ];
}
