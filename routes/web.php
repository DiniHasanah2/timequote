<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ECSFlavourController;
use App\Http\Controllers\NetworkMappingController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SolutionController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\VersionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SolutionTypeController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ServiceDescriptionController;
use App\Http\Controllers\ECSConfigurationController;
use App\Http\Controllers\MPDRaaSController;
use App\Http\Controllers\SecurityServiceController;
use App\Http\Controllers\NonStandardItemController;
use App\Http\Controllers\VMMappingsController;
use App\Http\Controllers\PFlavourMapController;
use App\Http\Controllers\PriceCatalogController;
use App\Http\Controllers\QuotationCsvController;
use App\Http\Controllers\InternalSummaryController;
use App\Http\Controllers\NonStandardOfferingController;
use App\Http\Controllers\CustomizationController;
use Illuminate\Support\Str;
use App\Models\Project; 
use App\Models\Version;
use App\Http\Controllers\CommercialLinkController;


use Illuminate\Support\Facades\Storage; 

Route::get('/obs/health', function () {
    $key = 'healthchecks/' . \Illuminate\Support\Str::uuid() . '.txt';
    Storage::disk('obs')->put($key, 'ok-' . now());
    $exists = Storage::disk('obs')->exists($key);
    return ['put' => true, 'exists' => $exists, 'key' => $key];
});

Route::middleware(['web','auth']) // ikut keperluan
  ->prefix('portal-links')->group(function () {
    Route::post('/pinned', [CommercialLinkController::class, 'createPinned']);  // {quoteId,versionId,ttl?}
    Route::post('/latest', [CommercialLinkController::class, 'createLatest']);  // {quoteId,ttl?}
    Route::delete('/{id}', [CommercialLinkController::class, 'revoke']);        // revoke
  });
  
Route::get('/dev/make-token/{quoteId}/{versionId}', function ($quoteId, $versionId) {
    $secret = env('PORTAL_LINK_JWT_SECRET', 'change-me');
    $aud    = env('PORTAL_LINK_JWT_AUD', 'CommercialPortal');
    $exp    = time() + 3600; // 1 jam

    $header  = base64_encode(json_encode(['alg'=>'HS256','typ'=>'JWT']));
    $payload = base64_encode(json_encode(['quoteId'=>(int)$quoteId,'versionId'=>(int)$versionId,'aud'=>$aud,'exp'=>$exp]));

    // base64url (tanpa = dan URL-safe)
    $b64url = fn($s) => rtrim(strtr($s, '+/', '-_'), '=');
    $header  = $b64url($header);
    $payload = $b64url($payload);

    $sig = hash_hmac('sha256', $header.'.'.$payload, $secret, true);
    $sig = $b64url(base64_encode($sig));

    return $header.'.'.$payload.'.'.$sig;
});

Route::get('/', fn () => redirect()->route('login'));

// Login routes
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Signed download for share link
Route::get('exports/d/{token}/{file}', [\App\Http\Controllers\QuotationController::class, 'shareDownload'])
    ->name('exports.download')
    ->middleware('signed'); // penting: signed URL

Route::get('/dashboard', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (in_array($role, ['presale', 'product'])) {
            return redirect()->route('presale.dashboard');
        }
    }

    return redirect()->route('login');
})->name('dashboard');

// Routes untuk user yang dah login
Route::middleware(['auth'])->group(function () {

    // Dashboard ikut role
    Route::get('/presale/dashboard', [DashboardController::class, 'presaleDashboard'])->name('presale.dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
 
    Route::get('/projects/{project}/versions/create', [VersionController::class, 'create'])
        ->name('projects.versions.create');
    Route::get('/projects/{project}/versions', [VersionController::class, 'index'])
        ->name('projects.versions.index');
    Route::get('projects/versions/create/select', [VersionController::class, 'create'])->name('projects.versions.select');
    Route::post('/projects/{project}/versions', [VersionController::class, 'store'])
        ->name('projects.versions.store');

    Route::get('/projects/{project}/assign-presales', [ProjectController::class, 'assignPresalesForm'])->name('projects.assignPresalesForm');
    Route::post('/projects/{project}/assign-presales', [ProjectController::class, 'assignPresales'])->name('projects.assignPresales');
    
    Route::prefix('versions/{version}')->group(function() {

        Route::get('solution_type', [SolutionTypeController::class, 'create'])->name('versions.solution_type.create');
        Route::match(['post', 'put'], 'solution_type', [SolutionTypeController::class, 'store'])->name('versions.solution_type.store');

        Route::get('region', [RegionController::class, 'create'])->name('versions.region.create');
        Route::match(['post', 'put'], 'region', [RegionController::class, 'store'])->name('versions.region.store');

        // Professional Services
        Route::get('region/professional-services/create', [RegionController::class, 'createProfessional'])->name('versions.region.professional.create');
        Route::match(['post', 'put'], 'region/professional-services', [RegionController::class, 'storeProfessional'])->name('versions.region.professional.store');

        // Network
        Route::get('region/network/create', [RegionController::class, 'createNetwork'])->name('versions.region.network.create');
        Route::post('region/network', [RegionController::class, 'storeNetwork'])->name('versions.region.network.store');

        // DR Settings
        Route::get('region/dr-settings/create', [RegionController::class, 'createDr'])->name('versions.region.dr.create');
        Route::post('region/dr-settings', [RegionController::class, 'storeDr'])->name('versions.region.dr.store');

        Route::get('mpdraas', [MPDRaaSController::class, 'create'])->name('versions.mpdraas.create');
        Route::post('mpdraas', [MPDRaaSController::class, 'store'])->name('versions.mpdraas.store');

        Route::post('mpdraas/autosave', [\App\Http\Controllers\MPDRaaSController::class, 'autosave'])
            ->name('versions.mpdraas.autosave');


        Route::post('mpdraas/vms/upsert', [\App\Http\Controllers\MPDRaaSController::class, 'upsertVm'])
    ->name('versions.mpdraas.vms.upsert');

Route::delete('mpdraas/vms/{vm}', [\App\Http\Controllers\MPDRaaSController::class, 'destroyVm'])
    ->name('versions.mpdraas.vms.destroy');
         

        // Security, internal summary, quotation, dll.
        Route::get('security-service', [SecurityServiceController::class, 'create'])->name('versions.security_service.create');
        Route::post('security-service', [SecurityServiceController::class, 'store'])->name('versions.security_service.store');

        Route::get('security-service/time', [SecurityServiceController::class, 'createTime'])
            ->name('versions.security_service.time.create');



// === NEW: Any-file upload/list/delete for Time Security Services ===
Route::post('security-service/time/files/upload', [SecurityServiceController::class, 'uploadTimeFile'])
    ->name('versions.security_service.time.files.upload');

Route::delete('security-service/time/files/{file}', [SecurityServiceController::class, 'deleteTimeFile'])
    ->name('versions.security_service.time.files.delete');

Route::get('security-service/time/files/{file}/download', [SecurityServiceController::class, 'downloadTimeFile'])
    ->name('versions.security_service.time.files.download');


        // Files (rujuk halaman Non-Standard Items)
        Route::post('non-standard-items/files', [NonStandardItemController::class, 'uploadAnyFile'])
            ->name('versions.non_standard_items.files.upload');
        Route::delete('non-standard-items/files/{file}', [NonStandardItemController::class, 'deleteAnyFile'])
            ->name('versions.non_standard_items.files.delete');
        Route::get('non-standard-items/files/{file}/download', [NonStandardItemController::class, 'downloadAnyFile'])
            ->name('versions.non_standard_items.files.download');

        Route::post('duplicate', [ProjectController::class, 'duplicateVersion'])
            ->name('versions.duplicate');

        Route::get('internal-summary', [\App\Http\Controllers\InternalSummaryController::class, 'index'])
            ->name('versions.internal_summary.show');

        Route::post('internal-summary/commit', [InternalSummaryController::class, 'commit'])
            ->name('versions.internal_summary.commit');

        Route::post('internal-summary/save', [\App\Http\Controllers\InternalSummaryController::class, 'saveOrUpdate'])
            ->name('versions.internal_summary.save');

        // Customization (entire subscription period)
        Route::get('customization', [CustomizationController::class, 'show'])
            ->name('versions.customization.show');
        Route::post('customization', [CustomizationController::class, 'save'])
            ->name('versions.customization.save');

        /*Route::get('ratecard', [\App\Http\Controllers\RateCardController::class, 'showRateCard'])
            ->name('versions.quotation.ratecard');*/
 


        Route::get('ratecard', [\App\Http\Controllers\RateCardController::class, 'showRateCard'])
    ->name('versions.quotation.ratecard')
    ->middleware('nocache');

Route::get('ratecard-pdf', [\App\Http\Controllers\RateCardController::class, 'downloadRateCardPdf'])
    ->name('versions.ratecard.pdf')
    ->middleware('nocache');

             


        Route::get('quotation', [\App\Http\Controllers\QuotationController::class, 'generateQuotation'])
            ->name('versions.quotation.preview');

        /*Route::get('quotation-annual', [QuotationController::class, 'generateQuotation'])
            ->name('versions.quotation.annual');*/

        Route::get('quotation-annual', [QuotationController::class, 'annual'])
            ->name('versions.quotation.annual');

        Route::get('generate-pdf', [QuotationController::class, 'generatePDF'])
            ->name('versions.quotation.generate_pdf');

        Route::get('download-table-pdf', [\App\Http\Controllers\QuotationPdfController::class, 'downloadTablePdf'])
            ->name('versions.quotation.download_table_pdf');

        Route::get('generate-csv', [\App\Http\Controllers\QuotationCsvController::class, 'generateCsv'])
            ->name('versions.quotation.generate_csv');

        Route::get('quotation.xlsx', [QuotationCsvController::class, 'generateXlsx'])
            ->name('versions.quotation.generate_xlsx');



            

        Route::get('generate-mpdraas-pdf', [QuotationController::class, 'generateMPDRaaSPdf'])
            ->name('versions.quotation.generate_mpdraas_pdf');

        Route::get('quotation-pdf', function (Version $version) {
            $pdfContent = $version->quotation_pdf; 
            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="quotation.pdf"');
        })->name('versions.quotation.pdf');

        // =================== Non-Standard Items (Page + CRUD + Import) ===================
        Route::get('non-standard-items/create', [NonStandardItemController::class, 'create'])->name('versions.non_standard_items.create');
        Route::post('non-standard-items', [NonStandardItemController::class, 'store'])->name('versions.non_standard_items.store');
        Route::get('non-standard-items', [NonStandardItemController::class, 'index'])->name('versions.non_standard_items.index');
        Route::post('non-standard-items/import', [NonStandardItemController::class, 'import'])->name('versions.non_standard_items.import');
        Route::delete('non-standard-items/{item}', [NonStandardItemController::class, 'destroy'])->name('non_standard_items.destroy');

        // >>> Dipindahkan ke dalam group versi (supaya route terima $version):
        Route::get('non-standard-items/{item}/edit', [NonStandardItemController::class, 'edit'])->name('non_standard_items.edit');
        Route::put('non-standard-items/{item}', [NonStandardItemController::class, 'update'])->name('non_standard_items.update');

        // =================== Non-Standard Offerings (Standard Services) ===================
        // >>> Baru: letak dalam versions/{version} supaya controller dapat $versionId
        Route::prefix('non-standard-offerings')->group(function () {
            Route::post('/', [NonStandardOfferingController::class, 'store'])
                ->name('versions.non_standard_offerings.store');

            Route::get('{offering}/edit', [NonStandardOfferingController::class, 'edit'])
                ->name('versions.non_standard_offerings.edit');

            Route::put('{offering}', [NonStandardOfferingController::class, 'update'])
                ->name('versions.non_standard_offerings.update');

            Route::delete('{offering}', [NonStandardOfferingController::class, 'destroy'])
                ->name('versions.non_standard_offerings.destroy');
        });

        // Inside Route::prefix('versions/{version}')
        Route::get('quotation-pdf', [QuotationController::class, 'generateQuotationPdf'])
            ->name('versions.quotation.generate_pdf_alt');

        Route::get('download-zip', [\App\Http\Controllers\QuotationController::class, 'downloadZip'])
            ->name('versions.download_zip');

        Route::get('export-link', [\App\Http\Controllers\QuotationController::class, 'exportLink'])
            ->name('versions.export_link'); 

        Route::get('internal-summary/pdf', [QuotationController::class, 'internalSummaryPdf'])
            ->name('versions.internal_summary.pdf');
    });

    Route::post('versions/{version}/region/autosave', [RegionController::class, 'autoSave'])
        ->name('versions.region.autosave');

    Route::post('/autosave/quotation/{version}', [QuotationController::class, 'autoSave']);


    Route::get('/projects/{project}/service_description', [RegionController::class, 'serviceDescription'])
        ->name('projects.service_description');
    Route::get('/services/from-pdf', [ServiceDescriptionController::class, 'showFromPdf'])->name('services.from_pdf');

    // Projects & Versions
    Route::resource('projects', ProjectController::class);
    Route::resource('projects.versions', VersionController::class)->only(['create', 'store']);

    Route::delete('/versions/{version}', [VersionController::class, 'destroy'])->name('versions.destroy');
    Route::get('/api/customers/{customer}/projects', function($customer) {
        $projects = Project::where('customer_id', $customer)->get();
        return response()->json($projects);
    });

    Route::get('versions/{version}/ecs-configuration', [ECSConfigurationController::class, 'create'])
        ->name('versions.ecs_configuration.create');
    Route::post('versions/{version}/ecs-configuration', [ECSConfigurationController::class, 'store'])
        ->name('versions.ecs_configuration.store');
    Route::put('versions/{version}/ecs-configuration', [ECSConfigurationController::class, 'store']);
    Route::post('/ecs-configurations/import', [ECSConfigurationController::class, 'import'])->name('ecs_configurations.import');
    Route::post('/versions/{version}/backup/import/save', [ECSConfigurationController::class, 'storePreview'])->name('ecs_configurations.store_preview');

    Route::get('versions/{version}/backup', [ECSConfigurationController::class, 'create'])
        ->name('versions.backup.create');
    Route::post('versions/{version}/backup', [ECSConfigurationController::class, 'store'])
        ->name('versions.backup.store');
    Route::put('versions/{version}/backup', [ECSConfigurationController::class, 'store']);

    Route::delete('/ecs-configurations/{id}', [ECSConfigurationController::class, 'destroy'])->name('ecs_configurations.destroy');

    Route::post('/ecs-configurations/bulk-destroy', [ECSConfigurationController::class, 'bulkDestroy'])
    ->name('ecs_configurations.bulk_destroy');


    // Solutions
    Route::resource('solutions', SolutionController::class);

    Route::resource('products', ProductController::class);
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');

    Route::get('/vm-mapping', [VMMappingsController::class, 'index'])->name('vm-mapping.index');
    Route::get('/flavour-map', [PFlavourMapController::class, 'index'])->name('flavour.index');

    Route::get('/categories/export', [CategoryController::class, 'export'])->name('categories.export');

    Route::resource('categories', CategoryController::class);
    Route::post('/categories/import', [CategoryController::class, 'import'])->name('categories.import');

    
// ===== Price Catalogs =====
Route::post('/price-catalogs', [PriceCatalogController::class, 'store'])
    ->name('price-catalogs.store');

Route::post('/price-catalogs/{catalog}/make-current', [PriceCatalogController::class, 'makeCurrent'])
    ->name('price-catalogs.makeCurrent');

Route::post('/price-catalogs/{catalog}/commit', [PriceCatalogController::class, 'commit'])
    ->name('price-catalogs.commit');

// ===== Services: static/custom first =====
Route::get('/services/export', [ServiceController::class, 'export'])
    ->name('services.export');

Route::post('/services/import', [ServiceController::class, 'import'])
    ->name('services.import');

Route::get('/services/{service}/history', [ServiceController::class, 'priceHistory'])
    ->name('services.priceHistory');

Route::get('/services/{service}/audit-logs', [ServiceController::class, 'auditLogs'])
    ->name('services.audit-logs');

Route::post('/services/bulk-log', [ServiceController::class, 'bulkLog'])
    ->name('services.bulkLog');

Route::post('/services/bulk-adjust', [ServiceController::class, 'bulkAdjust'])
    ->name('services.bulkAdjust');

// >>> Tambah route PREVIEW (baru)
Route::post('/services/bulk-preview', [ServiceController::class, 'bulkPreview'])
    ->name('services.bulkPreview');

// ===== Resource (elak duplikasi destroy) =====
// Pilih salah satu:
// A) Guna destroy daripada resource → buang route manual DELETE
Route::resource('services', ServiceController::class);

// B) ATAU kekalkan route manual DELETE, exclude destroy dari resource:
// Route::resource('services', ServiceController::class)->except(['destroy']);
// Route::delete('/services/{id}', [ServiceController::class, 'destroy'])->name('services.destroy');


    // ECS Flavours
    Route::get('/ecs-flavours/{id}/edit', [ECSFlavourController::class, 'edit'])->name('ecs-flavours.edit');
    Route::put('/ecs-flavours/{id}', [ECSFlavourController::class, 'update'])->name('ecs-flavours.update');

    Route::get('/ecs-flavours/export', [ECSFlavourController::class, 'export'])->name('ecs-flavours.export');

    Route::resource('ecs-flavours', ECSFlavourController::class);
    Route::post('/ecs-flavours/import', [ECSFlavourController::class, 'import'])->name('ecs-flavours.import');

    Route::post('/network-mappings/import', [NetworkMappingController::class, 'import'])
    ->name('network-mappings.import');
    
    Route::get('/network-mappings/export', [NetworkMappingController::class, 'export'])->name('network-mappings.export');

    Route::resource('network-mappings', NetworkMappingController::class);   

    // Customers (resourceful)
    Route::resource('customers', CustomerController::class);
});

// Routes for admin (manage users)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('users', UserController::class);
});
