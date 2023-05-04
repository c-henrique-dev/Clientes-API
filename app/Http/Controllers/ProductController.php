<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use OpenApi\Annotations as OA;

class ProductController extends Controller
{
    
    /**
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Create a new product",
     *     description="Save a new product in the database.",
     *     security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="description", type="string", maxLength=255, example="Produto A"),
     *             @OA\Property(property="price", type="number", minimum=0, example=99.99),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="description", type="string", maxLength=255, example="Produto A"),
     *             @OA\Property(property="price", type="number", minimum=0, example=99.99),
     *             @OA\Property(property="created_at", type="string", format="datetime", example="2023-05-04 12:34:56"),
     *             @OA\Property(property="updated_at", type="string", format="datetime", example="2023-05-04 12:34:56"),
     *         )
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        $product = Product::create($request->all());
        return response()->json($product, 201);

    }

    /**
     * @OA\Patch(
     *     path="/api/products/{id}",
     *     operationId="updateProduct",
     *     summary="Update an existing product",
     *     tags={"Products"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the product to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     maxLength=255,
     *                     description="The product description"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="number",
     *                     format="float",
     *                     minimum=0,
     *                     description="The product price"
     *                 ),
     *                 example={
     *                     "description": "New product description",
     *                     "price": 9.99
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             example={"message": "Updated product!"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             example={"message": "Product not found."}
     *         )
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     */
    public function update(Request $request, $id)
    {

        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], Response::HTTP_NOT_FOUND);
        }
            
           if($request->description) {
            $product->description = $request->input('description');
           }

           if($request->price) {
            $product->price = $request->input('price');
           }

            $product->save();

        return response()->json(['message' => 'Updated product!']);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product by ID",
     *     description="Delete a product by its ID",
     *     operationId="deleteProductById",
     *     tags={"Products"},
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         description="Product ID",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully deleted product!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found.")
     *         )
     *     )
     * )
     */
    public function delete($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.']);
        }

        $product->delete($product);

        return response()->json(['message' => 'Successfully deleted product!']);
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="List products",
     *     description="Returns a list of products, filtered by description and price paginated by 5 items per page.",
     *     tags={"Products"},
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
     *         name="description",
     *         in="query",
     *         description="Filter products by description (case insensitive).",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="price",
     *         in="query",
     *         description="Filter clients by price (case insensitive).",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
     *         @OA\JsonContent(
     *          @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="description", type="string", example="Notebook"),
     *              @OA\Property(property="price", type="number", example="3000"),
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

        $query = Product::query();
        
        if ($request->has('description')) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }

        if ($request->has('price')) {
            $query->where('price', 'like', '%'.$request->price.'%');
        }

        if(!$request->take) {
            $clients = $query->paginate(5);
        }
            return response()->json($clients = $query->paginate($request->take));
        }
}
