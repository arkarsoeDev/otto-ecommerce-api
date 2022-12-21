<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Photo;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
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
        $perPage = request()->per_page ?? 8;

        $products = Product::filter(request(['search']))
        ->paginate($perPage)->withQueryString();
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
                $request->name
            ),
            "price" => $request->price,
            "stock" => $request->stock,
            "details" => $request->details,
            "featured" => $request->featured,
            "brand_id" => $request->brand_id,
            "category_id" => $request->category_id,
            "description" => "Lorem ipsum dolor, sit amet consectetur adipisicing elit. Ab ut cupiditate ea. Eaque fugiat nihil quae eligendi harum possimus"
        ]);

        $photos = [];

        foreach ($request->file("photos") as $key => $photo) {
            $newName = $photo->store('public/uploads');
            $photos[$key] = new Photo(['name' => $newName]);
        }

        $product->photos()->saveMany($photos);

        return response()->json(new ProductResource($product), 201);
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
        $peopleAlsoBuy = Product::where('category_id', $product->category_id)->whereNot('id', $product->id)->inRandomOrder()->take(4)->get();

        if (is_null($product)) {
            return response()->json(["message" => "Product is not found"], 404);
        }
        return response()->json(["success" => true, "data" => new ProductResource($product), "peopleAlsoBuy" => $peopleAlsoBuy]);
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
        if (is_null($product)) {
            return response()->json(["message" => "Product is not found"], 404);
        }

        $product->name = $request->name ?? $product->name;
        $product->slug = $request->name ? Str::slug($request->name) : $product->slug;
        $product->price = $request->price ?? $product->price;
        $product->featured = $request->featured ?? $product->featured;
        $product->details = $request->details ?? $product->details;
        $product->description = $request->description ?? $product->description;
        $product->brand_id = $request->brand_id ?? $product->brand_id;
        $product->category_id = $request->category_id ?? $product->category_id;
        $product->stock = $request->stock ?? $product->stock;

        $product->update();

        return response()->json(new ProductResource($product));
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
        $photos = $product->photos->pluck('name')->toArray();
        if (is_null($product)) {
            return response()->json(["message" => "Product is not founnd"], 404);
        }

        Storage::delete($photos);
        $product->delete();

        return response()->json(["message" => "Product is deleted"], 204);
    }
}
