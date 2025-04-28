<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    /**
     * Display a listing of all inventories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $inventories = Inventory::with(['product', 'brand'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $inventories
        ]);
    }

    /**
     * Display a listing of inventories by product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function indexByProduct(Product $product)
    {
        $inventories = $product->inventories()->with('brand')->get();
        
        return response()->json([
            'success' => true,
            'data' => $inventories
        ]);
    }

    /**
     * Store a newly created inventory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'location' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'commissioning_date' => 'required|date',
            'additional_fields' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if inventory is required for this equipment type
        $product = Product::findOrFail($request->product_id);
        if ($product->equipmentType->inventory_required === false) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory is not required for this equipment type'
            ], 400);
        }

        // Validate additional fields if defined for this equipment type
        if ($product->equipmentType->additional_fields) {
            $additionalFields = json_decode($request->additional_fields ?: '{}', true);
            $requiredFields = json_decode($product->equipmentType->additional_fields, true);
            
            foreach ($requiredFields as $field => $config) {
                if (!empty($config['required']) && $config['required'] === true) {
                    if (!isset($additionalFields[$field]) || empty($additionalFields[$field])) {
                        return response()->json([
                            'success' => false,
                            'message' => "The field '{$field}' is required for this equipment type."
                        ], 422);
                    }
                }
            }
        }

        $inventory = Inventory::create([
            'product_id' => $request->product_id,
            'location' => $request->location,
            'brand_id' => $request->brand_id,
            'commissioning_date' => $request->commissioning_date,
            'additional_fields' => $request->additional_fields,
            'serial_number' => 'INV-' . Str::random(8)
        ]);

        $inventory->load('product', 'brand');

        return response()->json([
            'success' => true,
            'message' => 'Inventory created successfully',
            'data' => $inventory
        ], 201);
    }

    /**
     * Display the specified inventory.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        $inventory->load('product.equipmentType', 'brand');
        
        return response()->json([
            'success' => true,
            'data' => $inventory
        ]);
    }

    /**
     * Update the specified inventory in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'location' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'commissioning_date' => 'required|date',
            'additional_fields' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if inventory is required for this equipment type
        $product = Product::findOrFail($request->product_id);
        if ($product->equipmentType->inventory_required === false) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory is not required for this equipment type'
            ], 400);
        }

        // Validate additional fields if defined for this equipment type
        if ($product->equipmentType->additional_fields) {
            $additionalFields = json_decode($request->additional_fields ?: '{}', true);
            $requiredFields = json_decode($product->equipmentType->additional_fields, true);
            
            foreach ($requiredFields as $field => $config) {
                if (!empty($config['required']) && $config['required'] === true) {
                    if (!isset($additionalFields[$field]) || empty($additionalFields[$field])) {
                        return response()->json([
                            'success' => false,
                            'message' => "The field '{$field}' is required for this equipment type."
                        ], 422);
                    }
                }
            }
        }

        $inventory->update([
            'product_id' => $request->product_id,
            'location' => $request->location,
            'brand_id' => $request->brand_id,
            'commissioning_date' => $request->commissioning_date,
            'additional_fields' => $request->additional_fields
        ]);

        $inventory->load('product', 'brand');

        return response()->json([
            'success' => true,
            'message' => 'Inventory updated successfully',
            'data' => $inventory
        ]);
    }

    /**
     * Remove the specified inventory from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inventory deleted successfully'
        ]);
    }
}