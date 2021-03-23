<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {

            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $userRole = $user->role;
            if ($userRole) {
                $this->scope = $userRole->role_name;
            }
            $success['token'] =  $user->createToken(env('OAUTH_KEY'), [$this->scope])->accessToken;
            $success['name'] =  $user->name;
            $success['role'] =  $user->role;

            return $this->sendResponse($success, 'User register successfully.');
        } catch (\Throwable $th) {
            return $this->sendError(500, ['error' => $th]);
        }
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $userRole = $user->role;
            if ($userRole) {
                $this->scope = $userRole->role_name;
            }
            $success['token'] =  $user->createToken(env('OAUTH_KEY'), [$this->scope])->accessToken;
            $success['name'] =  $user->name;
            $success['role'] =  $user->role;

            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }

    public function logout(Request $request)
    {
        $logout = $request->user()->token()->revoke();
        if ($logout) {
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        }
    }
}
