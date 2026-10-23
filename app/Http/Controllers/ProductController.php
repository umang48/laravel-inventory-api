<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;


use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with('category')
            // Apply search scope if '?search=' exists
            ->when($request->search, function ($query, $search) {
                $query->search($search);
            })
            // Apply category scope (it handles null checks internally)
            ->filterByCategory($request->category_id)
            // Add optional sorting (e.g., ?sort=price_desc)
            ->when($request->sort === 'price_desc', function ($query) {
                $query->orderBy('price', 'desc');
            }, function ($query) {
                $query->latest(); // Default sort
            })
            ->paginate(10)
            // Critical: Ensure pagination links in the JSON response keep the current filters
            ->withQueryString(); 

        return ProductResource::collection($products);
    }
    
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());
        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        $product->load('category');
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        // Reusing StoreProductRequest for simplicity, though you might 
        // want an UpdateProductRequest to ignore the current product's unique SKU
        $product->update($request->validated());
        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 204);
    }
}
