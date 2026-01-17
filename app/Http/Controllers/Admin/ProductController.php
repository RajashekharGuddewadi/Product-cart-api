<?php

namespace App\Http\Controllers\Admin;

use App\Enums\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET /admin/products (Blade View)
     */
    public function indexBlade()
    {
        return view('admin.products.index');
    }

    /**
     * GET /api/admin/products (AJAX endpoint)
     */
    public function index(Request $request)
    {
        $products = Product::when($request->search, function ($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('sku', 'like', "%{$request->search}%");
        })->latest()->paginate(10);

        return response()->json([
            'status'  => true,
            'message' => 'Products fetched successfully',
            'data'    => ProductResource::collection($products)->response()->getData(true),
        ], HttpStatus::Ok->value);
    }

    /**
     * POST /api/admin/products
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Product created successfully',
            'data'    => new ProductResource($product),
        ], HttpStatus::Created->value);
    }

    /**
     * GET /api/admin/products/{product}
     */
    public function show(Product $product)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Product details fetched',
            'data'    => new ProductResource($product),
        ], HttpStatus::Ok->value);
    }

    /**
     * PUT /api/admin/products/{product}
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Product updated successfully',
            'data'    => new ProductResource($product),
        ], HttpStatus::Ok->value);
    }

    /**
     * DELETE /api/admin/products/{product}
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], HttpStatus::NotFound->value);
        }

        $product->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Product deleted successfully',
        ], HttpStatus::Ok->value);
    }

    /**
     * POST /api/admin/products/{product}/toggle
     */
    public function toggle($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], HttpStatus::NotFound->value);
        }

        $product->is_active = !$product->is_active;
        $product->save();

        return response()->json([
            'status'  => true,
            'message' => 'Product status updated successfully',
            'data'    => $product,
        ], HttpStatus::Ok->value);
    }
}
