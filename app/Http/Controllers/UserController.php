<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{

        /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Create a new user",
     *     description="Create a new user",
     *     operationId="createUser",
     *     tags={"Users"},
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="user data",
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="João da Silva"),
     *             @OA\Property(property="email", type="string", example="joao.silva@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid data",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="O campo name é obrigatório"),
     *             @OA\Property(property="errors", type="object", example={"name": {"O campo name é obrigatório"}})
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json($user, 201);
    }

}
