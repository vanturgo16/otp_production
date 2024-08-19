@extends('layouts.master')

@section('konten')
<div class="page-content">
    <div class="container-fluid">
    @csrf
		
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <!--h4 class="mb-sm-0 font-size-18"> Add Request Sparepart & Auxiliaries</h4-->
					<a href="/production-ent-report-slitting" class="btn btn-dark waves-effect waves-light mb-3"> 
					<i class="bx bx-list-ul" title="Back"></i> REPORT SLITTING</a>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Production</a></li>
                            <li class="breadcrumb-item active"> Add Report Slitting</li>
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
		@if (session('pesan_danger'))
            <div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                <i class="mdi mdi-alert-octagon-outline label-icon"></i><strong>Dangers</strong> - {{ session('pesan_danger') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
		@if(!empty($data[0]))
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Report Slitting</h4>
                        <!--  <p class="card-title-desc"> layout options : from inline, horizontal & custom grid implementations</p> -->
                    </div>
                    <div class="card-body p-4">

						<div class="col-sm-12">
                            <div class="mt-4 mt-lg-0">
								<form method="post" action="/production-ent-report-slitting-update" class="form-material m-t-40" enctype="multipart/form-data">
								@csrf
									<input type="hidden" class="form-control" name="request_id" value="{{ Request::segment(2) }}">
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Report Number</label>
										<div class="col-sm-9">
											<input type="text" name="report_number" class="form-control" value="{{ $data[0]->report_number }}" readonly>
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
												url: "/json_get_customer",
												data: { id_master_customers : {!! $data[0]->id_master_customers !!} },
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
												data: { data_work_center : {!! $data[0]->id_master_work_centers !!}, id_master_process_productions : '4' },
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
									<div class="row mb-4 field-wrapper">
										<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Engine Shutdown Description </label>
										<div class="col-sm-9">
											<textarea rows="5" class="form-control" name="engine_shutdown_description">{{ $data[0]->engine_shutdown_description }}</textarea>
											@if($errors->has('engine_shutdown_description'))
												<div class="text-danger"><b>{{ $errors->first('engine_shutdown_description') }}</b></div>
											@endif
										</div>
									</div> 	
									<div class="row mb-4 field-wrapper">
										<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Note </label>
										<div class="col-sm-9">
											<textarea rows="5" class="form-control" name="note">{{ $data[0]->note }}</textarea>
											@if($errors->has('note'))
												<div class="text-danger"><b>{{ $errors->first('note') }}</b></div>
											@endif
										</div>
									</div> 	
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
						<form method="post" action="/production-ent-report-slitting-update" class="form-material m-t-40" enctype="multipart/form-data">
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
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Press Roll <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_press_roll" value="Ok"
										{{ $data_detail_preparation[0]->press_roll=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_press_roll" value="Not Ok"
										{{ $data_detail_preparation[0]->press_roll=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('pc_press_roll'))
										<div class="text-danger"><b>{{ $errors->first('pc_press_roll') }}</b></div>
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
						<form method="post" action="/production-ent-report-slitting-update" class="form-material m-t-40" enctype="multipart/form-data">
						@csrf
							<input type="hidden" class="form-control" name="request_id" value="{{ Request::segment(2) }}">
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Press Rubber Roll <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_press_rubber_roll" value="Ok"
										{{ $data_detail_hygiene[0]->press_rubber_roll=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_press_rubber_roll" value="Not Ok"
										{{ $data_detail_hygiene[0]->press_rubber_roll=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('hc_press_rubber_roll'))
										<div class="text-danger"><b>{{ $errors->first('hc_press_rubber_roll') }}</b></div>
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
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Cutter <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_cutter" value="Ok"
										{{ $data_detail_hygiene[0]->cutter=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_cutter" value="Not Ok"
										{{ $data_detail_hygiene[0]->cutter=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('hc_cutter'))
										<div class="text-danger"><b>{{ $errors->first('hc_cutter') }}</b></div>
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
										<form method="post" action="/production-entry-report-slitting-detail-production-result-add" class="form-material m-t-40" enctype="multipart/form-data">
											@csrf
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Start Time </label>
												<div class="col-sm-8">
													<input type="time" class="form-control" name="start" id="start" value="07:30">
													<div id="displayHours_start" class="text-danger"></div>
													@if($errors->has('start'))
														<div class="text-danger"><b>{{ $errors->first('start') }}</b></div>
													@endif
													<script>
														document.getElementById('start').addEventListener('input', function() {
															let timeValue = this.value;
															let timeParts = timeValue.split(':');
															let hours = parseInt(timeParts[0], 10);
															let minutes = timeParts[1];
															let displayHours = (hours === 0) ? 24 : hours;

															document.getElementById('displayHours_start').textContent = `Konversi Waktu Lokal : ${displayHours}:${minutes}`;
														});
													</script>
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Finish Time </label>
												<div class="col-sm-8">
													<input type="time" class="form-control" name="finish" id="finish" value="07:30">
													<div id="displayHours_finish" class="text-danger"></div>
													@if($errors->has('finish'))
														<div class="text-danger"><b>{{ $errors->first('finish') }}</b></div>
													@endif
													<script>
														document.getElementById('finish').addEventListener('input', function() {
															let timeValue = this.value;
															let timeParts = timeValue.split(':');
															let hours = parseInt(timeParts[0], 10);
															let minutes = timeParts[1];
															let displayHours = (hours === 0) ? 24 : hours;

															document.getElementById('displayHours_finish').textContent = `Konversi Waktu Lokal : ${displayHours}:${minutes}`;
														});
													</script>
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Barcode Start</label>
												<div class="col-sm-8">
													<select class="form-select data-select2" name="id_master_barcode_start" id="id_master_barcode_start">
														<option value="">** Please Select A Barcodes</option>
													</select>
													@if($errors->has('id_master_barcode_start'))
														<div class="text-danger"><b>{{ $errors->first('id_master_barcode_start') }}</b></div>
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
														data: { where : 'SLITTING START' },
														dataType: "json",
														beforeSend: function(e) {
															if(e && e.overrideMimeType) {
																e.overrideMimeType("application/json;charset=UTF-8");
															}
														},
														success: function(response){
															$("#id_master_barcode_start").html(response.list_barcode).show();
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
												<label for="horizontal-password-input" class="col-sm-4 col-form-label">Work Orders </label>
												<div class="col-sm-8">
													<select class="form-select data-select2" name="id_work_orders" id="id_work_orders" required>
														<option value="">** Please Select A Work Orders</option>
														@foreach ($ms_work_orders as $data)
															<option value="{{ $data->id }}" data-id_master_customers="{{ $data->id_master_customers }}" data-type_product="{{ $data->type_product }}" data-id_master_products="{{ $data->id_master_products }}">{{ $data->wo_number }}</option>
														@endforeach
													</select>
													@if($errors->has('id_work_orders'))
														<div class="text-danger"><b>{{ $errors->first('id_work_orders') }}</b></div>
													@endif
												</div>
											</div>
											<script>									
												$(document).ready(function(){
													
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
													});
													
												});
											</script>
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Product Info </label>
												<div class="col-sm-8">
													<select class="form-select data-select2" name="id_master_products" id="id_master_products">
														<option value="">** Please Select A Products</option>
													</select>
													@if($errors->has('id_master_products'))
														<div class="text-danger"><b>{{ $errors->first('id_master_products') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Barcode </label>
												<div class="col-sm-8">
													<select class="form-select data-select2" name="id_master_barcode" id="id_master_barcode">
														<option value="">** Please Select A Barcodes</option>
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
														data: { where : 'SLITTING' },
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
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Waste </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="waste">
													@if($errors->has('waste'))
														<div class="text-danger"><b>{{ $errors->first('waste') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Cause Waste </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="cause_waste">
													@if($errors->has('cause_waste'))
														<div class="text-danger"><b>{{ $errors->first('cause_waste') }}</b></div>
													@endif
												</div>
											</div>
											<input type="hidden" class="form-control" name="request_id" value="{{ Request::segment(2) }}">
											
											
											
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
										@if(!empty($data_detail_production[0]))
											<div class="table-responsive">
												<table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
													<thead>
														<tr>
														<tr>
															<th width="20%">Start / Finish</th>
															<th width="40%">Product Info</th>
															<th width="20%">Weight Info</th>
															<th width="10%">Aksi</th>
														</tr>
													</thead>
													<tbody>
														@foreach ($data_detail_production as $data_detail)
														<tr>
															<td>
																At : <b>{{ $data_detail->start_time }}</b> /												
																Until : <b>{{ $data_detail->finish_time }}</b>
															</td>
															<?php $product = explode('|', $data_detail->note) ; ?>
															<td><p>
																Barcode Start : <b>{{ $data_detail->barcode_start }}</b><br>
																Barcode : <b>{{ $data_detail->barcode }}</b><br><br>
																Work Orders : <b>{{ $data_detail->wo_number }}</b><br>
																<code>Type Result : <b>{{ $data_detail->type_result }}</b></code><br>
																<footer class="blockquote-footer">Product : <cite><b>{{ $product['2'] }}</b></cite></footer></p>
															</td>
															<?php 
																if($data_detail->status=="Good"){
																	$colors = "success";
																}else if($data_detail->status=="Hold"){
																	$colors = "warning";
																}else{
																	$colors = "danger";
																}
															
															?>
															<td>
																Thickness : <b>{{ $data_detail->thickness }}</b> <br>
																Length : <b>{{ $data_detail->length }}</b> <br>
																Width : <b>{{ $data_detail->width }}</b> <br>
																Weight : <b>{{ $data_detail->weight }}</b> <br><br>
																Status : <b class="text-{{ $colors }}">{{ $data_detail->status }}</b> <br>
															</td>
															
															
															<td>	
																<center>
																	<form action="/production-entry-report-slitting-detail-production-result-delete" method="post" class="d-inline" enctype="multipart/form-data">
																		@csrf		
																		<input type="hidden" class="form-control" name="token_rs" value="{{ Request::segment(2) }}">
																		<button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure to delete this item ?')" value="{{ sha1($data_detail->id) }}" name="hapus_detail">
																			<i class="bx bx-trash-alt" title="Delete" ></i>
																		</button>
																	</form>	
																	<a href="/production-entry-report-slitting-detail-production-result-edit/{{ Request::segment(2) }}/{{ sha1($data_detail->id) }}" class="btn btn-info waves-effect waves-light">
																		<i class="bx bx-edit-alt" title="Edit"></i>
																	</a>
																</center>											
															</td>
														 
														</tr>
														@endforeach
													</tbody>
												</table>
											</div>
										@else
											<div class="row">
												<div class="col-lg-12 text-center">
													<label>Data Tidak Tersedia</label>
												</div>
											</div>
											
										@endif
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
		
		
		
    
                    <!-- end row -->
    </div>
</div>

@endsection