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
        $products = Product::when(request()->category, function ($query) {
            return $query->whereHas('category', function ($query) {
                return $query->where('slug', request()->category);
            });
        })->when(request()->sort, function ($query) {
            if (request()->sort == 'low_high') {
                return $query->orderBy('price');
            } else if (request()->sort == 'high_low') {
                return $query->orderBy('price', 'desc');
            }
        })->paginate(8)->withQueryString();
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
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $product = Product::where('slug',$slug)->first();

        $peopleAlsoBuy = Product::where('category_id',$product->category_id)->whereNot('id',$product->id)->inRandomOrder()->take(4)->get();

        if(is_null($product)) {
            return response()->json(["message" => "Product is not found"],404);
        }
        return response()->json(["success" => true,"data" => new ProductResource($product),"peopleAlsoBuy" => $peopleAlsoBuy]);
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
        return $request;
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
