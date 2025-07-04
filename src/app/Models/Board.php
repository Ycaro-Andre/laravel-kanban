<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
    ];


    public function lists()
    {
        return $this->hasMany(KanbanList::class)->orderBy('position');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
