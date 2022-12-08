<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Config;
use Auth;
use NextApps\VerificationCode\VerificationCode;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', 
            [
                'except' => [
                    'login',
                    'register', 
                    'verifyCode',
                    'resendVerificationCode'
                ]
            ]
        );
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
   
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();
        if(!$user)
        {
            return response([
                'message' => 'Please Register Your Account.'
            ], 401);
        }

        // Check password
        if( !Hash::check($credentials['password'], $user->password)) {
            return response([
                'message' => "You'r password is wrong, Please enter correct password."
            ], 401);
        }

        // Check verification
        if(!$user->hasVerifiedEmail()) {
            return response()->json(["message" => "Your account is inactive. Email verification link sent on your email id please verify your account first."]);
        }

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function register(Request $request){
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

        $user =  User::create($request->all());
        
        //verify via code
        VerificationCode::send($user->email);

        //verify via link
        // $user =  User::create($request->all())->sendEmailVerificationNotification();

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
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
