<?php

namespace App\Http\Controllers\Api\V1\Jwt;

use JWTAuth;
use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Requests\User\Store;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthenticateController extends Controller
{

    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        // all good so return the token
        return response()->json(compact('token'));
    }

    public function register(Store $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:users',
        ]);
        echo 1;die;
        $newUser = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ];
        $user = User::create($newUser);
        //p($user);die;
        $token = JWTAuth::fromUser($user);//根据用户得到token
        $data=[
            'token'=> $token
        ];
        return ajaxReturn($data, '注册成功', 200);
    }
}