<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\KanbanList;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KanbanListController extends Controller
{
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
            $board = Board::findOrFail($request->input('board_id'));
            KanbanList::create([
                'title' => $request->input('title'),
                'board_id' => $board->id,
                'position' => $board->lists()->max('position') !== null 
                    ? $board->lists()->max('position') + 1 
                    : 0
            ]);

            //Ensures it brings the last added list
            $board->load(['lists.cards']);

            Log::info('New List created successfully!',['KanbanListController::store']);
            return response()->json(['board' => $board], 200);

        } catch(Exception $e) {
            Log::error('Error creating new list: '. $e->getMessage() .' - '. $e->getFile() .' - '. $e->getLine(),['KanbanListController::store']);
            return response()->json(['message' => "Couldn't create new list. Try again later!"], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(KanbanList $kanbanList)
    {
        //
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
    public function update(Request $request, KanbanList $kanbanList)
    {
        try {
            $kanbanList->update([
                'title' => $request->input('title'),
            ]);

            Log::info('List updated successfully!',['KanbanListController::update']);
            return response()->json(['List' => $kanbanList], 200);

        } catch(Exception $e) {
            Log::error('Error updating List: '. $e->getMessage() .' - '. $e->getFile() .' - '. $e->getLine(),['KanbanListController::update']);
            return response()->json(['message' => "Couldn't update List. Try again later!"], 500);
        }
    }

    public function changePositions(Request $request)
    {
        try {
            $changes = $request->input('changes');

            DB::beginTransaction();
            foreach ($changes as $change) {
                KanbanList::where('position', $change['new_position'])
                    ->firstOrFail()
                    ->update(['position' => $change['old_position']]);

                KanbanList::findOrFail($change['kanban_list_id'])
                    ->update(['position' => $change['new_position']]);
            }
            DB::commit();
            Log::info('Positions changed successfully!',['KanbanListController::changePositions']);
            return response()->json(['status' => 'success']);

        } catch(Exception $e) {
            DB::rollBack();
            Log::error('Error changing lists position: '. $e->getMessage() .' - '. $e->getFile() .' - '. $e->getLine(),['KanbanListController::changePositions']);
            return response()->json(['message' => "Couldn't update lists positions. Try again later!"], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KanbanList $kanbanList)
    {
        try {
            DB::beginTransaction();
            KanbanList::where('board_id', $kanbanList->board_id)
                ->where('position', '>', $kanbanList->position)
                ->decrement('position');

            $kanbanList->delete();
            
            DB::commit();
            Log::info('List deleted successfully!',['KanbanListController::destroy']);
            return response()->json(['status' => 'success']);

        } catch(Exception $e) {
            DB::rollBack();
            Log::error('Error deleting the list: '. $e->getMessage() .' - '. $e->getFile() .' - '. $e->getLine(),['KanbanListController::delete']);
            return response()->json(['message' => "Couldn't delete the list. Try again later!"], 500);
        }
    }
}
