<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Document;
use App\Models\EquipmentType;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of all products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::with(['brand', 'equipmentType'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Display a listing of products by brand.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function indexByBrand(Brand $brand)
    {
        $products = $brand->products()->with('equipmentType')->get();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Display a listing of products by equipment type.
     *
     * @param  \App\Models\EquipmentType  $equipmentType
     * @return \Illuminate\Http\Response
     */
    public function indexByEquipmentType(EquipmentType $equipmentType)
    {
        $products = $equipmentType->products()->with('brand')->get();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Display a listing of associated products.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function indexAssociatedProducts(Product $product)
    {
        $associatedProducts = $product->associatedProducts()->with(['brand', 'equipmentType'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $associatedProducts
        ]);
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'equipment_type_id' => 'required|exists:equipment_types,id',
            'document_ids' => 'nullable|array',
            'document_ids.*' => 'exists:documents,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = Product::create([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'equipment_type_id' => $request->equipment_type_id,
            'serial_number' => 'PRD-' . Str::random(8)
        ]);

        // Associate with documents if provided
        if ($request->has('document_ids') && is_array($request->document_ids)) {
            $product->documents()->attach($request->document_ids);
        }

        $product->load('brand', 'equipmentType', 'documents');

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product->load('brand', 'equipmentType', 'documents', 'associatedProducts');
        
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'equipment_type_id' => 'required|exists:equipment_types,id',
            'document_ids' => 'nullable|array',
            'document_ids.*' => 'exists:documents,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'equipment_type_id' => $request->equipment_type_id
        ]);

        // Sync documents if provided
        if ($request->has('document_ids')) {
            $product->documents()->sync($request->document_ids);
        }

        $product->load('brand', 'equipmentType', 'documents');

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    /**
     * Associate product with another product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function associateProduct(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'associated_product_id' => 'required|exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if trying to associate with itself
        if ($product->id == $request->associated_product_id) {
            return response()->json([
                'success' => false,
                'message' => 'A product cannot be associated with itself'
            ], 400);
        }

        // Check if association already exists
        if ($product->associatedProducts()->where('associated_product_id', $request->associated_product_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Products are already associated'
            ], 400);
        }

        $product->associatedProducts()->attach($request->associated_product_id);

        return response()->json([
            'success' => true,
            'message' => 'Products associated successfully'
        ]);
    }

    /**
     * Dissociate product from another product.
     *
     * @param  \App\Models\Product  $product
     * @param  \App\Models\Product  $associatedProduct
     * @return \Illuminate\Http\Response
     */
    public function dissociateProduct(Product $product, Product $associatedProduct)
    {
        // Check if association exists
        if (!$product->associatedProducts()->where('associated_product_id', $associatedProduct->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Products are not associated'
            ], 400);
        }

        $product->associatedProducts()->detach($associatedProduct->id);

        return response()->json([
            'success' => true,
            'message' => 'Products dissociated successfully'
        ]);
    }

    /**
     * Attach document to product.
     *
     * @param  \App\Models\Product  $product
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function attachDocument(Product $product, Document $document)
    {
        // Check if document is already attached
        if ($product->documents()->where('document_id', $document->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Document is already attached to this product'
            ], 400);
        }

        $product->documents()->attach($document->id);

        return response()->json([
            'success' => true,
            'message' => 'Document attached successfully'
        ]);
    }

    /**
     * Detach document from product.
     *
     * @param  \App\Models\Product  $product
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function detachDocument(Product $product, Document $document)
    {
        // Check if document is attached
        if (!$product->documents()->where('document_id', $document->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Document is not attached to this product'
            ], 400);
        }

        $product->documents()->detach($document->id);

        return response()->json([
            'success' => true,
            'message' => 'Document detached successfully'
        ]);
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        // Check if product has inventories before deleting
        if ($product->inventories()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete product with associated inventories'
            ], 400);
        }

        // Detach all documents
        $product->documents()->detach();
        
        // Remove all product associations
        $product->associatedProducts()->detach();
        $product->associatedByProducts()->detach();
        
        // Delete the product
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }
}