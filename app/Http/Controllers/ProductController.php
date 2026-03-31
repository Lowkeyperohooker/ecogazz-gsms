<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.cost_price' => 'required|numeric',
            'products.*.selling_price' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->products as $item) {
                Product::where('id', $item['id'])->update([
                    'cost_price' => $item['cost_price'],
                    'selling_price' => $item['selling_price'],
                ]);
            }
        });

        return response()->json(['message' => 'Inventory successfully updated!']);
    }
}