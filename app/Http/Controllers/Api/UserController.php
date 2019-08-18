<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{


    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            return response()
                ->json([
                    'user' => new UserResource($user),
                    'token' => $user->createToken('V1.0')->accessToken
                ]);
        } else {
            return response()
                ->json(['message' => 'Unauthenticated.'])
                ->setStatusCode(401);
        }
    }


    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
//        dd($request);
        // make validator
        $validator = Validator::make($request->all(), [
            'fname' => 'required|alpha|max:255',
            'lname' => 'required|alpha|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|numeric|regex:/(05)[0-9]{8}/|unique:users',
            'password' => 'required|string|min:6',
            'c_password' => 'required|same:password',
        ]);

        // validate request
        if ($validator->fails()) {
            return response()
                ->json(['error' => $validator->errors()])
                ->setStatusCode(400);
        }

        // create user
        $input = $request->all(['fname', 'lname', 'email', 'phone', 'password']);
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);


        return response()
            ->json([
                'user' => new UserResource($user),
                'token' => $user->createToken('V1.0')->accessToken
            ]);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        return response()
            ->json([
                'user' => new UserResource($user),
            ]);
    }
}
