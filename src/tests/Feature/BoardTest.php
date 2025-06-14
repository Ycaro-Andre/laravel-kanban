<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Board;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        // Create and authenticate a user
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_can_create_board()
    {
        $payload = ['title' => 'New Board'];

        $response = $this->postJson(route('boards.store'), $payload);
        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'New Board']);

        $this->assertDatabaseHas('boards', ['title' => 'New Board']);
    }

    public function test_can_get_board()
    {
        $board = Board::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson(route('boards.show', $board->id));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Kanban/Board')
            ->where('board.id', $board->id)
        );
    }

    public function test_can_update_board()
    {
        $board = Board::factory()->create(['user_id' => $this->user->id]);
        $payload = ['title' => 'Updated Board Title'];

        $response = $this->putJson(route('boards.update', $board->id), $payload);
        $response->assertStatus(200)
            ->assertJsonFragment(['title' => 'Updated Board Title']);

        $this->assertDatabaseHas('boards', ['id' => $board->id, 'title' => 'Updated Board Title']);
    }

    public function test_can_delete_board()
    {
        $board = Board::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson(route('boards.delete', $board->id));
        $response->assertStatus(200);

        $this->assertSoftDeleted('boards', ['id' => $board->id]);
    }
}
