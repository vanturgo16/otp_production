@extends('layouts.master')

@section('konten')
<div class="page-content">
    <div class="container-fluid">
    @csrf
		
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <!--h4 class="mb-sm-0 font-size-18"> Add Request Sparepart & Auxiliaries</h4-->
					<a href="/production-ent-report-bag-making" class="btn btn-dark waves-effect waves-light mb-3"> 
					<i class="bx bx-list-ul" title="Back"></i> REPORT BAG MAKING</a>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Production</a></li>
                            <li class="breadcrumb-item active"> Add Report Bag Making</li>
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
                        <h4 class="card-title">Report Bag Making</h4>
                        <!--  <p class="card-title-desc"> layout options : from inline, horizontal & custom grid implementations</p> -->
                    </div>
                    <div class="card-body p-4">

						<div class="col-sm-12">
                            <div class="mt-4 mt-lg-0">
								<form method="post" action="/production-ent-report-bag-making-update" class="form-material m-t-40" enctype="multipart/form-data">
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
												data: { data_work_center : {!! $data[0]->id_master_work_centers !!}, id_master_process_productions : '1' },
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
										<label for="horizontal-password-input" class="col-sm-3 col-form-label">Ketua Regu </label>
										<div class="col-sm-9">
											<select class="form-select data-select2" name="id_ketua_regu" id="id_ketua_regu" required>
												<option value="">** Please Select A Ketua Regu</option>
												@foreach ($ms_ketua_regu as $data_for)
													<option value="{{ $data_for->id }}" {{ $data_for->id == $data[0]->ketua_regu ? 'selected' : '' }} >{{ $data_for->name}}</option>
												@endforeach
											</select>
											@if($errors->has('id_ketua_regu'))
												<div class="text-danger"><b>{{ $errors->first('id_ketua_regu') }}</b></div>
											@endif
										</div>
									</div>			
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-password-input" class="col-sm-3 col-form-label">Operator </label>
										<div class="col-sm-9">
											<select class="form-select data-select2" name="id_operator" id="id_operator" required>
												<option value="">** Please Select A Operator</option>
												@foreach ($ms_operator as $data_for)
													<option value="{{ $data_for->id }}" {{ $data_for->id == $data[0]->operator ? 'selected' : '' }}>{{ $data_for->name }}</option>
												@endforeach
											</select>
											@if($errors->has('id_operator'))
												<div class="text-danger"><b>{{ $errors->first('id_operator') }}</b></div>
											@endif
											
											<input type="hidden" name="id_cms_user" class="form-control" value="{{ Auth::user()->id }}">
										</div>
									</div>	
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-password-input" class="col-sm-3 col-form-label">Known By </label>
										<div class="col-sm-9">
											<select class="form-select data-select2" name="id_known_by" id="id_known_by" required>
												<option value="">** Please Select A Known By</option>
												@foreach ($ms_known_by as $data_for)
													<option value="{{ $data_for->id }}" {{ $data_for->id == $data[0]->known_by ? 'selected' : '' }} >{{ $data_for->name}}</option>
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
					<div class="card-body p-4" id="preparationCheck">

						<form method="post" action="/production-ent-report-bag-making-update#preparationCheck" class="form-material m-t-40" enctype="multipart/form-data">
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
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Pisau Seal <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_pisau_seal" value="Ok"
										{{ $data_detail_preparation[0]->pisau_seal=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_pisau_seal" value="Not Ok"
										{{ $data_detail_preparation[0]->pisau_seal=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('pc_pisau_seal'))
										<div class="text-danger"><b>{{ $errors->first('pc_pisau_seal') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Jarum Perforasi <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_jarum_perforasi" value="Ok"
										{{ $data_detail_preparation[0]->jarum_perforasi=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_jarum_perforasi" value="Not Ok"
										{{ $data_detail_preparation[0]->jarum_perforasi=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('pc_jarum_perforasi'))
										<div class="text-danger"><b>{{ $errors->first('pc_jarum_perforasi') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Pembuka Plastik <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_pembuka_plastik" value="Ok"
										{{ $data_detail_preparation[0]->pembuka_plastik=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_pembuka_plastik" value="Not Ok"
										{{ $data_detail_preparation[0]->pembuka_plastik=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('pc_pembuka_plastik'))
										<div class="text-danger"><b>{{ $errors->first('pc_pembuka_plastik') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Guide Rubber Roll <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_guide_rubber_roll" value="Ok"
										{{ $data_detail_preparation[0]->guide_rubber_roll=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="pc_guide_rubber_roll" value="Not Ok"
										{{ $data_detail_preparation[0]->guide_rubber_roll=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('pc_guide_rubber_roll'))
										<div class="text-danger"><b>{{ $errors->first('pc_guide_rubber_roll') }}</b></div>
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
					<div class="card-body p-4" id="hygieneCheck">

						<form method="post" action="/production-ent-report-bag-making-update#hygieneCheck" class="form-material m-t-40" enctype="multipart/form-data">
						@csrf
							<input type="hidden" class="form-control" name="request_id" value="{{ Request::segment(2) }}">
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Roll Guide Roll Karet <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_roll_guide_roll_karet" value="Ok"
										{{ $data_detail_hygiene[0]->roll_guide_roll_karet=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_roll_guide_roll_karet" value="Not Ok"
										{{ $data_detail_hygiene[0]->roll_guide_roll_karet=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('hc_roll_guide_roll_karet'))
										<div class="text-danger"><b>{{ $errors->first('hc_roll_guide_roll_karet') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Jarum Perforasi <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_jarum_perforasi" value="Ok"
										{{ $data_detail_hygiene[0]->jarum_perforasi=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_jarum_perforasi" value="Not Ok"
										{{ $data_detail_hygiene[0]->jarum_perforasi=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('hc_jarum_perforasi'))
										<div class="text-danger"><b>{{ $errors->first('hc_jarum_perforasi') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Pisau Seal <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_pisau_seal" value="Ok"
										{{ $data_detail_hygiene[0]->pisau_seal=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_pisau_seal" value="Not Ok"
										{{ $data_detail_hygiene[0]->pisau_seal=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('hc_pisau_seal'))
										<div class="text-danger"><b>{{ $errors->first('hc_pisau_seal') }}</b></div>
									@endif
								</div>
							</div><hr>
							<div class="row mb-3 field-wrapper">
								<label for="horizontal-firstname-input" class="col-sm-6 col-form-label"><i class="mdi mdi-arrow-right text-primary me-1"></i>Pembuka Plastik <code>*</code></label>
								<div class="col-sm-6">
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_pembuka_plastik" value="Ok"
										{{ $data_detail_hygiene[0]->pembuka_plastik=="Ok"?'checked':''; }} >
										<label class="form-check-label">
											OK
										</label>
									</div>
									<div class="form-check">
										<input class="form-check-input" type="radio" name="hc_pembuka_plastik" value="Not Ok"
										{{ $data_detail_hygiene[0]->pembuka_plastik=="Not Ok"?'checked':''; }} >
										<label>
											Not OK
										</label>
									</div>
									@if($errors->has('hc_pembuka_plastik'))
										<div class="text-danger"><b>{{ $errors->first('hc_pembuka_plastik') }}</b></div>
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
					<div class="card-body p-4" id="detailTableSection">
						<div class="row">
							<div class="col-lg-5">
								<div class="card">
									<div class="card-header">
										<h4 class="card-title">Form</h4>										
									</div>
									<div class="card-body p-4">
										<form method="post" action="/production-entry-report-bag-making-detail-production-result-add#detailTableSection" class="form-material m-t-40" enctype="multipart/form-data">
											@csrf
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-password-input" class="col-sm-4 col-form-label">Work Orders </label>
												<div class="col-sm-8">
													<select class="form-select data-select2" name="id_work_orders" id="id_work_orders" required>
														<option value="">** Please Select A Work Orders</option>
														@foreach ($ms_work_orders as $data)
															<option value="{{ $data->id }}" data-id_master_customers="{{ $data->id_master_customers }}" data-type_product="{{ $data->type_product }}" data-id_master_products="{{ $data->id_master_products }}" data-wo_number="{{ $data->wo_number }}">{{ $data->wo_number }}</option>
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
																$("#id_master_products_detail").html(response.list_products).show();
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
													<select class="form-select data-select2" name="id_master_products_detail" id="id_master_products_detail">
														<option value="">** Please Select A Products</option>
													</select>
													@if($errors->has('id_master_products_detail'))
														<div class="text-danger"><b>{{ $errors->first('id_master_products_detail') }}</b></div>
													@endif
												</div>
											</div> 
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
													$.ajax({
														type: "GET",
														url: "/json_get_barcode",
														data: { where : 'BAG START', wo_number : $('#id_work_orders option:selected').attr('data-wo_number') },
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
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-password-input" class="col-sm-4 col-form-label">
													Used Next Shift<br>
													<span class="badge bg-secondary-subtle text-secondary">
														Barcode START
													</span>
												</label>
												<div class="col-sm-8 mt-3">
													<label class="toggleSwitch nolabel" onclick="">
														<input type="checkbox" name="used_next_shift" checked />
														<a></a>
														<span>
															<span class="left-span">No</span>
															<span class="right-span">Yes</span>
														</span>											
													</label>
													@if($errors->has('used_next_shift'))
														<div class="text-danger"><b>{{ $errors->first('used_next_shift') }}</b></div>
													@endif
												</div>
											</div>											
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-password-input" class="col-sm-4 col-form-label">Join  </label>
												<div class="col-sm-8">
													<select class="form-select data-select2" name="join" id="join">
														<option value="">** Please Select A Join</option>
														<option value="1">1</option>
														<option value="2">2</option>
														<option value="3">3</option>
													</select>
													@if($errors->has('join'))
														<div class="text-danger"><b>{{ $errors->first('join') }}</b></div>
													@endif
												</div>
											</div>	
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Barcode End</label>
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
														data: { 
															where : 'BAG',
															barcode_end : '<?php $total = count($data_detail_production); $i = 0; foreach ($data_detail_production as $data_detail){ $separator = (++$i < $total)?',':''; echo '"'.$data_detail->barcode.'"'.$separator; } ?>'
															
														},
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
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-password-input" class="col-sm-4 col-form-label">
													Used Next Shift<br>
													<span class="badge bg-secondary-subtle text-secondary">
														Barcode END
													</span>
												</label>
												<div class="col-sm-8 mt-3">
													<label class="toggleSwitch nolabel" onclick="">
														<input type="checkbox" name="used_next_shift_barcode" checked />
														<a></a>
														<span>
															<span class="left-span">No</span>
															<span class="right-span">Yes</span>
														</span>											
													</label>
													@if($errors->has('used_next_shift_barcode'))
														<div class="text-danger"><b>{{ $errors->first('used_next_shift_barcode') }}</b></div>
													@endif
												</div>
											</div>
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Weight Starting </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="weight_starting">
													@if($errors->has('weight_starting'))
														<div class="text-danger"><b>{{ $errors->first('weight_starting') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Amount Result </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="amount_result" id="amount_result">
													<div class="text-secondary"><b>Pcs</b></div>
													@if($errors->has('amount_result'))
														<div class="text-danger"><b>{{ $errors->first('amount_result') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label mt-4">PCS Per Wrap </label>
												<div class="col-sm-8">
													<span class="badge bg-secondary-subtle text-secondary">
														( Estimasi Jumlah PCS Per Bungkus/Wrap )
													</span>
													<input type="text" class="form-control mt-1" name="pcs_wrap" id="pcs_wrap">
													<!--input type="hidden" class="form-control mt-1" name="pcs_wrap"-->
													<div class="text-secondary">
														<b>Pcs</b> 
													</div>
													@if($errors->has('pcs_wrap'))
														<div class="text-danger"><b>{{ $errors->first('pcs_wrap') }}</b></div>
													@endif
												</div>
											</div>
											<script>
												$(document).ready(function(){
													document.getElementById('amount_result').value = 0;
													document.getElementById('pcs_wrap').value = 0;
													
													konten = '<div class="alert alert-dark alert-dismissible alert-label-icon label-arrow fade show" role="alert"><i class="mdi mdi-alert-outline label-icon"></i><font class="text-white">Informasi Tidak Tersedia</font></div>';
													$( "#div-informasi" ).html( konten );
													/*
													$("#pcs_wrap").keyup(function() {
														var n_amount = parseFloat(document.getElementById('amount_result').value);
														var n_pcs_wrap = parseFloat(document.getElementById('pcs_wrap').value);

														if (!isNaN(n_amount) && !isNaN(n_pcs_wrap) && n_pcs_wrap!='0' && n_amount > n_pcs_wrap) {
															document.getElementById('pcs_wrap').value = n_amount;
														}
													});
													*/
													var amount_result = document.getElementById('amount_result');
											
													amount_result.addEventListener('input', function() {
														var n_amount = parseFloat(document.getElementById('amount_result').value);
														var n_pcs_wrap = parseFloat(document.getElementById('pcs_wrap').value);
														
														var n_pcs_wrap = n_pcs_wrap > n_amount ? n_amount : n_pcs_wrap ;
														
														var hasil = Math.floor(n_amount/n_pcs_wrap) ;
														var sisa = n_amount%n_pcs_wrap ;
														
														var hasil = !isNaN(hasil)?hasil:0;
														var sisa = !isNaN(sisa)?sisa:0;
														
														var hasil_akhir = sisa > 0 ? hasil + 1 : hasil;
														var hasil_info = sisa > 0 ? '<strong>'+hasil+'</strong> Bungkus Isi <strong><font>'+n_pcs_wrap+'</font></strong> Pcs<br><strong>1</strong> Bungkus Isi <strong><font>'+sisa+'</font></strong> Pcs':'<strong>'+hasil+'</strong> Bungkus Isi <strong><font>'+n_pcs_wrap+'</font></strong> Pcs';
														
														konten = '<div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert"><i class="mdi mdi-alert-outline label-icon"></i>Total :<br><strong><font>'+hasil_akhir+'</font></strong> Bungkus<br><br>Rincian :<br>'+hasil_info+'</div>';
														$( "#div-informasi" ).html( konten );
													});
													
													var pcs_wrap = document.getElementById('pcs_wrap');
											
													pcs_wrap.addEventListener('input', function() {
														var n_amount = parseFloat(document.getElementById('amount_result').value);
														var n_pcs_wrap = parseFloat(document.getElementById('pcs_wrap').value);
														
														var n_pcs_wrap = n_pcs_wrap > n_amount ? n_amount : n_pcs_wrap ;
														
														var hasil = Math.floor(n_amount/n_pcs_wrap) ;
														var sisa = n_amount%n_pcs_wrap ;
														
														var hasil = !isNaN(hasil) ? hasil:0;
														var sisa = !isNaN(sisa) ? sisa:0;
														
														var hasil_akhir = sisa > 0 ? hasil + 1 : hasil;
														var hasil_info = sisa > 0 ? '<strong>'+hasil+'</strong> Bungkus Isi <strong><font>'+n_pcs_wrap+'</font></strong> Pcs<br><strong>1</strong> Bungkus Isi <strong><font>'+sisa+'</font></strong> Pcs':'<strong>'+hasil+'</strong> Bungkus Isi <strong><font>'+n_pcs_wrap+'</font></strong> Pcs';
														
														konten = '<div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert"><i class="mdi mdi-alert-outline label-icon"></i>Total :<br><strong><font>'+hasil_akhir+'</font></strong> Bungkus<br><br>Rincian :<br>'+hasil_info+'</div>';
														$( "#div-informasi" ).html( konten );
													});
												});
											</script>
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Wrap</label>
												<div class="col-sm-8">
													<div id="div-informasi"></div>
												</div>
											</div>
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Wrap Note </label>
												<div class="col-sm-8">
													<textarea rows="5" class="form-control" name="keterangan"></textarea>
													@if($errors->has('keterangan'))
														<div class="text-danger"><b>{{ $errors->first('keterangan') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Waste </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="waste">
													<div class="text-secondary"><b>Kilogram</b></div>
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
									<div class="card-body p-4" id="detailTableSection">
										@if(!empty($data_detail_production[0]))
										<div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert">
											<i class="mdi mdi-alert-octagon-outline label-icon"></i><strong>Perhatian</strong><br>Jika data detail <b>production result</b> belum disesuaikan. Data stok <b>tidak bisa</b> di update.
											<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
										</div>
											<div class="table-responsive">
												<table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
													<thead>
														<tr>
														<tr>
															<th width="20%">Start / Finish</th>
															<th width="40%">Production Info</th>
															<th width="10%">Aksi</th>
														</tr>
													</thead>
													<tbody>
														@foreach ($data_detail_production as $data_detail)
														<tr>
															<td>
																At : <b>{{ $data_detail->start_time }}</b> /												
																Until : <b>{{ $data_detail->finish_time }}</b>
																<br><br>													
																Detail <b>Production Result</b> :<br>
																@if(( $data_detail->count_detail_pr == $data_detail->wrap )&&( $data_detail->sum_wrap_pcs_pr == $data_detail->amount_result ))
																	<span class="badge bg-success-subtle text-success">
																		Sudah Di Sesuaikan
																	</span>
																@else
																	<span class="badge bg-danger-subtle text-danger">
																		Belum Di Sesuaikan
																	</span>
																	@if( $data_detail->sum_wrap_pcs_pr != $data_detail->amount_result )
																	<br>
																	<code>
																		<b>amount result</b> dan total <b>wrap pcs</b> <br>tidak sesuai.
																	</code>
																	@endif
																@endif
															</td>
															<?php $product = explode('|', $data_detail->note) ; ?>
															<td><p>
																Barcode Start : <b>{{ $data_detail->barcode_start }}</b><br>
																Barcode End : <b>{{ $data_detail->barcode }}</b><br><br>
																Work Orders : <b>{{ $data_detail->wo_number }}</b><br>
																<footer class="blockquote-footer">Product : <cite><b>{{ $product['2'] }}</b></cite></footer>
																<footer class="blockquote-footer">Weight Starting : <cite><b>{{  $data_detail->weight_starting }}</b> Kg</cite></footer>
																<?php if($data_detail->waste!=''){ ?>
																<footer class="blockquote-footer">Waste : <cite><b>{{  $data_detail->waste }}</b> Kg</cite></footer>
																<?php }; ?>
																Amount Result : <b>{{ $data_detail->amount_result }}</b> Pcs<br>
																Wrap : <b>{{ $data_detail->wrap }}</b> Bungkus<br><br>
																<?php if($data_detail->keterangan!=''){ ?>
																<footer class="blockquote-footer">Keterangan : <cite><b>{{  $data_detail->keterangan }}</b></cite></footer>
																<?php }; ?>
																</p>
															</td>
															
															
															<td>	
																<center>
																	<form action="/production-entry-report-bag-making-detail-production-result-delete#detailTableSection" method="post" class="d-inline" enctype="multipart/form-data">
																		@csrf		
																		<input type="hidden" class="form-control" name="token_rb" value="{{ Request::segment(2) }}">
																		<button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure to delete this item ?')" value="{{ sha1($data_detail->id) }}" name="hapus_detail">
																			<i class="bx bx-trash-alt" title="Delete" ></i>
																		</button>
																	</form>	
																	<a target="_blank" href="/production-entry-report-bag-making-detail-production-result-edit/{{ Request::segment(2) }}/{{ sha1($data_detail->id) }}" class="btn btn-info waves-effect waves-light">
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
		<!--div class="row">
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
										<form method="post" action="/production-entry-report-bag-making-detail-waste-add" class="form-material m-t-40" enctype="multipart/form-data">
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
										@if(!empty($data_detail_waste[0]))
											<div class="table-responsive">
												<table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
													<thead>
														<tr>
														<tr>
															<th width="20%">Waste</th>
															<th width="60%">Cause Waste</th>
															<th width="20%">Aksi</th>
														</tr>
													</thead>
													<tbody>
														@foreach ($data_detail_waste as $data_detail)
														<tr>
															<td>
																{{ $data_detail->waste }}
															</td>
															<td>
																{{ $data_detail->cause_waste }}
															</td>
															
															
															<td>	
																<center>
																	<form action="/production-entry-report-bag-making-detail-waste-delete" method="post" class="d-inline" enctype="multipart/form-data">
																		@csrf		
																		<input type="hidden" class="form-control" name="token_rb" value="{{ Request::segment(2) }}">
																		<button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure to delete this item ?')" value="{{ sha1($data_detail->id) }}" name="hapus_detail">
																			<i class="bx bx-trash-alt" title="Delete" ></i>
																		</button>
																	</form>	
																	<a href="/production-entry-report-bag-making-detail-waste-edit/{{ Request::segment(2) }}/{{ sha1($data_detail->id) }}" class="btn btn-info waves-effect waves-light">
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
						
						
                    </div>
					
					
					
					
				</div>
			</div>
		</div--> 
                    <!-- end row -->
    </div>
</div>

@endsection
@section('styles')
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
@endsection