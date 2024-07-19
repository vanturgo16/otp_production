<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MstAccountCodesController;
use App\Http\Controllers\MstAccountTypesController;
use App\Http\Controllers\TransDataBankController;
use App\Http\Controllers\TransDataKasController;

//PRODUCTION
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProductionReportSlittingController;

//Route Login
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('auth/login', [AuthController::class, 'postlogin'])->name('postlogin')->middleware("throttle:5,2");

//Route Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
	//Production
	//START REQUEST SPAREPART AND AUXILIARIES
	Route::get('/production-req-sparepart-auxiliaries', [ProductionController::class, 'production_req_sparepart_auxiliaries'])->name('production_req_sparepart_auxiliaries');
	Route::get('/production-req-sparepart-auxiliaries-json', [ProductionController::class, 'production_req_sparepart_auxiliaries_json'])->name('production_req_sparepart_auxiliaries_json');
	Route::get('/production-req-sparepart-auxiliaries-add', [ProductionController::class, 'production_req_sparepart_auxiliaries_add'])->name('production_req_sparepart_auxiliaries_add');
	Route::post('/production-req-sparepart-auxiliaries-save', [ProductionController::class, 'production_req_sparepart_auxiliaries_save'])->name('production_req_sparepart_auxiliaries_save');	
	Route::get('/production-req-sparepart-auxiliaries-hold/{id}', [ProductionController::class, 'production_req_sparepart_auxiliaries_hold'])->name('production_req_sparepart_auxiliaries_hold');	
	Route::get('/production-req-sparepart-auxiliaries-approve/{id}', [ProductionController::class, 'production_req_sparepart_auxiliaries_approve'])->name('production_req_sparepart_auxiliaries_approve');	
	Route::get('/production-req-sparepart-auxiliaries-delete/{id}', [ProductionController::class, 'production_req_sparepart_auxiliaries_delete'])->name('production_req_sparepart_auxiliaries_delete');	
    Route::get('/production-req-sparepart-auxiliaries-detail/{id}', [ProductionController::class, 'production_req_sparepart_auxiliaries_detail'])->name('production_req_sparepart_auxiliaries_detail');
    Route::post('/production-req-sparepart-auxiliaries-detail-update', [ProductionController::class, 'production_req_sparepart_auxiliaries_detail_update'])->name('production_req_sparepart_auxiliaries_detail_update');	
    Route::post('/production-req-sparepart-auxiliaries-detail-add', [ProductionController::class, 'production_req_sparepart_auxiliaries_detail_add'])->name('production_req_sparepart_auxiliaries_detail_add');	Route::get('/production-req-sparepart-auxiliaries-detail-edit-get/{id}', [ProductionController::class, 'production_req_sparepart_auxiliaries_detail_edit_get'])->name('production_req_sparepart_auxiliaries_detail_edit_get');
	Route::put('/production-req-sparepart-auxiliaries-detail-edit-save/{id}', [ProductionController::class, 'production_req_sparepart_auxiliaries_detail_edit_save'])->name('production_req_sparepart_auxiliaries_detail_edit_save');
	Route::post('/production-req-sparepart-auxiliaries-detail-delete', [ProductionController::class, 'production_req_sparepart_auxiliaries_detail_delete'])->name('production_req_sparepart_auxiliaries_detail_delete');
	//END REQUEST SPAREPART AND AUXILIARIES
	//START ENTRY MATERIAL USE
	Route::get('/production-ent-material-use', [ProductionController::class, 'production_entry_material_use'])->name('production_entry_material_use');
	Route::get('/production-ent-material-use-json', [ProductionController::class, 'production_entry_material_use_json'])->name('production_entry_material_use_json');
	Route::get('/production-ent-material-use-add', [ProductionController::class, 'production_entry_material_use_add'])->name('production_entry_material_use_add');
	Route::get('/json_get_work_center', [ProductionController::class, 'jsonGetWorkCenter'])->name('jsonGetWorkCenter');
	Route::get('/json_get_material_info', [ProductionController::class, 'jsonGetMaterialInfo'])->name('jsonGetMaterialInfo');
	Route::get('/json_get_regu', [ProductionController::class, 'jsonGetRegu'])->name('jsonGetRegu');
	Route::post('/production-ent-material-use-save', [ProductionController::class, 'production_entry_material_use_save'])->name('production_entry_material_use_save');
	Route::post('/production-ent-material-use-update', [ProductionController::class, 'production_entry_material_use_update'])->name('production_entry_material_use_update');
	Route::get('/production-ent-material-use-approve/{id}', [ProductionController::class, 'production_entry_material_use_approve'])->name('production_entry_material_use_approve');
	Route::get('/production-ent-material-use-hold/{id}', [ProductionController::class, 'production_entry_material_use_hold'])->name('production_entry_material_use_hold');
	Route::get('/production-ent-material-use-delete/{id}', [ProductionController::class, 'production_entry_material_use_delete'])->name('production_entry_material_use_delete');
	Route::get('/production-ent-material-use-print/{id}', [ProductionController::class, 'production_entry_material_use_print'])->name('production_entry_material_use_print');
	Route::get('/production-ent-material-use-detail/{id}', [ProductionController::class, 'production_entry_material_use_detail'])->name('production_entry_material_use_detail');
    Route::post('/production-entry-material-use-detail-add', [ProductionController::class, 'production_entry_material_use_detail_add'])->name('production_entry_material_use_detail_add');
	Route::get('/production-entry-material-use-detail-edit/{id_rm}/{id_rm_detail}', [ProductionController::class, 'production_entry_material_use_detail_edit'])->name('production_entry_material_use_detail_edit');
	Route::post('/production-entry-material-use-detail-edit-save', [ProductionController::class, 'production_entry_material_use_detail_edit_save'])->name('production_entry_material_use_detail_edit_save');
	//Route::get('/production-entry-material-use-detail-edit-get/{id}', [ProductionController::class, 'production_entry_material_use_detail_edit_get'])->name('production_entry_material_use_detail_edit_get');
	//Route::put('/production-entry-material-use-detail-edit-save/{id}', [ProductionController::class, 'production_entry_material_use_detail_edit_save'])->name('production_entry_material_use_detail_edit_save');
    Route::post('/production-entry-material-use-detail-delete', [ProductionController::class, 'production_entry_material_use_detail_delete'])->name('production_entry_material_use_detail_delete');
	
	//END ENTRY MATERIAL USE
	//START REPORT BLOW
	Route::get('/production-ent-report-blow', [ProductionController::class, 'production_entry_report_blow'])->name('production_entry_report_blow');
	Route::get('/production-ent-report-blow-json', [ProductionController::class, 'production_entry_report_blow_json'])->name('production_entry_report_blow_json');
	Route::get('/production-ent-report-blow-add', [ProductionController::class, 'production_entry_report_blow_add'])->name('production_entry_report_blow_add');
	Route::get('/json_get_produk', [ProductionController::class, 'jsonGetProduk'])->name('jsonGetProduk');
	Route::get('/json_get_customer', [ProductionController::class, 'jsonGetCustomers'])->name('jsonGetCustomers');
	Route::post('/production-ent-report-blow-save', [ProductionController::class, 'production_entry_report_blow_save'])->name('production_entry_report_blow_save');
	Route::get('/production-ent-report-blow-detail/{id}', [ProductionController::class, 'production_entry_report_blow_detail'])->name('production_entry_report_blow_detail');
	Route::post('/production-ent-report-blow-update', [ProductionController::class, 'production_entry_report_blow_update'])->name('production_entry_report_blow_update');
	Route::get('/json_get_barcode', [ProductionController::class, 'jsonGetBarcode'])->name('jsonGetBarcode');
	Route::get('/production-ent-report-blow-material-use/{id}', [ProductionController::class, 'production_entry_report_blow_material_use'])->name('production_entry_report_blow_material_use');
	Route::get('/production-ent-report-blow-material-use-json', [ProductionController::class, 'production_entry_report_blow_material_use_json'])->name('production_entry_report_blow_material_use_json');
	
	Route::post('/production-entry-report-blow-detail-production-result-add', [ProductionController::class, 'production_entry_report_blow_detail_production_result_add'])->name('production_entry_report_blow_detail_production_result_add');
	Route::get('/production-entry-report-blow-detail-production-result-edit/{id_rb}/{id_rb_pr}', [ProductionController::class, 'production_entry_report_blow_detail_production_result_edit'])->name('production_entry_report_blow_detail_production_result_edit');
	Route::post('/production-entry-report-blow-detail-production-result-edit-save', [ProductionController::class, 'production_entry_report_blow_detail_production_result_edit_save'])->name('production_entry_report_blow_detail_production_result_edit_save');
	Route::post('/production-entry-report-blow-detail-production-result-delete', [ProductionController::class, 'production_entry_report_blow_detail_production_result_delete'])->name('production_entry_report_blow_detail_production_result_delete');
	
	Route::post('/production-entry-report-blow-detail-waste-add', [ProductionController::class, 'production_entry_report_blow_detail_waste_add'])->name('production_entry_report_blow_detail_waste_add');
	Route::get('/production-entry-report-blow-detail-waste-edit/{id_rb}/{id_rb_w}', [ProductionController::class, 'production_entry_report_blow_detail_waste_edit'])->name('production_entry_report_blow_detail_waste_edit');
	Route::post('/production-entry-report-blow-detail-waste-edit-save', [ProductionController::class, 'production_entry_report_blow_detail_waste_edit_save'])->name('production_entry_report_blow_detail_waste_edit_save');
	Route::post('/production-entry-report-blow-detail-waste-delete', [ProductionController::class, 'production_entry_report_blow_detail_waste_delete'])->name('production_entry_report_blow_detail_waste_delete');
	
	Route::get('/production-ent-report-blow-json-preparation', [ProductionController::class, 'production_entry_report_blow_json_preparation'])->name('production_entry_report_blow_json_preparation');
	Route::get('/production-ent-report-blow-json-hygiene', [ProductionController::class, 'production_entry_report_blow_json_hygiene'])->name('production_entry_report_blow_json_hygiene');
	Route::get('/production-ent-report-blow-json-update-stock', [ProductionController::class, 'production_entry_report_blow_json_update_stock'])->name('production_entry_report_blow_json_update_stock');
	Route::get('/production-entry-report-blow-update-stock/{id}', [ProductionController::class, 'production_entry_report_blow_update_stock'])->name('production_entry_report_blow_update_stock');//masih bermaslah di update stock
		
	Route::get('/production-ent-report-blow-print/{id}', [ProductionController::class, 'production_entry_report_blow_print'])->name('production_entry_report_blow_print');	
	//END REPORT BLOW
	
	//START REPORT SLITTING
	Route::get('/production-ent-report-slitting', [ProductionReportSlittingController::class, 'production_entry_report_slitting'])->name('production_entry_report_slitting');
	Route::get('/production-ent-report-slitting-json', [ProductionReportSlittingController::class, 'production_entry_report_slitting_json'])->name('production_entry_report_slitting_json');
	
	//END REPORT SLITTING
	
    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //AccountType
    Route::get('/accounttype', [MstAccountTypesController::class, 'index'])->name('accounttype.index');
    Route::post('/accounttype', [MstAccountTypesController::class, 'index'])->name('accounttype.index');
    Route::post('accounttype/create', [MstAccountTypesController::class, 'store'])->name('accounttype.store');
    Route::post('accounttype/update/{id}', [MstAccountTypesController::class, 'update'])->name('accounttype.update');
    Route::post('accounttype/activate/{id}', [MstAccountTypesController::class, 'activate'])->name('accounttype.activate');
    Route::post('accounttype/deactivate/{id}', [MstAccountTypesController::class, 'deactivate'])->name('accounttype.deactivate');
    
    //AccountCode
    Route::get('/accountcode', [MstAccountCodesController::class, 'index'])->name('accountcode.index');
    Route::post('/accountcode', [MstAccountCodesController::class, 'index'])->name('accountcode.index');
    Route::post('accountcode/create', [MstAccountCodesController::class, 'store'])->name('accountcode.store');
    Route::post('accountcode/update/{id}', [MstAccountCodesController::class, 'update'])->name('accountcode.update');
    Route::post('accountcode/activate/{id}', [MstAccountCodesController::class, 'activate'])->name('accountcode.activate');
    Route::post('accountcode/deactivate/{id}', [MstAccountCodesController::class, 'deactivate'])->name('accountcode.deactivate');

    //TransDataKas
    Route::get('/transdatakas', [TransDataKasController::class, 'index'])->name('transdatakas.index');
    Route::post('/transdatakas', [TransDataKasController::class, 'index'])->name('transdatakas.index');
    Route::post('transdatakas/create', [TransDataKasController::class, 'store'])->name('transdatakas.store');
    Route::post('transdatakas/update/{id}', [TransDataKasController::class, 'update'])->name('transdatakas.update');
    Route::post('transdatakas/delete/{id}', [TransDataKasController::class, 'delete'])->name('transdatakas.delete');

    //TransDataBank
    Route::get('/transdatabank', [TransDataBankController::class, 'index'])->name('transdatabank.index');
    Route::post('/transdatabank', [TransDataBankController::class, 'index'])->name('transdatabank.index');
    Route::post('transdatabank/create', [TransDataBankController::class, 'store'])->name('transdatabank.store');
    Route::post('transdatabank/update/{id}', [TransDataBankController::class, 'update'])->name('transdatabank.update');
    Route::post('transdatabank/delete/{id}', [TransDataBankController::class, 'delete'])->name('transdatabank.delete');

     // PPIC dev_hafidz
    include __DIR__.'/ppic/workOrder.php';
});