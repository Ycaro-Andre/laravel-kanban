<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CardLabel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'card_id',
        'label_id'
    ];
}
