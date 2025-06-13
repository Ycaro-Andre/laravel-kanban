<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Services\BoardService;

class KanbanController extends Controller
{
    protected $boardService;

    public function __construct (
        BoardService $boardService
    )
    {
        $this->boardService = $boardService;        
    }

    public function index()
    {
        return inertia('Kanban/Index', [
            'boards' => $this->boardService->getAuthUserBoards()
        ]);
    }
}
