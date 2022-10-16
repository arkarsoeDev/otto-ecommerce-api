<?php

namespace App\Http\Controllers;

use App\Http\Resources\BrandResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(BrandResource::collection(Brand::all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:3',
            'description' => 'nullable|string',
        ]);

        $brand = Brand::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
        ]);

        return response($brand, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $brand = Brand::where('slug', $slug)->first();
        return response(ProductResource::collection($brand->products));
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
        $request->validate([
            'title' => 'required|string|min:3',
            'description' => 'nullable|string',
        ]);

        $brand = Brand::find($id);

        if (is_null($brand)) {
            return response()->json(["message" => "Can't find the brand"], 404);
        }

        $brand->title = $request->title;
        $brand->slug = Str::slug($brand->title);
        $brand->description = $request->description;

        $brand->update();

        return response(new BrandResource($brand));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Brand::find($id);

        if (is_null($brand)) {
            return response()->json(["message" => "Can't find the brand"], 404);
        }

        $brand->delete();

        return response(['message' => 'Brand is deleted successfully'], 204);
    }
}
