<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\KanbanList;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CardController extends Controller
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
            $list = KanbanList::findOrFail($request->input('kanban_list_id'));
            $card = Card::create([
                'title' => $request->input('title'),
                'kanban_list_id' => $list->id,
                'position' => $list->cards()->max('position') !== null 
                    ? $list->cards()->max('position') + 1 
                    : 0
            ]);

            //Ensures it brings the last added list
            $list->load(['cards']);

            Log::info('New card created successfully!',['CardController::store']);
            return response()->json(['list' => $list, 'card' => $card], 200);

        } catch(Exception $e) {
            Log::error('Error creating new card: '. $e->getMessage() .' - '. $e->getFile() .' - '. $e->getLine(),['CardController::store']);
            return response()->json(['message' => "Couldn't create new card. Try again later!"], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
    public function update(Request $request, Card $card)
    {
        try {
            $card->update([
                'title' => $request->input('title'),
                'description' => $request->input('description')
            ]);

            Log::info('Card updated successfully!',['CardController::update']);
            return response()->json(['card' => $card], 200);

        } catch(Exception $e) {
            Log::error('Error updating Card: '. $e->getMessage() .' - '. $e->getFile() .' - '. $e->getLine(),['CardController::update']);
            return response()->json(['message' => "Couldn't update Card. Try again later!"], 500);
        }
    }

    public function changePositions(Request $request)
    {
        try {
            $changes = $request->input('changes');

            DB::beginTransaction();
            foreach ($changes as $change) {
                //If is the same list id, means that the card was only changed to another of the same list
                if ($change['old_list'] != $change['new_list']) {
                    //Increments all cards positions by one because of the new addition
                    Card::where('kanban_list_id', $change['new_list'])
                        ->where('position', '>=', $change['new_position'])
                        ->increment('position');

                    //Decrements all cards positions by one because of the removal of the card on the list
                    Card::where('kanban_list_id', $change['old_list'])
                        ->where('position', '>', $change['old_position'])
                        ->decrement('position');

                    //Save the modified card new position and list
                    Card::findOrFail($change['card_id'])
                        ->update([
                            'position' => $change['new_position'],
                            'kanban_list_id' => $change['new_list']
                        ]);

                    //Reorders the old list (to ensure that the positions are a perfect sequence)
                    $this->reorderList($change['old_list']);
                    $this->reorderList($change['new_list']);
                } else { //if is the same list, only changes the cards positions
                    Card::where('position', $change['new_position'])
                        ->firstOrFail()
                        ->update(['position' => $change['old_position']]);

                    Card::findOrFail($change['card_id'])
                        ->update(['position' => $change['new_position']]);
                }
            }
            DB::commit();
            Log::info('Positions changed successfully!',['CardController::changePositions']);
            return response()->json(['status' => 'success']);

        } catch(Exception $e) {
            DB::rollBack();
            Log::error('Error changing cards position: '. $e->getMessage() .' - '. $e->getFile() .' - '. $e->getLine(),['CardController::changePositions']);
            return response()->json(['message' => "Couldn't update cards positions. Try again later!"], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Card $card)
    {
        try {
            DB::beginTransaction();
            Card::where('kanban_list_id', $card->kanban_list_id)
                ->where('position', '>', $card->position)
                ->decrement('position');

            $card->delete();
            
            DB::commit();
            Log::info('Card deleted successfully!',['CardController::destroy']);
            return response()->json(['status' => 'success']);

        } catch(Exception $e) {
            DB::rollBack();
            Log::error('Error deleting the Card: '. $e->getMessage() .' - '. $e->getFile() .' - '. $e->getLine(),['CardController::delete']);
            return response()->json(['message' => "Couldn't delete the Card. Try again later!"], 500);
        }
    }

    protected function reorderList($listId)
    {
        $cards = Card::where('kanban_list_id', $listId)
            ->orderBy('position')
            ->get();

        //Verify if its equals to its index, if not, change to be to prevent ordering logic flaws
        foreach ($cards as $index => $card) {
            if ($card->position !== $index) {
                $card->update(['position' => $index]);
            }
        }
    }

}
