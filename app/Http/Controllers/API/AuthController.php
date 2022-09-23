<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\BaseController;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    // use ApiResponser;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'name' => 'required|string|min:2|max:32',
                'username' => 'required|string|min:2|max:32',
                'role_id' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|confirmed',
            ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $request->all();
        $user = new User();
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->password = bcrypt($input['password']);
        $user->username = $input['username'];
        // $user->role_id =$input['role_id'];
        
        $user->save();
        // event(new Registered($user));

        return $this->sendResponse(new UserResource($user), 'User register successfully.');

    }

    public function login(Request $request)
    {
        
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required|string|min:6',
        ]);

        if (!auth()->attempt($loginData)) {
            return $this->sendError('This user does not exist');
        }

        $user = User::where('email', $request->email)->first();

        // if(!$user->email_verified_at){

        //     return $this->sendError('the email not verified');
            
        // }

        return $this->sendResponse(new UserResource(auth()->user()), 'User login successfully');

    }
    
}
