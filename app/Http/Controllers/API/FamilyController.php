<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\FamilyRequest;
use App\Models\Domain;
use App\Models\Family;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    /**
     * Display a listing of all families.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $families = Family::with('domain')->get();
        
        return response()->json([
            'success' => true,
            'data' => $families
        ]);
    }

    /**
     * Display a listing of families by domain.
     *
     * @param  \App\Models\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function indexByDomain(Domain $domain)
    {
        $families = $domain->families;
        
        return response()->json([
            'success' => true,
            'data' => $families
        ]);
    }

    /**
     * Store a newly created family in storage.
     *
     * @param  \App\Http\Requests\FamilyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FamilyRequest $request)
    {
        $family = Family::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Family created successfully',
            'data' => $family
        ], 201);
    }

    /**
     * Display the specified family.
     *
     * @param  \App\Models\Family  $family
     * @return \Illuminate\Http\Response
     */
    public function show(Family $family)
    {
        $family->load('domain');
        
        return response()->json([
            'success' => true,
            'data' => $family
        ]);
    }

    /**
     * Update the specified family in storage.
     *
     * @param  \App\Http\Requests\FamilyRequest  $request
     * @param  \App\Models\Family  $family
     * @return \Illuminate\Http\Response
     */
    public function update(FamilyRequest $request, Family $family)
    {
        $family->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Family updated successfully',
            'data' => $family
        ]);
    }

    /**
     * Remove the specified family from storage.
     *
     * @param  \App\Models\Family  $family
     * @return \Illuminate\Http\Response
     */
    public function destroy(Family $family)
    {
        // Check if family has equipment types before deleting
        if ($family->equipmentTypes()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete family with associated equipment types'
            ], 400);
        }

        $family->delete();

        return response()->json([
            'success' => true,
            'message' => 'Family deleted successfully'
        ]);
    }
}