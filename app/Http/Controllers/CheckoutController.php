<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Cartalyst\Stripe\Exception\CardErrorException;
use Cartalyst\Stripe\Stripe;
use Exception;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CheckoutRequest $request)
    {
        $stripe = new Stripe(env('STRIPE_SECRET'));
        $paymentIntent = $stripe->paymentIntents()->find($request->pi);
        try {
            $paymentMethod = $stripe->paymentMethods()->create([
                'type' => 'card',
                'card' => [
                    'token' => $request->stoken,
                ],
                'billing_details' => [
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'phone' => $request->phone,
                    'address' => [
                        'city' => $request->city,
                        'state' => $request->state,
                        'postal_code' => $request->postal_code,
                        'line1' => $request->address,
                    ]
                ]
            ]);

            $paymentIntent = $stripe->paymentIntents()->confirm($request->pi, [
                'payment_method' => $paymentMethod['id'],
                'receipt_email' => $request->email,
            ]);

            $this->addToOrdersTable($request, $paymentIntent, null);

            //add to order table
            
            //decrease quantity
            //send mail
            //destroy cart
            return response()->json(['success' => true, 'success_message' =>'Thank you! Your payment has been successfully accepted']);
        } catch (CardErrorException $e) {
            //add to order table
            $this->addToOrdersTable(
                $request,
                $paymentIntent,
                $e->getMessage()
            );
            //send mail
            return response()->json(['error' => $e->getMessage()],400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|json'
        ]);

        $items = [];
        foreach (json_decode($request->items) as $item) {
            $product = Product::where('slug', $item->slug)->first();
            if (is_null($product)) {
                return response()->json(["message" => "You can't purchase the products which are not provided"], 400);
            } elseif ($product) {
                $product = new ProductResource($product);
                $product->quantity = $item->quantity;
                $items[] =  $product;
            }
        }

        try {
            $stripe = new Stripe(env('STRIPE_SECRET'));

            $paymentIntent = $stripe->paymentIntents()->create([
                'amount' => $this->getNumbers($items)->get('totalAmount') / 100,
                'currency' => 'usd',
                'description' => 'Order',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'contents' => $request->items,
                    'quantity' => $this->getNumbers($items)->get('totalQuantity'),
                ]
            ]);
        } catch (Exception $e) {
            return $e;
            return response()->json(['error' => $e]);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'paymentIntentId' => $paymentIntent['id'],
                'publicKey' => env("STRIPE_KEY"),
                'clientSecrect' => $paymentIntent['client_secret'],
                'total' => $this->getNumbers($items)->get('totalAmount'),
                'quantity' => $this->getNumbers($items)->get('totalQuantity'),
                'items' => $items
            ]
        ]);
    }

    private function getNumbers($items)
    {
        $collection = collect($items);
        $totalAmount = $collection->reduce(function ($carry, $item) {
            return $carry + $item->price;
        });
        $totalQuantity = $collection->reduce(function ($carry, $item) {
            return $carry + $item->quantity;
        });

        return collect([
            'totalAmount' => $totalAmount,
            'totalQuantity' => $totalQuantity,
        ]);
    }

    private function addToOrdersTable($request, $paymentIntent, $error) {
        $order = Order::create([
            'user_id' => auth()->user() ? auth()->user()->id : null,
            'billing_email' => $request->email,
            'billing_name' => $request->name,
            'billing_address' => $request->address,
            'billing_city' => $request->city,
            'billing_state' => $request->state,
            'billing_postal_code' => $request->postal_code,
            'billing_phone' => $request->phone,
            'billing_total' => $paymentIntent['amount'],
            'error' => $error,
        ]);

        foreach (json_decode($paymentIntent['metadata']['contents']) as $item) {
            $product = Product::where('slug', $item->slug)->first();
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $item->quantity
            ]);
        }
    }
}
