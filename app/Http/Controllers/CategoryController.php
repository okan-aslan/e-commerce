<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('products')->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $this->authorizeAdmin();

        $category = Category::create($request->validated());

        $message = 'Category created successfully';

        return $this->success(new CategoryResource($category), $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $this->authorizeAdmin();

        $category->update($request->all());

        $message = 'Category updated successfully';

        return $this->success(new CategoryResource($category), $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->authorizeAdmin();

        $category->delete();

        return response(null, 204);
    }

    /**
     * Perform authorization check for admin user.
     */
    private function authorizeAdmin()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, "You're not authorized to make this request");
        }
    }
}
