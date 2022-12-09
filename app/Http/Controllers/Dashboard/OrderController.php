<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request()->per_page ?? 8;

        $orders = Order::paginate($perPage)->withQueryString();
        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $order = new Order();

        // $order->user_id = $request->user_id ?? null;
        // $order->billing_email = $request->billing_email;
        // $order->billing_name = $request->billing_name;
        // $order->billing_address = $request->billing_address;
        // $order->billing_city = $request->billing_city;
        // $order->billing_state = $request->billing_state;
        // $order->billing_postal_code = $request->billing_postal_code;
        // $order->billing_phone = $request->billing_phone;
        // $order->billing_total = $request->billing_total;
        // $order->payment_gateway = $request->payment_gateway;
        // $order->shipped = $request->shipped;
        // $order->error = $request->error ?? null;

        // return $order;
        // $order->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::find($id);
        if(is_null($order)) {
            return response()->json(["message" => "Order is not found"], 404);
        }

        return response()->json(["success" => true, "data" => new OrderResource($order)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, $id)
    {
        $order = Order::findOrFail($id);
        if (is_null($order)) {
            return response()->json(["message" => "Order is not found"], 404);
        }

        $order->shipped = $request->shipped;
        $order->update();
        // return $request;
        return response()->json(["success" => true, "data" => new OrderResource($order)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
