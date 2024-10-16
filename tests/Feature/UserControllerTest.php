<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserControllerTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    protected $user;
    protected $testUser;

    protected function setUp(): void
    {
        parent::setUp();
        $password = Hash::make('test123');

        // Because the User Request expect an actuall image instead just the name
        // Create an image
        Storage::fake('public');
        $image = UploadedFile::fake()->image('account.jpg');


        // Create and authenticate a user
        $this->user = User::create([
            'name' => 'Noah',
            'middlename' => 'Yael',
            'lastname' => 'Telussa',
            'email' => 'noahtelussa@gmail.com',
            'password' => $password,
            'prefixname' => 'mr',
            'username' => 'noahtelussa',
            'photo' => $image,
            'type' => 'user',
        ]);

        $this->testUser = User::create([
            'name' => 'testAccount',
            'middlename' => 'Yael',
            'lastname' => 'Telussa',
            'email' => 'testUser@gmail.com',
            'password' => $password,
            'prefixname' => 'mr',
            'username' => 'testUser',
            'photo' => $image,
            'type' => 'user',
        ]);

        $this->actingAs($this->user);
    }

    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        // Arrangements
        $response = $this->get('user/');
        $users = User::all();

        // Actions

        // Assertions
        $response->assertStatus(200);
        $response->assertViewIs('users.index');
        $response->assertSee('Noah');
        $response->assertSee('testAccount');
    }

    public function test_show(): void
    {
        // Arrangements
        $response = $this->get('user/' . $this->user->id);

        // Actions

        // Assertions
        $response->assertStatus(200);
        $response->assertViewIs('users.show');
        $response->assertSee('Noah');
        $response->assertDontSee('testAccount');
    }

    public function test_create(): void
    {
        // Arrangements
        $response = $this->get('user/create');

        // Actions

        // Assertions
        $response->assertStatus(200);
        $response->assertViewIs('users.create');
        $response->assertSee('Register');
    }

    public function test_store(): void
    {
        // Arrangements
        $password = Hash::make('test123');
        $image = UploadedFile::fake()->image('account.jpg');
        $response = $this->post('user/store', [
            'name' => $this->faker->firstName,
            'middlename' => $this->faker->lastName,
            'lastname' => $this->faker->lastName,
            'email' => 'feature@test.com',
            'password' => $password,
            'prefixname' => 'mr',
            'username' => $this->faker->userName,
            'photo' => $image,
            'type' => 'user',
        ]);

        // Actions

        // Assertions
        // For some reason when doing the redirect the variable onEachSide is getting added
        $response->assertRedirect(route('users.index', ['users' => ['onEachSide' => 3]]));
        $this->assertDatabaseHas('users', [
            'email' => 'feature@test.com',
        ]);
    }
    public function test_update(): void
    {

        // Arrangements
        $response = $this->post(
            'user/update/' . $this->user->id,
            [
                'name' => 'Noah',
                'middlename' => 'Yael',
                'lastname' => 'Telussa',
                'email' => 'TestingUpdateNoah@email.com',
                'prefixname' => 'mr',
                'username' => 'noahtelussa',
                'type' => 'user',
            ]
        );

        // Actions

        // Assertions
        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'TestingUpdateNoah@email.com',
        ]);
    }

    public function test_edit() : void {
        // Arrangements
        $response = $this->get('user/edit/' . $this->user->id);

        // Actions

        // Assertions
        $response->assertStatus(200);
        $response->assertViewIs('users.edit');
        $response->assertSee('Noah');
    }


    public function test_destroy() : void {
        // Arrangements
        $response = $this->get(route('users.destroy', $this->user->id));

        // Actions

        // Assertions
        $response->assertStatus(302);
        // For some reason when doing the redirect the variable onEachSide is getting added
        $response->assertRedirect(route('users.index', ['users' => ['onEachSide' => 3]]));
        $response->assertDontSee('Noah');
    }

    public function test_trashed():void{
        // Arrangements
        $response = $this->get(route('users.thrashed'));
        $request = Request::create('user/thrashed','GET');
        $userService = new UserService($this->user, $request);

        // Actions
        $userService->destroy(1);

        // Assertions
        $response->assertStatus(200);
        // For some reason when doing the redirect the variable onEachSide is getting added
        $response->assertViewIs('users.thrashed');
        $response->assertSee('Noah');
    }

    public function test_restore() : void {
         // Arrangements
         $request = Request::create('user/thrashed','GET');
         $userService = new UserService($this->testUser, $request);
         $userService->destroy(1);

         $response = $this->get(route('users.restore', $this->testUser->id));
         // Actions
 
         // Assertions
         $response->assertStatus(200);
         // For some reason when doing the redirect the variable onEachSide is getting added
         $response->assertViewIs('users.thrashed');
         $response->assertDontSee('testAccount');
    }

    public function test_delete() : void{
         // Arrangements
         $request = Request::create('user/thrashed','GET');
         $userService = new UserService($this->testUser, $request);
         $userService->destroy(1);

         $response = $this->get(route('users.delete', $this->testUser->id));
         // Actions
 
         // Assertions
         $response->assertStatus(200);
         // For some reason when doing the redirect the variable onEachSide is getting added
         $response->assertViewIs('users.thrashed');
         $response->assertDontSee('testAccount');
    }
}
