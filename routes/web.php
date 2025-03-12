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
use App\Http\Controllers\ProductionReportFoldingController;
use App\Http\Controllers\ProductionReportBagMakingController;

//Route Login
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('auth/login', [AuthController::class, 'postlogin'])->name('postlogin')->middleware("throttle:5,2");

//Route Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'clear.permission.cache', 'permission:Produksi|Produksi_production-req-sparepart-auxiliaries|Produksi_production-ent-report-slitting|Produksi_production-ent-report-production|Produksi_production-ent-report-folding|Produksi_production-ent-report-blow|Produksi_production-ent-report-bag-marketing|Produksi_production-ent-material-use'])->group(function () {	//Production
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
	Route::get('/production-req-sparepart-auxiliaries-print/{id}', [ProductionController::class, 'production_req_sparepart_auxiliaries_print'])->name('production_req_sparepart_auxiliaries_print');
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
	
	Route::get('/production-ent-report-material-use-json-approve', [ProductionController::class, 'production_entry_report_material_use_json_approve'])->name('production_entry_report_material_use_json_approve');
	Route::get('/production-ent-report-material-use-json-hold', [ProductionController::class, 'production_entry_report_material_use_json_hold'])->name('production_entry_report_material_use_json_hold');
	
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
	Route::get('/json_get_produk_autofill', [ProductionController::class, 'jsonGetProdukAutofill'])->name('jsonGetProdukAutofill');
	Route::get('/json_get_customer', [ProductionController::class, 'jsonGetCustomers'])->name('jsonGetCustomers');
	Route::post('/production-ent-report-blow-save', [ProductionController::class, 'production_entry_report_blow_save'])->name('production_entry_report_blow_save');
	Route::get('/production-ent-report-blow-detail/{id}', [ProductionController::class, 'production_entry_report_blow_detail'])->name('production_entry_report_blow_detail');
	Route::post('/production-ent-report-blow-update', [ProductionController::class, 'production_entry_report_blow_update'])->name('production_entry_report_blow_update');
	Route::get('/json_get_barcode', [ProductionController::class, 'jsonGetBarcode'])->name('jsonGetBarcode');
	/*
	Route::get('/json_get_barcode_start_slitting', [ProductionController::class, 'jsonGetBarcodeStartSlitting'])->name('jsonGetBarcodeStartSlitting');
	Route::get('/json_get_barcode_slitting', [ProductionController::class, 'jsonGetBarcodeSlitting'])->name('jsonGetBarcodeSlitting');//ampe sini
	*/
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
	Route::get('/production-ent-report-blow-json-update-stock-info', [ProductionController::class, 'production_entry_report_blow_json_update_stock_info'])->name('production_entry_report_blow_json_update_stock_info');
	Route::get('/production-entry-report-blow-update-stock/{id}', [ProductionController::class, 'production_entry_report_blow_update_stock'])->name('production_entry_report_blow_update_stock');
	Route::get('/production-entry-report-blow-unposted/{id}', [ProductionController::class, 'production_entry_report_blow_unposted'])->name('production_entry_report_blow_unposted');
	
	Route::get('/production-ent-report-blow-delete/{id}', [ProductionController::class, 'production_entry_report_blow_delete'])->name('production_entry_report_blow_delete');
		
	Route::get('/production-ent-report-blow-print/{id}', [ProductionController::class, 'production_entry_report_blow_print'])->name('production_entry_report_blow_print');	
	//END REPORT BLOW
	
	//START REPORT SLITTING
	Route::get('/production-ent-report-slitting', [ProductionReportSlittingController::class, 'production_entry_report_slitting'])->name('production_entry_report_slitting');
	Route::get('/production-ent-report-slitting-json', [ProductionReportSlittingController::class, 'production_entry_report_slitting_json'])->name('production_entry_report_slitting_json');
	Route::get('/production-ent-report-slitting-add', [ProductionReportSlittingController::class, 'production_entry_report_slitting_add'])->name('production_entry_report_slitting_add');
	
	Route::post('/production-ent-report-slitting-save', [ProductionReportSlittingController::class, 'production_entry_report_slitting_save'])->name('production_entry_report_slitting_save');
	
	Route::get('/production-ent-report-slitting-detail/{id}', [ProductionReportSlittingController::class, 'production_entry_report_slitting_detail'])->name('production_entry_report_slitting_detail');
	
	Route::post('/production-ent-report-slitting-update', [ProductionReportSlittingController::class, 'production_entry_report_slitting_update'])->name('production_entry_report_slitting_update');
	
	Route::get('/production-ent-report-slitting-json-preparation', [ProductionReportSlittingController::class, 'production_entry_report_slitting_json_preparation'])->name('production_entry_report_slitting_json_preparation');
	Route::get('/production-ent-report-slitting-json-hygiene', [ProductionReportSlittingController::class, 'production_entry_report_slitting_json_hygiene'])->name('production_entry_report_slitting_json_hygiene');
	Route::get('/production-ent-report-slitting-json-update-stock', [ProductionReportSlittingController::class, 'production_entry_report_slitting_json_update_stock'])->name('production_entry_report_slitting_json_update_stock');
	Route::get('/production-ent-report-slitting-json-update-stock-info', [ProductionReportSlittingController::class, 'production_entry_report_slitting_json_update_stock_info'])->name('production_entry_report_slitting_json_update_stock_info');
	Route::get('/production-entry-report-slitting-update-stock/{id}', [ProductionReportSlittingController::class, 'production_entry_report_slitting_update_stock'])->name('production_entry_report_slitting_update_stock');	
	Route::get('/production-entry-report-slitting-unposted/{id}', [ProductionReportSlittingController::class, 'production_entry_report_slitting_unposted'])->name('production_entry_report_slitting_unposted');
	Route::get('/production-ent-report-slitting-delete/{id}', [ProductionReportSlittingController::class, 'production_entry_report_slitting_delete'])->name('production_entry_report_slitting_delete');
	
	Route::post('/production-entry-report-slitting-detail-production-result-add', [ProductionReportSlittingController::class, 'production_entry_report_slitting_detail_production_result_add'])->name('production_entry_report_slitting_detail_production_result_add');
	Route::get('/production-entry-report-slitting-detail-production-result-edit/{id_rb}/{id_rb_pr}', [ProductionReportSlittingController::class, 'production_entry_report_slitting_detail_production_result_edit'])->name('production_entry_report_slitting_detail_production_result_edit');
	Route::post('/production-entry-report-slitting-detail-production-result-edit-save', [ProductionReportSlittingController::class, 'production_entry_report_slitting_detail_production_result_edit_save'])->name('production_entry_report_slitting_detail_production_result_edit_save');
	Route::post('/production-entry-report-slitting-detail-production-result-delete', [ProductionReportSlittingController::class, 'production_entry_report_slitting_detail_production_result_delete'])->name('production_entry_report_slitting_detail_production_result_delete');	
	
	
	Route::get('/production-ent-report-slitting-print/{id}', [ProductionReportSlittingController::class, 'production_entry_report_slitting_print'])->name('production_entry_report_slitting_print');	
	//END REPORT SLITTING
	
	
	//START REPORT FOLDING
	Route::get('/production-ent-report-folding', [ProductionReportFoldingController::class, 'production_entry_report_folding'])->name('production_entry_report_folding');
	Route::get('/production-ent-report-folding-json', [ProductionReportFoldingController::class, 'production_entry_report_folding_json'])->name('production_entry_report_folding_json');
	Route::get('/production-ent-report-folding-add', [ProductionReportFoldingController::class, 'production_entry_report_folding_add'])->name('production_entry_report_folding_add');
	
	Route::post('/production-ent-report-folding-save', [ProductionReportFoldingController::class, 'production_entry_report_folding_save'])->name('production_entry_report_folding_save');
	
	Route::get('/production-ent-report-folding-detail/{id}', [ProductionReportFoldingController::class, 'production_entry_report_folding_detail'])->name('production_entry_report_folding_detail');
	
	Route::post('/production-ent-report-folding-update', [ProductionReportFoldingController::class, 'production_entry_report_folding_update'])->name('production_entry_report_folding_update');
	
	Route::post('/production-entry-report-folding-detail-production-result-add', [ProductionReportFoldingController::class, 'production_entry_report_folding_detail_production_result_add'])->name('production_entry_report_folding_detail_production_result_add');
	Route::get('/production-entry-report-folding-detail-production-result-edit/{id_rb}/{id_rb_pr}', [ProductionReportFoldingController::class, 'production_entry_report_folding_detail_production_result_edit'])->name('production_entry_report_folding_detail_production_result_edit');
	Route::post('/production-entry-report-folding-detail-production-result-edit-save', [ProductionReportFoldingController::class, 'production_entry_report_folding_detail_production_result_edit_save'])->name('production_entry_report_folding_detail_production_result_edit_save');
	Route::post('/production-entry-report-folding-detail-production-result-delete', [ProductionReportFoldingController::class, 'production_entry_report_folding_detail_production_result_delete'])->name('production_entry_report_folding_detail_production_result_delete');
	
	Route::get('/production-ent-report-folding-json-preparation', [ProductionReportFoldingController::class, 'production_entry_report_folding_json_preparation'])->name('production_entry_report_folding_json_preparation');
	Route::get('/production-ent-report-folding-json-hygiene', [ProductionReportFoldingController::class, 'production_entry_report_folding_json_hygiene'])->name('production_entry_report_folding_json_hygiene');
	Route::get('/production-ent-report-folding-json-update-stock', [ProductionReportFoldingController::class, 'production_entry_report_folding_json_update_stock'])->name('production_entry_report_folding_json_update_stock');
	Route::get('/production-ent-report-folding-json-update-stock-info', [ProductionReportFoldingController::class, 'production_entry_report_folding_json_update_stock_info'])->name('production_entry_report_folding_json_update_stock_info');
	
	Route::get('/production-entry-report-folding-update-stock/{id}', [ProductionReportFoldingController::class, 'production_entry_report_folding_update_stock'])->name('production_entry_report_folding_update_stock');	
	Route::get('/production-entry-report-folding-unposted/{id}', [ProductionReportFoldingController::class, 'production_entry_report_folding_unposted'])->name('production_entry_report_folding_unposted');
	Route::get('/production-ent-report-folding-delete/{id}', [ProductionReportFoldingController::class, 'production_entry_report_folding_delete'])->name('production_entry_report_folding_delete');
	
	Route::get('/production-ent-report-folding-print/{id}', [ProductionReportFoldingController::class, 'production_entry_report_folding_print'])->name('production_entry_report_folding_print');	
	//END REPORT FOLDING
	
	//START REPORT BAG MAKING
	Route::get('/production-ent-report-bag-making', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making'])->name('production_entry_report_bag_making');
	Route::get('/production-ent-report-bag-making-json', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_json'])->name('production_entry_report_bag_making_json');
	Route::get('/production-ent-report-bag-making-add', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_add'])->name('production_entry_report_bag_making_add');
	
	Route::post('/production-ent-report-bag-making-save', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_save'])->name('production_entry_report_bag_making_save');
	
	Route::get('/production-ent-report-bag-making-detail/{id}', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_detail'])->name('production_entry_report_bag_making_detail');
	
	Route::post('/production-ent-report-bag-making-update', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_update'])->name('production_entry_report_bag_making_update');
	
	Route::post('/production-entry-report-bag-making-detail-production-result-add', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_detail_production_result_add'])->name('production_entry_report_bag_making_detail_production_result_add');
	Route::get('/production-entry-report-bag-making-detail-production-result-edit/{id_rb}/{id_rb_pr}', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_detail_production_result_edit'])->name('production_entry_report_bag_making_detail_production_result_edit');
	Route::post('/production-entry-report-bag-making-detail-production-result-edit-save', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_detail_production_result_edit_save'])->name('production_entry_report_bag_making_detail_production_result_edit_save');
	Route::post('/production-entry-report-bag-making-detail-production-result-delete', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_detail_production_result_delete'])->name('production_entry_report_bag_making_detail_production_result_delete');
	
	Route::post('/production-entry-report-bag-making-detail-waste-add', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_detail_waste_add'])->name('production_entry_report_bag_making_detail_waste_add');
	Route::get('/production-entry-report-bag-making-detail-waste-edit/{id_rb}/{id_rb_w}', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_detail_waste_edit'])->name('production_entry_report_bag_making_detail_waste_edit');
	Route::post('/production-entry-report-bag-making-detail-waste-edit-save', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_detail_waste_edit_save'])->name('production_entry_report_bag_making_detail_waste_edit_save');
	Route::post('/production-entry-report-bag-making-detail-waste-delete', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_detail_waste_delete'])->name('production_entry_report_bag_making_detail_waste_delete');
	
	Route::get('/production-ent-report-bag-making-json-preparation', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_json_preparation'])->name('production_entry_report_bag_making_json_preparation');
	Route::get('/production-ent-report-bag-making-json-hygiene', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_json_hygiene'])->name('production_entry_report_bag_making_json_hygiene');
	Route::get('/production-ent-report-bag-making-json-update-stock', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_json_update_stock'])->name('production_entry_report_bag_making_json_update_stock');
	Route::get('/production-ent-report-bag-making-json-update-stock-info', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_json_update_stock_info'])->name('production_entry_report_bag_making_json_update_stock_info');
	
	Route::get('/production-entry-report-bag-making-update-stock/{id}', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_update_stock'])->name('production_entry_report_bag_making_update_stock');	
	Route::get('/production-entry-report-bag-making-unposted/{id}', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_unposted'])->name('production_entry_report_bag_making_unposted');
	Route::get('/production-ent-report-bag-making-delete/{id}', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_delete'])->name('production_entry_report_bag_making_delete');
	
	Route::get('/production-ent-report-bag-making-print/{id}', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_print'])->name('production_entry_report_bag_making_print');	
	
	Route::post('/production-entry-report-bag-making-wrap-add', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_wrap_add'])->name('production_entry_report_bag_making_wrap_add');
	Route::post('/production-entry-report-bag-making-wrap-edit', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_wrap_edit'])->name('production_entry_report_bag_making_wrap_edit');
	Route::post('/production-entry-report-bag-making-wrap-delete', [ProductionReportBagMakingController::class, 'production_entry_report_bag_making_wrap_delete'])->name('production_entry_report_bag_making_wrap_delete');
	//END REPORT BAG MAKING
	
    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    

     // PPIC dev_hafidz
    // include __DIR__.'/ppic/workOrder.php';
});