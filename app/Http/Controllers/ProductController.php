<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Photo;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::latest('id')->paginate(10);
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {

        $product = Product::create([
            "name" => $request->name,
            "slug" => Str::slug(
                $request->name),
            "price" => $request->price,
            "stock" => $request->stock,
            "description" => "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ab ut cupiditate ea. Eaque fugiat nihil quae eligendi harum possimus"
        ]);

        $photos = [];

        foreach($request->file("photos") as $key=>$photo) {
            $newName = $photo->store('public');
            $photos[$key] = new Photo(['name' => $newName]);
        }

        $product->photos()->saveMany($photos); 

        return response()->json($product,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if(is_null($product)) {
            return response()->json(["message" => "Product is not found"],404);
        }
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::find($id);
        if(is_null($product)) {
            return response()->json(["message" => "Product is not found"],404);
        }

        $product->name = $request->name ?? $product->name;
        $product->slug = $request->name ? Str::slug($request->name) : $product->slug;
        $product->price = $request->price ?? $product->price;
        $product->stock = $request->stock ?? $product->stock;
        $product->update();
        
        return response()->json($product);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if(is_null($product)) {
            return response()->json(["message"=> "Product is not founnd"],404);
        }

        $product->delete();
        
        return response()->json(["message" => "Product is deleted"],204);
    }
}
