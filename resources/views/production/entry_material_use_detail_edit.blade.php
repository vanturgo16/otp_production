@extends('layouts.master')

@section('konten')
<div class="page-content">
    <div class="container-fluid">
    @csrf
		
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <!--h4 class="mb-sm-0 font-size-18"> Add Request Sparepart & Auxiliaries</h4-->
					<a href="/production-ent-material-use" class="btn btn-dark waves-effect waves-light mb-3"> 
					<i class="bx bx-list-ul" title="Back"></i> REPORT MATERIAL USE</a>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Production</a></li>
                            <li class="breadcrumb-item active"> Edit Detail Report Material Use</li>
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
						<h4 class="card-title">Detail Report Material Use</h4>
						
					</div>
					<div class="card-body p-4">
						<p class="card-title-desc"></p>
						<div class="alert alert-info alert-border-left alert-dismissible fade show mb-0 text-justify" role="alert">
							<i class="mdi mdi-alert-circle-outline align-middle me-3"></i><strong>Info</strong> - Anda hanya diperkenankan melakukan perubahan angka usage dan sisa campuran. Jika terdapat kesalahan input barcode atau angka taking, silahkan hapus data barcode yang salah dan tambahkan kembali di menu detail report material use.
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
						
						<div class="mt-4 col-sm-12">
							<div class="mt-4 mt-lg-0">
								<form method="post" action="/production-entry-material-use-detail-edit-save" class="form-material m-t-40" enctype="multipart/form-data">
								@csrf
								
									<input type="hidden" class="form-control" name="token_rm" value="{{ Request::segment(2) }}">
									<input type="hidden" class="form-control" name="token_rm_detail" value="{{ Request::segment(3) }}">
									
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Sisa Campuran</label>
										<div class="col-sm-9">
											<input class="form-control" name="sisa_campuran" id="sisa_campuran" type="text" value="{{ $data[0]->sisa_camp }}">
											<p class="card-title-desc"><code>.Kg</code></p>
											@if($errors->has('sisa_campuran'))
												<div class="text-danger"><b>{{ $errors->first('sisa_campuran') }}</b></div>
											@endif
										</div>
									</div>
									<script>
										
										$(document).ready(function(){
											$("#sisa_campuran").keyup(function() {
												var n_taking = {{ $data[0]->taking }};
												var n_sisa_campuran = parseFloat(document.getElementById('sisa_campuran').value);

												if (!isNaN(n_taking) && !isNaN(n_sisa_campuran) && n_sisa_campuran > n_taking) {
													document.getElementById('sisa_campuran').value = n_taking;
												}
											});
										});
										
									</script>
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-password-input" class="col-sm-3 col-form-label">Barcode  </label>										
										<div class="col-sm-9">
											<div>
												<div class="alert alert-dark alert-dismissible alert-label-icon label-arrow fade show" role="alert">
													<i class="mdi mdi-alert-outline label-icon"></i>
													<font class="text-white">{{ $data[0]->lot_number }} ( EXT : {{ $data[0]->ext_lot_number }} ) - {{ $data[0]->description }} ( Taking : <strong> {{$data[0]->taking}} </strong> )</font>
												</div>
											</div>
										</div>
									</div>
									
									
									<div class="row mb-4 field-wrapper required-field">
										<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Usage</label>
										<div class="col-sm-9">
											<input type="text" class="form-control" name="usage" id="usage" value="{{ $data[0]->usage }}">
											<p class="card-title-desc"><code>.Kg - Catatan : Jumlah usage tidak boleh lebih banyak dari <strong>( Taking )</strong>.</code></p>
											@if($errors->has('usage'))
												<div class="text-danger"><b>{{ $errors->first('usage') }}</b></div>
											@endif
										</div>
									</div>
									<script>
										
										$(document).ready(function(){
											$("#usage").keyup(function() {
												var n_taking = {{ $data[0]->taking }};
												var n_usage = parseFloat(document.getElementById('usage').value);

												if (!isNaN(n_taking) && !isNaN(n_usage) && n_usage > n_taking) {
													document.getElementById('usage').value = n_taking;
												}
											});
										});
										
									</script>
									<div class="row mb-4 field-wrapper">
										<label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Remaining</label>
										<div class="col-sm-9">
											<div id="div-info-remaining" title="Sumber : ( Taking - Usage ) + Sisa Campuran"></div>
										</div>
									</div>
									<script>
										
										$(document).ready(function(){
											konten = '<div class="alert alert-warning alert-dismissible alert-label-icon label-arrow fade show" role="alert"><i class="mdi mdi-alert-outline label-icon"></i><font><strong>'+{{ $data[0]->remaining }}+'</strong></font> Kg</div>';
											$( "#div-info-remaining" ).html( konten );
											
											var usage = document.getElementById('usage');
											var sisa_campuran = document.getElementById('sisa_campuran');
											
											usage.addEventListener('input', function() {
												var n_taking = {{ $data[0]->taking }};
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
												var n_taking = {{ $data[0]->taking }};
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
									<div class="row justify-content-end">
										<div class="col-sm-9">
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
    
                    <!-- end row -->
    </div>
</div>

@endsection