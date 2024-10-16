@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Accounts overview') }}</div>
                <div class="card-body">
                    <a href="{{ route('users.index') }}"  class="float-end">Overview accounts</a>

                   <!-- table with users -->
                     <table class="table">
                      <thead>
                            <tr>
                             <th scope="col">Prefix name</th>
                             <th scope="col">First name</th>
                             <th scope="col">Middle name</th>
                             <th scope="col">Last name</th>
                             <th scope="col">Suffix name</th>
                             <th scope="col">Username</th>
                            <th scope="col">Details</th>
                            </tr>
                      </thead>
                      <tbody>
                            @foreach($users as $user)
                            <tr>
                             <td>{{ $user->prefixname }}</td>
                             <td>{{ $user->name }}</td>
                             <td>{{ $user->middlename }}</td>
                             <td>{{ $user->lastname }}</td>
                             <td>{{ $user->suffixname }}</td>
                             <td>{{ $user->username }}</td>
                             <td><a href="{{ route('users.show', $user->id) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
  <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
  <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
</svg>                                   
</a></td>
                            </tr>
                            @endforeach
                      </tbody>

                </div>
            </div>
        </div>
    </div>
    @endsection