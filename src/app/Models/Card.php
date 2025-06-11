<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kanban_list_id',
        'title',
        'description',
        'position',
    ];

    public function kanbanList()
    {
        return $this->belongsTo(KanbanList::class);
    }

}
