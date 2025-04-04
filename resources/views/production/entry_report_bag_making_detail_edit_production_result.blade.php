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
			<div class="col-lg-6">
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
											<div class="alert alert-info alert-dismissible alert-label-icon label-arrow fade show" role="alert">
												<i class="mdi mdi-alert-octagon-outline label-icon"></i><strong>Perhatian</strong><br>Jika kamu melakukan perubahan bungkus di bagian <b>WRAP</b>, kamu harus melakukan penyesuaian kembali di data <b>WRAP Detail</b>.
												<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
											</div><br>
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-password-input" class="col-sm-4 col-form-label">Work Orders </label>
												<div class="col-sm-8">
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
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Start Time </label>
												<div class="col-sm-8">
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
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Finish Time </label>
												<div class="col-sm-8">
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
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-password-input" class="col-sm-4 col-form-label">
													Used Next Shift<br>
													<span class="badge bg-secondary-subtle text-secondary">
														Barcode START
													</span>
												</label>
												<div class="col-sm-8 mt-3">
													<label class="toggleSwitch nolabel" onclick="">
														<input type="checkbox" name="used_next_shift" <?= $data[0]->used_next_shift == '1' ? 'checked' : '' ?>/>
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
														<option value="1" {{ $data[0]->join=='1'?'selected':'' }}>1</option>
														<option value="2" {{ $data[0]->join=='2'?'selected':'' }}>2</option>
														<option value="3" {{ $data[0]->join=='3'?'selected':'' }}>3</option>
													</select>
													@if($errors->has('join'))
														<div class="text-danger"><b>{{ $errors->first('join') }}</b></div>
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
														data: { 
															where : 'BAG', 
															barcode_end : '<?php $total = count($data_detail_production); $i = 0; foreach ($data_detail_production as $data_detail_production){ $separator = (++$i < $total)?',':''; echo '"'.$data_detail_production->barcode.'"'.$separator; } ?>',
															barcode_number : {!! "'".$data[0]->barcode."'" !!} 
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
														<input type="checkbox" name="used_next_shift_barcode" <?= $data[0]->used_next_shift_barcode == '1' ? 'checked' : '' ?>/>
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
													<input type="text" class="form-control" name="weight_starting" value="{{ $data[0]->weight_starting; }}">
													@if($errors->has('weight_starting'))
														<div class="text-danger"><b>{{ $errors->first('weight_starting') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Amount Result </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="amount_result" id="amount_result" value="{{ $data[0]->amount_result; }}">
													<div class="text-secondary"> Pcs</div>
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
													<input type="text" class="form-control mt-1" name="pcs_wrap" id="pcs_wrap" value="{{ $data[0]->pcs_wrap; }}">
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
													document.getElementById('amount_result').value = {{ $data[0]->amount_result; }};
													document.getElementById('pcs_wrap').value = {{ $data[0]->pcs_wrap; }};
													
													konten = '<div class="alert alert-dark alert-dismissible alert-label-icon label-arrow fade show" role="alert"><i class="mdi mdi-alert-outline label-icon"></i><font class="text-white">Informasi Tidak Tersedia</font></div>';
													$( "#div-informasi" ).html( konten );
													
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
													<textarea rows="5" class="form-control" name="keterangan">{{ $data[0]->keterangan; }}</textarea>
													@if($errors->has('keterangan'))
														<div class="text-danger"><b>{{ $errors->first('keterangan') }}</b></div>
													@endif
												</div>
											</div> 
											
											<div class="row mb-4 field-wrapper">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Waste </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="waste" value="{{ $data[0]->waste; }}">
													<div class="text-secondary"><b>Kilogram</b></div>
													@if($errors->has('waste'))
														<div class="text-danger"><b>{{ $errors->first('waste') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Cause Waste </label>
												<div class="col-sm-8">
													<input type="text" class="form-control" name="cause_waste" value="{{ $data[0]->cause_waste; }}">
													@if($errors->has('cause_waste'))
														<div class="text-danger"><b>{{ $errors->first('cause_waste') }}</b></div>
													@endif
												</div>
											</div> 
											
											
											<input type="hidden" class="form-control" name="token_rb" value="{{ Request::segment(2) }}">
											<input type="hidden" class="form-control" name="token_rb_pr" value="{{ sha1($data[0]->id); }}">
											
											
											
											<div class="row justify-content-end">
												<div class="col-sm-4">
												</div>
												<div class="col-sm-8">
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
			<div class="col-lg-6">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title">Table Wrap Detail</h4>						
					</div>
					<div class="card-body p-4">
						@if(count($data_detail)!=$data[0]->wrap)
						<div class="alert alert-danger alert-dismissible alert-label-icon label-arrow fade show" role="alert">
							<i class="mdi mdi-alert-octagon-outline label-icon"></i><strong>Perhatian</strong><br>Terdapat ketidaksesuaian data <b>WRAP Detail ( {{count($data_detail)}} )</b> dengan jumlah <b>WRAP ( {{$data[0]->wrap}} )</b>. Silahkan perbaharui data.
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
						@endif
						@if(count($data_detail) < $data[0]->wrap)							
							<button data-bs-toggle="modal" data-bs-target="#addItemModal" class="mb-3 btn btn-success waves-effect waves-light" name="tambah" id="tambah">
								<i class="bx bx-plus" title="Tambah"></i>
							</button>
							<!-- Modal Add-->
							<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title" id="addItemModalLabel">Add Wrap Detail</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
										</div>
										<form action="/production-entry-report-bag-making-wrap-add" method="POST">
											@csrf
											<div class="modal-body">
												<div class="mb-3 required-field">
													<label for="description" class="form-label">Jumlah Per Bungkus (Wrap)</label>
													<input type="text" class="form-control" name="wrap_pcs">
													<div class="text-secondary"><b>Pcs</b></div>
												</div>
												<div class="mb-3">
													<label for="description" class="form-label">Keterangan</label>
													<textarea rows="5" class="form-control" name="keterangan"></textarea>
												</div>
											</div>
											<div class="modal-footer">
												<input type="hidden" class="form-control" name="token_rb" value="{{ Request::segment(2) }}">
												<input type="hidden" class="form-control" name="token_rb_pr" value="{{ sha1($data[0]->id); }}">
												
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
													<i class="bx bx-x" title="Tutup"></i>												
												</button>
												<button type="submit" class="btn btn-success" name="save">
													<i class="bx bx-check " title="Simpan"></i>
												</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						@endif
						@if(count($data_detail)>0)
							
							<div class="table-responsive">
								<table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
									<thead>
										<tr>
										<tr>
											<th width="60%">Jumlah Per Bungkus (Wrap)</th>
											<th width="10%">Aksi</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($data_detail as $data_detail)
										<tr>
											<td>
												<div class="btn btn-{{ !empty($data_detail->wrap_pcs)?"dark":"warning" }} waves-effect waves-light">
												{{ !empty($data_detail->wrap_pcs)?$data_detail->wrap_pcs:"0" }} Pcs
												</div>
											</td>
											
											
											
											<td>	
												<center>
													<form action="/production-entry-report-bag-making-wrap-delete" method="post" class="d-inline" enctype="multipart/form-data">
														@csrf		
														<input type="hidden" class="form-control" name="token_rb" value="{{ Request::segment(2) }}">
														<input type="hidden" class="form-control" name="token_rb_pr" value="{{ sha1($data[0]->id); }}">
														<input type="hidden" class="form-control" name="token_rb_pr_detail" value="{{ sha1($data_detail->id); }}">
														
														<button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure to delete this item ?')" value="{{ sha1($data_detail->id) }}" name="hapus_detail">
															<i class="bx bx-trash-alt" title="Hapus" ></i>
														</button>
													</form>	
													<button data-bs-toggle="modal" data-bs-target="#editItemModal{{$data_detail->id}}" class="btn btn-info waves-effect waves-light" name="edit" id="edit{{$data_detail->id}}">
														<i class="bx bx-edit-alt" title="Edit"></i>
													</button>
													
												</center>											
											</td>
										 
										</tr>
										<!-- Modal Edit-->
										<div class="modal fade" id="editItemModal{{$data_detail->id}}" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="editItemModalLabel">Edit Wrap Detail</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
													</div>
													<form action="/production-entry-report-bag-making-wrap-edit" method="POST">
														@csrf
														<div class="modal-body">
															<div class="mb-3 required-field">
																<label for="description" class="form-label">Jumlah Per Bungkus (Wrap)</label>
																<input type="text" class="form-control" name="wrap_pcs" value="{{ $data_detail->wrap_pcs }}">
																<div class="text-secondary"><b>Pcs</b></div>
															</div>
															<div class="mb-3">
																<label for="description" class="form-label">Keterangan</label>
																<textarea rows="5" class="form-control" name="keterangan">
																{{ $data_detail->keterangan }}
																</textarea>
															</div>
														</div>
														<div class="modal-footer">
															<input type="hidden" class="form-control" name="token_rb" value="{{ Request::segment(2) }}">
															<input type="hidden" class="form-control" name="token_rb_pr" value="{{ sha1($data[0]->id); }}">
															<input type="hidden" class="form-control" name="token_rb_pr_detail" value="{{ sha1($data_detail->id); }}">
															
															<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
																<i class="bx bx-x" title="Tutup"></i>												
															</button>
															<button type="submit" class="btn btn-success" name="save">
																<i class="bx bx-check " title="Simpan"></i>
															</button>
														</div>
													</form>
												</div>
											</div>
										</div>
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

@endsection