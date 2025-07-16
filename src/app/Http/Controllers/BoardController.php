<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Label;
use App\Services\BoardService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BoardController extends Controller
{
    protected $boardService;

    public function __construct (
        BoardService $boardService
    )
    {
        $this->boardService = $boardService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Board::create([
                'title' => $request->input('title'),
                'user_id' => auth()->user()->id
            ]);

            Log::info('New board created successfully!',['BoardController::store']);
            return response()->json(['boards' => $this->boardService->getAuthUserBoards()], 200);

        } catch(Exception $e) {
            Log::error('Error creating new board: '. $e->getMessage() .' - '. $e->getFile() .' - '. $e->getLine(),['BoardController::store']);
            return response()->json(['message' => "Couldn't create new board. Try again later!"], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Board $board)
    {
        $board->load('lists.cards.labels');

        return Inertia::render('Kanban/Board', [
            'board' => $board,
            'labels' => Label::all()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Board $board)
    {
        try {
            $board->update([
                'title' => $request->input('title')
            ]);

            Log::info('Board updated successfully!',['BoardController::update']);
            return response()->json(['boards' => $this->boardService->getAuthUserBoards()], 200);

        } catch(Exception $e) {
            Log::error('Error updating board: '. $e->getMessage() .' - '. $e->getFile() .' - '. $e->getLine(),['BoardController::update']);
            return response()->json(['message' => "Couldn't update board. Try again later!"], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Board $board)
    {
        try {
            $board->delete();
            Log::info('Board deleted successfully!',['BoardController::destroy']);
            return response()->json(['boards' => $this->boardService->getAuthUserBoards()], 200);

        } catch(Exception $e) {
            Log::error('Error deleting board: '. $e->getMessage() .' - '. $e->getFile() .' - '. $e->getLine(),['BoardController::destroy']);
            return response()->json(['message' => "Couldn't delete board. Try again later!"], 500);
        }
    }
}
