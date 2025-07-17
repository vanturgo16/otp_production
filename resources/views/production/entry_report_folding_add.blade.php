@extends('layouts.master')

@section('konten')
<div class="page-content">
    <div class="container-fluid">
    <form method="post" action="/production-ent-report-folding-save" class="form-material m-t-40" enctype="multipart/form-data">
    @csrf
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <!--h4 class="mb-sm-0 font-size-18"> Add Request Sparepart & Auxiliaries</h4-->
					<a href="/production-ent-report-folding" class="btn btn-dark waves-effect waves-light mb-3"> 
					<i class="bx bx-list-ul" title="Back"></i> REPORT FOLDING</a>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Production</a></li>
                            <li class="breadcrumb-item active"> Add Report Folding</li>
                        </ol>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add Report Folding</h4>
                        <!--  <p class="card-title-desc"> layout options : from inline, horizontal & custom grid implementations</p> -->
                    </div>
                    <div class="card-body p-4">

                    <div class="col-sm-12">
                            <div class="mt-4 mt-lg-0">
                    
								<div class="row mb-4 field-wrapper required-field">
									<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Report Number</label>
									<div class="col-sm-9">
										<input type="text" name="request_number" class="form-control" value="{{ $formattedCode }}" readonly>
									</div>
								</div>
								<div class="row mb-4 field-wrapper required-field">
									<label for="horizontal-email-input" class="col-sm-3 col-form-label">Date</label>
									<div class="col-sm-9">
										<input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
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
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
										</select>
										@if($errors->has('shift'))
											<div class="text-danger"><b>{{ $errors->first('shift') }}</b></div>
										@endif
									</div>
								</div>	
								<div class="row mb-4 field-wrapper">
									<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Engine Shutdown Description </label>
									<div class="col-sm-9">
										<textarea rows="5" class="form-control" name="engine_shutdown_description"></textarea>
										@if($errors->has('engine_shutdown_description'))
											<div class="text-danger"><b>{{ $errors->first('engine_shutdown_description') }}</b></div>
										@endif
									</div>
								</div> 	
								<div class="row mb-4 field-wrapper">
									<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Note </label>
									<div class="col-sm-9">
										<textarea rows="5" class="form-control" name="note"></textarea>
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
											@foreach ($ms_ketua_regu as $data)
												<option value="{{ $data->id }}">{{ $data->name }}</option>
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
											@foreach ($ms_operator as $data)
												<option value="{{ $data->id }}">{{ $data->name }}</option>
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
											@foreach ($ms_known_by as $data)
												<option value="{{ $data->id }}">{{ $data->name }}</option>
											@endforeach
										</select>
										@if($errors->has('id_known_by'))
											<div class="text-danger"><b>{{ $errors->first('id_known_by') }}</b></div>
										@endif
									</div>
								</div>	
								<script>									
									$(document).ready(function(){
										//$('#id_work_orders').prop('selectedIndex', 0);
										$('#id_master_customers').prop('selectedIndex', 0);
										$('#id_master_work_centers').prop('selectedIndex', 0);
										$('#id_master_regus').prop('selectedIndex', 0);
										$('#shift').prop('selectedIndex', 0);
										
										$.ajax({
											type: "GET",
											url : '/json_get_produk_folding', 
											data: { type_product : 'FG', id_master_products : '' },
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
											//data: { id_master_customers : $('#id_work_orders option:selected').attr('data-id_master_customers') },
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
											data: { id_master_process_productions : '3' },
											dataType: "json",
											beforeSend: function(e) {
												if(e && e.overrideMimeType) {
													e.overrideMimeType("application/json;charset=UTF-8");
												}
											},
											success: function(response){
												$("#id_master_work_centers").html(response.list_work_center).show();
												//$('#id_master_regus').prop('selectedIndex', 0);
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
								<div class="row justify-content-end">
									<div class="col-sm-9">
										<div>
											<a href="/production-ent-report-slitting" class="btn btn-danger waves-effect waves-light"><i class="bx bx-chevron-left" title="Back"></i> BACK</a>
											
											<button type="submit" class="btn btn-success w-md" name="save"><i class="bx bx-save" title="Back"></i> SAVE</button>
										</div>
									</div>
								</div>                        
                        
                            </div>
							
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </form>
                    <!-- end row -->
    </div>
</div>

@endsection