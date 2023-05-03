<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


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

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ],
    );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json($user, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/users/profile",
     *     summary="Update user profile",
     *     description="Updates the user profile information and password if provided",
     *     operationId="updateProfile",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="User profile information and password if provided",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *             @OA\Property(property="current_password", type="string", example="oldpassword"),
     *             @OA\Property(property="new_password", type="string", example="newpassword"),
     *             @OA\Property(property="confirm_password", type="string", example="newpassword")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Profile updated successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object", example={"email": {"The email field is required."}})
     *         )
     *     )
     * )
     */
    public function updateUserProfile(Request $request)
    {
        $user = auth()->user();
         
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:8',
            'new_password' => 'required_with:password|string|min:8|different:password|same:confirm_password',
            'confirm_password' => 'required_with:password|string|min:8|same:new_password',
            ],
        );
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
    
        $user->name = $request->name;
        $user->email = $request->email;
        $current_password = $request->current_password;
        $new_password = $request->new_password;
        $confirm_password = $request->confirm_password;
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }
    
        if(!Hash::check($current_password, $user->password)) {
            return response()->json(['message' => 'password does not match']);
        }
    
        $user->password = bcrypt($confirm_password);
    
        $user->save();
    
        return response()->json(['message' => 'Profile updated successfully']);
    }

}
