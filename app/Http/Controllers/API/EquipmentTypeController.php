<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\EquipmentType;
use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EquipmentTypeController extends Controller
{
    /**
     * Display a listing of all equipment types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $equipmentTypes = EquipmentType::with('family')->get();
        
        return response()->json([
            'success' => true,
            'data' => $equipmentTypes
        ]);
    }

    /**
     * Display a listing of equipment types by family.
     *
     * @param  \App\Models\Family  $family
     * @return \Illuminate\Http\Response
     */
    public function indexByFamily(Family $family)
    {
        $equipmentTypes = $family->equipmentTypes;
        
        return response()->json([
            'success' => true,
            'data' => $equipmentTypes
        ]);
    }

    /**
     * Store a newly created equipment type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'family_id' => 'required|exists:families,id',
            'inventory_required' => 'boolean',
            'additional_fields' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $equipmentType = EquipmentType::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'family_id' => $request->family_id,
            'inventory_required' => $request->inventory_required ?? false,
            'additional_fields' => $request->additional_fields,
            'serial_number' => 'EQT-' . Str::random(8)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Equipment type created successfully',
            'data' => $equipmentType
        ], 201);
    }

    /**
     * Display the specified equipment type.
     *
     * @param  \App\Models\EquipmentType  $equipmentType
     * @return \Illuminate\Http\Response
     */
    public function show(EquipmentType $equipmentType)
    {
        $equipmentType->load('family');
        
        return response()->json([
            'success' => true,
            'data' => $equipmentType
        ]);
    }

    /**
     * Update the specified equipment type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EquipmentType  $equipmentType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EquipmentType $equipmentType)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'family_id' => 'required|exists:families,id',
            'inventory_required' => 'boolean',
            'additional_fields' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $equipmentType->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'family_id' => $request->family_id,
            'inventory_required' => $request->inventory_required ?? $equipmentType->inventory_required,
            'additional_fields' => $request->additional_fields
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Equipment type updated successfully',
            'data' => $equipmentType
        ]);
    }

    /**
     * Remove the specified equipment type from storage.
     *
     * @param  \App\Models\EquipmentType  $equipmentType
     * @return \Illuminate\Http\Response
     */
    public function destroy(EquipmentType $equipmentType)
    {
        // Check if equipment type has products before deleting
        if ($equipmentType->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete equipment type with associated products'
            ], 400);
        }

        $equipmentType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Equipment type deleted successfully'
        ]);
    }
}