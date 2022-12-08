<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Config;
use NextApps\VerificationCode\VerificationCode;

class AuthController extends Controller
{
    public function register(Request $request) {
        try {

            $validator = \Validator::make($request->all(), [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'password' => 'required|min:6|max:25|confirmed',
            ]);
            
            if ($validator->fails()) {
                return $validator->messages();
            }
            
            //Store Hash Password
            $request->merge([
                'password' => Hash::make($request->password)
            ]);
            
            //verify via code
            $user =  User::create($request->all());
            VerificationCode::send($user->email);

            //verify via link
            // $user =  User::create($request->all())->sendEmailVerificationNotification();
            
            return response([
                "success" => true,
                "message" => 'User register succesfully, please verify your account.',
                'user' => $user,
            ],200);

        } catch (\Exception $e) {
            return response([
                "error"=>$e->getMessage()
            ],500);
        }

    }

    public function login(Request $request) {

        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $fields['email'])->first();
        if(!$user)
        {
            return response([
                'message' => 'Please Register Your Account.'
            ], 401);
        }

        // Check password
        if( !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => "You'r password is wrong, Please enter correct password."
            ], 401);
        }

        if(!$user->hasVerifiedEmail()) {
            return response()->json(["message" => "Your account is inactive. Email verification link sent on your email id please verify your account first."]);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }
    
    public function logout()
    {
        auth()->user()->tokens()->delete();
        
        return [
            'token' => 'Tokens Revoked',
            'message' => 'User is logout.'
        ];
    }

    //Verification via link
        // public function verify($user_id, Request $request) {
            
        //     if (!$request->hasValidSignature()) {
        //         return response()->json(["msg" => "Invalid/Expired url provided."], 401);
        //     }
        
        //     $user = User::findOrFail($user_id);
        
        //     if (!$user->hasVerifiedEmail()) {
        //         $user->markEmailAsVerified();
        //     }
        
        //     return redirect()->to('/');
        // }
        
        // public function resend() {
        //     if (auth()->user()->hasVerifiedEmail()) {
        //         return response()->json(["msg" => "Email already verified."], 400);
        //     }
        
        //     auth()->user()->sendEmailVerificationNotification();
        
        //     return response()->json(["msg" => "Email verification link sent on your email id"]);
        // }
    // verification via link
    
    public function verifyCode(Request $request) {

        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $validator->messages();
        }

        $user = User::whereEmail($request->email)->first();
        if($user->hasVerifiedEmail()) {
            return response()->json(["message" => "Your account is already active."]);
        }

        $status = VerificationCode::verify($request->code, $request->email);

        if ($status) {
            User::whereEmail($request->email)->first()->markEmailAsVerified();
            return response()->json(["message" => "Your account is activated."]);
        }
        else {
            return response()->json(["message" => "Incorrect verification code."]);
        }
    }

    public function resendVerificationCode(Request $request) {
        
        $validator = \Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);
        
        if ($validator->fails()) {
            return $validator->messages();
        }

        VerificationCode::send($request->email);
        return response()->json(["message" => "Email verification code resent on your email id"]);
    }
}
