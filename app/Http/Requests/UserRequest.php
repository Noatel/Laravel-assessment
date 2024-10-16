<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'prefixname' => ['nullable', 'string', 'in:mr,mrs,ms'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'suffixname' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username,' . $userId],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,bmp,gif,svg'],
            'type' => ['nullable', 'string', 'in:user,admin'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
        ];
    }
}
