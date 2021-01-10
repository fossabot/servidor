<?php

namespace Tests\Unit\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Servidor\Http\Requests\UpdateProjectRequest;
use Servidor\Projects\Project;
use Tests\TestCase;
use Tests\ValidatesFormRequest;

class UpdateProjectRequestTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesFormRequest;

    public function setUp(): void
    {
        parent::setUp();

        $this->shouldValidate(UpdateProjectRequest::class);
    }

    /** @test */
    public function name_is_required_when_present(): void
    {
        $this->validateFieldFails('name', '');
        $this->validateFieldPasses('name', 'A name');
    }

    /** @test */
    public function name_must_be_a_string(): void
    {
        $this->validateFieldFails('name', true);
        $this->validateFieldFails('name', 42);
        $this->validateFieldFails('name', []);
    }

    /** @test */
    public function name_must_be_unique(): void
    {
        Project::create(['name' => 'Duplicate me!']);

        $this->validateFieldFails('name', 'Duplicate me!');
        $this->assertEquals(1, Project::count());
    }
}
