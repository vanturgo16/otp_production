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
                            <li class="breadcrumb-item active"> Edit Detail Waste Report Blow</li>
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
						<h4 class="card-title"><i data-feather="check-square"></i> Waste</h4>
						
					</div>
					<div class="card-body p-4">
						<div class="row">
							<div class="col-lg-12">
								<div class="card">
									<div class="card-header">
										<h4 class="card-title">Form</h4>
										
									</div>
									<div class="card-body p-4">
										<form method="post" action="/production-entry-report-blow-detail-waste-edit-save" class="form-material m-t-40" enctype="multipart/form-data">
											@csrf
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Waste </label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="waste" value="{{ $data[0]->waste; }}">
													@if($errors->has('waste'))
														<div class="text-danger"><b>{{ $errors->first('waste') }}</b></div>
													@endif
												</div>
											</div> 
											<div class="row mb-4 field-wrapper required-field">
												<label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Cause Waste </label>
												<div class="col-sm-10">
													<input type="text" class="form-control" name="cause_waste" value="{{ $data[0]->cause_waste; }}">
													@if($errors->has('cause_waste'))
														<div class="text-danger"><b>{{ $errors->first('cause_waste') }}</b></div>
													@endif
												</div>
											</div>  
											
											<input type="hidden" class="form-control" name="token_rb" value="{{ Request::segment(2) }}">
											<input type="hidden" class="form-control" name="token_rb_w" value="{{ sha1($data[0]->id); }}">
											
											
											
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