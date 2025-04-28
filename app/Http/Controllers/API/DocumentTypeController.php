<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of all document types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $documentTypes = DocumentType::all();
        
        return response()->json([
            'success' => true,
            'data' => $documentTypes
        ]);
    }

    /**
     * Store a newly created document type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:document_types'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $documentType = DocumentType::create([
            'name' => $request->name,
            'serial_number' => 'DCT-' . Str::random(8)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document type created successfully',
            'data' => $documentType
        ], 201);
    }

    /**
     * Display the specified document type.
     *
     * @param  \App\Models\DocumentType  $documentType
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentType $documentType)
    {
        return response()->json([
            'success' => true,
            'data' => $documentType
        ]);
    }

    /**
     * Update the specified document type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DocumentType  $documentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DocumentType $documentType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:document_types,name,' . $documentType->id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $documentType->update([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document type updated successfully',
            'data' => $documentType
        ]);
    }

    /**
     * Remove the specified document type from storage.
     *
     * @param  \App\Models\DocumentType  $documentType
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentType $documentType)
    {
        // Check if document type has documents before deleting
        if ($documentType->documents()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete document type with associated documents'
            ], 400);
        }

        $documentType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Document type deleted successfully'
        ]);
    }
}