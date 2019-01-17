<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller {

    public $successStatus = 200;

    public function login(){
        $credentials = request(['email', 'password']);
        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $token = $user->createToken('Token Acess');
            $success['token_type'] = 'Bearer';
            $success['expires_at'] =  Carbon::parse(
                $token->token->expires_at
            )->toDateTimeString();
            $success['token'] =  $token-> accessToken;
            return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required',
            'cpf' => 'required|unique:users|digits:11|numeric',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['name'] =  $user->name;
        $success['cpf'] =  $user->cpf;
        $success['email'] =  $user->email;
        return response()->json(['success'=>$success], 201);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'cpf' => 'required|unique:users|digits:11|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $user = Auth::user();
        $input = $request->all();
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->cpf = $input['cpf'];
        $user->save();
        $success['name'] =  $user->name;
        $success['cpf'] =  $user->cpf;
        $success['email'] =  $user->email;

        return response()->json(['success'=>$success], $this-> successStatus);
    }

    public function changepassword(Request $request){
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $user = Auth::user();
        $input = $request->all();
        $user->password = bcrypt($input['password']);
        $user->save();
        $user->token()->revoke();

        return response()->json([
            'message'=> 'Password changed successfully'
        ], $this-> successStatus);
    }

    public function delete(Request $request)
    {
      $user = Auth::user();
      $user->token()->revoke();
      $user->delete();
      return response()->json([
          'message' => 'User deleted successfully'
      ]);
    }

    public function details()
    {
        $user = Auth::user();
        $success['name'] =  $user->name;
        $success['cpf'] =  $user->cpf;
        $success['email'] =  $user->email;
        $success['last_update'] =  $user->updated_at;
        $success['email_verified'] = $user->email_verified_at != null ? true : false ;
        return response()->json(['success' => $success], $this-> successStatus);
    }
}
