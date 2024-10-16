<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Events\UserSaved;
use Illuminate\Support\Facades\Event;
use App\Listeners\SaveUserBackgroundInformation;
use Illuminate\Http\Request;
use App\Services\UserService;

class DetailTest extends TestCase
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
    }
    /**
     * A basic feature test example.
     */
    public function test_save_user_details(): void
    {
        // Arrange
        Event::fake();
        $this->user->name = 'Testing';
        $this->user->save();

        $event = new UserSaved($this->user);
        $request = Request::create('user/update', 'GET');
        $userService = new UserService($this->user, $request);

        $listener = new SaveUserBackgroundInformation($userService);

        // Act
        event($event); // Dispatch the event
        $listener->handle($event);

        // Assert
        Event::assertDispatched(UserSaved::class);
    }
}
