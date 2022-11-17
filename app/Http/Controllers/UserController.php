<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateUserRequest;
use App\Http\Requests\CreateUserRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function create(CreateUserRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                Client::create([
                    'surname' => $request->post('surname'),
                    'birth_date' => $request->post('birthDate'),
                ]);
                User::create([
                    'name' => $request->post('name'),
                    'email' => $request->post('email'),
                    'password' => Hash::make($request->post('password')),
                ]);
                $client->user()->save($user);
            });
        } catch (QueryException $e) {
            return [
                'result' => 'Unable to create user right now.',
            ];
        }

        return [
            'result' => 'User registered succesfully.',
        ];
    }

    public function authenticate(AuthenticateUserRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'), $request->post('remember'))) {
            return [
                'result' => 'Wrong password or email.',
            ];
        }

        return [
            'result' => 'Succesfully logged in.',
            'subscriptionId' => User::firstWhere('id', Auth::id())->client->subscription_id,
            'subscription' => Auth::user()->client->subscription->type,
        ];
    }

    public function update(UpdateUserRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                User::find(Auth::id())->update([
                    'name' => $request->post('name'),
                    'email' => $request->post('email'),
                ]);
                if (!empty($request->post('password'))) {
                    User::find(Auth::id())->update([
                        'password' => Hash::make($request->post('password')),
                    ]);
                }

                User::find(Auth::id())->client->update([
                    'surname' => $request->post('surname'),
                    'birth_date' => $request->post('birthdate'),
                ]);
            });
        } catch (QueryExcpetion $e) {
            return [
                'result' => 'Unable to update user right now.',
            ];
        }

        return [
            'result' => 'User information updated succesfully.',
        ];
    }

    
}
