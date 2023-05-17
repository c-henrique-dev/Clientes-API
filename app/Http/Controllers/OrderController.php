<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Enums\OrderStatus;
use App\Models\Dtos\OrderInformationDTO;
use App\Models\Dtos\OrderItemInformationDTO;
use Carbon\Carbon;
use OpenApi\Annotations as OA;

class OrderController extends Controller{
    
    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create a new order",
     *     description="Create a new order and associated items",
     *     security={ {"sanctum": {} }},
     *     operationId="createOrder",
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Order object that needs to be created",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="client",
     *                 type="string",
     *                 description="The ID of the client that is placing the order",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="total",
     *                 type="number",
     *                 format="float",
     *                 description="The total value of the order",
     *                 example="50.99"
     *             ),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 description="The list of items in the order",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="product",
     *                         type="string",
     *                         description="The ID of the product being ordered",
     *                         example="3"
     *                     ),
     *                     @OA\Property(
     *                         property="quantity",
     *                         type="integer",
     *                         description="The quantity of the product being ordered",
     *                         example="2"
     *                     )
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The newly created order",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="The ID of the newly created order",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="total",
     *                 type="number",
     *                 format="float",
     *                 description="The total value of the order",
     *                 example="50.99"
     *             ),
     *             @OA\Property(
     *                 property="client_id",
     *                 type="integer",
     *                 description="The ID of the client that placed the order",
     *                 example="1"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="The status of the order",
     *                 example="REALIZADO"
     *             ),
     *             @OA\Property(
     *                 property="created_at",
     *                 type="string",
     *                 description="The timestamp when the order was created",
     *                 example="2023-05-04T10:00:00.000000Z"
     *             ),
     *             @OA\Property(
     *                 property="updated_at",
     *                 type="string",
     *                 description="The timestamp when the order was last updated",
     *                 example="2023-05-04T10:00:00.000000Z"
     *             ),
     *             @OA\Property(
     *                 property="orders_items",
     *                 type="array",
     *                 description="The list of items in the order",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         description="The ID of the order item",
     *                         example="1"
     *                     ),
     *                     @OA\Property(
     *                         property="quantity",
     *                         type="integer",
     *                         description="The quantity of the product being ordered",
     *                         example="2"
     *                     ),
     *                     @OA\Property(
     *                         property="product_id",
     *                         type="integer",
     *                         description="The ID of the product being ordered",
     *                         example="3"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         description="The timestamp when the order item was created",
     *                         example="2023-05-04T10:00:00.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         description="The timestamp when the order item was last updated",
     *                         example="2023-05-04T10:00:00.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="product",
     *                         type="object",
     *                         description="The product being ordered",
     *                         @OA\Property(
     *                             property="id",
     *                             type="integer",
     *                             description="The ID of the product",
     *                             example="3"
     *                         ),
     *                         @OA\Property(
     *                             property="name",
     *                             type="string",
     *                             description="The name of the product",
     *                             example="Product Name"
     *                         ),
     *                         @OA\Property(
     *                             property="price",
     *                             type="number",
     *                             format="float",
     *                             description="The price of the product",
     *                             example="25.49"
     *                         ),
     *                         @OA\Property(
     *                             property="created_at",
     *                             type="string",
     *                             description="The timestamp when the product was created",
     *                             example="2023-05-04T10:00:00.000000Z"
     *                         ),
     *                         @OA\Property(
     *                             property="updated_at",
     *                             type="string",
     *                             description="The timestamp when the product was last updated",
     *                             example="2023-05-04T10:00:00.000000Z"
     *                         ),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input data",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="The error message",
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="The list of validation errors",
     *                 example={
     *                     "client": {
     *                         "The selected client is invalid."
     *                     },
     *                     "items.0.product": {
     *                         "The selected items.0.product is invalid."
     *                     }
     *                 }
     *             )
     *         )
     *     ),
     * )
     */
    public function store(Request $request) {

        $validator = $request->validate([
            'client' => 'required|string|exists:clients,id',
            'total' => 'required|numeric|min:0',
            'items.*.product' => 'required|string|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $client = Client::findOrFail($validator['client']);

        if (!$client) {
            return ['message' => 'Client not found'];
        }
    
        $order = new Order();
        $order->total = $validator['total'];
        $order->client_id = $client->id;
        $order->status = OrderStatus::REALIZADO->value;
        $order->save();

        foreach ($validator['items'] as $item) {
            $product = Product::findOrFail($item['product']);
            $orderItem = new OrderItem();
            $orderItem->quantity = $item['quantity'];
            $orderItem->product_id = $product->id;
            $order->ordersItems()->save($orderItem);
        }

        $order->load('ordersItems');
    
        return $order;
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Get order information",
     *     description="Retrieve order information by ID",
     *     operationId="getOrderById",
     *     security={ {"sanctum": {} }},
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Order ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="code",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="orderDate",
     *                 type="string",
     *                 example="17/05/2023"
     *             ),
     *             @OA\Property(
     *                 property="clientName",
     *                 type="string",
     *                 example="example"
     *             ),
     *             @OA\Property(
     *                 property="total",
     *                 type="string",
     *                 example="4000.00"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="REALIZADO"
     *             ),
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(
     *                         property="productDescription",
     *                         type="string",
     *                         example="TV"
     *                     ),
     *                     @OA\Property(
     *                         property="price",
     *                         type="string",
     *                         example="4000.00"
     *                     ),
     *                     @OA\Property(
     *                         property="quantity",
     *                         type="integer",
     *                         example=2
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Order not found."
     *             )
     *         )
     *     )
     * )
     */
    public function getById(Request $request, $id)  {
        $order = Order::with(['ordersItems', 'client'])->find($id);

        if (!$order) {
            return ['message' => 'order not found.'];
        }

        $newOrder = $this->orderInformation($order);

        return $newOrder;
    }


    private function orderInformation(Order $order) {   
        return response()->json([
            'code' => $order->id,
            'orderDate' => Carbon::parse($order->orderData)->format('d/m/Y'),
            'clientName' => $order->client->name,
            'total' => $order->total,
            'status' => $order->status,
            'items' => $this->orderItemsInformation($order->ordersItems)
        ]);
    }

    private function orderItemsInformation($items) {
    
        $result = [];

        foreach ($items as $item) {
            $result[] = (object) [
                'productDescription' => $item->product->description,
                'price' => $item->product->price,
                'quantity' => $item->quantity
            ];
        }
    
        return $result;
    }

    /**
     * @OA\Put(
     *     path="/api/orders/{id}/cancel",
     *     tags={"Orders"},
     *     summary="Cancel an order",
     *     description="Cancel an order",
     *     security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the order to be canceled",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Successfully updated."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Order not found"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated"
     *             )
     *         )
     *     )
     * )
     */
    public function cancelOrder($id) {
        $status = OrderStatus::CANCELADO->value;

        $order = Order::findOrFail($id);
        $order->status = $status;
        $order->save();
        return ['message' => 'Successfully updated.'];
    }
}