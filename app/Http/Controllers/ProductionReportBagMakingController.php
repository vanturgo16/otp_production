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

use App\Models\ProductionEntryReportBagMaking;
use App\Models\ProductionEntryReportBagMakingHygiene;
use App\Models\ProductionEntryReportBagMakingPreparation;
use App\Models\ProductionEntryReportBagMakingProductionResult;
use App\Models\ProductionEntryReportBagMakingWaste;

//END REQUEST SPAREPART AND AUXILIARIES

class ProductionReportBagMakingController extends Controller
{
    use AuditLogsTrait;
	
	//START ENTRY REPORT BLOW
	public function production_entry_report_bag_making()
    {
		//echo "report bag making";exit;
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
        $activity='View List Entry Report Bag Making';
        $this->auditLogs($username,$ipAddress,$location,$access_from,$activity);		

        //return view('production.entry_report_blow', compact('datas'));
        return view('production.entry_report_bag_making');
    }
	public function production_entry_report_bag_making_json()
    {
        $datas = ProductionEntryReportBagMaking::leftJoin('master_regus AS c', 'report_bags.id_master_regus', '=', 'c.id')
				->leftJoin('master_work_centers AS d', 'report_bags.id_master_work_centers', '=', 'd.id')
				->leftJoin('master_customers AS e', 'report_bags.id_master_customers', '=', 'e.id')
                ->select('report_bags.*', 'c.regu', 'd.work_center', 'e.name')
                //->whereRaw( "left(report_number,2) = 'RF'")
                ->orderBy('report_bags.created_at', 'desc')
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
				
				$status = empty($data->status)?"Tidak Tersedia":$data->status;			
				//$order_info = '<p><b>'.$order_name.'</b><br><code>Customer : </code><br>'.$data->name.'<br><footer class="blockquote-footer">Status : <cite>'.$status.'</cite></footer></p>';
				$order_info = '<p><code>Customer : </code><br>'.$data->name.'<br><footer class="blockquote-footer">Status : <cite>'.$status.'</cite></footer></p>';
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
						<a onclick="'.$return_unposted.'" href="/production-entry-report-bag-making-unposted/'.sha1($data->id).'" class="btn btn-primary waves-effect btn-label waves-light mt-1" onclick="return confirm('."'Anda yakin unposted data ?'".')">
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
							<a target="_blank" href="/production-ent-report-bag-making-detail/'.sha1($data->id).'" class="btn btn-outline-info waves-effect waves-light">
								<i class="bx bx-edit-alt" title="Edit"></i> EDIT
							</a>
							<a onclick="'.$return_delete.'" href="/production-ent-report-bag-making-delete/'.sha1($data->id).'" class="btn btn-outline-danger waves-effect waves-light" onclick="return confirm('."'Anda yakin mau menghapus item ini ?'".')">
								<i class="bx bx-trash-alt" title="Delete" ></i> DELETE
							</a>
							<a target="_blank" href="/production-ent-report-bag-making-print/'.sha1($data->id).'" class="btn btn-dark waves-effect waves-light">
								<i class="bx bx-printer" title="Print"></i> PRINT
							</a>
						</center>					
					';
				}else{
					$tombol .= '
							<a target="_blank" href="/production-ent-report-bag-making-print/'.sha1($data->id).'" class="btn btn-dark waves-effect waves-light">
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
	
	public function production_entry_report_bag_making_json_preparation(Request $request){	
		$id_rb = request()->get('id');
		
		$data = ProductionEntryReportBagMakingPreparation::leftJoin('report_bags as b', 'report_bag_preparation_checks.id_report_bags', '=', 'b.id')
				->select('report_bag_preparation_checks.*', 'b.report_number')
				->whereRaw( "sha1(report_bag_preparation_checks.id_report_bags) = $id_rb")
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
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Pisau Seal</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->pisau_seal=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->pisau_seal=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Jarum Perforasi</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->jarum_perforasi=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->jarum_perforasi=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Pembuka Plastik</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->pembuka_plastik=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->pembuka_plastik=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Guide Rubber Roll</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->guide_rubber_roll=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->guide_rubber_roll=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						
					</table>
				</div>				
			</div>
		
		<?php
	}	
	public function production_entry_report_bag_making_json_hygiene(Request $request){	
		$id_rb = request()->get('id');
		
		$data = ProductionEntryReportBagMakingHygiene::leftJoin('report_bags as b', 'report_bag_hygiene_checks.id_report_bags', '=', 'b.id')
				->select('report_bag_hygiene_checks.*', 'b.report_number')
				->whereRaw( "sha1(report_bag_hygiene_checks.id_report_bags) = $id_rb")
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
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Roll Guide Roll Karet </td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->roll_guide_roll_karet=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->roll_guide_roll_karet=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Jarum Perforasi</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->jarum_perforasi=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->jarum_perforasi=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Pisau Seal</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->pisau_seal=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->pisau_seal=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Pembuka Plastik</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->pembuka_plastik=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->pembuka_plastik=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Lantai Mesin</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->lantai_mesin=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->lantai_mesin=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Body Mesin</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->body_mesin=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->body_mesin=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
					</table>
				</div>				
			</div>
		
		<?php
	}	
	public function production_entry_report_bag_making_json_update_stock(Request $request){
		
		/*
		?>
		Test
		<?php
		*/
		
		$id_rb = request()->get('id');
		
		$data = ProductionEntryReportBagMakingProductionResult::select('b.report_number','report_bag_production_results.id_report_bags', 'report_bag_production_results.id', 'report_bag_production_results.note')
				->selectRaw('SUM(report_bag_production_results.amount_result) AS amount')
				->selectRaw('SUM(report_bag_production_results.wrap) AS wrap')
				->rightJoin('report_bags AS b', 'report_bag_production_results.id_report_bags', '=', 'b.id')
				->whereRaw( "sha1(report_bag_production_results.id_report_bags) = $id_rb")
				->groupBy('id_report_bags')
				->groupBy('report_bag_production_results.note')
                ->get();
		//print_r($data[0]);
		if(!empty($data[0]->id_report_bags)){
			if(!empty($data[0]->note)){
			?>					
				<!--form method="post" action="/production-entry-report-blow-update-stock" class="form-material m-t-40" enctype="multipart/form-data"-->
					<div class="card-header">
						<p class="card-title-desc">
							Production Result : Report Number <b><?= $data[0]->report_number; ?></b>
						</p>
					</div>
					<?php foreach($data as $data_for) { ?>
						<div class="card-body">
							<p class="card-title-desc">
								<?php $product = explode('|', $data_for->note); ?>
								Product : <br><b><?= $product[2]; ?></b><br><br>
							</p>
							<div class="row g-4">	
								<div class="col-sm-6">
									<div class="alert alert-success alert-dismissible fade show px-4 mb-0 text-center" role="alert">
										<p>Amount Result :</p>
										<i class="mdi mdi-chart-bubble d-block display-4 mt-2 mb-3 text-success"></i>
										<h5 class="text-success"><?= $data_for->amount; ?> Pcs</h5>
									</div>
								</div><!-- end col -->

								<div class="col-sm-6">
									<div class="alert alert-warning alert-dismissible fade show px-4 mb-0 text-center" role="alert">
										<p>Sliccing On :</p>
										<i class="mdi mdi-package-variant d-block display-4 mt-2 mb-3 text-warning"></i>
										<h5 class="text-warning"><?= $data_for->wrap; ?> Bungkus</h5>
									</div>
								</div><!-- end col -->
							</div><!-- end row -->
						</div>
					<?php }; ?>
					
					<div class="modal-footer">
						<a href="/production-entry-report-bag-making-update-stock/<?= sha1($data[0]->id_report_bags); ?>" type="submit" class="btn btn-primary btn-lg"><i class="bx bx-save" title="Update"></i> UPDATE</a>
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
		}else{
		?>
			<div class="card-body">
				<p class="card-title-desc">Production Result : TIDAK TERSEDIA</b></p>
			</div>
		<?php
		}	
	}	
	public function production_entry_report_bag_making_json_update_stock_info(Request $request){
		
		/*
		?>
		Test
		<?php
		*/
		
		$id_rb = request()->get('id');
		
		$data = ProductionEntryReportBagMakingProductionResult::select('b.report_number','report_bag_production_results.id_report_bags', 'report_bag_production_results.id', 'report_bag_production_results.note')
				->selectRaw('SUM(report_bag_production_results.amount_result) AS amount')
				->selectRaw('SUM(report_bag_production_results.wrap) AS wrap')
				->rightJoin('report_bags AS b', 'report_bag_production_results.id_report_bags', '=', 'b.id')
				->whereRaw( "sha1(report_bag_production_results.id_report_bags) = $id_rb")
				->groupBy('id_report_bags')
				->groupBy('report_bag_production_results.note')
                ->get();
				
		if(!empty($data[0]->id_report_bags)){
			if(!empty($data[0]->note)){
				?>					
				<!--form method="post" action="/production-entry-report-blow-update-stock" class="form-material m-t-40" enctype="multipart/form-data"-->
					<div class="card-header">
						<p class="card-title-desc">
							Production Result : Report Number <b><?= $data[0]->report_number; ?></b>
						</p>
					</div>
					<?php foreach($data as $data_for) { ?>
						<div class="card-body">
							<p class="card-title-desc">
								<?php $product = explode('|', $data_for->note); ?>
								Product : <br><b><?= $product[2]; ?></b><br><br>
							</p>
							<div class="row g-4">	
								<div class="col-sm-6">
									<div class="alert alert-success alert-dismissible fade show px-4 mb-0 text-center" role="alert">
										<p>Amount Result :</p>
										<i class="mdi mdi-chart-bubble d-block display-4 mt-2 mb-3 text-success"></i>
										<h5 class="text-success"><?= $data_for->amount; ?> Pcs</h5>
									</div>
								</div><!-- end col -->

								<div class="col-sm-6">
									<div class="alert alert-warning alert-dismissible fade show px-4 mb-0 text-center" role="alert">
										<p>Sliccing On :</p>
										<i class="mdi mdi-package-variant d-block display-4 mt-2 mb-3 text-warning"></i>
										<h5 class="text-warning"><?= $data_for->wrap; ?> Bungkus</h5>
									</div>
								</div><!-- end col -->
							</div><!-- end row -->
						</div>
					<?php }; ?>
			<?php
			}else{
			?>
				
				<div class="card-body">
					<p class="card-title-desc">Production Result Data Aplikasi Lama : TIDAK TERSEDIA</b></p>
				</div>
			<?php
			}
		}else{
		?>
			<div class="card-body">
				<p class="card-title-desc">Production Result : TIDAK TERSEDIA</b></p>
			</div>
		<?php
		}	
	}
	
	public function production_entry_report_bag_making_add(Request $request){
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
		
        $ms_known_by = DB::table('master_employees')
                        ->select('id','name')
                        ->whereRaw( "id_master_bagians IN('3','4')")
                        ->get();
		//print_r($ms_work_orders);exit;
        $formattedCode = $this->production_entry_report_bag_making_create_code();
		
        //Audit Log
        $username= auth()->user()->email; 
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $location='0';
        $access_from=Browser::browserName();
        $activity='Add Entry Report Bag Making';
        $this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

        return view('production.entry_report_bag_making_add',compact('ms_departements','ms_known_by','formattedCode'));			
    }
	private function production_entry_report_bag_making_create_code(){
		$lastCode = ProductionEntryReportBagMaking::whereRaw( "left(report_number,3) = 'RBM'")
		->orderBy('created_at', 'desc')
        ->value(DB::raw('RIGHT(report_number, 5)'));
    
        // Jika tidak ada nomor urut sebelumnya, atur ke 0
        $lastCode = $lastCode ? $lastCode : 0;
		//echo $lastCode;exit;
        // Tingkatkan nomor urut
        $nextCode = $lastCode + 1;

        // Format kode dengan panjang 7 karakter
        $formattedCode = 'RBM' . str_pad($nextCode, 5, '0', STR_PAD_LEFT);
		
		return $formattedCode;
	}
	
	public function production_entry_report_bag_making_save(Request $request){
		//echo "disini";exit;
		
		//print_r($_POST);exit;
        if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('save')) {
            $pesan = [
                'date.required' => 'Cannot Be Empty',
                //'id_master_products.required' => 'Cannot Be Empty',
                'id_master_customers.required' => 'Cannot Be Empty',
                'id_master_work_centers.required' => 'Cannot Be Empty',
                'id_master_regus.required' => 'Cannot Be Empty',                
                'shift.required' => 'Cannot Be Empty',                
                'id_known_by.required' => 'Cannot Be Empty',                
            ];

            $validatedData = $request->validate([
                'date' => 'required',
                //'id_master_products' => 'required',
                'id_master_customers' => 'required',
                'id_master_work_centers' => 'required',
                'id_master_regus' => 'required',
                'shift' => 'required',
                'id_known_by' => 'required',

            ], $pesan);			
			
			//$validatedData['order_name'] = $_POST['id_master_products'];
			$validatedData['report_number'] = $this->production_entry_report_bag_making_create_code();
			$validatedData['engine_shutdown_description'] = $_POST['engine_shutdown_description'];
			$validatedData['note'] = $_POST['note'];
			$validatedData['known_by'] = $_POST['id_known_by'];
			//$validatedData['type'] = 'Folding';
			$validatedData['status'] = 'Un Posted';
			
            $response = ProductionEntryReportBagMaking::create($validatedData);
			
			if(!empty($response)){
				$dataHygiene = array(
									'id_report_bags' => $response->id,
									'roll_guide_roll_karet' => 'Ok',
									'jarum_perforasi' => 'Ok',
									'pisau_seal' => 'Ok',
									'pembuka_plastik' => 'Ok',
									'lantai_mesin' => 'Ok',
									'body_mesin' => 'Ok',
									'created_at' => date('Y-m-d H:i:s'),
									'updated_at' => date('Y-m-d H:i:s')
								);
				ProductionEntryReportBagMakingHygiene::create($dataHygiene);
				
				$dataPreparation = array(
									'id_report_bags' => $response->id,
									'material' => 'Ok',
									'ukuran' => 'Ok',
									'pisau_seal' => 'Ok',
									'jarum_perforasi' => 'Ok',
									'pembuka_plastik' => 'Ok',
									'guide_rubber_roll' => 'Ok',
									'created_at' => date('Y-m-d H:i:s'),
									'updated_at' => date('Y-m-d H:i:s')
								);
				ProductionEntryReportBagMakingPreparation::create($dataPreparation);
			}
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Save Entry Report Bag Making ID="'.$response->id.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);//tinggal uji insert
			
            return Redirect::to('/production-ent-report-bag-making-detail/'.sha1($response->id))->with('pesan', 'Add Successfuly.');
            //return Redirect::to('/production-ent-report-bag-making');
        }
    }
	public function production_entry_report_bag_making_detail($response_id){
		$data = ProductionEntryReportBagMaking::select("report_bags.*")
				->whereRaw( "sha1(report_bags.id) = '$response_id'")
                ->get();
		//print_r($data);exit;
		if(!empty($data[0])){
			if($data[0]->status=="Un Posted"){
				
				$data_detail_production = DB::table('report_bag_production_results AS a')
						->leftJoin('work_orders AS b', 'a.id_work_orders', '=', 'b.id')
						->select('a.*','b.wo_number')
						->whereRaw( "sha1(a.id_report_bags) = '$response_id'")
						->get();	
				/*
				//Jika 1 Report hanya boleh 1 WO
				if(!empty($data_detail_production[0])){
					$ms_work_orders = DB::table('work_orders AS a')
						->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
						->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
						->select('a.*','c.id AS id_master_customers')
						->whereRaw( "left(wo_number,5) = 'WOFLD'")
						->whereRaw( "a.id = '".$data_detail_production[0]->id_work_orders."'")
						->get();
				}else{
					$ms_work_orders = DB::table('work_orders AS a')
						->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
						->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
						->select('a.*','c.id AS id_master_customers')
						->whereRaw( "left(wo_number,5) = 'WOFLD'")
						//->whereRaw( "a.type_product = 'WIP'")
						->get();
				}
				*/
				//1 Customer Bisa Beberapa WO
				$id_master_customers = $data[0]->id_master_customers;
				$ms_work_orders = DB::table('work_orders AS a')
					->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
					->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
					->leftJoin('sales_orders AS d', 'a.id_sales_orders', '=', 'd.id')
					->select('a.*','c.id AS id_master_customers')
					->whereRaw( "left(wo_number,5) = 'WOBGM'")
					->whereRaw( "a.type_product = 'FG'")
					->whereRaw( "d.id_master_customers = '$id_master_customers'")
					->get();
				
				$data_detail_preparation = DB::table('report_bag_preparation_checks')
						->select('report_bag_preparation_checks.*')
						->whereRaw( "sha1(id_report_bags) = '$response_id'")
						->get();
						
				$data_detail_hygiene = DB::table('report_bag_hygiene_checks')
						->select('report_bag_hygiene_checks.*')
						->whereRaw( "sha1(id_report_bags) = '$response_id'")
						->get();
				
				$data_detail_waste = DB::table('report_bag_wastes')
							->select('report_bag_wastes.*')
							->whereRaw( "sha1(id_report_bags) = '$response_id'")
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
				$activity='Detail Entry Report Bag Making ID="'.$data[0]->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

				return view('production.entry_report_bag_making_detail',compact('data','ms_work_orders','data_detail_preparation','data_detail_hygiene','data_detail_production','data_detail_waste','ms_known_by'));
				
			}else{
				return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error.');
			}
		}else{
			return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error.');
		}
		
    }   
	public function  production_entry_report_bag_making_update(Request $request){
		//print_r($_POST);exit;
		if ($request->has('rb_update')) {            
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryReportBagMaking::whereRaw( "sha1(report_bags.id) = '$request_id'")
				->select('id')
				->get();
				
            $pesan = [
                'date.required' => 'Cannot Be Empty',
                //'id_master_products.required' => 'Cannot Be Empty',
                'id_master_customers.required' => 'Cannot Be Empty',
                'id_master_work_centers.required' => 'Cannot Be Empty',
                'id_master_regus.required' => 'Cannot Be Empty',                
                'shift.required' => 'Cannot Be Empty',                
                'id_known_by.required' => 'Cannot Be Empty',                
            ];

            $validatedData = $request->validate([
                'date' => 'required',
                //'id_master_products' => 'required',
                'id_master_customers' => 'required',
                'id_master_work_centers' => 'required',
                'id_master_regus' => 'required',
                'shift' => 'required',
                'id_known_by' => 'required',

            ], $pesan);			
			
			$validatedData['engine_shutdown_description'] = $_POST['engine_shutdown_description'];
			$validatedData['note'] = $_POST['note'];
			
			$validatedData['known_by'] = $_POST['id_known_by'];
			unset($validatedData["id_known_by"]);
			
			//$validatedData['order_name'] = $_POST['id_master_products'];
			//unset($validatedData["id_master_products"]);
			
            ProductionEntryReportBagMaking::where('id', $data[0]->id)
				->update($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Update Entry Report Bag Making ID="'.$data[0]->id.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
			return Redirect::to('/production-ent-report-bag-making-detail/'.$request_id)->with('pesan', 'Update Successfuly.');
        } elseif ($request->has('pc_update')) {
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryReportBagMakingPreparation::whereRaw( "sha1(report_bag_preparation_checks.id_report_bags) = '$request_id'")
				->select('id', 'id_report_bags')
				->get();
			
            $pesan = [
                'pc_material.required' => 'Cannot Be Empty',
                'pc_ukuran.required' => 'Cannot Be Empty',
                'pc_pisau_seal.required' => 'Cannot Be Empty',       
                'pc_jarum_perforasi.required' => 'Cannot Be Empty',   
                'pc_pembuka_plastik.required' => 'Cannot Be Empty',          
                'pc_guide_rubber_roll.required' => 'Cannot Be Empty',          
            ];

            $validatedData = $request->validate([
                'pc_material' => 'required',
                'pc_ukuran' => 'required',
                'pc_pisau_seal' => 'required',
                'pc_jarum_perforasi' => 'required',
                'pc_pembuka_plastik' => 'required',
                'pc_guide_rubber_roll' => 'required',

            ], $pesan);	
			
            $updatedData = [
                'material' => $validatedData['pc_material'],
                'ukuran' => $validatedData['pc_ukuran'],
                'pisau_seal' => $validatedData['pc_pisau_seal'],
                'jarum_perforasi' => $validatedData['pc_jarum_perforasi'],
                'pembuka_plastik' => $validatedData['pc_pembuka_plastik'],
                'guide_rubber_roll' => $validatedData['pc_guide_rubber_roll'],
            ] ;
			
            ProductionEntryReportBagMakingPreparation::where('id', $data[0]->id)
				->where('id_report_bags', $data[0]->id_report_bags)
				->update($updatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Update Report Bag Making Preparation Check ID="'.$data[0]->id.'", ID Report Bag Making="'.$data[0]->id_report_sfs.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-report-bag-making-detail/'.$request_id)->with('pesan', 'Update Successfuly.');
		} elseif ($request->has('hc_update')) {
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryReportBagMakingHygiene::whereRaw( "sha1(report_bag_hygiene_checks.id_report_bags) = '$request_id'")
				->select('id', 'id_report_bags')
				->get();
			//print_r($_POST);exit;
            $pesan = [
                'hc_roll_guide_roll_karet.required' => 'Cannot Be Empty',     
                'hc_jarum_perforasi.required' => 'Cannot Be Empty',     
                'hc_pisau_seal.required' => 'Cannot Be Empty',     
                'hc_pembuka_plastik.required' => 'Cannot Be Empty',
                'hc_lantai_mesin.required' => 'Cannot Be Empty',    
                'hc_body_mesin.required' => 'Cannot Be Empty',  
            ];

            $validatedData = $request->validate([
                'hc_roll_guide_roll_karet' => 'required',
                'hc_jarum_perforasi' => 'required',
                'hc_pisau_seal' => 'required',
                'hc_pembuka_plastik' => 'required',
                'hc_lantai_mesin' => 'required',
                'hc_body_mesin' => 'required',

            ], $pesan);			
			
			$updatedData = [
                'roll_guide_roll_karet' => $validatedData['hc_roll_guide_roll_karet'],
                'jarum_perforasi' => $validatedData['hc_jarum_perforasi'],
                'pisau_seal' => $validatedData['hc_pisau_seal'],
                'pembuka_plastik' => $validatedData['hc_pembuka_plastik'],
                'lantai_mesin' => $validatedData['hc_lantai_mesin'],
                'body_mesin' => $validatedData['hc_body_mesin'],
            ] ;
			
            ProductionEntryReportBagMakingHygiene::where('id', $data[0]->id)
				->where('id_report_bags', $data[0]->id_report_bags)
				->update($updatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Update Report Bag Making Hygiene Check ID="'.$data[0]->id.'", ID Report Bag Making="'.$data[0]->id_report_sfs.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-report-bag-making-detail/'.$request_id)->with('pesan', 'Update Successfuly.');
		} else {
			return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error.');
		}
    }
	public function production_entry_report_bag_making_detail_production_result_add(Request $request){
		//print_r($_POST);exit;
        if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('save')) {
			
			$barcode_start = $_POST['id_master_barcode_start'];
			$data_slitting = ProductionEntryReportSFProductionResult::whereRaw( "report_sf_production_results.barcode = '$barcode_start'")
				->select('*')
				->get();
			
			//$type_wo = explode('|', $_POST['id_master_products']);
			//echo $_POST['id_master_products'];exit;
			//echo($data_blow[0]->id);exit;
			//echo($data_slitting[0]->id_report_sfs);exit;
			
			if(!empty($data_slitting[0]->id_report_sfs)){
				//print_r($_POST);exit;
				$request_id = $_POST['request_id'];		
				$data = ProductionEntryReportBagMaking::whereRaw( "sha1(report_bags.id) = '$request_id'")
					->select('id')
					->get();
					
				$pesan = [
					'id_work_orders.required' => 'Cannot Be Empty',
					'start.required' => 'Cannot Be Empty',
					'finish.required' => 'Cannot Be Empty',
					'id_master_barcode_start.required' => 'Cannot Be Empty',
					'weight_starting.required' => 'Cannot Be Empty',
					'amount_result.required' => 'Cannot Be Empty',
					'wrap_pcs.required' => 'Cannot Be Empty',
					'wrap.required' => 'Cannot Be Empty',
					'id_master_barcode.required' => 'Cannot Be Empty',
					    
				];

				$validatedData = $request->validate([
					'id_work_orders' => 'required',
					'start' => 'required',
					'finish' => 'required',
					'id_master_barcode_start' => 'required',
					'weight_starting' => 'required',
					'amount_result' => 'required',
					'wrap_pcs' => 'required',
					'wrap' => 'required',
					'id_master_barcode' => 'required',
					
				], $pesan);			
				
				$validatedData['start_time'] = $_POST['start'];		
				$validatedData['finish_time'] = $_POST['finish'];		
				$validatedData['barcode_start'] = $_POST['id_master_barcode_start'];
				$validatedData['barcode'] = $_POST['id_master_barcode'];
				$validatedData['note'] = $_POST['id_master_products_detail'];
				$validatedData['waste'] = $_POST['waste'];
				$validatedData['keterangan'] = $_POST['keterangan'];
				
				unset($validatedData["start"]);
				unset($validatedData["finish"]);
				unset($validatedData["id_master_barcode_start"]);
				unset($validatedData["id_master_barcode"]);
				unset($validatedData["id_master_products_detail"]);
				unset($validatedData["waste"]);
				unset($validatedData["keterangan"]);
				
				$validatedData['id_report_bags'] = $data[0]->id;
				$validatedData['id_report_sfs'] = $data_slitting[0]->id_report_sfs;
				$validatedData['id_report_sf_production_results'] = $data_slitting[0]->id;
				//$validatedData['type_result'] = 'Folding';
				
				$response = ProductionEntryReportBagMakingProductionResult::create($validatedData);
				
				if(!empty($response)){
					//HARUS UPDATE STATUS BARCODE
					//$instock_type = $type_wo[0] == 'WIP' ? 'In Stock SLT WIP' : 'In Stock SLT FG';		
					
					$updatedData['status'] = 'In Stock BAG';//pukul rata jenis nya join lagi berdasarkan wo
				
					DB::table('barcode_detail')
					->where('barcode_number', $response->barcode)
					->update($updatedData);
					
					
					//Audit Log		
					$username= auth()->user()->email; 
					$ipAddress=$_SERVER['REMOTE_ADDR'];
					$location='0';
					$access_from=Browser::browserName();
					$activity='Add Production Result Entry Report Bag Making ID ="'.$response->id.'"';
					$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
					
					return Redirect::to('/production-ent-report-bag-making-detail/'.$request_id)->with('pesan', 'Add Successfuly.');
				}else{
					return Redirect::to('/production-ent-report-bag-making-detail/'.$request_id)->with('pesan_danger', 'There Is An Error.');
				}
			}else{
				return Redirect::to('/production-ent-report-bag-making-detail/'.$request_id)->with('pesan_danger', 'There Is An Error.');
			}
        }
    }
	public function production_entry_report_bag_making_detail_production_result_edit($response_id_rb, $response_id_rb_pr){
		//echo $response_id_rb.' - '.$response_id_rb_pr; exit;
		//print_r($_POST);exit;
		
		$data = DB::table('report_bag_production_results as a')
			->leftJoin('report_bags as b', 'a.id_report_bags', '=', 'b.id')
			->select('a.*', 'b.id_master_customers')
			->whereRaw( "sha1(a.id_report_bags) = '$response_id_rb'")
			->whereRaw( "sha1(a.id) = '$response_id_rb_pr'")
			->get();
			
		$id_master_customers = $data[0]->id_master_customers;
		$ms_work_orders = DB::table('work_orders AS a')
			->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
			->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
			->leftJoin('sales_orders AS d', 'a.id_sales_orders', '=', 'd.id')
			->select('a.*','c.id AS id_master_customers')
			->whereRaw( "a.type_product = 'FG'")
			->whereRaw( "d.id_master_customers = '$id_master_customers'")
			->get();	
			
		//print_r($data);exit;
		if(!empty($data[0])){			
			return view('production.entry_report_bag_making_detail_edit_production_result', compact('data','ms_work_orders'));			
		}else{
			return Redirect::to('/production-ent-report-bag-making-detail/'.$response_id_rs)->with('pesan_danger', 'There Is An Error.');
		}
    } 
	public function production_entry_report_bag_making_detail_production_result_edit_save(Request $request){
		
		//sampe sini cek data sebelum edit terutama update status barcode
		$response_id_rb = $_POST['token_rb'];
		$response_id_rb_pr = $_POST['token_rb_pr'];
		
		$data = DB::table('report_bag_production_results as a')
			->select('a.*')
			->whereRaw( "sha1(a.id_report_bags) = '$response_id_rb'")
			->whereRaw( "sha1(a.id) = '$response_id_rb_pr'")
			->get();
			
		$barcode_start = $_POST['id_master_barcode_start'];
		$data_slitting = ProductionEntryReportSFProductionResult::whereRaw( "report_sf_production_results.barcode = '$barcode_start'")
			->select('*')
			->get();
		//$type_wo = explode('|', $_POST['id_master_products']);
		
		//print_r($_POST);exit;
		if(!empty($data[0])){	
			$pesan = [
				'id_work_orders.required' => 'Cannot Be Empty',
				'start.required' => 'Cannot Be Empty',
				'finish.required' => 'Cannot Be Empty',
				'id_master_barcode_start.required' => 'Cannot Be Empty',
				'weight_starting.required' => 'Cannot Be Empty',
				'amount_result.required' => 'Cannot Be Empty',
				'wrap_pcs.required' => 'Cannot Be Empty',
				'wrap.required' => 'Cannot Be Empty',
				'id_master_barcode.required' => 'Cannot Be Empty',
					
			];

			$validatedData = $request->validate([
				'id_work_orders' => 'required',
				'start' => 'required',
				'finish' => 'required',
				'id_master_barcode_start' => 'required',
				'weight_starting' => 'required',
				'amount_result' => 'required',
				'wrap_pcs' => 'required',
				'wrap' => 'required',
				'id_master_barcode' => 'required',
				
			], $pesan);			
			
			$validatedData['start_time'] = $_POST['start'];		
			$validatedData['finish_time'] = $_POST['finish'];		
			$validatedData['barcode_start'] = $_POST['id_master_barcode_start'];
			$validatedData['barcode'] = $_POST['id_master_barcode'];
			$validatedData['note'] = $_POST['id_master_products'];
			$validatedData['waste'] = $_POST['waste'];
			$validatedData['keterangan'] = $_POST['keterangan'];
			
			unset($validatedData["start"]);
			unset($validatedData["finish"]);
			unset($validatedData["id_master_barcode_start"]);
			unset($validatedData["id_master_barcode"]);
			unset($validatedData["id_master_products"]);
			
			$validatedData['id_report_sfs'] = $data_slitting[0]->id_report_sfs;
			$validatedData['id_report_sf_production_results'] = $data_slitting[0]->id;
						
			$response = ProductionEntryReportBagMakingProductionResult::where('id', $data[0]->id)
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
				$updatedData['status'] = 'In Stock BAG';
			
				$response_barcode = DB::table('barcode_detail')
					->where('barcode_number', $validatedData['barcode'])
					->update($updatedData);
				
				if($validatedData['barcode'] <> $data[0]->barcode){	
					
					DB::table('barcode_detail')
					->where('barcode_number', $data[0]->barcode)
					//->update(['status' => 'Un Used']);
					//Jika Barcode Bisa Digunakan Lagi, Sesuaikan status data barcode menjadi NULL
					->update(['status' => null]);
					
				}
				
				//Audit Log		
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Save Edit Detail Production Result Entry Report Bag Making '.$data[0]->id;
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
				return Redirect::to('/production-ent-report-bag-making-detail/'.$response_id_rb)->with('pesan', 'Update Successfuly.');  
			}else{
				return Redirect::to('/production-ent-report-bag-making-detail/'.$response_id_rb)->with('pesan', 'There Is An Error.');
			}
		}else{
			return Redirect::to('/production-ent-report-bag-making-detail/'.$response_id_rb)->with('pesan', 'There Is An Error.');
		}
		
    }
	public function production_entry_report_bag_making_detail_production_result_delete(Request $request){	
		//print_r($_POST);exit;
		
		$id_rb = $_POST['token_rb'];
		$id = $_POST['hapus_detail'];
		
		$data = ProductionEntryReportBagMakingProductionResult::select("*")
				->whereRaw( "sha1(id) = '$id'")
                ->get();
		$barcode = $data[0]->barcode;
		
		if(!empty($data[0])){
			
			$delete = ProductionEntryReportBagMakingProductionResult::whereRaw( "sha1(id) = '$id'" )->delete();
			//echo $delete; exit;
			
			if($delete){
				//Jika Barcode Bisa Digunakan Lagi, Sesuaikan status data barcode menjadi NULL
				$updatedData['status'] = null;
				
				//$updatedData['status'] = 'Un Used';
				
				DB::table('barcode_detail')
				->where('barcode_number', $barcode)
				->update($updatedData);
				
				//Audit Log		
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Delete Entry Report Bag Making Detail Production Result ID="'.$data[0]->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
				return Redirect::to('/production-ent-report-bag-making-detail/'.$id_rb)->with('pesan', 'Delete Successfuly.');
			}else{
				return Redirect::to('/production-ent-report-bag-making-detail/'.$id_rb)->with('pesan_danger', 'There Is An Error.');
			}
			
		}else{
			return Redirect::to('/production-ent-report-bag-making-detail/'.$id_rb)->with('pesan_danger', 'There Is An Error.');
		}
	}
	public function production_entry_report_bag_making_detail_waste_add(Request $request){
        if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('save')) {
			//print_r($_POST);exit;
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryReportBagMaking::whereRaw( "sha1(report_bags.id) = '$request_id'")
				->select('id')
				->get();
				
            $pesan = [
                'waste.required' => 'Cannot Be Empty',
                'cause_waste.required' => 'Cannot Be Empty',         
            ];

            $validatedData = $request->validate([
                'waste' => 'required',
                'cause_waste' => 'required',

            ], $pesan);			
			
			$validatedData['id_report_bags'] = $data[0]->id;
			
            $response = ProductionEntryReportBagMakingWaste::create($validatedData);
			
			if(!empty($response)){
				//Audit Log		
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Add Waste Entry Report Bag Making ID ="'.$response->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
				return Redirect::to('/production-ent-report-bag-making-detail/'.$request_id)->with('pesan', 'Add Successfuly.');
			}else{
				return Redirect::to('/production-ent-report-bag-making-detail/'.$request_id)->with('pesan_danger', 'There Is An Error.');
			}
        }
    }
	public function production_entry_report_bag_making_detail_waste_edit($response_id_rb, $response_id_rb_w){
		//echo $response_id_rb.' - '.$response_id_rb_pr; exit;
		//print_r($_POST);exit;
		$data = DB::table('report_bag_wastes as a')
			->leftJoin('report_bags as b', 'a.id_report_bags', '=', 'b.id')
			->select('a.*')
			->whereRaw( "sha1(a.id_report_bags) = '$response_id_rb'")
			->whereRaw( "sha1(a.id) = '$response_id_rb_w'")
			->get();
		//print_r($data);exit;
		if(!empty($data[0])){			
			return view('production.entry_report_bag_making_detail_edit_waste', compact('data'));			
		}else{
			return Redirect::to('/production-ent-report-bag-making-detail/'.$response_id_rb)->with('pesan_danger', 'There Is An Error.');
		}
    } 
	public function production_entry_report_bag_making_detail_waste_edit_save(Request $request){
		//print_r($_POST);exit;
		
		$response_id_rb = $_POST['token_rb'];
		$response_id_rb_w = $_POST['token_rb_w'];
		
		$data = DB::table('report_bag_wastes as a')
			->select('a.*')
			->whereRaw( "sha1(a.id_report_bags) = '$response_id_rb'")
			->whereRaw( "sha1(a.id) = '$response_id_rb_w'")
			->get();
		//print_r($data);exit;
		if(!empty($data[0])){			
			$pesan = [
                'waste.required' => 'Cannot Be Empty',
                'cause_waste.required' => 'Cannot Be Empty',       
            ];

            $validatedData = $request->validate([
                'waste' => 'required',
                'cause_waste' => 'required',

            ], $pesan);				
			
			ProductionEntryReportBagMakingWaste::where('id', $data[0]->id)
				->where('id_report_bags', $data[0]->id_report_bags)
				->update($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Save Edit Detail Waste Entry Report Bag Making '.$data[0]->id;
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-report-bag-making-detail/'.$response_id_rb)->with('pesan', 'Update Successfuly.');  			
		}else{
			return Redirect::to('/production-ent-report-bag-making-detail/'.$response_id_rb)->with('pesan_danger', 'There Is An Error.');
		}
		
    }
	public function production_entry_report_bag_making_detail_waste_delete(Request $request){	
		//print_r($_POST);exit;
		
		$id_rb = $_POST['token_rb'];
		$id = $_POST['hapus_detail'];
		
		$data = ProductionEntryReportBagMakingWaste::select("*")
				->whereRaw( "sha1(id) = '$id'")
                ->get();	
		
		if(!empty($data[0])){
			
			$delete = ProductionEntryReportBagMakingWaste::whereRaw( "sha1(id) = '$id'" )->delete();
			//echo $delete; exit;
			
			if($delete){
				//Audit Log		
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Delete Entry Report Bag Making Detail Waste ID="'.$data[0]->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
				return Redirect::to('/production-ent-report-bag-making-detail/'.$id_rb)->with('pesan', 'Delete Successfuly.');
			}else{
				return Redirect::to('/production-ent-report-bag-making-detail/'.$id_rb)->with('pesan_danger', 'There Is An Error.');
			}
			
		}else{
			return Redirect::to('/production-ent-report-bag-making-detail/'.$id_rb)->with('pesan_danger', 'There Is An Error.');
		}
	}
	public function production_entry_report_bag_making_print($response_id)
    {
		//print_r($response_id);exit;
		$data = ProductionEntryReportBagMaking::select("report_bags.*", "c.name", "d.work_center_code", "d.work_center", "e.name AS nama_know_by")
				//->leftJoin('work_orders AS b', 'report_blows.id_work_orders', '=', 'b.id')
				->leftJoin('master_customers AS c', 'report_bags.id_master_customers', '=', 'c.id')
				->leftJoin('master_work_centers AS d', 'report_bags.id_master_work_centers', '=', 'd.id')
				->leftJoin('master_employees AS e', 'report_bags.known_by', '=', 'e.id')
				->whereRaw( "sha1(report_bags.id) = '$response_id'")
                ->get();
		//print_r($data);exit;
		if(!empty($data[0])){
			
			$data_detail_preparation = DB::table('report_bag_preparation_checks')
					->select('report_bag_preparation_checks.*')
					->whereRaw( "sha1(id_report_bags) = '$response_id'")
					->get();
			$data_detail_hygiene = DB::table('report_bag_hygiene_checks')
					->select('report_bag_hygiene_checks.*')
					->whereRaw( "sha1(id_report_bags) = '$response_id'")
					->get();
			$data_detail_waste = DB::table('report_bag_wastes')
					->select('report_bag_wastes.*')
					->whereRaw( "sha1(id_report_bags) = '$response_id'")
					->get();
			//$data_detail_waste = DB::table('report_blow_wastes')
			//		->select('report_blow_wastes.*')
			//		->whereRaw( "sha1(id_report_blows) = '$response_id'")
			//		->get();      
			$data_detail_production = DB::table('report_bag_production_results as a')
					->leftJoin('report_sf_production_results as b', 'a.barcode_start', '=', 'b.barcode')//disesuaikan ke table sfs
					->leftJoin('report_sfs as c', 'a.id_report_sfs', '=', 'c.id')//disesuaikan ke table sfs
					->leftJoin('work_orders as d', 'a.id_work_orders', '=', 'd.id')
					->whereRaw( "sha1(a.id_report_bags) = '$response_id'")
					->select('b.note as order_name_sf', 'b.weight as weight_sf', 'd.wo_number', 'a.*')
					->groupBy('a.id')
					->get();//PERBAIKI QUERY DETAIL UNTUK GET WO DAN PRODUCT
			/*		
			$table_product = $order_name[0] == 'WIP' ? 'master_wips' : 'master_product_fgs';
			
			$data_product = DB::table($table_product)
					->select('*')
					->where('id', $order_name[1])
					->get();
			*/
			
			$order_name = explode('|', $data_detail_production[0]->note);
			
			if(count($order_name)>1){
			//Audit Log
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Print Entry Report Bag Making ID="'.$data[0]->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

				return view('production.entry_report_bag_making_print',compact('data','data_detail_preparation','data_detail_hygiene','data_detail_production','data_detail_waste'));
			}else{
				return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'Data Report Bag Making Versi Aplikasi Sebelumnya Tidak Bisa Di Print');
			}
		}else{
			return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error.');
		}
		
    }
	public function production_entry_report_bag_making_update_stock($response_id){
		//echo $response_id;exit;
		//print_r($_POST);exit;
		
		$id_rb = $response_id;
		
		$data_update = ProductionEntryReportBagMakingProductionResult::select('b.report_number','report_bag_production_results.id_report_bags', 'report_bag_production_results.id', 'report_bag_production_results.note')
				->selectRaw('SUM(report_bag_production_results.amount_result) AS amount')
				->selectRaw('SUM(report_bag_production_results.wrap) AS wrap')
				->rightJoin('report_bags AS b', 'report_bag_production_results.id_report_bags', '=', 'b.id')
				->whereRaw( "sha1(report_bag_production_results.id_report_bags) = '$id_rb'")
				->groupBy('id_report_bags')
				->groupBy('report_bag_production_results.note')
                ->get();			
		
		if(!empty($data_update[0])){
			foreach($data_update as $data_for){
				$order_name = explode('|', $data_for->note);
				
				$data_product = DB::table('master_product_fgs')
					->select('*')
					->whereRaw( "id = '".$order_name[1]."'")
					->get();
				//print_r($data_product);exit;
				if(!empty($data_product[0])){	
					
					$validatedData = ([
						'id_good_receipt_notes_details' => $data_for->report_number,
						'type_product' => $order_name[0],
						'id_master_products' => $order_name[1],
						'qty' => $data_for->amount,
						'type_stock' => 'IN',
						'date' => date("Y-m-d"),
						'remarks' => 'Product : '.$data_for->note
					]);	
					$responseHistory = HistoryStock::create($validatedData);
						
					
					if($responseHistory){		
						$stock_akhir = $data_product[0]->stock + $data_for->amount;				
						
						DB::table('master_product_fgs')->where('id', $order_name[1])->update(array('stock' => $stock_akhir, 'updated_at' => date('Y-m-d H:i:s'))); 						
						
					}
				}else{
					return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error. Data Produk Not Found.');
				}
			}
			
			$validatedData = ([
				'status' => 'Closed',
			]);				
			
			ProductionEntryReportBagMaking::where('report_number', $data_update[0]->report_number)
				->update($validatedData);
			
			//Audit Log
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Update Histori Stock Bag Making Report Number ="'.$data_update[0]->report_number.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
			return Redirect::to('/production-ent-report-bag-making')->with('pesan', 'Update Stock Successfuly.');
		}else{
			return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error.');
		}
    }
	public function production_entry_report_bag_making_unposted($response_id){
		$id_rb = $response_id;
		
		$data_update = ProductionEntryReportBagMakingProductionResult::select('b.report_number','report_bag_production_results.id_report_bags', 'report_bag_production_results.id', 'report_bag_production_results.note')
				->selectRaw('SUM(report_bag_production_results.amount_result) AS amount')
				->selectRaw('SUM(report_bag_production_results.wrap) AS wrap')
				->rightJoin('report_bags AS b', 'report_bag_production_results.id_report_bags', '=', 'b.id')
				->whereRaw( "sha1(report_bag_production_results.id_report_bags) = '$id_rb'")
				->groupBy('id_report_bags')
				->groupBy('report_bag_production_results.note')
                ->get();	
				
		if(!empty($data_update[0])){
			if(!empty($data_update[0]->note)){				
				foreach($data_update as $data_for){
					$order_name = explode('|', $data_for->note);
					
					$data_product = DB::table('master_product_fgs')
						->select('*')
						->whereRaw( "id = '".$order_name[1]."'")
						->get();
					//print_r($data_product);exit;
					if(!empty($data_product[0])){	
						
						$validatedData = ([
							'id_good_receipt_notes_details' => $data_for->report_number,
							'type_product' => $order_name[0],
							'id_master_products' => $order_name[1],
							'qty' => $data_for->amount,
							'type_stock' => 'Un Posted',
							'date' => date("Y-m-d"),
							'remarks' => 'Product : '.$data_for->note
						]);	
						$responseHistory = HistoryStock::create($validatedData);
							
						
						if($responseHistory){		
							$stock_akhir = $data_product[0]->stock - $data_for->amount;				
							
							DB::table('master_product_fgs')->where('id', $order_name[1])->update(array('stock' => $stock_akhir, 'updated_at' => date('Y-m-d H:i:s'))); 						
							
						}
					}else{
						return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error. Data Produk Not Found.');
					}
				}
				
				$validatedData = ([
					'status' => 'Un Posted',
				]);				
				
				ProductionEntryReportBagMaking::where('report_number', $data_update[0]->report_number)
					->update($validatedData);
				
				//Audit Log
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Un Posted Histori Stock Bag Making Report Number ="'.$data_update[0]->report_number.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
					
				return Redirect::to('/production-ent-report-bag-making')->with('pesan', 'Update Stock Successfuly.');
			}else{
				return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'Data Report Bag Making Versi Aplikasi Sebelumnya Tidak Bisa Di Unposted.');
			}
		}else{
			return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error.');
		}
    }
	public function production_entry_report_bag_making_delete($response_id){
		$id_rb = $response_id;
		//echo $id_rb; exit;
		$data_update = ProductionEntryReportBagMakingProductionResult::select('b.report_number','c.type_product','b.order_name','report_bag_production_results.id_report_bags', 'report_bag_production_results.id', 'report_bag_production_results.note')
			//->selectRaw('SUM(IF(report_bag_production_results.status="Good", 1, 0)) AS good')
			//->selectRaw('SUM(IF(report_bag_production_results.status="Hold", 1, 0)) AS hold')
			//->selectRaw('SUM(IF(report_bag_production_results.status="Reject", 1, 0)) AS reject')
			->selectRaw('b.id AS id_rb')
			->rightJoin('report_bags AS b', 'report_bag_production_results.id_report_bags', '=', 'b.id')
			->rightJoin('work_orders AS c', 'report_bag_production_results.id_work_orders', '=', 'c.id')
			->whereRaw( "sha1(report_bag_production_results.id_report_bags) = '$id_rb'")
			->groupBy('report_bag_production_results.id_report_bags')
			->get();		
		
		if(!empty($data_update[0])){	
			
			/*
			$order_name = explode('|', $data_update[0]->note);			
			$master_table = $data_update[0]->type_product=="WIP"?'master_wips':'master_product_fgs';
			
			$data_product = DB::table($master_table)
				->select('*')
				->whereRaw( "id = '".$order_name[1]."'")
				->get();
			*/
			/*
			if(!empty($data_product[0])){
			*/			
				$data_detail = ProductionEntryReportBagMakingProductionResult::select('*')
					->whereRaw( "sha1(report_bag_production_results.id_report_bags) = '$id_rb'")
					->get();
					
				if($data_detail&&(!empty($data_update[0]->note))){
					
					$deleteHistori = HistoryStock::whereRaw( "id_good_receipt_notes_details = '".$data_update[0]->report_number."'" )->delete();
					
					$deleteHygiene = ProductionEntryReportBagMakingHygiene::whereRaw( "id_report_bags = '".$data_update[0]->id_rb."'" )->delete();
					$deletePreparation = ProductionEntryReportBagMakingPreparation::whereRaw( "id_report_bags = '".$data_update[0]->id_rb."'" )->delete();
					$deleteProductionResult = ProductionEntryReportBagMakingProductionResult::whereRaw( "id_report_bags = '".$data_update[0]->id_rb."'" )->delete();
					$deleteProductionWaste = ProductionEntryReportBagMakingWaste::whereRaw( "id_report_bags = '".$data_update[0]->id_rb."'" )->delete();
					$deleteBagMaking = ProductionEntryReportBagMaking::whereRaw( "id = '".$data_update[0]->id_rb."'" )->delete();
					
					if($deleteBagMaking){
						$updatedData['status'] = null;	
						
						foreach($data_detail as $data){
							DB::table('barcode_detail')
								->where('barcode_number', $data->barcode)
								->update($updatedData);
						}			
						
						//Audit Log
						$username= auth()->user()->email; 
						$ipAddress=$_SERVER['REMOTE_ADDR'];
						$location='0';
						$access_from=Browser::browserName();
						$activity='Deleted Bag Making Report Number ="'.$data_update[0]->report_number.'"';
						$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
					
						return Redirect::to('/production-ent-report-bag-making')->with('pesan', 'Delete Successfuly.');
					}else{
						return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error.');
					}						
				}else{
					return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error.');
				}
			/*	
			}else{
				return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error. Data Produk Not Found.');
			}
			*/
		}else{
			$data_bag_making = DB::table('report_bags')
				->selectRaw('id AS id_rb')
				->selectRaw('report_number')
				->whereRaw( "sha1(id) = '".$id_rb."'")
				->get();
			
			//print_r($data_blow);exit;
			if($data_bag_making){
				$report_number = $data_bag_making[0]->report_number;
				
				$deleteHygiene = ProductionEntryReportBagMakingHygiene::whereRaw( "id_report_bags = '".$data_bag_making[0]->id_rb."'" )->delete();
				$deletePreparation = ProductionEntryReportBagMakingPreparation::whereRaw( "id_report_bags = '".$data_bag_making[0]->id_rb."'" )->delete();
				$deleteProductionResult = ProductionEntryReportBagMakingProductionResult::whereRaw( "id_report_bags = '".$data_bag_making[0]->id_rb."'" )->delete();
				$deleteProductionWaste = ProductionEntryReportBagMakingWaste::whereRaw( "id_report_bags = '".$data_bag_making[0]->id_rb."'" )->delete();
				$deleteBagMaking = ProductionEntryReportBagMaking::whereRaw( "id = '".$data_bag_making[0]->id_rb."'" )->delete();
				
				if($deleteBagMaking){
					//Audit Log
					$username= auth()->user()->email; 
					$ipAddress=$_SERVER['REMOTE_ADDR'];
					$location='0';
					$access_from=Browser::browserName();
					$activity='Deleted Bag Making Report Number ="'.$report_number.'"';
					$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
					return Redirect::to('/production-ent-report-bag-making')->with('pesan', 'Delete Successfuly.');
				}else{
					return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error.');
				}	
			}else{
				return Redirect::to('/production-ent-report-bag-making')->with('pesan_danger', 'There Is An Error.');
			}
		}
    }
	//END ENTRY REPORT BLOW
}
