<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Board;
use App\Models\KanbanList;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KanbanListTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Board $board;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->board = Board::factory()->create(['user_id' => $this->user->id]);
        $this->actingAs($this->user);
    }

    public function test_can_create_list()
    {
        $payload = [
            'title' => 'New List',
            'board_id' => $this->board->id,
        ];

        $response = $this->postJson(route('lists.store'), $payload);
        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'New List']);

        $this->assertDatabaseHas('kanban_lists', ['title' => 'New List', 'board_id' => $this->board->id]);
    }

    public function test_can_update_list()
    {
        $list = KanbanList::factory()->create(['board_id' => $this->board->id]);
        $payload = ['title' => 'Updated List Title'];

        $response = $this->putJson(route('lists.update', $list->id), $payload);
        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated List Title']);

        $this->assertDatabaseHas('kanban_lists', ['id' => $list->id, 'title' => 'Updated List Title']);
    }

    public function test_can_delete_list()
    {
        $list = KanbanList::factory()->create(['board_id' => $this->board->id]);

        $response = $this->deleteJson(route('lists.delete', $list->id));
        $response->assertStatus(200);

        $this->assertSoftDeleted('kanban_lists', ['id' => $list->id]);
    }
}
