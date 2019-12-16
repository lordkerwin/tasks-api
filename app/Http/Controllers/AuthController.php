<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers\Api;

namespace App\Http\Controllers;
use Laravel\Passport\Token;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    // -------------- [ User Registration ] ------------------
    public function register(Request $request)
    {
        // validate the request
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|min:3',
                'email' => 'required|email',
                'password' => 'required|alpha_num|min:5',
                'c_password' => 'required|same:password'
            ]
        );

        // if validation fails, return an error
        if ($validator->fails()) {
            return $this->sendError('Validation errors', $validator->errors(), 400);
        }

        $input = array(
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        );

        // check if email already registered
        $user = User::where('email', $request->email)->first();
        if (!is_null($user)) {
            $data['message'] = "Sorry! this email is already registered";
            return $this->sendError('Error', $data, 422);
        }

        $user = User::create($input);
        $success['message'] = "You have registered successfully";
        $data = [
            'user' => $user
        ];
        return $this->sendResponse($data, 'User Created Successfully!');
    }

    // -------------- [ User Login ] ------------------
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {

            // getting auth user after auth login
            $user = Auth::user();

            // TODO: Revoke old tokens if user logs in again!

            $token = $user->createToken('token')->accessToken;
            $data = [
                'token' => $token,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ];

            return $this->sendResponse($data, 'Success! you are logged in successfully');
        } else {
            return $this->sendError('Error logging in, email or password is incorrect', null, 401);
        }
    }

}
