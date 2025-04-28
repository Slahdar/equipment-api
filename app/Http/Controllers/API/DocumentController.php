<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Display a listing of all documents.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Document::with('documentType');
        
        // Filter by archived status if provided
        if ($request->has('archived')) {
            $query->where('archived', $request->archived);
        }
        
        $documents = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    /**
     * Display a listing of documents by product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function indexByProduct(Product $product)
    {
        $documents = $product->documents()->with('documentType')->get();
        
        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    /**
     * Store a newly created document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'document_type_id' => 'required|exists:document_types,id',
            'file' => 'required|file|mimes:pdf',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'version' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Store the file
        $filePath = $request->file('file')->store('documents', 'public');

        $document = Document::create([
            'name' => $request->name,
            'document_type_id' => $request->document_type_id,
            'file_path' => $filePath,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'version' => $request->version,
            'reference' => $request->reference,
            'archived' => false,
            'serial_number' => 'DOC-' . Str::random(8)
        ]);

        // Associate with products if provided
        if ($request->has('product_ids') && is_array($request->product_ids)) {
            $document->products()->attach($request->product_ids);
        }

        $document->load('documentType', 'products');

        return response()->json([
            'success' => true,
            'message' => 'Document created successfully',
            'data' => $document
        ], 201);
    }

    /**
     * Display the specified document.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function show(Document $document)
    {
        $document->load('documentType', 'products');
        
        return response()->json([
            'success' => true,
            'data' => $document
        ]);
    }

    /**
     * Update the specified document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Document $document)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'document_type_id' => 'required|exists:document_types,id',
            'file' => 'nullable|file|mimes:pdf',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'version' => 'nullable|string|max:50',
            'reference' => 'nullable|string|max:100',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = [
            'name' => $request->name,
            'document_type_id' => $request->document_type_id,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'version' => $request->version,
            'reference' => $request->reference
        ];

        // Handle file update if provided
        if ($request->hasFile('file')) {
            // Delete old file if it exists
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            // Store new file
            $data['file_path'] = $request->file('file')->store('documents', 'public');
        }

        $document->update($data);

        // Sync products if provided
        if ($request->has('product_ids')) {
            $document->products()->sync($request->product_ids);
        }

        $document->load('documentType', 'products');

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully',
            'data' => $document
        ]);
    }

    /**
     * Archive or unarchive the specified document.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function archive(Request $request, Document $document)
    {
        $validator = Validator::make($request->all(), [
            'archived' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $document->update([
            'archived' => $request->archived
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->archived ? 'Document archived successfully' : 'Document unarchived successfully',
            'data' => $document
        ]);
    }

    /**
     * Download the specified document file.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        return Storage::disk('public')->download($document->file_path, $document->name . '.pdf');
    }

    /**
     * Remove the specified document from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\Response
     */
    public function destroy(Document $document)
    {
        // Delete the file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }
        
        // Detach all products
        $document->products()->detach();
        
        // Delete the document record
        $document->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    }
}