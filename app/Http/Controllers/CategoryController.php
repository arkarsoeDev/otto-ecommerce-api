<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(CategoryResource::collection(Category::all()));
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

        $category = Category::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
        ]);

        return response($category,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  string $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $category = Category::where('slug',$slug)->first();

        if (is_null($category)) {
            return response()->json(["message" => "Can't find product in this category"], 404);
        }

        return response(ProductResource::collection($category->products));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|min:3',
            'description' => 'nullable|string',
        ]);

        $category = Category::find($id);

        if (is_null($category)) {
            return response()->json(["message" => "Can't find the category"], 404);
        }

        $category->title = $request->title;
        $category->slug = Str::slug($category->title);
        $category->description = $request->description;

        $category->update();

        return response(new CategoryResource($category));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);

        if (is_null($category)) {
            return response()->json(["message" => "Can't find the category"], 404);
        }

        $category->delete();

        return response(['message' => 'Category is deleted successfully'], 204);
    }
}
