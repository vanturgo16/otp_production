@extends('layouts.master')

@section('konten')

<div class="page-content">
        <div class="container-fluid">
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
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18"> Report Blow</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Production</a></li>
                                <li class="breadcrumb-item active"> Report Blow</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="/production-ent-report-blow-add" class="btn btn-success waves-effect waves-light">
										<i class="bx bx-plus" title="Add Data" ></i>
										ADD
									</a>                                   
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered dt-responsive nowrap w-100 datatable-rb-json">
                                    <thead>
                                        <tr>
                                            <th width='10%'>Report Info</th>
                                            <th width='10%'>Order Info</th>
                                            <th width='20%'>Team Info</th>
                                            <th width='10%'>Checklist</th>
                                            <th width='10%'>Update Stok</th>
                                            <th width='10%'>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
								<script type="text/javascript">
								  $(function () {
									var table = $('.datatable-rb-json').DataTable({
										processing: true,
										serverSide: true,
										ajax: '/production-ent-report-blow-json',
										columns: [
											{data: 'report_info', name: 'report_info', orderable: true, searchable: true},
											{data: 'order_info', name: 'order_info', orderable: true, searchable: true},
											{data: 'team', name: 'team', orderable: true, searchable: true},
											{data: 'checklist', name: 'checklist', orderable: false, searchable: false},
											{data: 'update', name: 'update', orderable: false, searchable: false},
											{data: 'action', name: 'action', orderable: false, searchable: false},
										],
										aaSorting: [
											[1, 'desc']
										],
									});
								  });
								</script>
								
								<div id="modal_preparation" class="modal fade" tabindex="-1" aria-labelledby="edit_poLabel" aria-hidden="true" data-bs-scroll="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="font-size-16">
													<b>Preparation Check</b>
												</h4>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body" id="modal_preparation_body">								
												
											</div>
											<div class="modal-footer">
											</div>
										</div>
									</div>
								</div>
								<script>
									function showPreparation(id)
									{
										$.ajax({
											type: "GET",
											url: "/production-ent-report-blow-json-preparation",
											data: "id='"+id+"'",
											dataType: "html",
											success: function (response) {
												$('#modal_preparation_body').empty();
												$('#modal_preparation_body').append(response);
											}
										});
										
									}
								</script>
								
								<div id="modal_hygiene" class="modal fade" tabindex="-1" aria-labelledby="edit_poLabel" aria-hidden="true" data-bs-scroll="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="font-size-16">
													<b>Hygiene Check</b>
												</h4>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body" id="modal_hygiene_body">								
												
											</div>
											<div class="modal-footer">
											</div>
										</div>
									</div>
								</div>
								<script>
									function showHygiene(id)
									{
										$.ajax({
											type: "GET",
											url: "/production-ent-report-blow-json-hygiene",
											data: "id='"+id+"'",
											dataType: "html",
											success: function (response) {
												$('#modal_hygiene_body').empty();
												$('#modal_hygiene_body').append(response);
											}
										});
										
									}
								</script>
								
								<div id="modal_update_stock" class="modal fade" tabindex="-1" aria-labelledby="edit_poLabel" aria-hidden="true" data-bs-scroll="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="font-size-16">
													<b>Update Stock</b>
												</h4>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body" id="modal_update_stock_body">								
												
											</div>
											<div class="modal-footer">
											</div>
										</div>
									</div>
								</div>
								<script>
									function showUpdateStock(id)
									{
										$.ajax({
											type: "GET",
											url: "/production-ent-report-blow-json-update-stock",
											data: "id='"+id+"'",
											dataType: "html",
											success: function (response) {
												$('#modal_update_stock_body').empty();
												$('#modal_update_stock_body').append(response);
											}
										});
										
									}
								</script>
								
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection