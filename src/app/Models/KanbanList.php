<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KanbanList extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'board_id',
        'title',
        'position',
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class)->orderBy('position');
    }

}
