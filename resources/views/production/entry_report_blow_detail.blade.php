@extends('layouts.master')

@section('konten')
<div class="page-content">
    <div class="container-fluid">
    @csrf
		
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <!--h4 class="mb-sm-0 font-size-18"> Add Request Sparepart & Auxiliaries</h4-->
					<a href="/production-ent-report-blow" class="btn btn-dark waves-effect waves-light mb-3"> 
					<i class="bx bx-list-ul" title="Back"></i> REPORT BLOW</a>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Production</a></li>
                            <li class="breadcrumb-item active"> Add Report Blow</li>
                        </ol>
                    </div>
                </div>
                
            </div>
        </div>
		@if (session('pesan'))
            <div class="alert alert-success alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                <i class="mdi mdi-check-all label-icon"></i><strong>Success</strong> - {{ session('pesan') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
		@if(!empty($data[0]))
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Report Blow</h4>
                        <!--  <p class="card-title-desc"> layout options : from inline, horizontal & custom grid implementations</p> -->
                    </div>
                    <div class="card-body p-4">

						<div class="col-sm-12">
                            <div class="mt-4 mt-lg-0">
								<form method="post" action="/production-ent-report-blow-update" class="form-material m-t-40" enctype="multipart/form-data">
								@csrf
									<input type="hidden" class="form-control" name="request_id" value="{{ Request::segment(2) }}">
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Report Number</label>
										<div class="col-sm-9">
											<input type="text" name="report_number" class="form-control" value="{{ $data[0]->report_number }}" readonly>
										</div>
									</div>
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-password-input" class="col-sm-3 col-form-label">Work Orders </label>
										<div class="col-sm-9">
											<select class="form-select data-select2" name="id_work_orders" id="id_work_orders" required>
												<option value="">** Please Select A Work Orders</option> 
												@foreach ($ms_work_orders as $data_for)
													<option value="{{ $data_for->id }}" data-id_master_customers="{{ $data[0]->id_master_customers }}" data-type_product="{{ explode('|', $data[0]->order_name)[0] }}" data-id_master_products="{{ explode('|', $data[0]->order_name)[1] }}" {{ $data_for->id == $data[0]->id_work_orders ? 'selected' : '' }}>{{ $data_for->wo_number }}</option>
												@endforeach
											</select>
											@if($errors->has('id_work_orders'))
												<div class="text-danger"><b>{{ $errors->first('id_work_orders') }}</b></div>
											@endif
										</div>
									</div>
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-email-input" class="col-sm-3 col-form-label">Date</label>
										<div class="col-sm-9">
											<input type="date" name="date" class="form-control" value="{{ $data[0]->date }}">
											@if($errors->has('date'))
												<div class="text-danger"><b>{{ $errors->first('date') }}</b></div>
											@endif
										</div>
									</div>
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Order Name </label>
										<div class="col-sm-9">
											<select class="form-select data-select2" name="id_master_products" id="id_master_products">
												<option value="">** Please Select A Products</option>
											</select>
											@if($errors->has('id_master_products'))
												<div class="text-danger"><b>{{ $errors->first('id_master_products') }}</b></div>
											@endif
										</div>
									</div> 
									
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-password-input" class="col-sm-3 col-form-label">Customers </label>
										<div class="col-sm-9">
											<select class="form-select data-select2" name="id_master_customers" id="id_master_customers">
												<option value="">** Please Select A Customers</option>
											</select>
											@if($errors->has('id_master_customers'))
												<div class="text-danger"><b>{{ $errors->first('id_master_customers') }}</b></div>
											@endif
										</div>
									</div> 
									<div class="row mb-4 field-wrapper">
										<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Type </label>
										<div class="col-sm-9">
											<input type="text" class="form-control" name="type" value="{{ $data[0]->type }}">
											@if($errors->has('type'))
												<div class="text-danger"><b>{{ $errors->first('type') }}</b></div>
											@endif
										</div>
									</div> 								
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-password-input" class="col-sm-3 col-form-label">Work Centers </label>
										<div class="col-sm-9">
											<select class="form-select data-select2" name="id_master_work_centers" id="id_master_work_centers" required>
												<option value="">** Please Select A Work Centers</option>
											</select>
											@if($errors->has('id_master_work_centers'))
												<div class="text-danger"><b>{{ $errors->first('id_master_work_centers') }}</b></div>
											@endif
										</div>
									</div>
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-password-input" class="col-sm-3 col-form-label">Regu  </label>
										<div class="col-sm-9">
											<select class="form-select data-select2" name="id_master_regus" id="id_master_regus" required>
												<option value="">** Please Select A Regu</option>											
											</select>
											@if($errors->has('id_master_regus'))
												<div class="text-danger"><b>{{ $errors->first('id_master_regus') }}</b></div>
											@endif
										</div>
									</div>
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-password-input" class="col-sm-3 col-form-label">Shift  </label>
										<div class="col-sm-9">
											<select class="form-select data-select2" name="shift" id="shift" required>
												<option value="">** Please Select A Shift</option>
													<option value="1" {{ $data[0]->shift=='1'?'selected':'' }}>1</option>
													<option value="2" {{ $data[0]->shift=='2'?'selected':'' }}>2</option>
													<option value="3" {{ $data[0]->shift=='3'?'selected':'' }}>3</option>
											</select>
											@if($errors->has('shift'))
												<div class="text-danger"><b>{{ $errors->first('shift') }}</b></div>
											@endif
										</div>
									</div>	
										
									<script>									
										$(document).ready(function(){
											//$('#id_work_orders').prop('selectedIndex', 0);
											//$('#id_master_work_centers').prop('selectedIndex', 0);
											//$('#id_master_regus').prop('selectedIndex', 0);
											//$('#shift').prop('selectedIndex', 0);
											
											$.ajax({
												type: "GET",
												url: "/json_get_produk",
												data: { type_product : {!! "'".explode('|', $data[0]->order_name)[0]."'" !!}, id_master_products : {!! explode('|', $data[0]->order_name)[1] !!} },
												dataType: "json",
												beforeSend: function(e) {
													if(e && e.overrideMimeType) {
														e.overrideMimeType("application/json;charset=UTF-8");
													}
												},
												success: function(response){
													$("#id_master_products").html(response.list_products).show();
													//$('#id_master_regus').prop('selectedIndex', 0);
													//$('#shift').prop('selectedIndex', 0);
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
												}
											});	
											
											
											$.ajax({
												type: "GET",
												url: "/json_get_customer",
												data: { id_master_customers : $('#id_work_orders option:selected').attr('data-id_master_customers') },
												dataType: "json",
												beforeSend: function(e) {
													if(e && e.overrideMimeType) {
														e.overrideMimeType("application/json;charset=UTF-8");
													}
												},
												success: function(response){
													$("#id_master_customers").html(response.list_customers).show();
													//$('#id_master_regus').prop('selectedIndex', 0);
													//$('#shift').prop('selectedIndex', 0);
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
												}
											});
										
											$.ajax({
												type: "GET",
												url: "/json_get_work_center",
												data: { data_work_center : {!! $data[0]->id_master_work_centers !!} },
												dataType: "json",
												beforeSend: function(e) {
													if(e && e.overrideMimeType) {
														e.overrideMimeType("application/json;charset=UTF-8");
													}
												},
												success: function(response){
													$("#id_master_work_centers").html(response.list_work_center).show();
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
												}
											});
											
											$.ajax({
												type: "GET",
												url: "/json_get_regu",
												data: { id_master_work_centers : {!! $data[0]->id_master_work_centers !!}, data_regus : {!! $data[0]->id_master_regus !!} },
												dataType: "json",
												beforeSend: function(e) {
													if(e && e.overrideMimeType) {
														e.overrideMimeType("application/json;charset=UTF-8");
													}
												},
												success: function(response){
													$("#id_master_regus").html(response.list_regu).show();
													//$('#shift').prop('selectedIndex', 0);
												},
												error: function (xhr, ajaxOptions, thrownError) {
													alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
												}
											});
											
											$("#id_work_orders").change(function(){		
												
												$.ajax({
													type: "GET",
													url: "/json_get_produk",
													data: { type_product : $('#id_work_orders option:selected').attr('data-type_product'), id_master_products : $('#id_work_orders option:selected').attr('data-id_master_products') },
													dataType: "json",
													beforeSend: function(e) {
														if(e && e.overrideMimeType) {
															e.overrideMimeType("application/json;charset=UTF-8");
														}
													},
													success: function(response){
														$("#id_master_products").html(response.list_products).show();
													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
													}
												});
												
												
												$.ajax({
													type: "GET",
													url: "/json_get_customer",
													data: { id_master_customers : $('#id_work_orders option:selected').attr('data-id_master_customers') },
													dataType: "json",
													beforeSend: function(e) {
														if(e && e.overrideMimeType) {
															e.overrideMimeType("application/json;charset=UTF-8");
														}
													},
													success: function(response){
														$("#id_master_customers").html(response.list_customers).show();
													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
													}
												});
												
												$.ajax({
													type: "GET",
													url: "/json_get_work_center",
													data: { id_master_process_productions : $('#id_work_orders option:selected').attr('data-id_master_process_productions') },
													dataType: "json",
													beforeSend: function(e) {
														if(e && e.overrideMimeType) {
															e.overrideMimeType("application/json;charset=UTF-8");
														}
													},
													success: function(response){
														$("#id_master_work_centers").html(response.list_work_center).show();
														$('#id_master_regus').prop('selectedIndex', 0);
														$('#shift').prop('selectedIndex', 0);
													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
													}
												});
											});
											
											$("#id_master_work_centers").change(function(){										
												$.ajax({
													type: "GET",
													url: "/json_get_regu",
													data: { id_master_work_centers : $("#id_master_work_centers").val() },
													dataType: "json",
													beforeSend: function(e) {
														if(e && e.overrideMimeType) {
															e.overrideMimeType("application/json;charset=UTF-8");
														}
													},
													success: function(response){
														$("#id_master_regus").html(response.list_regu).show();
														$('#shift').prop('selectedIndex', 0);
													},
													error: function (xhr, ajaxOptions, thrownError) {
														alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
													}
												});
											});
											
											$("#id_master_regus").change(function(){	
												$('#shift').prop('selectedIndex', 0);
											});
											
										});
									</script>
									
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-password-input" class="col-sm-3 col-form-label">Known By </label>
										<div class="col-sm-9">
											<select class="form-select data-select2" name="id_known_by" id="id_known_by" required>
												<option value="">** Please Select A Known By</option>
												@foreach ($ms_known_by as $data_for)
													<option value="{{ $data_for->id }}" {{ $data_for->id == $data[0]->know_by ? 'selected' : '' }} >{{ $data_for->name}}</option>
												@endforeach
											</select>
											@if($errors->has('id_known_by'))
												<div class="text-danger"><b>{{ $errors->first('id_known_by') }}</b></div>
											@endif
										</div>
									</div>	
									
									
									<div class="row justify-content-end">
										<div class="col-sm-9">
											<div>											
												<button type="submit" class="btn btn-success w-md" name="rb_update"><i class="bx bx-save" title="Back"></i> UPDATE</button>
											</div>
										</div>
									</div>                        
								</form> 
                            </div>
							
                        </div>
                    </div>
                </div>
            </div>
        </div> 
		<div class="row">
			<div class="col-lg-6">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><i data-feather="check-square"></i> Preparation Check</h4>
					</div>
					<div class="card-body p-4">
						<form method="post" action="/production-ent-report-blow-update" class="form-material m-t-40" enctype="multipart/form-data">
						@csrf
							<input type="hidden" class="form-control" name="request_id" value="{{ Request::segment(2) }}">
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Material <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_material" value="Ok"
										{{ $data_detail_preparation[0]->material=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_material" value="Not Ok"
										{{ $data_detail_preparation[0]->material=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('pc_material'))
										<div class="text-danger"><b>{{ $errors->first('pc_material') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Ukuran <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_ukuran" value="Ok"
										{{ $data_detail_preparation[0]->ukuran=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_ukuran" value="Not Ok"
										{{ $data_detail_preparation[0]->ukuran=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('pc_ukuran'))
										<div class="text-danger"><b>{{ $errors->first('pc_ukuran') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Ratio Camp Resin <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_ratio_camp_resin" value="Ok"
										{{ $data_detail_preparation[0]->ratio_camp_resin=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_ratio_camp_resin" value="Not Ok"
										{{ $data_detail_preparation[0]->ratio_camp_resin=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('pc_ratio_camp_resin'))
										<div class="text-danger"><b>{{ $errors->first('pc_ratio_camp_resin') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Temp Heater <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_temp_heater" value="Ok"
										{{ $data_detail_preparation[0]->temp_heater=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_temp_heater" value="Not Ok"
										{{ $data_detail_preparation[0]->temp_heater=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('pc_temp_heater'))
										<div class="text-danger"><b>{{ $errors->first('pc_temp_heater') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Guide Roll <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_guide_roll" value="Ok"
										{{ $data_detail_preparation[0]->guide_roll=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_guide_roll" value="Not Ok"
										{{ $data_detail_preparation[0]->guide_roll=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('pc_guide_roll'))
										<div class="text-danger"><b>{{ $errors->first('pc_guide_roll') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Rubber Roll <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_rubber_roll" value="Ok"
										{{ $data_detail_preparation[0]->rubber_roll=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_rubber_roll" value="Not Ok"
										{{ $data_detail_preparation[0]->rubber_roll=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('pc_rubber_roll'))
										<div class="text-danger"><b>{{ $errors->first('pc_rubber_roll') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Saringan Resin <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_saringan_resin" value="Ok"
										{{ $data_detail_preparation[0]->saringan_resin=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_saringan_resin" value="Not Ok"
										{{ $data_detail_preparation[0]->saringan_resin=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('pc_saringan_resin'))
										<div class="text-danger"><b>{{ $errors->first('pc_saringan_resin') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row justify-content-end">
								<div class="col-sm-3">
									<div>											
										<button type="submit" class="btn btn-success w-md" name="pc_update"><i class="bx bx-save" title="Back"></i> UPDATE</button>
									</div>
								</div>
							</div>
						</form> 
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><i data-feather="check-square"></i> Hygiene Check</h4>
						
					</div>
					<div class="card-body p-4">
						<form method="post" action="/production-ent-report-blow-update" class="form-material m-t-40" enctype="multipart/form-data">
						@csrf
							<input type="hidden" class="form-control" name="request_id" value="{{ Request::segment(2) }}">
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Guide Rubber Roll <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_guide_rubber_roll" value="Ok"
										{{ $data_detail_hygiene[0]->guide_rubber_roll=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_guide_rubber_roll" value="Not Ok"
										{{ $data_detail_hygiene[0]->guide_rubber_roll=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('hc_guide_rubber_roll'))
										<div class="text-danger"><b>{{ $errors->first('hc_guide_rubber_roll') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Bak Resin <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_bak_resin" value="Ok"
										{{ $data_detail_hygiene[0]->bak_resin=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_bak_resin" value="Not Ok"
										{{ $data_detail_hygiene[0]->bak_resin=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('hc_bak_resin'))
										<div class="text-danger"><b>{{ $errors->first('hc_bak_resin') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Mixer Resin <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_mixer_resin" value="Ok"
										{{ $data_detail_hygiene[0]->mixer_resin=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_mixer_resin" value="Not Ok"
										{{ $data_detail_hygiene[0]->mixer_resin=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('hc_mixer_resin'))
										<div class="text-danger"><b>{{ $errors->first('hc_mixer_resin') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Ember Resin <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_ember_resin" value="Ok"
										{{ $data_detail_hygiene[0]->ember_resin=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_ember_resin" value="Not Ok"
										{{ $data_detail_hygiene[0]->ember_resin=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('hc_ember_resin'))
										<div class="text-danger"><b>{{ $errors->first('hc_ember_resin') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Body Mesin <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_body_mesin" value="Ok"
										{{ $data_detail_hygiene[0]->body_mesin=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_body_mesin" value="Not Ok"
										{{ $data_detail_hygiene[0]->body_mesin=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('hc_body_mesin'))
										<div class="text-danger"><b>{{ $errors->first('hc_body_mesin') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Lantai Mesin <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_lantai_mesin" value="Ok"
										{{ $data_detail_hygiene[0]->lantai_mesin=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_lantai_mesin" value="Not Ok"
										{{ $data_detail_hygiene[0]->lantai_mesin=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('hc_lantai_mesin'))
										<div class="text-danger"><b>{{ $errors->first('hc_lantai_mesin') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row justify-content-end">
								<div class="col-sm-3">
									<div>											
										<button type="submit" class="btn btn-success w-md" name="hc_update"><i class="bx bx-save" title="Back"></i> UPDATE</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><i data-feather="check-square"></i> Production Result</h4>
						
					</div>
					<div class="card-body p-4">
						<div class="row">
							<div class="col-lg-5">
								<div class="card">
									<div class="card-header">
										<h4 class="card-title">Form</h4>
										
									</div>
									<div class="card-body p-4">
										<form method="post" action="/production-entry-material-use-detail-add" class="form-material m-t-40" enctype="multipart/form-data">
											@csrf
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Start Time </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="start">
													@if($errors->has('start'))
														<div class="text-danger"><b>{{ $errors->first('start') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Finish Time </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="finish">
													@if($errors->has('finish'))
														<div class="text-danger"><b>{{ $errors->first('finish') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Barcode </label>
												<div class="col-sm-8">
													<select class="form-select data-select2" name="id_master_barcode" id="id_master_barcode">
														<option value="">** Please Select A Customers</option>
													</select>
													@if($errors->has('id_master_barcode'))
														<div class="text-danger"><b>{{ $errors->first('id_master_barcode') }}</b></div>
													@endif
												</div>
											</div> 
											<script>									
												$(document).ready(function(){
													//$('#id_work_orders').prop('selectedIndex', 0);
													//$('#id_master_work_centers').prop('selectedIndex', 0);
													//$('#id_master_regus').prop('selectedIndex', 0);
													//$('#shift').prop('selectedIndex', 0);
													$.ajax({
														type: "GET",
														url: "/json_get_barcode",
														//data: { id_master_customers : $('#id_work_orders option:selected').attr('data-id_master_customers') },
														dataType: "json",
														beforeSend: function(e) {
															if(e && e.overrideMimeType) {
																e.overrideMimeType("application/json;charset=UTF-8");
															}
														},
														success: function(response){
															$("#id_master_barcode").html(response.list_barcode).show();
															//$('#id_master_regus').prop('selectedIndex', 0);
															//$('#shift').prop('selectedIndex', 0);
														},
														error: function (xhr, ajaxOptions, thrownError) {
															alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
														}
													});
												});
											</script>	
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Thickness </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="thickness">
													@if($errors->has('thickness'))
														<div class="text-danger"><b>{{ $errors->first('thickness') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Length </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="length">
													@if($errors->has('length'))
														<div class="text-danger"><b>{{ $errors->first('length') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Width </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="width">
													@if($errors->has('width'))
														<div class="text-danger"><b>{{ $errors->first('width') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Weight </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="weight">
													@if($errors->has('weight'))
														<div class="text-danger"><b>{{ $errors->first('weight') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label"><br>Status </label>
												<div class="col-sm-8">
													<div class="form-check">
														<input class="form-check-input" type="radio" name="status" value="Good">
														<label>
															Good
														</label>
													</div>
													<div class="form-check">
														<input class="form-check-input" type="radio" name="status" value="Hold">
														<label>
															Hold
														</label>
													</div>
													<div class="form-check">
														<input class="form-check-input" type="radio" name="status" value="Reject">
														<label>
															Reject
														</label>
													</div>
													@if($errors->has('status'))
														<div class="text-danger"><b>{{ $errors->first('status') }}</b></div>
													@endif
												</div>
											</div> 
											
											<input id="request_id_original" type="hidden" class="form-control" name="request_id" value="{{ Request::segment(2) }}">
											
											
											
											<div class="row justify-content-end">
												<div class="col-sm-12">
													<div>
														<button type="reset" class="btn btn-secondary w-md"><i class="bx bx-refresh" title="Reset"></i> RESET</button>
														<button type="submit" class="btn btn-primary w-md" name="save"><i class="bx bx-list-plus" title="Add"></i> ADD to TABLE DETAIL</button>
													</div>
												</div>
											</div>
										</form> 
									</div>
								</div>
							</div>
							<div class="col-lg-7">
								<div class="card">
									<div class="card-header">
										<h4 class="card-title">Table Detail</h4>
										
									</div>
									<div class="card-body p-4">
									</div>
								</div>
							</div>
						</div>
						
						@else
							<div class="row">
								<div class="col-lg-12 text-center">
									<div class="card">
										<div class="card-body">
											<label>Data Tidak Tersedia</label>
										</div>
									</div>
								</div>
							</div>
							
						@endif
						
						
                    </div>
					
					
					
					
				</div>
			</div>
		</div> 
		
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><i data-feather="check-square"></i> Waste</h4>
						
					</div>
					<div class="card-body p-4">
						<div class="row">
							<div class="col-lg-5">
								<div class="card">
									<div class="card-header">
										<h4 class="card-title">Form</h4>
										
									</div>
									<div class="card-body p-4">
										<form method="post" action="/production-entry-material-use-detail-add" class="form-material m-t-40" enctype="multipart/form-data">
											@csrf
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Waste </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="waste">
													@if($errors->has('waste'))
														<div class="text-danger"><b>{{ $errors->first('waste') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Cause Waste </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="cause_waste">
													@if($errors->has('cause_waste'))
														<div class="text-danger"><b>{{ $errors->first('cause_waste') }}</b></div>
													@endif
												</div>
											</div> 											
											
											<input id="request_id_original" type="hidden" class="form-control" name="request_id" value="{{ Request::segment(2) }}">
											
											
											
											
											
											<!--
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-password-input" class="col-sm-3 col-form-label">Barcode  </label>
												<div class="col-sm-9">
													<select class="form-select data-select2" name="id_barcode" id="id_barcode" required>
														<option value="">** Please Select A Barcode</option>
														
													</select>
													@if($errors->has('id_barcode'))
														<div class="text-danger"><b>{{ $errors->first('id_barcode') }}</b></div>
													@endif
													<input type="hidden" name="token" id="token">
													<input type="hidden" name="token_rmu" id="token_rmu" class="form-control" value="{{ Request::segment(2) }}">
													<input type="hidden" name="sisa_ext" id="sisa_ext">
												</div>
											</div>	
											<script>
												$(document).ready(function(){
													konten = '<div class="alert alert-dark alert-dismissible alert-label-icon label-arrow fade show" role="alert"><i class="mdi mdi-alert-outline label-icon"></i><font class="text-white">Informasi Tidak Tersedia</font></div>';
													$( "#div-informasi" ).html( konten );
													
													$("#id_barcode").change(function(){
														konten_remaining = '<div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert"><i class="mdi mdi-alert-outline label-icon"></i><font class="text-white">Informasi Tidak Tersedia</font></div>';
														$( "#div-info-remaining" ).html( konten_remaining );
													
														document.getElementById('token').value = $('#id_barcode option:selected').attr('data-id');
														document.getElementById('sisa_ext').value = $('#id_barcode option:selected').attr('data-sisa');
														document.getElementById('taking').value = '';
														document.getElementById('usage').value = '';
														
														$.ajax({
															type: "GET",
															url: "/json_get_material_info",
															data: { lot_number : $('#id_barcode option:selected').attr('data-lot_number') },
															
															dataType: "json",
															beforeSend: function(e) {
																if(e && e.overrideMimeType) {
																	e.overrideMimeType("application/json;charset=UTF-8");
																}
															},
															success: function(response){
																konten = '<div class="alert alert-dark alert-dismissible alert-label-icon label-arrow fade show" role="alert"><i class="mdi mdi-alert-outline label-icon"></i><strong><font id="sisa_camp">'+response.stok_ext_all+'</font></strong> Kg - <font class="text-white">Sisa material secara keseluruhan</font></div>';
																$( "#div-informasi" ).html( konten );
															},
															error: function (xhr, ajaxOptions, thrownError) {
																konten = '<div class="alert alert-dark alert-dismissible alert-label-icon label-arrow fade show" role="alert"><i class="mdi mdi-alert-outline label-icon"></i><font class="text-white">Informasi Tidak Tersedia</font></div>';
																$( "#div-informasi" ).html( konten );
																alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
															}
														});
														
													});
													
												});
											</script>
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-firstname-input" class="col-sm-3 col-form-label"></label>
												<div class="col-sm-9">
													<div id="div-informasi"></div>
												</div>
											</div>
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Taking</label>
												<div class="col-sm-9">
													<input class="form-control" name="taking" id="taking" type="text">
													<p class="card-title-desc"><code>.Kg - Catatan : Jumlah taking tidak boleh lebih banyak dari <strong>( Sisa Per EXT )</strong>.</code></p>
													@if($errors->has('taking'))
														<div class="text-danger"><b>{{ $errors->first('taking') }}</b></div>
													@endif
												</div>
											</div>
											<script>
												$(document).ready(function(){
													$("#taking").keyup(function() {
														var n_taking = parseFloat(document.getElementById('taking').value);
														var n_sisa_ext = parseFloat(document.getElementById('sisa_ext').value);

														if (!isNaN(n_taking) && !isNaN(n_sisa_ext) && n_taking>n_sisa_ext) {
															document.getElementById('taking').value = n_sisa_ext;
														}
													});
												});
											</script>
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Usage</label>
												<div class="col-sm-9">
													<input type="text" class="form-control" name="usage" id="usage">
													<p class="card-title-desc"><code>.Kg</code></p>
													@if($errors->has('usage'))
														<div class="text-danger"><b>{{ $errors->first('usage') }}</b></div>
													@endif
												</div>
											</div>
											<script>
												$(document).ready(function(){
													$("#usage").keyup(function() {
														var n_taking = parseFloat(document.getElementById('taking').value);
														var n_usage = parseFloat(document.getElementById('usage').value);

														if (!isNaN(n_taking) && !isNaN(n_usage) && n_usage > n_taking) {
															document.getElementById('usage').value = n_taking;
														}
													});
												});
											</script>
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Sisa Campuran</label>
												<div class="col-sm-9">
													<input class="form-control" name="sisa_campuran" id="sisa_campuran" type="text">
													<p class="card-title-desc"><code>.Kg</code></p>
													@if($errors->has('sisa_campuran'))
														<div class="text-danger"><b>{{ $errors->first('sisa_campuran') }}</b></div>
													@endif
												</div>
											</div>
											<script>
												$(document).ready(function(){
													$("#sisa_campuran").keyup(function() {
														var n_taking = parseFloat(document.getElementById('taking').value);
														var n_sisa_campuran = parseFloat(document.getElementById('sisa_campuran').value);

														if (!isNaN(n_taking) && !isNaN(n_sisa_campuran) && n_sisa_campuran > n_taking) {
															document.getElementById('sisa_campuran').value = n_taking;
														}
													});
												});
											</script>
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Remaining</label>
												<div class="col-sm-9">
													<div id="div-info-remaining"></div>
												</div>
											</div>
											<script>
												$(document).ready(function(){
													konten = '<div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert"><i class="mdi mdi-alert-outline label-icon"></i><font class="text-white">Informasi Tidak Tersedia</font></div>';
													$( "#div-info-remaining" ).html( konten );
													
													var usage = document.getElementById('usage');
													var sisa_campuran = document.getElementById('sisa_campuran');
													
													usage.addEventListener('input', function() {
														var n_taking = parseFloat(document.getElementById('taking').value);
														var n_usage = parseFloat(document.getElementById('usage').value);
														var n_sisa_campuran = parseFloat(document.getElementById('sisa_campuran').value);
														
														var n_sisa_campuran = !isNaN(n_sisa_campuran)?n_sisa_campuran:0;
														
														var n_usage = n_usage > n_taking ? n_taking : n_usage ;
														var n_sisa_campuran = n_sisa_campuran > n_taking ? n_taking : n_sisa_campuran ;
														
														var hasil = ((n_taking-n_usage)+n_sisa_campuran).toFixed(1);
														konten = '<div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert"><i class="mdi mdi-alert-outline label-icon"></i><strong><font>'+hasil+'</font></strong> Kg</div>';
														$( "#div-info-remaining" ).html( konten );
													});
													
													sisa_campuran.addEventListener('input', function() {
														var n_taking = parseFloat(document.getElementById('taking').value);
														var n_usage = parseFloat(document.getElementById('usage').value);
														var n_sisa_campuran = parseFloat(document.getElementById('sisa_campuran').value);
														
														var n_sisa_campuran = !isNaN(n_sisa_campuran)?n_sisa_campuran:0;
														
														var n_usage = n_usage > n_taking ? n_taking : n_usage ;
														var n_sisa_campuran = n_sisa_campuran > n_taking ? n_taking : n_sisa_campuran ;
														
														var hasil = ((n_taking-n_usage)+n_sisa_campuran).toFixed(1);
														konten = '<div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert"><i class="mdi mdi-alert-outline label-icon"></i><strong><font>'+hasil+'</font></strong> Kg</div>';
														$( "#div-info-remaining" ).html( konten );
													});											
													
												});
											</script>
											-->
											<div class="row justify-content-end">
												<div class="col-sm-12">
													<div>
														<button type="reset" class="btn btn-secondary w-md"><i class="bx bx-refresh" title="Reset"></i> RESET</button>
														<button type="submit" class="btn btn-primary w-md" name="save"><i class="bx bx-list-plus" title="Add"></i> ADD to TABLE DETAIL</button>
													</div>
												</div>
											</div>
										</form> 
									</div>
								</div>
							</div>
							<div class="col-lg-7">
								<div class="card">
									<div class="card-header">
										<h4 class="card-title">Table Detail</h4>
										
									</div>
									<div class="card-body p-4">
									</div>
								</div>
							</div>
						</div>
						
						
                    </div>
					
					
					
					
				</div>
			</div>
		</div> 
		
    
                    <!-- end row -->
    </div>
</div>

@endsection