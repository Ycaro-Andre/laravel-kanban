<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KanbanController extends Controller
{
    public function index()
    {
        return Inertia::render('Kanban/Index', [
            'boards' => Board::where('user_id', auth()->user()->id)->with('lists.cards')->get()
        ]);
    }
}
