<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Label extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title'
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
