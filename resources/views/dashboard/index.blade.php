@extends('layouts.master')

@section('konten')

<div class="page-content">
    <div class="container-fluid">
        <!-- Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">Dashboard Production</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Production</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible alert-label-icon label-arrow fade show" role="alert">
                <i class="mdi mdi-check-all label-icon"></i><strong>Success</strong> - {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Req. sparepart & Aux</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-start">
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-muted mb-0">Request</p>
                                    <h4 class="mb-1">{{ $reportAux['Request'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-info mb-0">Approve</p>
                                    <h4 class="mb-1 text-info">{{ $reportAux['Approve'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-success mb-0">Hold</p>
                                    <h4 class="mb-1 text-success">{{ $reportAux['Hold'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3 ">
                                    <p class="text-purple mb-0">Total</p>
                                    <h4 class="mb-1 text-purple">{{ $reportAux['total'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Entry Maaterial Use</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-start">
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-muted mb-0">Active</p>
                                    <h4 class="mb-1">{{ $reportRaw['Active'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-info mb-0">Hold</p>
                                    <h4 class="mb-1 text-info">{{ $reportAux['Hold'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-primary mb-0">Total</p>
                                    <h4 class="mb-1 text-primary">{{ $reportRaw['total'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 1: Production Request -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Report Blow</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-start">
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-muted mb-0">Request / Un Post</p>
                                    <h4 class="mb-1">{{ $reportBlow['unposted'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-info mb-0">Posted / Created PO</p>
                                    <h4 class="mb-1 text-info">{{ $reportBlow['posted'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-success mb-0">Closed</p>
                                    <h4 class="mb-1 text-success">{{ $reportBlow['closed']}}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-primary mb-0">Total</p>
                                    <h4 class="text-primary mb-1">{{ $reportBlow['total'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Production Order -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Report Slitting</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-start">
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-muted mb-0">Request / Un Post</p>
                                    <h4 class="mb-1">{{ $reportSlt['unposted'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-info mb-0">Posted</p>
                                    <h4 class="mb-1 text-info">{{ $reportSlt['posted'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-success mb-0">Closed</p>
                                    <h4 class="text-success mb-1">{{ $reportSlt['closed'] }}</h4>
                                  <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-primary mb-0">Total</p>
                                    <h4 class="mb-1 text-primary">{{ $reportSlt['total'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Report Folding</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-start">
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-muted mb-0">Request / Un Post</p>
                                    <h4 class="mb-1">{{ $reportFld['unposted'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-info mb-0">Posted / Created PO</p>
                                    <h4 class="mb-1 text-info">{{ $reportFld['posted'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-success mb-0">Closed</p>
                                    <h4 class="mb-1 text-success">{{ $reportFld['closed'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-primary mb-0">Total</p>
                                    <h4 class="mb-1 text-primary">{{ $reportFld['total'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">Report Bag Making</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-start">
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-muted mb-0">Request / Un Post</p>
                                    <h4 class="mb-1">{{ $reportBag['unposted'] }}</h4>
                                   <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-info mb-0">Posted / Created PO</p>
                                    <h4 class="text-info mb-1">{{ $reportBag['posted'] }}</h4>
                                   <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-success mb-0">Closed</p>
                                    <h4 class="text-success mb-1">{{ $reportBag['closed'] }}</h4>
                                   <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-3">
                                    <p class="text-primary mb-0">Total</p>
                                    <h4 class="mb-1 text-primary">{{ $reportBag['total'] }}</h4>
                                    <small class="text-muted"><span class="text-bg-success rounded-2">+0</span> Hari Ini</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
