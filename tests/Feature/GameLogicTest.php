<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameLogicTest extends TestCase
{
    use RefreshDatabase;


    public function test_cannot_cheat_in_console_by_making_multiple_moves_in_one_turn()
    {
        /** @var \App\Models\User $playerX */
        
        $playerX = User::factory()->create();
        $playerO = User::factory()->create();
        //set the two player

       

        $game = Game::factory()->create([
            'player_one_id' => $playerX->id,
            'player_two_id' => $playerO->id,
            'state' => [0, 0, 0, 0, 0, 0, 0, 0, 0],
        ]);

        $response1 = $this
            ->actingAs($playerX)
            ->put(route('games.update', $game->id), [
                'state' => [-1, 0, 0, 0, 0, 0, 0, 0, 0],
            ]);

       
        $response1->assertStatus(200);

        $cheatResponse = $this
            ->actingAs($playerX)
            ->put(route('games.update', $game->id), [
                'state' => [-1, -1, -1, -1, 0, 0, 0, 0, 0],
            ]);

        $cheatResponse->assertStatus(422);

        $game->refresh();
        $this->assertEquals(
            [-1, 0, 0, 0, 0, 0, 0, 0, 0],
            $game->state,
            'can not cheat'
        );
    }
}