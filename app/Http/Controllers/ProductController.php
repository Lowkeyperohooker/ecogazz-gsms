<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Fetch all inventory items ordered by Brand.
     */
    public function index()
    {
        $products = Product::orderBy('brand')->orderBy('name')->get();
        return response()->json($products);
    }

    /**
     * Handle rapid inline table edits for cost, sell, and stock quantities.
     */
    public function bulkUpdate(Request $request)
    {
        $products = $request->input('products');

        if (!$products) {
            return response()->json(['message' => 'No data provided'], 400);
        }

        foreach ($products as $productData) {
            $product = Product::find($productData['id']);
            
            if ($product) {
                $product->update([
                    'cost_price' => $productData['cost_price'],
                    'selling_price' => $productData['selling_price'],
                    'stock_quantity' => $productData['stock_quantity']
                ]);
            }
        }

        return response()->json(['message' => 'Inventory updated successfully!']);
    }

    /**
     * Create a brand new product from the Admin Modal.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'cost_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product successfully added.',
            'product' => $product
        ], 201);
    }
}