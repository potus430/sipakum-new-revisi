<?php

namespace App\Actions\Fortify;

// use App\Concerns\PasswordValidationRules;
// use App\Concerns\ProfileValidationRules;
// use App\Models\User;
// use Illuminate\Support\Facades\Validator;
// use Laravel\Fortify\Contracts\CreatesNewUsers;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Validation\Rule;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            // Validasi nama tetap ada
            'name' => ['required', 'string', 'max:255'],
            // Mengganti email dengan username
            'username' => [
                'required', 
                'string', 
                'alpha_dash', 
                'max:255', 
                Rule::unique(User::class)
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'username' => $input['username'],
            'password' => Hash::make($input['password']),
            // Aturan bisnis SIPAKUM
            'role' => 'user',      //
            'is_active' => false,  //
        ]);
        
        // Validator::make($input, [
        //     ...$this->profileRules(),
        //     'password' => $this->passwordRules(),
        // ])->validate();

        // return User::create([
        //     'name' => $input['name'],
        //     'email' => $input['email'],
        //     'password' => $input['password'],
        // ]);
    }
}
