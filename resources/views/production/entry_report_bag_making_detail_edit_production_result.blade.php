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
                            <li class="breadcrumb-item active"> Edit Detail Production Result Report Bag Making</li>
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
										<form method="post" action="/production-entry-report-bag-making-detail-production-result-edit-save" class="form-material m-t-40" enctype="multipart/form-data">
											@csrf
																						
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-password-input" class="col-sm-2 col-form-label">Work Orders </label>
												<div class="col-sm-10">
													<select class="form-select data-select2" name="id_work_orders" id="id_work_orders" required>
														<option value="">** Please Select A Work Orders</option>
														@foreach ($ms_work_orders as $data_for)
															<option value="{{ $data_for->id }}" data-id_master_customers="{{ $data_for->id_master_customers }}" data-type_product="{{ $data_for->type_product }}" data-id_master_products="{{ $data_for->id_master_products }}" data-wo_number="{{ $data_for->wo_number }}" {{ $data_for->id == $data[0]->id_work_orders ? 'selected' : '' }}>{{ $data_for->wo_number }}</option>
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
														data: { where : 'BAG START', barcode_number : {!! "'".$data[0]->barcode_start."'" !!}, wo_number : $('#id_work_orders option:selected').attr('data-wo_number')},
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
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Weight Starting </label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="weight_starting" value="{{ $data[0]->weight_starting; }}">
													@if($errors->has('weight_starting'))
														<div class="text-danger"><b>{{ $errors->first('weight_starting') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Amount Result </label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="amount_result" value="{{ $data[0]->amount_result; }}">
													<div class="text-secondary"> Pcs</div>
													@if($errors->has('amount_result'))
														<div class="text-danger"><b>{{ $errors->first('amount_result') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Wrap Info </label>
												<div class="col-sm-5">
													<input type="text" class="form-control" name="wrap_pcs" value="{{ $data[0]->wrap_pcs; }}">
													<div class="text-secondary"> Pcs</div>
													@if($errors->has('wrap_pcs'))
														<div class="text-danger"><b>{{ $errors->first('wrap_pcs') }}</b></div>
													@endif
												</div>
												<div class="col-sm-5">
													<input type="text" class="form-control" name="wrap" value="{{ $data[0]->wrap; }}">
													<div class="text-secondary"> Bungkus</div>
													@if($errors->has('wrap'))
														<div class="text-danger"><b>{{ $errors->first('wrap') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Wrap Note </label>
												<div class="col-sm-10">
													<textarea rows="5" class="form-control" name="keterangan" value=" {{ $data[0]->keterangan; }} "></textarea>
													@if($errors->has('keterangan'))
														<div class="text-danger"><b>{{ $errors->first('keterangan') }}</b></div>
													@endif
												</div>
											</div> 
											
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Waste </label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="waste" value="{{ $data[0]->waste; }}">
													<div class="text-secondary"><b>Kilogram</b></div>
													@if($errors->has('waste'))
														<div class="text-danger"><b>{{ $errors->first('waste') }}</b></div>
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
														data: { where : 'BAG', barcode_number : {!! "'".$data[0]->barcode."'" !!} },
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
											
											
											<input type="hidden" class="form-control" name="token_rb" value="{{ Request::segment(2) }}">
											<input type="hidden" class="form-control" name="token_rb_pr" value="{{ sha1($data[0]->id); }}">
											
											
											
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