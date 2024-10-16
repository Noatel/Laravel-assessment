<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Auth\RegisterController;
use App\Services\UserService;
use Carbon\Carbon;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    public function index()
    {
        $users = $this->userService->list();

        return view('users.index', compact('users'));
    }


    public function show($id)
    {
        $user = $this->userService->findWithThrashed($id);

        return view('users.show', compact('user'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(UserRequest $request)
    {
        // Dont want to duplicate code, call register controller create method
        $registerController = new RegisterController();
        $user = $registerController->create($request);

        $users = $this->userService->list();
        return redirect()->route('users.index', compact('users'));
    }

    public function update(UserRequest $request, $id)
    {
        $this->userService->update($id, $request->all());

        return redirect()->route('users.index');
    }

    public function edit($id)
    {
        $user = $this->userService->find($id);

        return view('users.edit', compact('user'));
    }

    public function destroy($id)
    {
        $this->userService->destroy($id);

        $users = $this->userService->list();
        return redirect()->route('users.index', compact('users'));
    }

    public function thrashed()
    {
        $users = $this->userService->listTrashed();

        return view('users.thrashed', compact('users'));
    }

    public function restore($id)
    {
        $user = $this->userService->restore($id);
        $users = $this->userService->listTrashed();

        return view('users.thrashed', compact('users'));
    }

    public function delete($id)
    {
        $user = $this->userService->findThrashedUsers($id);

        if ($user) {
            $this->userService->delete($user->id);
        }

        $users = $this->userService->listTrashed();
        return view('users.thrashed', compact('users'));
    }
}
