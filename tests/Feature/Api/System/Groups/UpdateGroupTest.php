<?php

namespace Tests\Feature\Api\System\Groups;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\RequiresAuth;
use Tests\TestCase;

class UpdateGroupTest extends TestCase
{
    use RefreshDatabase;
    use RequiresAuth;

    /**
     * @var array
     */
    private $deleteGroupIds = [];

    protected function tearDown()
    {
        $this->deleteTemporaryGroups();
    }

    /** @test */
    public function can_update_group()
    {
        $group = $this->authed()->postJson('/api/system/groups', [
            'name' => 'updatetestgroup',
        ])->json();

        $response = $this->authed()->putJson('/api/system/groups/'.$group['gid'], [
            'name' => 'updatetestgroup-renamed',
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['name' => 'updatetestgroup-renamed']);

        $this->deleteGroupIds[] = $response->json()['gid'];

        return $response;
    }

    /**
     * @test
     * @depends can_update_group
     */
    public function update_response_contains_all_keys($response)
    {
        $response->assertJsonStructure($this->expectedKeys());
    }

    /** @test */
    public function cannot_update_nonexistant_group()
    {
        $response = $this->authed()->putJson('/api/system/groups/9032', [
            'name' => 'nogrouptest',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function deleteTemporaryGroups()
    {
        $endpoint = '/api/system/groups/';

        foreach ($this->deleteGroupIds as $gid) {
            $this->authed()->deleteJson($endpoint.$gid);
        }
    }

    private function expectedKeys()
    {
        return [
            'gid',
            'name',
            'users',
        ];
    }
}
