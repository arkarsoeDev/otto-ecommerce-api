<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perPage = request()->per_page ?? 8;
        $count = request()->count ?? 8;
        
        $products = Product::filter(request(['search','category','sort']));
        if(request('paginate') === 'true') {
            $products = $products->latest('id')->paginate($perPage)->withQueryString();
        } else {
            $products = $products->latest('id')->take($count)->get();
        }
        return ProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)->first();

        if (is_null($product)) {
            return response()->json(["message" => "Product is not found"], 404);
        }

        $peopleAlsoBuy = Product::where('category_id', $product->category_id)->whereNot('id', $product->id)->inRandomOrder()->take(4)->get();

        return response()->json(["success" => true, "data" => new ProductResource($product), "peopleAlsoBuy" => ProductResource::collection($peopleAlsoBuy)]);
    }
}
