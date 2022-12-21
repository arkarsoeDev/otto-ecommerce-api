<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    /**
     * Calculate the count of active products.
     *
     * @return \Illuminate\Http\Response
     */
    public function activeProducts()
    {
        return Product::count();
    }

    public function userCount()
    {
        return User::count();
    }

    public function unshippedOrders()
    {
        return Order::where('shipped', '=', '0')->count();
        
    }

    public function totalIncome()
    {
        return Order::sum('billing_total');
    }

    public function latestUsers()
    {
        return UserResource::collection(User::latest('id')->take(5)->get());
    }

    public function latestOrders()
    {
        return OrderResource::collection(Order::latest('id')->take(5)->get());
    }

    public function saleValues()
    {
        $from = Carbon::now()->subDays(8);
        $to = Carbon::now();
        return Order::groupBy('date')->select('id', DB::raw('DATE(created_at) AS date'), DB::raw('sum(billing_total) as sum'))
        ->whereDate('created_at', '<=', $to)->get();
    }

    public function orderCount() {
        $from = Carbon::now()->subDays(8);
        $to = Carbon::now();
        return Order::select(DB::raw('DATE(created_at) AS date'), DB::raw('count(*) as total'))->whereDate('created_at', '<=', $to)->groupBy('date')->get();
    }

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
}
