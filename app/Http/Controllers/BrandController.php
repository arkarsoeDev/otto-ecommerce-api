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
            'name' => 'required|string|min:3',
            'description' => 'nullable|string',
        ]);

        $brand = Brand::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
        ]);

        return response(new BrandResource($brand), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $brand = Brand::find($id);
        return response(new BrandResource($brand));
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
            'name' => 'nullable|string|min:3',
            'description' => 'nullable|string',
        ]);

        $brand = Brand::find($id);

        if (is_null($brand)) {
            return response()->json(["message" => "Can't find the brand"], 404);
        }

        $brand->name = $request->name ?? $brand->name;
        $brand->slug = Str::slug($brand->name);
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
