<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

        /**
     * Authenticate user and generate access token
     *
     * @OA\Post(
     *      path="/api/login",
     *      tags={"Authentication"},
     *      summary="Authenticate user and generate access token",
     *      description="Authenticate user using email and password and generate an access token",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password123")
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Authentication successful",
     *          @OA\JsonContent(
     *              @OA\Property(property="token", type="string", description="Access token"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid credentials",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Invalid credentials"),
     *          ),
     *      ),
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            return response()->json([
                'token' => $token,
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }

        /**
     *
     * @OA\Post(
     *      path="/api/logout",
     *      tags={"Authentication"},
     *      summary="Revoke all access tokens for authenticated user",
     *      description="Revoke all access tokens for authenticated user, logging them out of the system",
     *      security={ {"sanctum": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Access tokens revoked successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Logged out"),
     *          ),
     *      ),
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logged out',
        ]);
    }
}
