<?php

namespace App\Services;

use App\Models\Board;

class BoardService 
{
    public function getAuthUserBoards() {
        return Board::where('user_id', auth()->user()->id)->get();
    }
}