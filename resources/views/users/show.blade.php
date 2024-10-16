@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Account details') }}</div>
                <div class="card-body">

                    <form>
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Prefix name') }}</label>

                            <div class="col-md-6">
                                <div class="col-md-6">
                                    <input id="name" type="text" readonly class="form-control-plaintext @error('prefix') is-invalid @enderror" name="name" value="{{ $user->prefixname ?? '' }}" required autocomplete="name" autofocus>
                                </div>
                            </div>
                        </div>


                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('First name') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" readonly class="form-control-plaintext" name="name" value="{{ $user->name ?? '' }}" required autocomplete="name" autofocus>
                            </div>
                        </div>


                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Middle name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" readonly class="form-control-plaintext" name="middlename" value="{{ $user->middlename ?? '' }}" autocomplete="name" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Last name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" readonly class="form-control-plaintext" name="lastname" value="{{ $user->lastname ?? '' }}" required autocomplete="name" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Suffix name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" readonly class="form-control-plaintext" name="suffixname" value="{{ $user->suffixname ?? '' }}" autocomplete="name" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Username') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" readonly class="form-control-plaintext" name="username" value="{{ $user->username ?? '' }}" required autocomplete="name" autofocus>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" readonly class="form-control-plaintext" name="email" value="{{ $user->email ?? '' }}" required autocomplete="email">

                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="photo" class="col-md-4 col-form-label text-md-end">{{ __('Photo') }}</label>

                            <div class="col-md-6">

                                <img src="{{$user->getAvatarAttribute()}}" class="img-thumbnail">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-12">
                                @if(!isset($user->deleted_at))

                                <!-- Cant delete as the user logged in -->
                                @if (auth()->user()->id !== $user->id)
                                    <a href="{{ route('users.destroy', $user->id) }}" class="btn btn-danger float-start">
                                        {{ __('Delete user') }}
                                    </a>
                                @endif

                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary float-end">
                                    {{ __('Edit') }}
                                </a>
                                @else
                                <a href="{{ route('users.restore', $user->id) }}" class="btn btn-primary float-start">
                                    {{ __('Restore user') }}
                                </a>
                                <a href="{{ route('users.delete', $user->id) }}" class="btn btn-danger float-end">
                                    {{ __('Premanently delete user') }}
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>



                </div>
            </div>
        </div>
    </div>
    @endsection