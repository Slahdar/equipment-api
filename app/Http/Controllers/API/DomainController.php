<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DomainRequest;
use App\Models\Domain;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    /**
     * Display a listing of the domains.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $domains = Domain::all();
        
        return response()->json([
            'success' => true,
            'data' => $domains
        ]);
    }

    /**
     * Store a newly created domain in storage.
     *
     * @param  \App\Http\Requests\DomainRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DomainRequest $request)
    {
        $domain = Domain::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Domain created successfully',
            'data' => $domain
        ], 201);
    }

    /**
     * Display the specified domain.
     *
     * @param  \App\Models\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function show(Domain $domain)
    {
        return response()->json([
            'success' => true,
            'data' => $domain
        ]);
    }

    /**
     * Update the specified domain in storage.
     *
     * @param  \App\Http\Requests\DomainRequest  $request
     * @param  \App\Models\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function update(DomainRequest $request, Domain $domain)
    {
        $domain->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Domain updated successfully',
            'data' => $domain
        ]);
    }

    /**
     * Remove the specified domain from storage.
     *
     * @param  \App\Models\Domain  $domain
     * @return \Illuminate\Http\Response
     */
    public function destroy(Domain $domain)
    {
        // Check if domain has families before deleting
        if ($domain->families()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete domain with associated families'
            ], 400);
        }

        $domain->delete();

        return response()->json([
            'success' => true,
            'message' => 'Domain deleted successfully'
        ]);
    }
}