<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use RealRashid\SweetAlert\Facades\Alert;
use Browser;
use Illuminate\Support\Facades\Crypt;
//use Yajra\DataTables\Facades\Datatables;
use DataTables;//test test test test 

// Model
//START REQUEST SPAREPART AND AUXILIARIES
use App\Models\ProductionReqSparepartAuxiliaries;
use App\Models\ProductionReqSparepartAuxiliariesDetail;

use App\Models\ProductionEntryMaterialUse;
use App\Models\ProductionEntryMaterialUseDetail;

use App\Models\ProductionEntryReportSF;
use App\Models\ProductionEntryReportSFHygiene;
use App\Models\ProductionEntryReportSFPreparation;
use App\Models\ProductionEntryReportSFProductionResult;
use App\Models\HistoryStock;

use App\Models\ProductionEntryReportBlowProductionResult;

//END REQUEST SPAREPART AND AUXILIARIES

class ProductionReportFoldingController extends Controller
{
    use AuditLogsTrait;
	
	//START ENTRY REPORT BLOW
	public function production_entry_report_folding()
    {
		/*TEST
        $datas = ProductionEntryReportSF::leftJoin('work_orders AS b', 'report_sfs.id_work_orders', '=', 'b.id')
                ->select('report_sfs.*', 'b.wo_number')
                ->orderBy('report_sfs.created_at', 'desc')
                ->get();
		*/
        //Audit Log		
        $username= auth()->user()->email; 
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $location='0';
        $access_from=Browser::browserName();
        $activity='View List Entry Report Folding';
        $this->auditLogs($username,$ipAddress,$location,$access_from,$activity);		

        //return view('production.entry_report_blow', compact('datas'));
        return view('production.entry_report_folding');
    }
	public function production_entry_report_folding_json()
    {
        $datas = ProductionEntryReportSF::leftJoin('master_regus AS c', 'report_sfs.id_master_regus', '=', 'c.id')
				->leftJoin('master_work_centers AS d', 'report_sfs.id_master_work_centers', '=', 'd.id')
				->leftJoin('master_customers AS e', 'report_sfs.id_master_customers', '=', 'e.id')
				->leftJoin('master_employees AS f', 'report_sfs.operator', '=', 'f.id')
				
                ->select('report_sfs.*', 'c.regu', 'd.work_center', 'e.name')                
				->selectRaw('f.name AS operator')
				
                ->whereRaw( "left(report_number,2) = 'RF'")
                ->orderBy('report_sfs.created_at', 'desc')
                ->get();
		//print_r($datas);exit;
		return DataTables::of($datas) 			
			->addColumn('report_info', function ($data) {			
				//$report_info = '<p>Report Number : '.$data->report_number.'<br><code>Work Order : </code><br><footer class="blockquote-footer">Date : <cite>'.$data->date.'</cite></footer></p>';
				$report_info = '<p>Report Number : <b>'.$data->report_number.'</b><br><footer class="blockquote-footer">Date : <cite>'.$data->date.'</cite></footer></p>';
				return $report_info;
			})
			->addColumn('order_info', function ($data) {	
				$order_name = explode('|', $data->order_name);	
				$order_name = count($order_name)>1?$order_name[2]:$order_name[0];
				$operator = !empty($data->operator)?'<br><span class="badge bg-success-subtle text-success">Operator : '.$data->operator.'</span>':'';
				
				$status = empty($data->status)?"Tidak Tersedia":$data->status;			
				$order_info = '<p><b>'.$order_name.'</b><br><code>Customer :</code><br>'.$data->name.'<br><footer class="blockquote-footer">Status : <cite>'.$status.'</cite>'.$operator.'</footer></p>';
				return $order_info;
			})
			->addColumn('team', function ($data) {				
				$team = '<p>Regus : '.$data->regu.'<br><code>Shift : '.$data->shift.'</code><br><footer class="blockquote-footer">Work Center : <cite>'.$data->work_center.'</cite></footer></p>';
				return $team;
			})
			->addColumn('checklist', function ($data) {		
				/*
				$checklist = '
				<a href="/production-ent-report-blow-detail/'.sha1($data->id).'" class="btn btn-danger waves-effect btn-label waves-light"><i class="bx bx-check-double label-icon"></i>  Preparation Check</a><br>
				<a href="/production-ent-report-blow-detail/'.sha1($data->id).'" class="btn btn-danger waves-effect btn-label waves-light mt-1"><i class="bx bx-check-double label-icon"></i> Hygiene Check</a>';
				*/
				$id = "'".sha1($data->id)."'";
				$checklist = '
					<a data-bs-toggle="modal" onclick="showPreparation('.$id.')" data-bs-target="#modal_preparation" class="btn btn-danger waves-effect btn-label waves-light"><i class="bx bx-check-double label-icon"></i>  Preparation Check</a><br>
					<a data-bs-toggle="modal" onclick="showHygiene('.$id.')" data-bs-target="#modal_hygiene" class="btn btn-danger waves-effect btn-label waves-light"><i class="bx bx-check-double label-icon"></i>  Hygiene Check</a>';
				
				return $checklist;
			})
			->addColumn('update', function ($data) {	
				/*
				$good = DB::table('report_blow_production_results')
						->select('id','id_report_blows')
						->whereRaw("id_report_blows = '$data->id'")
						->whereRaw("status = 'Good'")
						->groupBy('id','id_report_blows')
						->get()->count();
				*/
				$id = "'".sha1($data->id)."'";
				$return_unposted = "return confirm('Are you sure to un posted this item ?')";
				
				if($data->status=='Un Posted'){
					$update = '
						<a data-bs-toggle="modal" onclick="showUpdateStock('.$id.')" data-bs-target="#modal_update_stock" class="btn btn-success waves-effect btn-label waves-light"><i class="bx bx-sync label-icon"></i>  Update Stock</a>
						
						<!--a href="#" class="btn btn-success waves-effect btn-label waves-light"><i class="bx bx-sync label-icon"></i>  Update Stock</a><br-->
						<!--p class="mt-2"><code>Result : </code><br>
						<footer class="blockquote-footer">Good : <cite>20</cite></footer>
						<footer class="blockquote-footer">Hold : <cite>20</cite></footer>
						<footer class="blockquote-footer">Reject : <cite>20</cite></footer>
						</p-->';
				}else{
					$update = '
						<a data-bs-toggle="modal" onclick="showUpdateStockInfo('.$id.')" data-bs-target="#modal_update_stock_info" class="btn btn-info waves-effect btn-label waves-light"><i class="bx bx-info-circle  label-icon"></i>  Stock Updated</a><br>						
						<a onclick="'.$return_unposted.'" href="/production-entry-report-folding-unposted/'.sha1($data->id).'" class="btn btn-primary waves-effect btn-label waves-light mt-1" onclick="return confirm('."'Anda yakin unposted data ?'".')">
							<i class="bx bx-reply label-icon"></i> Un Posted
						</a>';
				}
				
				return $update;
			})
			->addColumn('action', function ($data) {
				$return_approve = "return confirm('Are you sure to approve this item ?')";
				$return_hold = "return confirm('Are you sure to hold this item ?')";
				$return_delete = "return confirm('Are you sure to delete this item ?')";
				
				$tombol = '
					<center>
				';
				
				if($data->status=='Un Posted'){
					$tombol .= '
							<a target="_blank" href="/production-ent-report-folding-detail/'.sha1($data->id).'" class="btn btn-outline-info waves-effect waves-light">
								<i class="bx bx-edit-alt" title="Edit"></i> EDIT
							</a>
							<a onclick="'.$return_delete.'" href="/production-ent-report-folding-delete/'.sha1($data->id).'" class="btn btn-outline-danger waves-effect waves-light" onclick="return confirm('."'Anda yakin mau menghapus item ini ?'".')">
								<i class="bx bx-trash-alt" title="Delete" ></i> DELETE
							</a>
							<a target="_blank" href="/production-ent-report-folding-print/'.sha1($data->id).'" class="btn btn-dark waves-effect waves-light">
								<i class="bx bx-printer" title="Print"></i> PRINT
							</a>
						</center>					
					';
				}else{
					$tombol .= '
							<a target="_blank" href="/production-ent-report-folding-print/'.sha1($data->id).'" class="btn btn-dark waves-effect waves-light">
								<i class="bx bx-printer" title="Print"></i> PRINT
							</a>
						</center>						
					';
				}
				
				return $tombol;
			})
			->rawColumns(array("report_info", "order_info", "team", "checklist", "update", "action"))
		->make(true);
    }
	
	public function production_entry_report_folding_json_preparation(Request $request){	
		$id_rs = request()->get('id');
		
		$data = ProductionEntryReportSFPreparation::leftJoin('report_sfs as b', 'report_sf_preparation_checks.id_report_sfs', '=', 'b.id')
				->select('report_sf_preparation_checks.*', 'b.report_number')
				->whereRaw( "sha1(report_sf_preparation_checks.id_report_sfs) = $id_rs")
                ->get();
				
		?>
			
			<div class="row field-wrapper">
				<div class="table-responsive">
					<table class="table table-bordered dt-responsive  nowrap w-100">
						<tr>
							<td>Report Number : <b><?=$data[0]->report_number?></b></td>
							<th class="text-center">Ok</th>
							<th class="text-center">Not Ok</th>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Material</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->material=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->material=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Ukuran</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->ukuran=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->ukuran=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Press Roll</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->press_roll=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->press_roll=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Rubber Roll</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->rubber_roll=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->rubber_roll=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Segitiga Pelipat</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->segitiga_pelipat=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->segitiga_pelipat=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Jarum Perforasi</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->jarum_perforasi=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->jarum_perforasi=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						
					</table>
				</div>				
			</div>
		
		<?php
	}	
	public function production_entry_report_folding_json_hygiene(Request $request){	
		$id_rs = request()->get('id');
		
		$data = ProductionEntryReportSFHygiene::leftJoin('report_sfs as b', 'report_sf_hygiene_checks.id_report_sfs', '=', 'b.id')
				->select('report_sf_hygiene_checks.*', 'b.report_number')
				->whereRaw( "sha1(report_sf_hygiene_checks.id_report_sfs) = $id_rs")
                ->get();
				
		?>		
			<div class="row field-wrapper">
				<div class="table-responsive">
					<table class="table table-bordered dt-responsive  nowrap w-100">
						<tr>
							<td>Report Number : <b><?=$data[0]->report_number?></b></td>
							<th class="text-center">Ok</th>
							<th class="text-center">Not Ok</th>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Press Rubber Roll </td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->press_rubber_roll=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->press_rubber_roll=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Body Mesin</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->body_mesin=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->body_mesin=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Lantai Mesin</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->lantai_mesin=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->lantai_mesin=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Cutter</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->cutter=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->cutter=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
					</table>
				</div>				
			</div>
		
		<?php
	}	
	public function production_entry_report_folding_json_update_stock(Request $request){
		
		/*
		?>
		Test
		<?php
		*/
		
		$id_rf = request()->get('id');
		
		$data = ProductionEntryReportSFProductionResult::select('b.report_number','report_sf_production_results.id_report_sfs', 'report_sf_production_results.id')
				->selectRaw('SUM(IF(report_sf_production_results.status="Good", 1, 0)) AS good')
				->selectRaw('SUM(IF(report_sf_production_results.status="Hold", 1, 0)) AS hold')
				->selectRaw('SUM(IF(report_sf_production_results.status="Reject", 1, 0)) AS reject')
				->rightJoin('report_sfs AS b', 'report_sf_production_results.id_report_sfs', '=', 'b.id')
				->whereRaw( "sha1(report_sf_production_results.id_report_sfs) = $id_rf")
				->groupBy('id_report_sfs')
                ->get();
		//print_r($data);
		if(!empty($data[0]->id_report_sfs)){
		?>					
			<!--form method="post" action="/production-entry-report-blow-update-stock" class="form-material m-t-40" enctype="multipart/form-data"-->
			
				<div class="card-header">
					<p class="card-title-desc">
						Production Result : Report Number <b><?= $data[0]->report_number; ?></b><br>
						Total Product : <b><?= $data[0]->good+$data[0]->hold+$data[0]->reject; ?></b>
					</p>
				</div>
				<div class="card-body">
					<div class="row g-4">
						<div class="col-sm-4">
							<div class="alert alert-success alert-dismissible fade show px-4 mb-0 text-center" role="alert">
								<i class="mdi mdi-check-all d-block display-4 mt-2 mb-3 text-success"></i>
								<h5 class="text-success"><?= $data[0]->good; ?></h5>
								<p>Good Product</p>
							</div>
						</div><!-- end col -->

						<div class="col-sm-4">
							<div class="alert alert-warning alert-dismissible fade show px-4 mb-0 text-center" role="alert">
								<i class="mdi mdi-alert-outline d-block display-4 mt-2 mb-3 text-warning"></i>
								<h5 class="text-warning"><?= $data[0]->hold; ?></h5>
								<p>Hold Product</p>
							</div>
						</div><!-- end col -->

						<div class="col-sm-4">
							<div class="alert alert-danger alert-dismissible fade show px-4 mb-0 text-center" role="alert">
								<i class="mdi mdi-block-helper d-block display-4 mt-2 mb-3 text-danger"></i>
								<h5 class="text-danger"><?= $data[0]->reject; ?></h5>
								<p>Reject Product</p>
							</div>
						</div><!-- end col -->
					</div><!-- end row -->
				</div>
				<div class="modal-footer">
					<a href="/production-entry-report-folding-update-stock/<?= sha1($data[0]->id_report_sfs); ?>" type="submit" class="btn btn-primary btn-lg"><i class="bx bx-save" title="Update"></i> UPDATE</a>
					<!--a href="#" type="submit" class="btn btn-primary btn-lg"><i class="bx bx-save" title="Update"></i> UPDATE</a-->
				</div>
			<!--/form-->
		<?php
		}else{
		?>
			<div class="card-body">
				<p class="card-title-desc">Production Result : TIDAK TERSEDIA</b></p>
			</div>
		<?php
		}
	}	
	public function production_entry_report_folding_json_update_stock_info(Request $request){
		
		/*
		?>
		Test
		<?php
		*/
		
		$id_rs = request()->get('id');
		
		$data = ProductionEntryReportSFProductionResult::select('b.report_number','report_sf_production_results.id_report_sfs', 'report_sf_production_results.id')
				->selectRaw('SUM(IF(report_sf_production_results.status="Good", 1, 0)) AS good')
				->selectRaw('SUM(IF(report_sf_production_results.status="Hold", 1, 0)) AS hold')
				->selectRaw('SUM(IF(report_sf_production_results.status="Reject", 1, 0)) AS reject')
				->rightJoin('report_sfs AS b', 'report_sf_production_results.id_report_sfs', '=', 'b.id')
				->whereRaw( "sha1(report_sf_production_results.id_report_sfs) = $id_rs")
				->groupBy('id_report_sfs')
                ->get();
		//print_r($data);
		if(!empty($data[0]->id_report_sfs)){
		?>					
			<!--form method="post" action="/production-entry-report-blow-update-stock" class="form-material m-t-40" enctype="multipart/form-data"-->
			
				<div class="card-header">
					<p class="card-title-desc">
						Production Result : Report Number <b><?= $data[0]->report_number; ?></b><br>
						Total Product : <b><?= $data[0]->good+$data[0]->hold+$data[0]->reject; ?></b>
					</p>
				</div>
				<div class="card-body">
					<div class="row g-4">
						<div class="col-sm-4">
							<div class="alert alert-success alert-dismissible fade show px-4 mb-0 text-center" role="alert">
								<i class="mdi mdi-check-all d-block display-4 mt-2 mb-3 text-success"></i>
								<h5 class="text-success"><?= $data[0]->good; ?></h5>
								<p>Good Product</p>
							</div>
						</div><!-- end col -->

						<div class="col-sm-4">
							<div class="alert alert-warning alert-dismissible fade show px-4 mb-0 text-center" role="alert">
								<i class="mdi mdi-alert-outline d-block display-4 mt-2 mb-3 text-warning"></i>
								<h5 class="text-warning"><?= $data[0]->hold; ?></h5>
								<p>Hold Product</p>
							</div>
						</div><!-- end col -->

						<div class="col-sm-4">
							<div class="alert alert-danger alert-dismissible fade show px-4 mb-0 text-center" role="alert">
								<i class="mdi mdi-block-helper d-block display-4 mt-2 mb-3 text-danger"></i>
								<h5 class="text-danger"><?= $data[0]->reject; ?></h5>
								<p>Reject Product</p>
							</div>
						</div><!-- end col -->
					</div><!-- end row -->
				</div>
			<!--/form-->
		<?php
		}else{
		?>
			<div class="card-body">
				<p class="card-title-desc">Production Result : TIDAK TERSEDIA</b></p>
			</div>
		<?php
		}
	}
	
	public function production_entry_report_folding_add(Request $request){
		$ms_departements = DB::table('master_departements')
                        ->select('name','id')
                        ->get();
        
		/*
        $ms_work_orders = DB::table('work_orders AS a')
						->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
						->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
                        ->select('a.*','c.id AS id_master_customers')
                        ->whereRaw( "left(wo_number,5) = 'WOBLW'")
                        ->whereRaw( "a.type_product = 'WIP'")
                        ->get();
		*/
		$ms_ketua_regu = DB::table('master_employees')
                        ->select('id','name')
                        ->whereRaw( "status = 'Active'")
                        ->get();
						
		$ms_operator = DB::table('master_employees')
                        ->select('id','name')
                        ->whereRaw( "status = 'Active'")
                        ->get();	
						
        $ms_known_by = DB::table('master_employees')
                        ->select('id','name')
                        ->whereRaw( "id_master_bagians IN('3','4')")
                        ->get();
		//print_r($ms_work_orders);exit;
        $formattedCode = $this->production_entry_report_folding_create_code();
		
        //Audit Log
        $username= auth()->user()->email; 
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $location='0';
        $access_from=Browser::browserName();
        $activity='Add Entry Report Folding';
        $this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

        return view('production.entry_report_folding_add',compact('ms_departements','ms_ketua_regu','ms_operator','ms_known_by','formattedCode'));			
    }
	private function production_entry_report_folding_create_code(){
		$lastCode = ProductionEntryReportSF::whereRaw( "left(report_number,2) = 'RF'")
		->orderBy('created_at', 'desc')
        ->value(DB::raw('RIGHT(report_number, 6)'));
    
        // Jika tidak ada nomor urut sebelumnya, atur ke 0
        $lastCode = $lastCode ? $lastCode : 0;

        // Tingkatkan nomor urut
        $nextCode = $lastCode + 1;

        // Format kode dengan panjang 7 karakter
        $formattedCode = 'RF' . str_pad($nextCode, 6, '0', STR_PAD_LEFT);
		
		return $formattedCode;
	}
	
	public function production_entry_report_folding_save(Request $request){
		//echo "disini";exit;
		
		//print_r($_POST);exit;
        if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('save')) {
            $pesan = [
                'date.required' => 'Cannot Be Empty',
                'id_master_products.required' => 'Cannot Be Empty',
                'id_master_customers.required' => 'Cannot Be Empty',
                'id_master_work_centers.required' => 'Cannot Be Empty',
                'id_master_regus.required' => 'Cannot Be Empty',                    
                'id_operator.required' => 'Cannot Be Empty',                
                'shift.required' => 'Cannot Be Empty',            
                'id_ketua_regu.required' => 'Cannot Be Empty',             
                'id_known_by.required' => 'Cannot Be Empty',                
            ];

            $validatedData = $request->validate([
                'date' => 'required',
                'id_master_products' => 'required',
                'id_master_customers' => 'required',
                'id_master_work_centers' => 'required',
                'id_master_regus' => 'required',
                'shift' => 'required',
                'id_ketua_regu' => 'required',
                'id_operator' => 'required',
                'id_known_by' => 'required',

            ], $pesan);			
			
			$validatedData['order_name'] = $_POST['id_master_products'];
			$validatedData['report_number'] = $this->production_entry_report_folding_create_code();
			$validatedData['engine_shutdown_description'] = $_POST['engine_shutdown_description'];
			$validatedData['note'] = $_POST['note'];
			$validatedData['ketua_regu'] = $_POST['id_ketua_regu'];
			//$validatedData['id_cms_users'] =  Auth::user()->id;
			$validatedData['operator'] =  $_POST['id_operator'];
			$validatedData['id_cms_users'] =  $_POST['id_cms_user'];
			$validatedData['know_by'] = $_POST['id_known_by'];
			$validatedData['type'] = 'Folding';
			$validatedData['status'] = 'Un Posted';
			
            $response = ProductionEntryReportSF::create($validatedData);
			
			if(!empty($response)){
				$dataHygiene = array(
									'id_report_sfs' => $response->id,
									'press_rubber_roll' => 'Ok',
									'body_mesin' => 'Ok',
									'lantai_mesin' => 'Ok',
									'cutter' => 'Ok',
									'created_at' => date('Y-m-d H:i:s'),
									'updated_at' => date('Y-m-d H:i:s')
								);
				ProductionEntryReportSFHygiene::create($dataHygiene);
				
				$dataPreparation = array(
									'id_report_sfs' => $response->id,
									'material' => 'Ok',
									'ukuran' => 'Ok',
									'press_roll' => 'Ok',
									'rubber_roll' => 'Ok',
									'segitiga_pelipat' => 'Ok',
									'jarum_perforasi' => 'Ok',
									'created_at' => date('Y-m-d H:i:s'),
									'updated_at' => date('Y-m-d H:i:s')
								);
				ProductionEntryReportSFPreparation::create($dataPreparation);
			}
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Save Entry Report Folding ID="'.$response->id.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);//tinggal uji insert
			
            return Redirect::to('/production-ent-report-folding-detail/'.sha1($response->id))->with('pesan', 'Add Successfuly.');
            //return Redirect::to('/production-ent-report-slitting');
        }
    }
	public function production_entry_report_folding_detail($response_id){
		$data = ProductionEntryReportSF::select("report_sfs.*")
				->whereRaw( "sha1(report_sfs.id) = '$response_id'")
                ->get();
		//print_r($data);exit;
		if(!empty($data[0])){
			if($data[0]->status=="Un Posted"){
				
				$data_detail_production = DB::table('report_sf_production_results AS a')
						->leftJoin('work_orders AS b', 'a.id_work_orders', '=', 'b.id')
						->leftJoin('barcode_detail as c', 'a.barcode_start', '=', 'c.barcode_number')
						->select('a.*','b.wo_number', 'c.join')
						->whereRaw( "sha1(a.id_report_sfs) = '$response_id'")
						->get();	
				
				//Jika 1 Report hanya boleh 1 WO
				if(!empty($data_detail_production[0])){
					$ms_work_orders = DB::table('work_orders AS a')
						->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
						->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
						->select('a.*','c.id AS id_master_customers')
						->whereRaw( "a.type_product = 'FG'")
						->whereRaw( "left(wo_number,5) = 'WOFLD'")
						->whereRaw( "a.id = '".$data_detail_production[0]->id_work_orders."'")
						->get();
				}else{
					$order_name = explode('|', $data[0]->order_name);	
					
					$ms_work_orders = DB::table('work_orders AS a')
						->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
						->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
						->select('a.*','c.id AS id_master_customers')
						->whereRaw( "left(wo_number,5) = 'WOFLD'")
						->whereRaw( "a.id_master_products = '".$order_name[1]."'")
						->get();
				}
				
				/*
				//Jika 1 report bisa banyak wo
				$ms_work_orders = DB::table('work_orders AS a')
					->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
					->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
					->select('a.*','c.id AS id_master_customers')
					->whereRaw( "left(wo_number,5) = 'WOFLD'")
					//->whereRaw( "a.type_product = 'WIP'")
					->get();
				*/
				$data_detail_preparation = DB::table('report_sf_preparation_checks')
						->select('report_sf_preparation_checks.*')
						->whereRaw( "sha1(id_report_sfs) = '$response_id'")
						->get();
				$data_detail_hygiene = DB::table('report_sf_hygiene_checks')
						->select('report_sf_hygiene_checks.*')
						->whereRaw( "sha1(id_report_sfs) = '$response_id'")
						->get();
				
				$ms_ketua_regu = DB::table('master_employees')
						->select('id','name')
						->whereRaw( "status = 'Active'")
						->get(); 
				$ms_operator = DB::table('master_employees')
						->select('id','name')
						->whereRaw( "status = 'Active'")
						->get(); 		
				$ms_known_by = DB::table('master_employees')
						->select('id','name')
						->whereRaw( "id_master_bagians IN('3','4')")
						->get();
							
				//Audit Log
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Detail Entry Report Folding ID="'.$data[0]->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

				return view('production.entry_report_folding_detail',compact('data','ms_work_orders','data_detail_preparation','data_detail_hygiene','data_detail_production','ms_ketua_regu','ms_operator','ms_known_by'));
				
			}else{
				return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error.');
			}
		}else{
			return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error.');
		}
		
    }   
	public function  production_entry_report_folding_update(Request $request){
		//print_r($_POST);exit;
		if ($request->has('rf_update')) {            
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryReportSF::whereRaw( "sha1(report_sfs.id) = '$request_id'")
				->select('id')
				->get();
				
            $pesan = [
                'date.required' => 'Cannot Be Empty',
                'id_master_products.required' => 'Cannot Be Empty',
                'id_master_customers.required' => 'Cannot Be Empty',
                'id_master_work_centers.required' => 'Cannot Be Empty',
                'id_master_regus.required' => 'Cannot Be Empty',                
                'shift.required' => 'Cannot Be Empty',            
				'id_ketua_regu.required' => 'Cannot Be Empty',                    
                'id_operator.required' => 'Cannot Be Empty',    
                'id_known_by.required' => 'Cannot Be Empty',                
            ];

            $validatedData = $request->validate([
                'date' => 'required',
                'id_master_products' => 'required',
                'id_master_customers' => 'required',
                'id_master_work_centers' => 'required',
                'id_master_regus' => 'required',
                'shift' => 'required',
				'id_ketua_regu' => 'required',
                'id_operator' => 'required',
                'id_known_by' => 'required',

            ], $pesan);			
			
			$validatedData['engine_shutdown_description'] = $_POST['engine_shutdown_description'];
			$validatedData['note'] = $_POST['note'];
			$validatedData['id_cms_users'] =  $_POST['id_cms_user'];
			
			$validatedData['know_by'] = $_POST['id_known_by'];
			unset($validatedData["id_known_by"]);
			
			$validatedData['ketua_regu'] = $_POST['id_ketua_regu'];
			unset($validatedData["id_ketua_regu"]);
			
			$validatedData['operator'] = $_POST['id_operator'];
			unset($validatedData["id_operator"]);
			
			$validatedData['order_name'] = $_POST['id_master_products'];
			unset($validatedData["id_master_products"]);
			
            ProductionEntryReportSF::where('id', $data[0]->id)
				->update($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Update Entry Report Folding ID="'.$data[0]->id.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
			return Redirect::to('/production-ent-report-folding-detail/'.$request_id)->with('pesan', 'Update Successfuly.');
        } elseif ($request->has('pc_update')) {
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryReportSFPreparation::whereRaw( "sha1(report_sf_preparation_checks.id_report_sfs) = '$request_id'")
				->select('id', 'id_report_sfs')
				->get();
			
            $pesan = [
                'pc_material.required' => 'Cannot Be Empty',
                'pc_ukuran.required' => 'Cannot Be Empty',
                'pc_press_roll.required' => 'Cannot Be Empty',
                'pc_rubber_roll.required' => 'Cannot Be Empty',          
                'pc_segitiga_pelipat.required' => 'Cannot Be Empty',          
                'pc_jarum_perforasi.required' => 'Cannot Be Empty',          
            ];

            $validatedData = $request->validate([
                'pc_material' => 'required',
                'pc_ukuran' => 'required',
                'pc_press_roll' => 'required',
                'pc_rubber_roll' => 'required',
                'pc_segitiga_pelipat' => 'required',
                'pc_jarum_perforasi' => 'required',

            ], $pesan);	
			
            $updatedData = [
                'material' => $validatedData['pc_material'],
                'ukuran' => $validatedData['pc_ukuran'],
                'press_roll' => $validatedData['pc_press_roll'],
                'rubber_roll' => $validatedData['pc_rubber_roll'],
                'segitiga_pelipat' => $validatedData['pc_segitiga_pelipat'],
                'jarum_perforasi' => $validatedData['pc_jarum_perforasi'],
            ] ;
			
            ProductionEntryReportSFPreparation::where('id', $data[0]->id)
				->where('id_report_sfs', $data[0]->id_report_sfs)
				->update($updatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Update Report Folding Preparation Check ID="'.$data[0]->id.'", ID Report Folding="'.$data[0]->id_report_sfs.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-report-folding-detail/'.$request_id)->with('pesan', 'Update Successfuly.');
		} elseif ($request->has('hc_update')) {
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryReportSFHygiene::whereRaw( "sha1(report_sf_hygiene_checks.id_report_sfs) = '$request_id'")
				->select('id', 'id_report_sfs')
				->get();
			
            $pesan = [
                'hc_press_rubber_roll.required' => 'Cannot Be Empty',
                'hc_body_mesin.required' => 'Cannot Be Empty',
                'hc_lantai_mesin.required' => 'Cannot Be Empty',
                'hc_cutter.required' => 'Cannot Be Empty',           
            ];

            $validatedData = $request->validate([
                'hc_press_rubber_roll' => 'required',
                'hc_body_mesin' => 'required',
                'hc_lantai_mesin' => 'required',
                'hc_cutter' => 'required',

            ], $pesan);			
			
			$updatedData = [
                'press_rubber_roll' => $validatedData['hc_press_rubber_roll'],
                'body_mesin' => $validatedData['hc_body_mesin'],
                'lantai_mesin' => $validatedData['hc_lantai_mesin'],
                'cutter' => $validatedData['hc_cutter'],
            ] ;
			
            ProductionEntryReportSFHygiene::where('id', $data[0]->id)
				->where('id_report_sfs', $data[0]->id_report_sfs)
				->update($updatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Update Report Folding Hygiene Check ID="'.$data[0]->id.'", ID Report Folding="'.$data[0]->id_report_sfs.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-report-folding-detail/'.$request_id)->with('pesan', 'Update Successfuly.');
		} else {
			return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error.');
		}
    }
	public function production_entry_report_folding_detail_production_result_add(Request $request){
        if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('save')) {
			
			$barcode_start = $_POST['id_master_barcode_start'];
			/*
			$data_slitting = ProductionEntryReportSFProductionResult::whereRaw( "report_sf_production_results.barcode = '$barcode_start'")
				->select('*')
				->get();
			*/
			$data_slitting = DB::table('report_sf_production_results as a')
				->leftJoin('report_sfs as b', 'a.id_report_sfs', '=', 'b.id')
				->whereRaw( "a.barcode = '$barcode_start'" )
				->select('a.*', 'b.report_number', 'b.order_name')
				->get();
			
			$cek_attempt_barcode_start = DB::table('barcode_detail')
				->select('*')
				->where('barcode_number', $_POST['id_master_barcode_start'])
				->get();
				
			$order_name = explode('|', $data_slitting[0]->note);
			
			//$type_wo = explode('|', $_POST['id_master_products']);
			//echo $_POST['id_master_products'];exit;
			//echo($data_blow[0]->id);exit;
			//echo($data_slitting[0]->id_report_sfs);exit;
			
			if($cek_attempt_barcode_start[0]->used_attempt+1>10){
				return Redirect::to('/production-ent-report-folding-detail/'.$request_id)->with('pesan_danger','Barcode Start Tidak Mencukupi');
			}else{
				
				if(!empty($data_slitting[0]->id_report_sfs)){
					//print_r($_POST);exit;
					$request_id = $_POST['request_id'];		
					$data = ProductionEntryReportSF::whereRaw( "sha1(report_sfs.id) = '$request_id'")
						->select('id')
						->get();
						
					$pesan = [
						'start.required' => 'Cannot Be Empty',
						'finish.required' => 'Cannot Be Empty',
						'id_master_barcode_start.required' => 'Cannot Be Empty',
						'id_work_orders.required' => 'Cannot Be Empty',
						'id_master_barcode.required' => 'Cannot Be Empty',
						'thickness.required' => 'Cannot Be Empty',
						'length.required' => 'Cannot Be Empty',
						'width.required' => 'Cannot Be Empty',                
						'weight.required' => 'Cannot Be Empty',
						'status.required' => 'Cannot Be Empty',
						'id_work_orders.required' => 'Cannot Be Empty',                
					];

					$validatedData = $request->validate([
						'start' => 'required',
						'finish' => 'required',
						'id_master_barcode_start' => 'required',
						'id_work_orders' => 'required',
						'id_master_barcode' => 'required',
						'thickness' => 'required',
						'length' => 'required',
						'width' => 'required',
						'weight' => 'required',
						'status' => 'required',
						'id_work_orders' => 'required',

					], $pesan);			
					
					$validatedData['start_time'] = $_POST['start'];		
					$validatedData['finish_time'] = $_POST['finish'];		
					$validatedData['barcode_start'] = $_POST['id_master_barcode_start'];
					$validatedData['barcode'] = $_POST['id_master_barcode'];
					$validatedData['note'] = $_POST['id_master_products_detail'];
					$validatedData['waste'] = $_POST['waste'];
					$validatedData['cause_waste'] = $_POST['cause_waste'];
					
					unset($validatedData["start"]);
					unset($validatedData["finish"]);
					unset($validatedData["id_master_barcode_start"]);
					unset($validatedData["id_master_barcode"]);
					unset($validatedData["id_master_products_detail"]);
					
					$validatedData['id_report_sfs'] = $data[0]->id;
					$validatedData['id_report_blows'] = $data_slitting[0]->id_report_sfs;
					$validatedData['id_report_blow_production_result'] = $data_slitting[0]->id;
					$validatedData['type_result'] = 'Folding';
					
					$response = ProductionEntryReportSFProductionResult::create($validatedData);
					
					if(!empty($response)){
						//HARUS UPDATE STATUS BARCODE
						//$instock_type = $type_wo[0] == 'WIP' ? 'In Stock SLT WIP' : 'In Stock SLT FG';		
						
						$updatedData['status'] = $_POST['status']=="Good"? 'In Stock FLD' : $_POST['status'] ;//pukul rata jenis nya join lagi berdasarkan wo
						
						DB::table('barcode_detail')
						->where('barcode_number', $response->barcode)
						->update($updatedData);
						/*OLD
						if(empty($_POST['used_next_shift']) || isset($_POST['join'])){
							if(empty($_POST['used_next_shift'])){
								$updatedDataBS['used_next_shift'] = '0';						
							}
							if(isset($_POST['join'])){
								$updatedDataBS['join'] = $_POST['join'];	
							}
							DB::table('barcode_detail')
							->where('barcode_number', $response->barcode_start)
							->update($updatedDataBS);
						}
						*/
						if(!isset($_POST['used_next_shift']) || isset($_POST['used_next_shift']) || isset($_POST['join'])){
							$data_barcode_start = DB::table('barcode_detail')
								->select('*')
								->where('barcode_number', $response->barcode_start)
								->get();
							
							if(!isset($_POST['used_next_shift'])){
								$updatedDataBS['used_next_shift'] = '0';						
							}
							if(isset($_POST['used_next_shift'])){
								$updatedDataBS['used_next_shift'] = '1';						
							}
							//if(isset($_POST['join'])){
							if(isset($_POST['join']) && !empty($_POST['join'])){
								$updatedDataBS['join'] = $_POST['join'];	
							}
							$updatedDataBS['used_product'] = !empty($data_barcode_start[0]->used_product) ? $data_barcode_start[0]->used_product : 'FG' ;
							$updatedDataBS['used_attempt'] = $data_barcode_start[0]->used_attempt + 1;
							
							DB::table('barcode_detail')
							->where('barcode_number', $response->barcode_start)
							->update($updatedDataBS);
						}
						//PENGURANGAN STOCK WIP/FG
						if($updatedDataBS['used_product']=='FG' AND $updatedDataBS['used_attempt']=='10'){
							
							if($updatedDataBS['used_product']=='FG' AND $updatedDataBS['used_attempt']=='10'){
								//echo "FG";
								$data_produk = DB::table('master_wips')
									->select('*')
									->where('id', $order_name[1])
									->get();
								//print_r($data_produk);
								$updatedDataMS['stock'] = $data_produk[0]->stock - 1 ;
								$updatedDataMS['weight'] = $data_produk[0]->weight - $data_slitting[0]->weight ;//
								
								$responseUpdateMaster = DB::table('master_wips')
															->where('id', $order_name[1])
															->update($updatedDataMS);
							}
							
							if($responseUpdateMaster){	
								$data_usage_to = DB::table('report_sf_production_results as a')
									->leftJoin('report_sfs as b', 'a.id_report_sfs', '=', 'b.id')
									->whereRaw( "a.barcode_start = '$response->barcode_start'" )
									->whereRaw( "a.type_result = 'Folding'" )
									->select(
										DB::raw('GROUP_CONCAT(DISTINCT report_number SEPARATOR ", ") AS report_number')
									)
									->groupBy('a.barcode_start')
									->get();
								
								$validatedData = ([
									'id_good_receipt_notes_details' => $data_slitting[0]->report_number,
									'usage_to' => $data_usage_to[0]->report_number,//hasil nya bisa lebih dari satu report number
									'type_product' => $order_name[0],
									'id_master_products' => $order_name[1],
									'qty' => '1',
									'type_stock' => 'OUT',
									'weight' => $data_slitting[0]->weight,//
									'date' => date("Y-m-d"),
									'barcode' => $response->barcode_start,
									'remarks' => 'From Slitting Usage To Folding'
								]);	
								HistoryStock::create($validatedData);
							}
							
							
						}
						
						//Audit Log		
						$username= auth()->user()->email; 
						$ipAddress=$_SERVER['REMOTE_ADDR'];
						$location='0';
						$access_from=Browser::browserName();
						$activity='Add Production Result Entry Report Folding ID ="'.$response->id.'"';
						$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
						
						return Redirect::to('/production-ent-report-folding-detail/'.$request_id)->with('pesan', 'Add Successfuly.');
					}else{
						return Redirect::to('/production-ent-report-folding-detail/'.$request_id)->with('pesan_danger', 'There Is An Error.');
					}
				}else{
					return Redirect::to('/production-ent-report-folding-detail/'.$request_id)->with('pesan_danger', 'There Is An Error.');
				}
			}
        }
    }
	public function production_entry_report_folding_detail_production_result_edit($response_id_rs, $response_id_rs_pr){
		//echo $response_id_rb.' - '.$response_id_rb_pr; exit;
		//print_r($_POST);exit;
		$data = DB::table('report_sf_production_results as a')
			->leftJoin('report_sfs as b', 'a.id_report_sfs', '=', 'b.id')
			->leftJoin('barcode_detail as c', 'a.barcode_start', '=', 'c.barcode_number')
			->select('a.*', 'c.used_next_shift', 'c.join')
			->whereRaw( "sha1(a.id_report_sfs) = '$response_id_rs'")
			->whereRaw( "sha1(a.id) = '$response_id_rs_pr'")
			->get();
		
		$ms_work_orders = DB::table('work_orders AS a')
			->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
			->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
			->select('a.*','c.id AS id_master_customers')
			->whereRaw( "left(wo_number,5) = 'WOFLD'")
			->whereRaw( "a.id = '".$data[0]->id_work_orders."'")
			//->whereRaw( "a.type_product = 'WIP'")
			->get();
			
		//print_r($data);exit;
		if(!empty($data[0])){			
			return view('production.entry_report_folding_detail_edit_production_result', compact('data','ms_work_orders'));			
		}else{
			return Redirect::to('/production-ent-report-folding-detail/'.$response_id_rs)->with('pesan_danger', 'There Is An Error.');
		}
    } 
	public function production_entry_report_folding_detail_production_result_edit_save(Request $request){
		//print_r($_POST);exit;
		//sampe sini cek data sebelum edit terutama update status barcode
		$response_id_rf = $_POST['token_rf'];
		$response_id_rf_pr = $_POST['token_rf_pr'];
		/*
		$data = DB::table('report_sf_production_results as a')
			->select('a.*')
			->whereRaw( "sha1(a.id_report_sfs) = '$response_id_rf'")
			->whereRaw( "sha1(a.id) = '$response_id_rf_pr'")
			->get();
		*/
		$data = DB::table('report_sf_production_results as a')
			->leftJoin('report_sfs as b', 'a.id_report_sfs', '=', 'b.id')
			->whereRaw( "sha1(a.id_report_sfs) = '$response_id_rf'")
			->whereRaw( "sha1(a.id) = '$response_id_rf_pr'")
			->select('a.*', 'b.report_number')
			->get();
			
		$barcode_start = $_POST['id_master_barcode_start'];
		/*
		$data_slitting = ProductionEntryReportSFProductionResult::whereRaw( "report_sf_production_results.barcode = '$barcode_start'")
			->select('*')
			->get();
		*/
		$data_slitting = DB::table('report_sf_production_results as a')
			->leftJoin('report_sfs as b', 'a.id_report_sfs', '=', 'b.id')
			->whereRaw( "a.barcode = '$barcode_start'" )
			->select('a.*', 'b.report_number', 'b.order_name')
			->get();
		
		$cek_attempt_barcode_start = DB::table('barcode_detail')
			->select('*')
			->where('barcode_number', $_POST['id_master_barcode_start'])
			->get();
		
		$order_name = explode('|', $data_slitting[0]->note);
		
		$type_wo = explode('|', $_POST['id_master_products']);
		
		if($cek_attempt_barcode_start[0]->used_attempt+1>10){
			return Redirect::to('/production-ent-report-folding-detail/'.$response_id_rf)->with('pesan','Barcode Start Tidak Mencukupi');
		}else{
			if(!empty($data[0])){	
				$pesan = [
					'start.required' => 'Cannot Be Empty',
					'finish.required' => 'Cannot Be Empty',
					'id_master_barcode_start.required' => 'Cannot Be Empty',
					'id_work_orders.required' => 'Cannot Be Empty',
					'id_master_barcode.required' => 'Cannot Be Empty',
					'thickness.required' => 'Cannot Be Empty',
					'length.required' => 'Cannot Be Empty',
					'width.required' => 'Cannot Be Empty',                
					'weight.required' => 'Cannot Be Empty',
					'status.required' => 'Cannot Be Empty',
					'id_work_orders.required' => 'Cannot Be Empty',            
				];

				$validatedData = $request->validate([
					'start' => 'required',
					'finish' => 'required',
					'id_master_barcode_start' => 'required',
					'id_work_orders' => 'required',
					'id_master_barcode' => 'required',
					'thickness' => 'required',
					'length' => 'required',
					'width' => 'required',
					'weight' => 'required',
					'status' => 'required',
					'id_work_orders' => 'required',

				], $pesan);			
				
					
				$validatedData['start_time'] = $_POST['start'];		
				$validatedData['finish_time'] = $_POST['finish'];		
				$validatedData['barcode_start'] = $_POST['id_master_barcode_start'];
				$validatedData['barcode'] = $_POST['id_master_barcode'];
				$validatedData['note'] = $_POST['id_master_products'];
				$validatedData['waste'] = $_POST['waste'];
				$validatedData['cause_waste'] = $_POST['cause_waste'];
				
				unset($validatedData["start"]);
				unset($validatedData["finish"]);
				unset($validatedData["id_master_barcode_start"]);
				unset($validatedData["id_master_barcode"]);
				unset($validatedData["id_master_products"]);
				
				$validatedData['id_report_blows'] = $data_slitting[0]->id_report_blows;
				$validatedData['id_report_blow_production_result'] = $data_slitting[0]->id;
							
				$response = ProductionEntryReportSFProductionResult::where('id', $data[0]->id)
					->where('id', $data[0]->id)
					->update($validatedData);
				
				/*
				//Jika Barcode Bisa Digunakan Lagi, Sesuaikan status data barcode menjadi NULL
				$updatedData['status'] = 'Un Used';
				
				DB::table('barcode_detail')
				->where('barcode_number', $data->barcode)
				->update($updatedData);
				*/
				if ($response){
						
					//$instock_type = $type_wo[0] == 'WIP' ? 'In Stock SLT WIP' : 'In Stock SLT FG';			
					$updatedData['status'] = $_POST['status']=="Good" ? 'In Stock FLD' : $_POST['status'];
				
					$response_barcode = DB::table('barcode_detail')
						->where('barcode_number', $validatedData['barcode'])
						->update($updatedData);
					
					if(!isset($_POST['used_next_shift']) || isset($_POST['used_next_shift']) || isset($_POST['join'])){
						$data_barcode_start = DB::table('barcode_detail')
							->select('*')
							->where('barcode_number', $validatedData['barcode_start'])
							->get();
						
						if(!isset($_POST['used_next_shift'])){
							$updatedDataBS['used_next_shift'] = '0';						
						}
						if(isset($_POST['used_next_shift'])){
							$updatedDataBS['used_next_shift'] = '1';						
						}
						if(isset($_POST['join'])){
							$updatedDataBS['join'] = $_POST['join']==''?'-':$_POST['join'];	
						}
						
						$updatedDataBS['used_product'] = !empty($data_barcode_start[0]->used_product) ? $data_barcode_start[0]->used_product : 'FG' ;
						$updatedDataBS['used_attempt'] = $data_barcode_start[0]->used_attempt + 1;
						
						DB::table('barcode_detail')
						->where('barcode_number', $validatedData['barcode_start'])
						->update($updatedDataBS);					
					}
					
					//PENYESUAIAN BARCODE END
					if($validatedData['barcode'] <> $data[0]->barcode){						
						DB::table('barcode_detail')
						->where('barcode_number', $data[0]->barcode)
						->update(['status' => null]);
						
					}
					
					//PENYESUAIAN BARCODE START
					if($validatedData['barcode_start'] <> $data[0]->barcode_start){	
						/*
						DB::table('barcode_detail')
						->where('barcode_number', $data[0]->barcode_start)
						->update(['used_next_shift' => '1', 'join' => '-']);
						*/
						
						//Penyesuaian Barcode Start OLD
						$data_barcode_start_old = DB::table('barcode_detail')
							->select('*')
							->where('barcode_number', $data[0]->barcode_start)
							->get();
							
						$data_slitting_barcode_start_old = DB::table('report_sf_production_results as a')
							->leftJoin('report_sfs as b', 'a.id_report_sfs', '=', 'b.id')
							->whereRaw( "a.barcode = '".$data[0]->barcode_start."'" )
							->select('a.*', 'b.report_number', 'b.order_name')
							->get();
							
						$order_name_barcode_start_old = explode('|', $data_slitting_barcode_start_old[0]->note);
						
						$updatedDataBSO['used_next_shift'] = '1';
						$updatedDataBSO['join'] = '-';
						$updatedDataBSO['used_product'] = ($data_barcode_start_old[0]->used_attempt - 1) > 0 ? $data_barcode_start_old[0]->used_product : null ;
						$updatedDataBSO['used_attempt'] = $data_barcode_start_old[0]->used_attempt - 1;
						
						$update_bso = DB::table('barcode_detail')
							->where('barcode_number', $data[0]->barcode_start)
							->update($updatedDataBSO);					
						
						if($update_bso AND ($updatedDataBSO['used_product']=='FG' AND $data_barcode_start_old[0]->used_attempt=='10')){
							
							if($updatedDataBSO['used_product']=='FG' AND $data_barcode_start_old[0]->used_attempt=='10'){
								
								$data_produk = DB::table('master_wips')
									->select('*')
									->where('id', $order_name_barcode_start_old[1])
									->get();
								
								$updatedDataMS_BSO['stock'] = $data_produk[0]->stock + 1 ;
								$updatedDataMS_BSO['weight'] = $data_produk[0]->weight + $data_slitting_barcode_start_old[0]->weight;
								
								$responseUpdateMasterBSO = DB::table('master_wips')
										->where('id', $order_name_barcode_start_old[1])
										->update($updatedDataMS_BSO);
							}
							
							if($responseUpdateMasterBSO){	
								//ProductionEntryReportBlowProductionResult::whereRaw( "sha1(id) = '$id'" )->delete();
								
								DB::table('history_stocks')
								->whereRaw( "barcode = '".$data[0]->barcode_start."' and type_stock='OUT'" )
								->delete();
							}
						}
						
						//Penyesuaian Barcode Start NEW
						if($updatedDataBS['used_product']=='FG' AND $updatedDataBS['used_attempt']=='10'){
							
							if($updatedDataBS['used_product']=='FG' AND $updatedDataBS['used_attempt']=='10'){
								//echo "FG";
								$data_produk = DB::table('master_wips')
									->select('*')
									->where('id', $order_name[1])
									->get();
								//print_r($data_produk);
								$updatedDataMS['stock'] = $data_produk[0]->stock - 1 ;
								$updatedDataMS['weight'] = $data_produk[0]->weight - $data_slitting[0]->weight ;
								
								$responseUpdateMaster = DB::table('master_wips')
															->where('id', $order_name[1])
															->update($updatedDataMS);
							}
							
							if($responseUpdateMaster){	
								$data_usage_to = DB::table('report_sf_production_results as a')
									->leftJoin('report_sfs as b', 'a.id_report_sfs', '=', 'b.id')
									->whereRaw( "a.barcode_start = '$response->barcode_start'" )
									->select(
										DB::raw('GROUP_CONCAT(DISTINCT report_number SEPARATOR ", ") AS report_number')
									)
									->groupBy('a.barcode_start')
									->get();
								
								$validatedData = ([
									'id_good_receipt_notes_details' => $data_slitting[0]->report_number,
									'usage_to' => $data_usage_to[0]->report_number,//hasil nya bisa lebih dari satu report number
									'type_product' => $order_name[0],
									'id_master_products' => $order_name[1],
									'qty' => '1',
									'weight' => $data_slitting[0]->weight,
									'type_stock' => 'OUT',
									'date' => date("Y-m-d"),
									'barcode' => $response->barcode_start,
									'remarks' => 'From Slitting Usage To Folding'
								]);	
								HistoryStock::create($validatedData);
							}
							
							
						}
					}
					
					//Audit Log		
					$username= auth()->user()->email; 
					$ipAddress=$_SERVER['REMOTE_ADDR'];
					$location='0';
					$access_from=Browser::browserName();
					$activity='Save Edit Detail Production Result Entry Report Folding '.$data[0]->id;
					$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
					
					return Redirect::to('/production-ent-report-folding-detail/'.$response_id_rf)->with('pesan', 'Update Successfuly.');  
				}else{
					return Redirect::to('/production-ent-report-folding-detail/'.$response_id_rf)->with('pesan', 'There Is An Error.');
				}
			}else{
				return Redirect::to('/production-ent-report-folding-detail/'.$response_id_rf)->with('pesan', 'There Is An Error.');
			}
		}
		
    }
	public function production_entry_report_folding_detail_production_result_delete(Request $request){	
		//print_r($_POST);exit;
		
		$id_rf = $_POST['token_rf'];
		$id = $_POST['hapus_detail'];
		
		$data = ProductionEntryReportSFProductionResult::select("*")
				->whereRaw( "sha1(id) = '$id'")
                ->get();
		/*
		print_r($_POST);
		print_r($data);
		exit;
		*/
		$barcode = $data[0]->barcode;
		$barcode_start = $data[0]->barcode_start;
		
		if(!empty($data[0])){
			//new
			$data_barcode_start = DB::table('barcode_detail')
				->select('*')
				->where('barcode_number', $barcode_start)
				->get();
			
			//new
			$data_slitting = DB::table('report_sf_production_results as a')
			->leftJoin('report_sfs as b', 'a.id_report_sfs', '=', 'b.id')
			->whereRaw( "a.barcode = '$barcode_start'" )
			->select('a.*', 'b.report_number', 'b.order_name')
			->get();
		
			$order_name = explode('|', $data_slitting[0]->note);
			
			$delete = ProductionEntryReportSFProductionResult::whereRaw( "sha1(id) = '$id'" )->delete();
			//echo $delete; exit;
			
			if($delete){
				//Jika Barcode Bisa Digunakan Lagi, Sesuaikan status data barcode menjadi NULL
				$updatedData['status'] = null;
				
				DB::table('barcode_detail')
				->where('barcode_number', $barcode)
				->update($updatedData);
				
				$updatedDataBS['used_next_shift'] = '1';
				$updatedDataBS['join'] = '-';
				$updatedDataBS['used_product'] = ($data_barcode_start[0]->used_attempt - 1) > 0 ? $data_barcode_start[0]->used_product : null ;
				$updatedDataBS['used_attempt'] =  $data_barcode_start[0]->used_attempt - 1;
				//$updatedDataBS['used_attempt'] =  ($data_barcode_start[0]->used_attempt - 1) > 0 ? $data_barcode_start[0]->used_attempt - 1 : 0 ;
				
				$update_bs = DB::table('barcode_detail')
					->where('barcode_number', $barcode_start)
					->update($updatedDataBS);
				
				if($update_bs AND ($updatedDataBS['used_product']=='FG' AND $data_barcode_start[0]->used_attempt=='10')){
						
					if($updatedDataBS['used_product']=='FG' AND $data_barcode_start[0]->used_attempt=='10'){
						
						$data_produk = DB::table('master_wips')
							->select('*')
							->where('id', $order_name[1])
							->get();
							
						$updatedDataMS_BS['stock'] = $data_produk[0]->stock + 1 ;
						$updatedDataMS_BS['weight'] = $data_produk[0]->weight + $data_slitting[0]->weight ;
						
						$responseUpdateMasterBS = DB::table('master_wips')
								->where('id', $order_name[1])
								->update($updatedDataMS_BS);
					}
					if($responseUpdateMasterBS){	
						DB::table('history_stocks')
						->whereRaw( "barcode = '".$barcode_start."' and type_stock='OUT'" )
						->delete();
					}
				}
				
				//Audit Log		
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Delete Entry Report Folding Detail Production Result ID="'.$data[0]->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
				return Redirect::to('/production-ent-report-folding-detail/'.$id_rf)->with('pesan', 'Delete Successfuly.');
			}else{
				return Redirect::to('/production-ent-report-folding-detail/'.$id_rf)->with('pesan_danger', 'There Is An Error.');
			}
			
		}else{
			return Redirect::to('/production-ent-report-folding-detail/'.$id_rf)->with('pesan_danger', 'There Is An Error.');
		}
	}
	
	public function production_entry_report_folding_print($response_id)
    {
		//print_r($response_id);exit;
		$data = ProductionEntryReportSF::select("report_sfs.*", "c.name", "d.work_center_code", "d.work_center", "e.name AS nama_know_by")
				//->leftJoin('work_orders AS b', 'report_blows.id_work_orders', '=', 'b.id')
				->leftJoin('master_customers AS c', 'report_sfs.id_master_customers', '=', 'c.id')
				->leftJoin('master_work_centers AS d', 'report_sfs.id_master_work_centers', '=', 'd.id')
				->leftJoin('master_employees AS e', 'report_sfs.know_by', '=', 'e.id')
				->leftJoin('users AS f', 'report_sfs.id_cms_users', '=', 'f.id')
				->leftJoin('master_employees AS g', 'report_sfs.ketua_regu', '=', 'g.id')
				
				->selectRaw('e.name AS pengawas, f.name AS operator, g.name AS ketua_regu')
				->whereRaw( "sha1(report_sfs.id) = '$response_id'")
                ->get();
		//print_r($data);exit;
		if(!empty($data[0])){
			
			$data_detail_preparation = DB::table('report_sf_preparation_checks')
					->select('report_sf_preparation_checks.*')
					->whereRaw( "sha1(id_report_sfs) = '$response_id'")
					->get();
			$data_detail_hygiene = DB::table('report_sf_hygiene_checks')
					->select('report_sf_hygiene_checks.*')
					->whereRaw( "sha1(id_report_sfs) = '$response_id'")
					->get();
			//$data_detail_waste = DB::table('report_blow_wastes')
			//		->select('report_blow_wastes.*')
			//		->whereRaw( "sha1(id_report_blows) = '$response_id'")
			//		->get();      
			$data_detail_production = DB::table('report_sf_production_results as a')
					->leftJoin('report_sf_production_results as b', 'a.barcode_start', '=', 'b.barcode')//disesuaikan ke table sfs
					->leftJoin('report_sfs as c', 'b.id_report_blows', '=', 'c.id')//disesuaikan ke table sfs
					->leftJoin('work_orders as d', 'a.id_work_orders', '=', 'd.id')
					->whereRaw( "sha1(a.id_report_sfs) = '$response_id'")
					->select('b.note as order_name_blow', 'b.weight as weight_blow', 'd.wo_number', 'a.*')
					->get();//PERBAIKI QUERY DETAIL UNTUK GET WO DAN PRODUCT
			/*		
			$table_product = $order_name[0] == 'WIP' ? 'master_wips' : 'master_product_fgs';
			
			$data_product = DB::table($table_product)
					->select('*')
					->where('id', $order_name[1])
					->get();
			*/
			if(!empty($data_detail_production[0]->note)){	
				$order_name = explode('|', $data_detail_production[0]->note);
				
				if(count($order_name)>1){
				//Audit Log
					$username= auth()->user()->email; 
					$ipAddress=$_SERVER['REMOTE_ADDR'];
					$location='0';
					$access_from=Browser::browserName();
					$activity='Print Entry Report Folding ID="'.$data[0]->id.'"';
					$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

					return view('production.entry_report_folding_print',compact('data','data_detail_preparation','data_detail_hygiene','data_detail_production'));
				}else{
					return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'Data Report Folding Versi Aplikasi Sebelumnya Tidak Bisa Di Print');
				}
			}else{
				return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'Data Report Folding Versi Aplikasi Sebelumnya Tidak Bisa Di Print / Data Detail Report Folding Belum Tersedia');
			}
		}else{
			return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error.');
		}
    }
	public function production_entry_report_folding_update_stock($response_id){
		$id_rf = $response_id;
		
		/*QUERY LAMA SEBELUM INSERT BARCODE DENGAN GROUP CONCAT
		$data_update = ProductionEntryReportSFProductionResult::select('b.report_number','c.type_product','b.order_name','report_sf_production_results.id_report_sfs', 'report_sf_production_results.id', 'report_sf_production_results.note')
			->selectRaw('SUM(IF(report_sf_production_results.status="Good", 1, 0)) AS good')
			->selectRaw('SUM(IF(report_sf_production_results.status="Hold", 1, 0)) AS hold')
			->selectRaw('SUM(IF(report_sf_production_results.status="Reject", 1, 0)) AS reject')
			->rightJoin('report_sfs AS b', 'report_sf_production_results.id_report_sfs', '=', 'b.id')
			->rightJoin('work_orders AS c', 'report_sf_production_results.id_work_orders', '=', 'c.id')
			->whereRaw( "sha1(report_sf_production_results.id_report_sfs) = '$id_rf'")
			->groupBy('report_sf_production_results.id_report_sfs')
			->get();
		*/
		
		$data_update = DB::table('report_sf_production_results AS a')
			->rightJoin('report_sfs AS b', 'a.id_report_sfs', '=', 'b.id')
			->rightJoin('work_orders AS c', 'a.id_work_orders', '=', 'c.id')
			->select(
				'b.report_number',
				'c.type_product',
				'b.order_name',
				'a.id_report_sfs',
				'a.id',
				'a.note',
				DB::raw('SUM(IF(a.status="Good", 1, 0)) AS good'),
				DB::raw('SUM(IF(a.status="Good", a.weight, 0)) AS weight_good'),
				DB::raw('SUM(IF(a.status="Hold", 1, 0)) AS hold'),
				DB::raw('SUM(IF(a.status="Hold", a.weight, 0)) AS weight_hold'),
				DB::raw('SUM(IF(a.status="Reject", 1, 0)) AS reject'),
				DB::raw('SUM(IF(a.status="Reject", a.weight, 0)) AS weight_reject'),
				DB::raw('GROUP_CONCAT(CASE WHEN a.status="Good" THEN barcode END SEPARATOR ", ") AS barcode_good'),
				DB::raw('GROUP_CONCAT(CASE WHEN a.status="Hold" THEN barcode END SEPARATOR ", ") AS barcode_hold'),
				DB::raw('GROUP_CONCAT(CASE WHEN a.status="Reject" THEN barcode END SEPARATOR ", ") AS barcode_reject')
			)
			->whereRaw( "sha1(a.id_report_sfs) = '$id_rf'")
			->groupBy('a.id_report_sfs')
			->get();
			
		//print_r($data_update);exit;
			
		$order_name = explode('|', $data_update[0]->note);			
		$master_table = $data_update[0]->type_product=="WIP"?'master_wips':'master_product_fgs';
		
		if(!empty($data_update[0])){	
			$data_product = DB::table($master_table)
				->select('*')
				->whereRaw( "id = '".$order_name[1]."'")
				->get();
				
			if(!empty($data_product[0])){	
				if($data_update[0]->good>0){
					$validatedData = ([
						'id_good_receipt_notes_details' => $data_update[0]->report_number,
						'type_product' => $data_update[0]->type_product,
						'id_master_products' => $order_name[1],
						'qty' => $data_update[0]->good,
						'weight' => $data_update[0]->weight_good,
						'is_closed' => '1',
						'type_stock' => 'IN',
						'date' => date("Y-m-d"),
						'barcode' => $data_update[0]->barcode_good,
					]);	
					$responseGood = HistoryStock::create($validatedData);
					
					
				}
				if($data_update[0]->hold>0){
					$validatedData = ([
						'id_good_receipt_notes_details' => $data_update[0]->report_number,
						'type_product' => $data_update[0]->type_product,
						'id_master_products' => $order_name[1],
						'qty' => $data_update[0]->hold,
						'weight' => $data_update[0]->weight_hold,
						'is_closed' => '1',
						'type_stock' => 'HOLD',
						'date' => date("Y-m-d"),
						'barcode' => $data_update[0]->barcode_hold,
					]);	
					$responseHold = HistoryStock::create($validatedData);
				}
				if($data_update[0]->reject>0){
					$validatedData = ([
						'id_good_receipt_notes_details' => $data_update[0]->report_number,
						'type_product' => $data_update[0]->type_product,
						'id_master_products' => $order_name[1],
						'qty' => $data_update[0]->reject,
						'weight' => $data_update[0]->weight_reject,
						'is_closed' => '1',
						'type_stock' => 'REJECT',
						'date' => date("Y-m-d"),
						'barcode' => $data_update[0]->barcode_reject,
					]);	
					$responseReject = HistoryStock::create($validatedData);
				}
				
				if($responseGood or $responseHold or $responseReject){
					if($responseGood){					
						$stock_akhir = $data_product[0]->stock + $data_update[0]->good;//STOK
						$weight_akhir = $data_product[0]->weight + $data_update[0]->weight_good;//WEIGTH
						
						DB::table($master_table)->where('id', $order_name[1])->update(array('weight' => $weight_akhir, 'stock' => $stock_akhir, 'updated_at' => date('Y-m-d H:i:s'))); 						
					}
					
					$validatedData = ([
						'status' => 'Closed',
					]);				
					
					ProductionEntryReportSF::where('report_number', $data_update[0]->report_number)
						->update($validatedData);
					
					//Audit Log
					$username= auth()->user()->email; 
					$ipAddress=$_SERVER['REMOTE_ADDR'];
					$location='0';
					$access_from=Browser::browserName();
					$activity='Update Histori Stock Folding Report Number ="'.$data_update[0]->report_number.'" (Good : '.$data_update[0]->good.', Hold : '.$data_update[0]->hold.', Reject : '.$data_update[0]->reject.')';
					$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
						
					return Redirect::to('/production-ent-report-folding')->with('pesan', 'Update Stock Successfuly.');
				}else{
					return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error.');
				}
			}else{
				return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error. Data Produk Not Found.');
			}
		}else{
			return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error.');
		}
    }
	public function production_entry_report_folding_unposted($response_id){
	
		$id_rf = $response_id;
		
		$data_check = ProductionEntryReportSFProductionResult::select('report_sf_production_results.*')
			->rightJoin('report_bag_production_results AS b', 'report_sf_production_results.barcode', '=', 'b.barcode_start')
			->whereRaw( "sha1(report_sf_production_results.id_report_sfs) = '$id_rf'")
			->get();
				
		if($data_check->isNotEmpty()){
			return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'Report Tidak Dapat Di Unposted, Terdapat Report Bag Making Yang Menggunakan Barcode Production Result Report Ini.');
		}else{
			
			/*QUERY LAMA SEBELUM INSERT BARCODE DENGAN GROUP CONCAT
			$data_update = ProductionEntryReportSFProductionResult::select('b.report_number','c.type_product','b.order_name','report_sf_production_results.id_report_sfs', 'report_sf_production_results.id', 'report_sf_production_results.note')
				->selectRaw('SUM(IF(report_sf_production_results.status="Good", 1, 0)) AS good')
				->selectRaw('SUM(IF(report_sf_production_results.status="Hold", 1, 0)) AS hold')
				->selectRaw('SUM(IF(report_sf_production_results.status="Reject", 1, 0)) AS reject')
				->rightJoin('report_sfs AS b', 'report_sf_production_results.id_report_sfs', '=', 'b.id')
				->rightJoin('work_orders AS c', 'report_sf_production_results.id_work_orders', '=', 'c.id')
				->whereRaw( "sha1(report_sf_production_results.id_report_sfs) = '$id_rf'")
				->groupBy('report_sf_production_results.id_report_sfs')
				->get();
			*/
			
			$data_update = DB::table('report_sf_production_results AS a')
				->rightJoin('report_sfs AS b', 'a.id_report_sfs', '=', 'b.id')
				->rightJoin('work_orders AS c', 'a.id_work_orders', '=', 'c.id')
				->select(
					'b.report_number',
					'c.type_product',
					'b.order_name',
					'a.id_report_sfs',
					'a.id',
					'a.note',
					DB::raw('SUM(IF(a.status="Good", 1, 0)) AS good'),
					DB::raw('SUM(IF(a.status="Good", a.weight, 0)) AS weight_good'),
					DB::raw('SUM(IF(a.status="Hold", 1, 0)) AS hold'),
					DB::raw('SUM(IF(a.status="Hold", a.weight, 0)) AS weight_hold'),
					DB::raw('SUM(IF(a.status="Reject", 1, 0)) AS reject'),
					DB::raw('SUM(IF(a.status="Reject", a.weight, 0)) AS weight_reject'),
					DB::raw('GROUP_CONCAT(CASE WHEN a.status="Good" THEN barcode END SEPARATOR ", ") AS barcode_good'),
					DB::raw('GROUP_CONCAT(CASE WHEN a.status="Hold" THEN barcode END SEPARATOR ", ") AS barcode_hold'),
					DB::raw('GROUP_CONCAT(CASE WHEN a.status="Reject" THEN barcode END SEPARATOR ", ") AS barcode_reject')
				)
				->whereRaw( "sha1(a.id_report_sfs) = '$id_rf'")
				->groupBy('a.id_report_sfs')
				->get();
				
			//print_r($data_update);exit;
			
			if(!empty($data_update[0])){
				
				$master_table = $data_update[0]->type_product=="WIP"?'master_wips':'master_product_fgs';
				$order_name = explode('|', $data_update[0]->note);	
				
				if(count($order_name)>1){
					$data_product = DB::table($master_table)
						->select('*')
						->whereRaw( "id = '".$order_name[1]."'")
						->get();
					
					if(!empty($data_product[0])){	
						if($data_update[0]->good>0){
							/*
							$validatedData = ([
								'id_good_receipt_notes_details' => $data_update[0]->report_number,
								'type_product' => $data_update[0]->type_product,
								'id_master_products' => $order_name[1],
								'qty' => $data_update[0]->good,
								'type_stock' => 'Un Posted',
								'date' => date("Y-m-d"),
								'barcode' => $data_update[0]->barcode_good,
								'remarks' => 'From GOOD Posted'
							]);	
							$responseGood = HistoryStock::create($validatedData);
							*/
							//if($responseGood){					
								$stock_akhir = $data_product[0]->stock - $data_update[0]->good;//STOK
								$weight_akhir = $data_product[0]->weight - $data_update[0]->weight_good;//WEIGTH		
								
								$responseMaster = DB::table($master_table)->where('id', $order_name[1])->update(array('weight' => $weight_akhir,'stock' => $stock_akhir, 'updated_at' => date('Y-m-d H:i:s'))); 						
							//}
						}
						/*
						if($data_update[0]->hold>0){
							$validatedData = ([
								'id_good_receipt_notes_details' => $data_update[0]->report_number,
								'type_product' => $data_update[0]->type_product,
								'id_master_products' => $order_name[1],
								'qty' => $data_update[0]->hold,
								'type_stock' => 'Un Posted',
								'date' => date("Y-m-d"),
								'barcode' => $data_update[0]->barcode_hold,
								'remarks' => 'From HOLD Posted'
							]);	
							$responseHold = HistoryStock::create($validatedData);
						}
						if($data_update[0]->reject>0){
							$validatedData = ([
								'id_good_receipt_notes_details' => $data_update[0]->report_number,
								'type_product' => $data_update[0]->type_product,
								'id_master_products' => $order_name[1],
								'qty' => $data_update[0]->reject,
								'type_stock' => 'Un Posted',
								'date' => date("Y-m-d"),
								'barcode' => $data_update[0]->barcode_reject,
								'remarks' => 'From REJECT Posted'
							]);	
							$responseReject = HistoryStock::create($validatedData);
						}
						*/
						//if($responseGood or $responseHold or $responseReject){
						if($responseMaster){
							
							$validatedData = ([
								'status' => 'Un Posted',
							]);				
							
							$responseUpdate = ProductionEntryReportSF::where('report_number', $data_update[0]->report_number)
								->update($validatedData);
							
							if($responseUpdate){								
								DB::table('history_stocks')
									->whereRaw( "id_good_receipt_notes_details = '".$data_update[0]->report_number."' and type_stock != 'OUT'" )
									->delete();
									
								//Audit Log
								$username= auth()->user()->email; 
								$ipAddress=$_SERVER['REMOTE_ADDR'];
								$location='0';
								$access_from=Browser::browserName();
								$activity='Un Posted Histori Stock Folding Report Number ="'.$data_update[0]->report_number.'" (Good : '.$data_update[0]->good.', Hold : '.$data_update[0]->hold.', Reject : '.$data_update[0]->reject.')';
								$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
									
								return Redirect::to('/production-ent-report-folding')->with('pesan', 'Un Posted Successfuly.');
							}else{
								return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error.');
							}	
						}else{
							return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error.');
						}
					}else{
						return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error. Data Produk Not Found.');
					}
				}else{
					return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'Data Report Folding Versi Aplikasi Sebelumnya Tidak Bisa Di Unposted');
				}
			}else{
				return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error. May Be It Is Old Data.');
			}
		}
    }
	public function production_entry_report_folding_delete($response_id){
		$id_rf = $response_id;
		
		$data_update = ProductionEntryReportSFProductionResult::select('b.report_number','c.type_product','b.order_name','report_sf_production_results.id_report_sfs', 'report_sf_production_results.id', 'report_sf_production_results.note')
			->selectRaw('SUM(IF(report_sf_production_results.status="Good", 1, 0)) AS good')
			->selectRaw('SUM(IF(report_sf_production_results.status="Hold", 1, 0)) AS hold')
			->selectRaw('SUM(IF(report_sf_production_results.status="Reject", 1, 0)) AS reject')
			->selectRaw('b.id AS id_rf')
			->rightJoin('report_sfs AS b', 'report_sf_production_results.id_report_sfs', '=', 'b.id')
			->rightJoin('work_orders AS c', 'report_sf_production_results.id_work_orders', '=', 'c.id')
			->whereRaw( "sha1(report_sf_production_results.id_report_sfs) = '$id_rf'")
			->groupBy('report_sf_production_results.id_report_sfs')
			->get();		
		
		if(!empty($data_update[0])){	
			
			
			$order_name = explode('|', $data_update[0]->note);			
			$master_table = $data_update[0]->type_product=="WIP"?'master_wips':'master_product_fgs';
			
			$data_product = DB::table($master_table)
				->select('*')
				->whereRaw( "id = '".$order_name[1]."'")
				->get();
			
			
			if(!empty($data_product[0])){	
				$data_detail = ProductionEntryReportSFProductionResult::select('*')
					->whereRaw( "sha1(report_sf_production_results.id_report_sfs) = '$id_rf'")
					->get();
					
				if($data_detail){
					$deleteHistori = HistoryStock::whereRaw( "id_good_receipt_notes_details = '".$data_update[0]->report_number."'" )->delete();
					
					$deleteHygiene = ProductionEntryReportSFHygiene::whereRaw( "id_report_sfs = '".$data_update[0]->id_rs."'" )->delete();
					$deletePreparation = ProductionEntryReportSFPreparation::whereRaw( "id_report_sfs = '".$data_update[0]->id_rs."'" )->delete();
					$deleteProductionResult = ProductionEntryReportSFProductionResult::whereRaw( "id_report_sfs = '".$data_update[0]->id_rs."'" )->delete();
					$deleteFolding = ProductionEntryReportSF::whereRaw( "id = '".$data_update[0]->id_rf."'" )->delete();
					
					if($deleteFolding){
						$updatedData['status'] = null;	
						
						foreach($data_detail as $data){
							
							DB::table('barcode_detail')
								->where('barcode_number', $data->barcode)
								->update($updatedData);
							
							//PENYESUAIAN BARCODE START
							$barcode_start = $data->barcode_start;							
							//new
							$data_barcode_start = DB::table('barcode_detail')
								->select('*')
								->where('barcode_number', $barcode_start)
								->get();
							
							//new
							$data_slitting = DB::table('report_sf_production_results as a')
							->leftJoin('report_sfs as b', 'a.id_report_sfs', '=', 'b.id')
							->whereRaw( "a.barcode = '$barcode_start'" )
							->select('a.*', 'b.report_number', 'b.order_name')
							->get();
						
							$order_name = explode('|', $data_slitting[0]->note);
							
							$updatedDataBS['used_next_shift'] = '1';
							$updatedDataBS['join'] = '-';
							$updatedDataBS['used_product'] = ($data_barcode_start[0]->used_attempt - 1) > 0 ? $data_barcode_start[0]->used_product : null ;
							$updatedDataBS['used_attempt'] =  $data_barcode_start[0]->used_attempt - 1;
							
							$update_bs = DB::table('barcode_detail')
								->where('barcode_number', $barcode_start)
								->update($updatedDataBS);
							
							if($update_bs AND ($updatedDataBS['used_product']=='FG' AND $data_barcode_start[0]->used_attempt=='10')){
									
								if($updatedDataBS['used_product']=='FG' AND $data_barcode_start[0]->used_attempt=='10'){
									
									$data_produk = DB::table('master_wips')
										->select('*')
										->where('id', $order_name[1])
										->get();
										
									$updatedDataMS_BS['stock'] = $data_produk[0]->stock + 1 ;
									$updatedDataMS_BS['weight'] = $data_produk[0]->weight + $data_slitting[0]->weight ;
									
									$responseUpdateMasterBS = DB::table('master_wips')
											->where('id', $order_name[1])
											->update($updatedDataMS_BS);
								}
								if($responseUpdateMasterBS){	
									DB::table('history_stocks')
									->whereRaw( "barcode = '".$barcode_start."' and type_stock='OUT'" )
									->delete();
								}
							}
							//PENYESUAIAN BARCODE START
							
						}			
						
						//Audit Log
						$username= auth()->user()->email; 
						$ipAddress=$_SERVER['REMOTE_ADDR'];
						$location='0';
						$access_from=Browser::browserName();
						$activity='Deleted Folding Report Number ="'.$data_update[0]->report_number.'" (Good : '.$data_update[0]->good.', Hold : '.$data_update[0]->hold.', Reject : '.$data_update[0]->reject.')';
						$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
					
						return Redirect::to('/production-ent-report-folding')->with('pesan', 'Delete Successfuly.');
					}else{
						return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error.');
					}						
				}else{
					return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error.');
				}
			}else{
				return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error. Data Produk Not Found.');
			}
		}else{
			$data_folding = DB::table('report_sfs')
				->selectRaw('id AS id_rf')
				->selectRaw('report_number')
				->whereRaw( "sha1(id) = '".$id_rf."'")
				->get();
			
			//print_r($data_blow);exit;
			if($data_folding){
				$report_number = $data_folding[0]->report_number;
				
				$deleteHygiene = ProductionEntryReportSFHygiene::whereRaw( "id_report_sfs = '".$data_folding[0]->id_rf."'" )->delete();
				$deletePreparation = ProductionEntryReportSFPreparation::whereRaw( "id_report_sfs = '".$data_folding[0]->id_rf."'" )->delete();
				$deleteProductionResult = ProductionEntryReportSFProductionResult::whereRaw( "id_report_sfs = '".$data_folding[0]->id_rf."'" )->delete();
				$deleteFolding = ProductionEntryReportSF::whereRaw( "id = '".$data_folding[0]->id_rf."'" )->delete();
				
				if($deleteFolding){
					//Audit Log
					$username= auth()->user()->email; 
					$ipAddress=$_SERVER['REMOTE_ADDR'];
					$location='0';
					$access_from=Browser::browserName();
					$activity='Deleted Folding Report Number ="'.$report_number.'" (Good : "-", Hold : "-", Reject : "-")';
					$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
					return Redirect::to('/production-ent-report-folding')->with('pesan', 'Delete Successfuly.');
				}else{
					return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error.');
				}	
			}else{
				return Redirect::to('/production-ent-report-folding')->with('pesan_danger', 'There Is An Error.');
			}
		}
    }
	//END ENTRY REPORT BLOW
}
