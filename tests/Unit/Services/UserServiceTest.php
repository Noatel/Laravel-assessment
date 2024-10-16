<?php

namespace Tests\Unit\Services;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Services\UserServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class UserServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $image;

    public function setUp(): void
    {
        parent::setUp();
        $password = Hash::make('test123');

        // Because the User Request expect an actuall image instead just the name
        // Create an image
        Storage::fake('public');
        $image = UploadedFile::fake()->image('account.jpg');

        // Create a test user
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

        // Create 9 more random users so we have 10 in total, nice roudn number
        for ($i = 0; $i < 9; $i++) {
            User::create([
                'name' => $this->faker->firstName,
                'middlename' => $this->faker->lastName,
                'lastname' => $this->faker->lastName,
                'email' => $this->faker->unique()->safeEmail,
                'password' => $password,
                'prefixname' => 'mr',
                'username' => $this->faker->userName,
                'photo' => $image,
                'type' => 'user',
            ]);
        }
    }

    /**
     * @test
     * @return void
     */
    public function it_can_return_a_paginated_list_of_users()
    {
        // Arrangements
        $request = Request::create('/user', 'GET');
        $userService = new UserService($this->user, $request);

        // Actions
        $list = $userService->list();

        // Assertions
        $this->assertCount(10, $list);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_store_a_user_to_database()
    {
        // Arrangements
        $request = Request::create('/user/store', 'POST');
        $userService = new UserService($this->user, $request);
        $password = Hash::make('test123');

        // Actions
        $userService->store([
            'name' => $this->faker->firstName,
            'middlename' => $this->faker->lastName,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $password,
            'prefixname' => 'mr',
            'username' => $this->faker->userName,
            'photo' => $this->image,
            'type' => 'user',
        ]);

        // Assertions
        $users = $userService->list();
        $this->assertCount(11, $users);
    }


    /**
     * @test
     * @return void
     */
    public function it_cant_store_a_user_to_database()
    {
        // Arrangements
        $request = Request::create('/user/store', 'POST');
        $userService = new UserService($this->user, $request);
        $password = Hash::make('test123');

        // Actions
        $userService->store([
            'middlename' => $this->faker->lastName,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $password,
            'prefixname' => 'mr',
            'username' => $this->faker->userName,
            'photo' => $this->image,
            'type' => 'user',
        ]);

        // Assertions
        $users = $userService->list();

        // Check if the count is still 10, since its an invalid array (name is missing)
        $this->assertCount(10, $users);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_find_and_return_an_existing_user()
    {
        // Arrangements
        $request = Request::create('/user/1', 'GET');
        $userService = new UserService($this->user, $request);

        // Actions
        // Since we create an user in the setup, user 1 always exsist (that is me)
        $user = $userService->find(1);

        // Assertions
        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @test
     * @return void
     */
    public function it_cant_find_and_return_an_existing_user()
    {
        // Arrangements
        $request = Request::create('/user/1345678', 'GET');
        $userService = new UserService($this->user, $request);

        // Actions
        // Since we create an user in the setup, user 1 always exsist (that is me)
        $user = $userService->find(1345678);

        // Assertions
        // Check if user is empty, not found, since i just typed a random number
        $this->assertNull($user);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_update_an_existing_user()
    {
        // Arrangements
        $request = Request::create('/user/update/1', 'POST');
        $userService = new UserService($this->user, $request);

        // Actions
        $isUpdated = $userService->update(1, ['name' => 'Test']);

        // Assertions
        $user = $userService->find(1);

        $this->assertTrue($isUpdated);
        $this->assertEquals('Test', $user->name);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_soft_delete_an_existing_user()
    {
        // Arrangements
        $request = Request::create('user/1/destroy', 'get');
        $userService = new UserService($this->user, $request);

        // Actions
        $userService->destroy(1);

        // Assertions
        $user = $userService->findThrashedUsers(1);
        $this->assertNotNull($user->deleted_at);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_users()
    {
        // Arrangements
        $request = Request::create('user/thrashed', 'GET');
        $userService = new UserService($this->user, $request);

        // Actions
        $userService->destroy(1);
        $users = $userService->listTrashed();

        // Assertions
        $this->assertCount(1, $users);
    }

    /**
     * @test
     * @return void
     */
    public function it_cant_return_enough_paginated_list_of_trashed_users()
    {
        // Arrangements
        $request = Request::create('user/thrashed', 'GET');
        $userService = new UserService($this->user, $request);

        // Actions
        // Removed the destory action so no user is trashed and acutall number is 0
        $users = $userService->listTrashed();

        // Assertions
        $this->assertCount(expectedCount: 0, haystack: $users);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_restore_a_soft_deleted_user()
    {
        // Arrangements
        $request = Request::create('user/1/restore', 'get');
        $userService = new UserService($this->user, $request);

        // Actions
        $userService->destroy(1);
        $userService->restore(1);

        // Assertions
        $users = $userService->listTrashed();
        $this->assertCount(expectedCount: 0, haystack: $users);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_user()
    {
        // Arrangements
        $request = Request::create('user/1/delete', 'get');
        $userService = new UserService($this->user, $request);

        // Actions
        $userService->destroy(1);
        $userService->delete(1);

        // Assertions
        $users = $userService->listTrashed();
        $thrashedUser = $userService->findThrashedUsers(1);
        $user = $userService->find(1);

        $this->assertCount(expectedCount: 0, haystack: $users);
        $this->assertNull($user);
        $this->assertNull($thrashedUser);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_upload_photo()
    {
        // Arrangements
        $request = Request::create('/upload/photo', 'get');
        $userService = new UserService($this->user, $request);
        $image = UploadedFile::fake()->image('account.jpg');

        // Actions
        $photo = $userService->upload($image);

        // Assertions
        $this->assertNotFalse($photo);
    }
}
