<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreInventoryTransactionRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function updateStock(StoreInventoryTransactionRequest $request, Product $product)
    {
        $validated = $request->validated();

        // Run both operations inside a single database transaction
        DB::transaction(function () use ($validated, $product, $request) {
            
            // 1. Prevent subtracting more stock than available
            if ($validated['type'] === 'subtract' && $product->stock_level < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => 'Insufficient stock available.'
                ]);
            }

            // 2. Create the audit trail ledger entry
            $product->inventoryTransactions()->create([
                'user_id' => $request->user()->id,
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // 3. Update the actual product stock level
            if ($validated['type'] === 'add') {
                $product->increment('stock_level', $validated['quantity']);
            } else {
                $product->decrement('stock_level', $validated['quantity']);
            }
        });

        // Return the fresh product data
        return response()->json([
            'message' => 'Stock updated successfully',
            'current_stock' => $product->fresh()->stock_level
        ]);
    }
}
