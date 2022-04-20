<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function register(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, $this->userRepository->rules($id=null));
        if($validator->fails())
        {
            return response([__('message')=> __('Errors'),'Errors'=> $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
        $validated = $validator->validated();
        $user = $this->userRepository->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ],Response::HTTP_CREATED);
    }
    
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ],Response::HTTP_UNAUTHORIZED);
        }
        $user = $this->userRepository->all()->where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ],Response::HTTP_OK);
    }
}
