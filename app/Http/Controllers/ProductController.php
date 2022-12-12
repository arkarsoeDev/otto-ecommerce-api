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
        $products = Product::when(request()->search, function ($query) {
            $query->where(function ($query) {
                $query->where('name', 'like', "%" . request()->search . "%")->orWhere('details', 'like', "%" . request()->search . "%");
            });
        })->when(request()->category, function ($query) {
            $query->whereHas('category', function ($query) {
                $query->where('slug', request()->category);
            });
        })->when(request()->sort, function ($query) {
            if (request()->sort == 'low_high') {
                $query->orderBy('price');
            } else if (request()->sort == 'high_low') {
                $query->orderBy('price', 'desc');
            }
        });
        if(request('paginate') === 'true') {
            $products = $products->paginate($perPage)->withQueryString();
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
