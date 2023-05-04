<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;


class ClientController extends Controller
{
    /**
     * @OA\Post (
     *     path="/api/clients",
     *     tags={"Clients"},
     *     security={ {"sanctum": {} }},
     *     summary="Create a new client",
     *     description="Create a new client",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="phone",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="user_id",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="address",
     *                          type="object",
     *                          @OA\Property(
     *                              property="cep",
     *                              type="string"
     *                          ),
     *                          @OA\Property(
     *                              property="number",
     *                              type="integer"
     *                          ),
     *                          @OA\Property(
     *                              property="neighborhood",
     *                              type="string"
     *                          ),
     *                          @OA\Property(
     *                              property="city",
     *                              type="string"
     *                          ),
     *                         @OA\Property(
     *                              property="state",
     *                              type="string"
     *                          ),
     *                      ),
     * 
     *                 ),
     *                 example={
     *                     "name":"Carlos Henrique",
     *                     "email":"email@gmail.com",
     *                     "phone":"99999999",
     *                     "user_id":1,
     *                     "address": {
     *                          "cep":"55730000",
     *                          "number":5,
     *                          "neighborhood":"Derby",
     *                          "city":"Bom Jardim",
     *                          "state":"PE",
     *                     }
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Carlos Henrique"),
     *              @OA\Property(property="email", type="string", example="email@gmail.com"),
     *              @OA\Property(property="phone", type="string", example="99999999"),
     *              @OA\Property(
     *                  property="address",
     *                  type="object",
     *                  @OA\Property(
     *                      property="cep",
     *                      type="string",
     *                      example="55730000"
     *                  ),
     *                  @OA\Property(
     *                      property="number",
     *                      type="integer",
     *                      example=5
     *                  ),
     *                  @OA\Property(
     *                      property="neighborhood",
     *                      type="string",
     *                      example="Derby"
     *                  ),
     *                  @OA\Property(
     *                      property="city",
     *                      type="string",
     *                      example="Bom Jardim"
     *                  ),
     *                 @OA\Property(
     *                      property="state",
     *                      type="string",
     *                      example="PE"
     *                  ),
     *              ),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="msg", type="string", example="fail"),
     *          )
     *      )
     * )
     */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
        ],
        [
            'name.required' => 'O nome é obrigatório',
            'email.email' => 'O campo :attribute deve ser um endereço de email válido.',
        ]
    );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $client = new Client();
        $client->name = $request->name;
        $client->email = $request->email;
        $client->phone = $request->phone;
        $client->user_id = $request->user_id;
        $client->save();

        $address = new Address();
        $address->cep = $request->address['cep'];
        $address->number = $request->address['number'];
        $address->neighborhood = $request->address['neighborhood'];
        $address->city = $request->address['city'];  
        $address->state = $request->address['state']; 
        $address->client_id = $client->id;
        $address->save();

        $client = Client::with('address')->find($client->id);

        return response()->json($client, 201);
      
    }

    /**
     * @OA\Put(
     *      path="/api/clients/{id}",
     *      operationId="updateClient",
     *      tags={"Clients"},
     *      summary="Update an existing client",
     *      description="Update an existing client based on the given ID",
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *          name="id",
     *          description="Client ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          description="Client data to update",
     *          @OA\JsonContent(
     *              required={"name","email","phone"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", example="johndoe@example.com"),
     *              @OA\Property(property="phone", type="string", example="555-1234"),
     *              @OA\Property(
     *                  property="address",
     *                  type="object",
     *                  description="Client address information",
     *                  @OA\Property(property="cep", type="string", example="12345678"),
     *                  @OA\Property(property="number", type="integer", example=123),
     *                  @OA\Property(property="neighborhood", type="string", example="Centro"),
     *                  @OA\Property(property="city", type="string", example="São Paulo"),
     *                  @OA\Property(property="state", type="string", example="PE")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Client updated successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Client updated successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorized")
     *          )
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="Client not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Client not found")
     *          )
     *      )
     * )
     */
    public function update(Request $request, $id) {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'message' => 'Client not found',
            ], 404);
        }
        
        if (auth()->user()->id !== $client->user_id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }


        $client->name = $request->name;
        $client->email = $request->email;
        $client->phone = $request->phone;
        $client->save();

        $address = Address::where('client_id', $client->id)->first();

        if ($address) {
            $address->cep = $request->address['cep'];
            $address->number = $request->address['number'];
            $address->neighborhood = $request->address['neighborhood'];
            $address->city = $request->address['city'];
            $address->state = $request->address['state'];
            $address->save();
        }

        return response()->json([
            'message' => 'Client updated successfully',
        ]);
    }

    /**
     * @OA\Delete(
     *      path="/api/clients/{id}",
     *      operationId="deleteClient",
     *      tags={"Clients"},
     *      summary="Delete a client",
     *      description="Delete a client based on the given ID",
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *          name="id",
     *          description="Client ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="Client deleted successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Client deleted successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthorized")
     *          )
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="Client not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Client not found")
     *          )
     *      )
     * )
     */
    public function destroy($id) {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'message' => 'Client not found',
            ], 404);
        }
 
        if (auth()->user()->id !== $client->user_id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $client->delete();

        return response()->json([
            'message' => 'Client deleted successfully',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/clients",
     *     summary="List clients",
     *     description="Returns a list of clients, filtered by name, email or phone, paginated by 10 items per page.",
     *     tags={"Clients"},
     *     security={ {"sanctum": {} }},
     *  @OA\Parameter(
     *         name="take",
     *         in="query",
     *         description="Selects a specified number of elements",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter clients by name (case insensitive).",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="Filter clients by email (case insensitive).",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="Filter clients by phone (case insensitive).",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of clients",
     *         @OA\JsonContent(
     *          @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="name", type="string", example="Carlos Henrique"),
     *              @OA\Property(property="email", type="string", example="email@gmail.com"),
     *              @OA\Property(property="phone", type="string", example="99999999"),
     *              @OA\Property(
     *                  property="address",
     *                  type="object",
     *                  @OA\Property(
     *                      property="cep",
     *                      type="string",
     *                      example="55730000"
     *                  ),
     *                  @OA\Property(
     *                      property="number",
     *                      type="integer",
     *                      example=5
     *                  ),
     *                  @OA\Property(
     *                      property="neighborhood",
     *                      type="string",
     *                      example="Derby"
     *                  ),
     *                  @OA\Property(
     *                      property="city",
     *                      type="string",
     *                      example="Bom Jardim"
     *                  ),
     *              ),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request) {

    $query = Client::query();

    
    if ($request->has('name')) {
        $query->where('name', 'like', '%'.$request->name.'%');
    }

    if ($request->has('email')) {
        $query->where('email', 'like', '%'.$request->email.'%');
    }

    if ($request->has('phone')) {
        $query->where('phone', 'like', '%'.$request->phone.'%');
    }

    if(!$request->take) {
        $clients = $query->where('user_id', auth()->id())->paginate(5);
    }

    $clients = $query->with('address')->where('user_id', auth()->id())->paginate($request->take);

    return response()->json($clients);
}

    /**
     * @OA\Get(
     *     path="/api/clients/stats",
     *     summary="Get statistics of clients by city and state",
     *     tags={"Clients"},
     *     security={ {"sanctum": {} }},
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(
     *                     property="city",
     *                     type="string",
     *                     example="New York"
     *                 ),
     *                 @OA\Property(
     *                     property="state",
     *                     type="string",
     *                     example="NY"
     *                 ),
     *                 @OA\Property(
     *                     property="total",
     *                     type="integer",
     *                     example=10
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="401",
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response="403",
     *         description="Forbidden"
     *     ),
     * )
     */
    public function stats() {
        $stats = Client::join('address', 'clients.id', '=', 'address.client_id')
        ->select('address.city', 'address.state', DB::raw('count(*) as total'))
        ->groupBy('address.city', 'address.state')
        ->get();

        return response()->json($stats);
    }


    
}
