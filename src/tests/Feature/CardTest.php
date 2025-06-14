<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Board;
use App\Models\KanbanList;
use App\Models\Card;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Board $board;
    protected KanbanList $list;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->board = Board::factory()->create(['user_id' => $this->user->id]);
        $this->list = KanbanList::factory()->create(['board_id' => $this->board->id]);
        $this->actingAs($this->user);
    }

    public function test_can_create_card()
    {
        $payload = [
            'title' => 'New Card',
            'description' => 'Card description',
            'kanban_list_id' => $this->list->id,
            'position' => 1,
        ];

        $response = $this->postJson(route('cards.store'), $payload);
        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'New Card']);

        $this->assertDatabaseHas('cards', ['title' => 'New Card', 'kanban_list_id' => $this->list->id]);
    }

    public function test_can_update_card()
    {
        $card = Card::factory()->create(['kanban_list_id' => $this->list->id]);
        $payload = [
            'title' => 'Updated Card Title',
            'description' => 'Updated description',
        ];

        $response = $this->putJson(route('cards.update', $card->id), $payload);
        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Card Title']);

        $this->assertDatabaseHas('cards', ['id' => $card->id, 'title' => 'Updated Card Title']);
    }

    public function test_can_delete_card()
    {
        $card = Card::factory()->create(['kanban_list_id' => $this->list->id]);

        $response = $this->deleteJson(route('cards.delete', $card->id));
        $response->assertStatus(200);

        $this->assertSoftDeleted('cards', ['id' => $card->id]);
    }
}
