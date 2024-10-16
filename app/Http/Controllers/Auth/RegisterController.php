<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UserRequest;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public function create(UserRequest $request)
    {
        // check if there is an image
        // if there is an image store it and save the name
        $imageName = null;
        if ($request->hasFile('photo')) {
            $request->file('photo')->store('public/users');
            $imageName = $request->file('photo')->hashName();
        }

        // Create the user
        User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'prefixname' => $request->input('prefixname'),
            'middlename' => $request->input('middlename'),
            'lastname' => $request->input('lastname'),
            'suffixname' => $request->input('suffixname'),
            'username' => $request->input('username'),
            'photo' => $imageName,
            'type' => $request->input('type', 'user'),
            'password' => Hash::make($request->input('password')),
        ]);


        // Going to check if the user is already loggedin, if not it create only the acc
        if (!auth()->check()) {
            auth()->attempt($request->only('email', 'password'));
            return redirect()->route('login');
        }
    }

    // register
    public function register()
    {
        return view('auth.register');
    }
}
