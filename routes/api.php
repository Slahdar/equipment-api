<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DomainController;
use App\Http\Controllers\API\FamilyController;
use App\Http\Controllers\API\EquipmentTypeController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\DocumentTypeController;
use App\Http\Controllers\API\DocumentController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\InventoryController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/domains', [DomainController::class, 'index']);
Route::get('/domains/{domain}', [DomainController::class, 'show']);

Route::get('/families', [FamilyController::class, 'index']);
Route::get('/families/{family}', [FamilyController::class, 'show']);
Route::get('/domains/{domain}/families', [FamilyController::class, 'indexByDomain']);

 Route::get('/equipment-types', [EquipmentTypeController::class, 'index']);
 Route::get('/equipment-types/{equipmentType}', [EquipmentTypeController::class, 'show']);
Route::get('/families/{family}/equipment-types', [EquipmentTypeController::class, 'indexByFamily']);

Route::get('/brands', [BrandController::class, 'index']);
Route::get('/brands/{brand}', [BrandController::class, 'show']);
Route::get('/document-types', [DocumentTypeController::class, 'index']);
Route::get('/document-types/{documentType}', [DocumentTypeController::class, 'show']);

Route::get('/documents', [DocumentController::class, 'index']);
Route::get('/documents/{document}', [DocumentController::class, 'show']);
Route::get('/products/{product}/documents', [DocumentController::class, 'indexByProduct']);
Route::get('/documents/{document}/download', [DocumentController::class, 'download']);

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/brands/{brand}/products', [ProductController::class, 'indexByBrand']);
Route::get('/equipment-types/{equipmentType}/products', [ProductController::class, 'indexByEquipmentType']);
Route::get('/products/{product}/associated-products', [ProductController::class, 'indexAssociatedProducts']);
        
Route::get('/inventories', [InventoryController::class, 'index']);
Route::get('/inventories/{inventory}', [InventoryController::class, 'show']);
Route::get('/products/{product}/inventories', [InventoryController::class, 'indexByProduct']);
        
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User information
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User management (admin only)
    Route::group(['middleware' => ['role:admin']], function () {
        Route::apiResource('users', UserController::class);
        Route::post('/users/{user}/assign-role', [UserController::class, 'assignRole']);
        Route::post('/users/{user}/remove-role', [UserController::class, 'removeRole']);
        Route::post('/users/{user}/assign-permission', [UserController::class, 'assignPermission']);
        Route::post('/users/{user}/remove-permission', [UserController::class, 'removePermission']);
    });
    
    // Domains
    Route::group(['middleware' => ['permission:view domains']], function () {
        
        Route::middleware(['permission:create domains'])->post('/domains', [DomainController::class, 'store']);
        Route::middleware(['permission:edit domains'])->put('/domains/{domain}', [DomainController::class, 'update']);
        Route::middleware(['permission:delete domains'])->delete('/domains/{domain}', [DomainController::class, 'destroy']);
    });
    
    // Families
    Route::group(['middleware' => ['permission:view families']], function () {
        Route::middleware(['permission:create families'])->post('/families', [FamilyController::class, 'store']);
        Route::middleware(['permission:edit families'])->put('/families/{family}', [FamilyController::class, 'update']);
        Route::middleware(['permission:delete families'])->delete('/families/{family}', [FamilyController::class, 'destroy']);
    });
    
    // Equipment Types
    Route::group(['middleware' => ['permission:view equipment_types']], function () {
        
        
       
        Route::middleware(['permission:create equipment_types'])->post('/equipment-types', [EquipmentTypeController::class, 'store']);
        Route::middleware(['permission:edit equipment_types'])->put('/equipment-types/{equipmentType}', [EquipmentTypeController::class, 'update']);
        Route::middleware(['permission:delete equipment_types'])->delete('/equipment-types/{equipmentType}', [EquipmentTypeController::class, 'destroy']);
    });
    
    // Brands
    Route::group(['middleware' => ['permission:view brands']], function () {
        
        
        
        Route::middleware(['permission:create brands'])->post('/brands', [BrandController::class, 'store']);
        Route::middleware(['permission:edit brands'])->put('/brands/{brand}', [BrandController::class, 'update']);
        Route::middleware(['permission:delete brands'])->delete('/brands/{brand}', [BrandController::class, 'destroy']);
    });
    
    // Document Types
    Route::group(['middleware' => ['permission:view document_types']], function () {
        
       
        
        Route::middleware(['permission:create document_types'])->post('/document-types', [DocumentTypeController::class, 'store']);
        Route::middleware(['permission:edit document_types'])->put('/document-types/{documentType}', [DocumentTypeController::class, 'update']);
        Route::middleware(['permission:delete document_types'])->delete('/document-types/{documentType}', [DocumentTypeController::class, 'destroy']);
    });
    
    // Documents
    Route::group(['middleware' => ['permission:view documents']], function () {
        Route::middleware(['permission:create documents'])->post('/documents', [DocumentController::class, 'store']);
        Route::middleware(['permission:edit documents'])->put('/documents/{document}', [DocumentController::class, 'update']);
        Route::middleware(['permission:archive documents'])->patch('/documents/{document}/archive', [DocumentController::class, 'archive']);
        Route::middleware(['permission:delete documents'])->delete('/documents/{document}', [DocumentController::class, 'destroy']);
    });
    
    // Products
    Route::group(['middleware' => ['permission:view products']], function () {

        Route::middleware(['permission:create products'])->post('/products', [ProductController::class, 'store']);
        Route::middleware(['permission:edit products'])->put('/products/{product}', [ProductController::class, 'update']);
        Route::middleware(['permission:associate products'])->post('/products/{product}/associate', [ProductController::class, 'associateProduct']);
        Route::middleware(['permission:associate products'])->delete('/products/{product}/dissociate/{associatedProduct}', [ProductController::class, 'dissociateProduct']);
        Route::middleware(['permission:delete products'])->delete('/products/{product}', [ProductController::class, 'destroy']);
        
        // Attach/Detach documents to products
        Route::middleware(['permission:edit products'])->post('/products/{product}/documents/{document}', [ProductController::class, 'attachDocument']);
        Route::middleware(['permission:edit products'])->delete('/products/{product}/documents/{document}', [ProductController::class, 'detachDocument']);
    });
    
    // Inventories
    Route::group(['middleware' => ['permission:view inventories']], function () {

        Route::middleware(['permission:create inventories'])->post('/inventories', [InventoryController::class, 'store']);
        Route::middleware(['permission:edit inventories'])->put('/inventories/{inventory}', [InventoryController::class, 'update']);
        Route::middleware(['permission:delete inventories'])->delete('/inventories/{inventory}', [InventoryController::class, 'destroy']);
    });
});