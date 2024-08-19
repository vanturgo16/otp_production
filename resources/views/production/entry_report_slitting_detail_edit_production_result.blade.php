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
                            <li class="breadcrumb-item active"> Edit Detail Production Result Report Slitting</li>
                        </ol>
                    </div>
                </div>
                
            </div>
        </div>
		
		@if(!empty($data[0]))       
		
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title"><i data-feather="check-square"></i> Production Result</h4>
						
					</div>
					<div class="card-body p-4">
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-header">
										<h4 class="card-title">Form</h4>
										
									</div>
									<script>
										window.onload = function () {
											start_convert();
											finish_convert();
										}
										
										function start_convert() {
											let timeValue = document.getElementById('start').value;
											let timeParts = timeValue.split(':');
											let hours = parseInt(timeParts[0], 10);
											let minutes = timeParts[1];
											let displayHours = (hours === 0) ? 24 : hours;

											document.getElementById('displayHours_start').textContent = `Konversi Waktu Lokal : ${displayHours}:${minutes}`;
										}
								 
										function finish_convert() {
											let timeValue = document.getElementById('finish').value;
											let timeParts = timeValue.split(':');
											let hours = parseInt(timeParts[0], 10);
											let minutes = timeParts[1];
											let displayHours = (hours === 0) ? 24 : hours;

											document.getElementById('displayHours_finish').textContent = `Konversi Waktu Lokal : ${displayHours}:${minutes}`;
										}
									</script>
									<div class="card-body p-4">
										<form method="post" action="/production-entry-report-slitting-detail-production-result-edit-save" class="form-material m-t-40" enctype="multipart/form-data">
											@csrf
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Start Time </label>
												<div class="col-sm-10">
													<input type="time" class="form-control" name="start" id="start" value="{{ $data[0]->start_time; }}">
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
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Finish Time </label>
												<div class="col-sm-10">
													<input type="time" class="form-control" name="finish" id="finish" value="{{ $data[0]->finish_time; }}">
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
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Barcode Start</label>
												<div class="col-sm-10">
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
														data: { where : 'SLITTING START', barcode_number : {!! "'".$data[0]->barcode_start."'" !!}},
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
												<label for="horizontal-password-input" class="col-sm-2 col-form-label">Work Orders </label>
												<div class="col-sm-10">
													<select class="form-select data-select2" name="id_work_orders" id="id_work_orders" required>
														<option value="">** Please Select A Work Orders</option>
														@foreach ($ms_work_orders as $data_for)
															<option value="{{ $data_for->id }}" data-id_master_customers="{{ $data_for->id_master_customers }}" data-type_product="{{ $data_for->type_product }}" data-id_master_products="{{ $data_for->id_master_products }}" {{ $data_for->id == $data[0]->id_work_orders ? 'selected' : '' }}>{{ $data_for->wo_number }}</option>
														@endforeach
													</select>
													@if($errors->has('id_work_orders'))
														<div class="text-danger"><b>{{ $errors->first('id_work_orders') }}</b></div>
													@endif
												</div>
											</div>
											<script>									
												$(document).ready(function(){
													$.ajax({
														type: "GET",
														url: "/json_get_produk",
														data: { type_product : {!! "'".explode('|', $data[0]->note)[0]."'" !!}, id_master_products : {!! explode('|', $data[0]->note)[1] !!} },//baru sampe sinihhh
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
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Product Info </label>
												<div class="col-sm-10">
													<select class="form-select data-select2" name="id_master_products" id="id_master_products">
														<option value="">** Please Select A Products</option>
													</select>
													@if($errors->has('id_master_products'))
														<div class="text-danger"><b>{{ $errors->first('id_master_products') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Barcode </label>
												<div class="col-sm-10">
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
														data: { where : 'SLITTING', barcode_number : {!! "'".$data[0]->barcode."'" !!} },
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
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Thickness </label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="thickness" value="{{ $data[0]->thickness; }}">
													@if($errors->has('thickness'))
														<div class="text-danger"><b>{{ $errors->first('thickness') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Length </label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="length" value="{{ $data[0]->length; }}">
													@if($errors->has('length'))
														<div class="text-danger"><b>{{ $errors->first('length') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Width </label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="width" value="{{ $data[0]->width; }}">
													@if($errors->has('width'))
														<div class="text-danger"><b>{{ $errors->first('width') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Weight </label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="weight" value="{{ $data[0]->weight; }}">
													@if($errors->has('weight'))
														<div class="text-danger"><b>{{ $errors->first('weight') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label"><br>Status </label>
												<div class="col-sm-10">
													<div class="form-check">
														<input class="form-check-input" type="radio" name="status" value="Good" {{ $data[0]->status=="Good"?'checked':''; }}>
														<label>
															Good
														</label>
													</div>
													<div class="form-check">
														<input class="form-check-input" type="radio" name="status" value="Hold" {{ $data[0]->status=="Hold"?'checked':''; }}>
														<label>
															Hold
														</label>
													</div>
													<div class="form-check">
														<input class="form-check-input" type="radio" name="status" value="Reject" {{ $data[0]->status=="Reject"?'checked':''; }}>
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
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Waste </label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="waste" value="{{ $data[0]->waste; }}">
													@if($errors->has('waste'))
														<div class="text-danger"><b>{{ $errors->first('waste') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Cause Waste </label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="cause_waste" value="{{ $data[0]->cause_waste; }}">
													@if($errors->has('cause_waste'))
														<div class="text-danger"><b>{{ $errors->first('cause_waste') }}</b></div>
													@endif
												</div>
											</div>
											
											
											<input type="hidden" class="form-control" name="token_rs" value="{{ Request::segment(2) }}">
											<input type="hidden" class="form-control" name="token_rs_pr" value="{{ sha1($data[0]->id); }}">
											
											
											
											<div class="row justify-content-end">
												<div class="col-sm-2">
												</div>
												<div class="col-sm-10">
													<div>
														<button type="button" class="btn btn-danger waves-effect" onclick="window.history.go(-1); return false;">
															<i class="bx bx-x-circle" title="Cancel" ></i> CANCEL
														</button> 
														<button type="submit" class="btn btn-success waves-effect waves-light">
															<i class="bx bx-save" title="Back"></i> UPDATE
														</button>
													</div>
												</div>
											</div>
										</form> 
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
    </div>
</div>

@endsection