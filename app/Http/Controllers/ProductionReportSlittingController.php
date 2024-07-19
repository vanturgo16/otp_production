<?php

namespace App\Http\Controllers;

use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use RealRashid\SweetAlert\Facades\Alert;
use Browser;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\Datatables;

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

//END REQUEST SPAREPART AND AUXILIARIES

class ProductionReportSlittingController extends Controller
{
    use AuditLogsTrait;
	
	//START ENTRY REPORT BLOW
	public function production_entry_report_slitting()
    {
        $datas = ProductionEntryReportSF::leftJoin('work_orders AS b', 'report_sfs.id_work_orders', '=', 'b.id')
                ->select('report_sfs.*', 'b.wo_number')
                ->orderBy('report_sfs.created_at', 'desc')
                ->get();

        //Audit Log		
        $username= auth()->user()->email; 
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $location='0';
        $access_from=Browser::browserName();
        $activity='View List Entry Report Slitting';
        $this->auditLogs($username,$ipAddress,$location,$access_from,$activity);		

        return view('production.entry_report_blow', compact('datas'));
    }
	public function production_entry_report_slitting_json()
    {
        $datas = ProductionEntryReportBlow::leftJoin('work_orders AS b', 'report_blows.id_work_orders', '=', 'b.id')
				->leftJoin('master_regus AS c', 'report_blows.id_master_regus', '=', 'c.id')
				->leftJoin('master_work_centers AS d', 'report_blows.id_master_work_centers', '=', 'd.id')
				->leftJoin('master_customers AS e', 'report_blows.id_master_customers', '=', 'e.id')
				//->leftJoin('report_blows_production_results AS f', 'report_blows.id', '=', 'f.id_report_blows')
                ->select('report_blows.*', 'b.wo_number', 'c.regu', 'd.work_center', 'e.name')
                //->selectRaw('SUM(IF(f.status="Good", 1, 0)) AS good')
                ->orderBy('report_blows.created_at', 'desc')
                ->get();
		//print_r($datas);exit;
		return DataTables::of($datas) 			
			->addColumn('report_info', function ($data) {			
				$report_info = '<p>Report Number : '.$data->report_number.'<br><code>Work Order : '.$data->wo_number.'</code><br><footer class="blockquote-footer">Date : <cite>'.$data->date.'</cite></footer></p>';
				return $report_info;
			})
			->addColumn('order_info', function ($data) {	
				$order_name = explode('|', $data->order_name);	
				$order_name = count($order_name)>1?$order_name[2]:$order_name[0];
				//$order_name = $data->order_name;	
				$status = empty($data->status)?"Tidak Tersedia":$data->status;			
				$order_info = '<p>Order Name : '.$order_name.'<br><code>Customer : '.$data->name.'</code><br><footer class="blockquote-footer">Status : <cite>'.$status.'</cite></footer></p>';
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
						<a href="#" class="btn btn-info waves-effect btn-label waves-light"><i class="bx bx-info-circle  label-icon"></i>  Stock Updated</a><br>
						<a href="#" class="btn btn-primary waves-effect btn-label waves-light mt-1"><i class="bx bx-reply label-icon"></i> Un Posted</a>';
				}
				
				return $update;
			})
			->addColumn('action', function ($data) {
				$return_approve = "return confirm('Are you sure to approve this item ?')";
				$return_hold = "return confirm('Are you sure to hold this item ?')";
				$return_delete = "return confirm('Are you sure to delete this item ?')";
				
				$tombol = '
					<center>
						<a target="_blank" href="/production-ent-report-blow-material-use/'.sha1($data->id_work_orders).'" class="btn btn-outline-dark waves-effect waves-light">
							<i class="bx bx-file" title="Material Use"></i> Material Used
						</a>
				';
				
				if($data->status=='Un Posted'){
					$tombol .= '
							<a target="_blank" href="/production-ent-report-blow-detail/'.sha1($data->id).'" class="btn btn-outline-info waves-effect waves-light">
								<i class="bx bx-edit-alt" title="Edit"></i> EDIT
							</a>
							<a onclick="'.$return_delete.'" href="#" class="btn btn-outline-danger waves-effect waves-light" onclick="return confirm('."'Anda yakin mau menghapus item ini ?'".')">
								<i class="bx bx-trash-alt" title="Delete" ></i> DELETE
							</a>
							<a target="_blank" href="/production-ent-report-blow-print/'.sha1($data->id).'" class="btn btn-dark waves-effect waves-light">
								<i class="bx bx-printer" title="Print"></i> PRINT
							</a>
						</center>					
					';
				}else{
					$tombol .= '
							<a target="_blank" href="/production-ent-report-blow-print/'.sha1($data->id).'" class="btn btn-dark waves-effect waves-light">
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
	
	public function production_entry_report_blow_json_preparation(Request $request){	
		$id_rb = request()->get('id');
		
		$data = ProductionEntryReportBlowPreparation::leftJoin('report_blows as b', 'report_blow_preparation_checks.id_report_blows', '=', 'b.id')
				->select('report_blow_preparation_checks.*', 'b.report_number')
				->whereRaw( "sha1(report_blow_preparation_checks.id_report_blows) = $id_rb")
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
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Ratio Camp Resin</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->ratio_camp_resin=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->ratio_camp_resin=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Temp Heater</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->temp_heater=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->temp_heater=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Guide Roll</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->guide_roll=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->guide_roll=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Rubber Roll</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->rubber_roll=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->rubber_roll=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Saringan Resin</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->saringan_resin=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->saringan_resin=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
					</table>
				</div>				
			</div>
		
		<?php
	}	
	public function production_entry_report_blow_json_hygiene(Request $request){	
		$id_rb = request()->get('id');
		
		$data = ProductionEntryReportBlowHygiene::leftJoin('report_blows as b', 'report_blow_hygiene_checks.id_report_blows', '=', 'b.id')
				->select('report_blow_hygiene_checks.*', 'b.report_number')
				->whereRaw( "sha1(report_blow_hygiene_checks.id_report_blows) = $id_rb")
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
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Guide Rubber Roll </td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->guide_rubber_roll=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->guide_rubber_roll=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Bak Resin</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->bak_resin=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->bak_resin=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Mixer Resin</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->mixer_resin=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->mixer_resin=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
						</tr>
						<tr>
							<td><i class="mdi mdi-arrow-right text-primary me-1"></i>Ember Resin</td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->ember_resin=="Ok"?'check':'close'; ?> text-primary me-1"></i></td>
							<td class="text-center"><i class="mdi mdi-<?= $data[0]->ember_resin=="Not Ok"?'check':'close'; ?> text-primary me-1"></i></td>
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
					</table>
				</div>				
			</div>
		
		<?php
	}	
	public function production_entry_report_blow_json_update_stock(Request $request){
		
		/*
		?>
		Test
		<?php
		*/
		
		$id_rb = request()->get('id');
		
		$data = ProductionEntryReportBlowProductionResult::select('b.report_number','report_blow_production_results.id_report_blows', 'report_blow_production_results.id')
				->selectRaw('SUM(IF(report_blow_production_results.status="Good", 1, 0)) AS good')
				->selectRaw('SUM(IF(report_blow_production_results.status="Hold", 1, 0)) AS hold')
				->selectRaw('SUM(IF(report_blow_production_results.status="Reject", 1, 0)) AS reject')
				->rightJoin('report_blows AS b', 'report_blow_production_results.id_report_blows', '=', 'b.id')
				->whereRaw( "sha1(report_blow_production_results.id_report_blows) = $id_rb")
				->groupBy('id_report_blows')
                ->get();
		//print_r($data);
		if(!empty($data[0]->id_report_blows)){
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
					<!--a href="/production-entry-report-blow-update-stock/<?= sha1($data[0]->id_report_blows); ?>" type="submit" class="btn btn-primary btn-lg"><i class="bx bx-save" title="Update"></i> UPDATE</a-->
					<a href="#" type="submit" class="btn btn-primary btn-lg"><i class="bx bx-save" title="Update"></i> UPDATE</a>
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
	public function production_entry_report_blow_add(Request $request){
		$ms_departements = DB::table('master_departements')
                        ->select('name','id')
                        ->get();
        $ms_tool_auxiliaries = DB::table('master_tool_auxiliaries')
                        ->select('description','id')
                        ->get();
        $ms_work_orders = DB::table('work_orders AS a')
						->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
						->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
                        ->select('a.*','c.id AS id_master_customers')
                        ->whereRaw( "left(wo_number,5) = 'WOBLW'")
                        ->whereRaw( "a.type_product = 'WIP'")
                        ->get();
        $ms_known_by = DB::table('master_employees')
                        ->select('id','name')
                        ->whereRaw( "id_master_bagians IN('3','4')")
                        ->get();
		//print_r($ms_work_orders);exit;
        $formattedCode = $this->production_entry_report_blow_create_code();
		
        //Audit Log
        $username= auth()->user()->email; 
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $location='0';
        $access_from=Browser::browserName();
        $activity='Add Entry Report Blow';
        $this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

        return view('production.entry_report_blow_add',compact('ms_departements','ms_tool_auxiliaries','ms_work_orders','ms_known_by','formattedCode'));			
    }
	private function production_entry_report_blow_create_code(){
		$lastCode = ProductionEntryReportBlow::orderBy('created_at', 'desc')
        ->value(DB::raw('RIGHT(report_number, 6)'));
    
        // Jika tidak ada nomor urut sebelumnya, atur ke 0
        $lastCode = $lastCode ? $lastCode : 0;

        // Tingkatkan nomor urut
        $nextCode = $lastCode + 1;

        // Format kode dengan panjang 7 karakter
        $formattedCode = 'RB' . str_pad($nextCode, 6, '0', STR_PAD_LEFT);
		
		return $formattedCode;
	}
	public function jsonGetProduk()
    {
        $type_product = request()->get('type_product');
        $id_master_products = request()->get('id_master_products');
        
		$table = $type_product=='FG'?'master_product_fgs':'master_wips';
		
		$datas = DB::table($table)
			->select('description','id')
			->whereRaw( "id = '$id_master_products'")
			->get();
			
		$lists = "<option value='' disabled='' selected=''>** Please Select A Product</option>";		
		foreach($datas as $data){
			$selected = $data->id==$id_master_products?'selected':'';
			$lists .= "<option value='".$type_product.'|'.$data->id.'|'.$data->description."' ".$selected.">".$data->description."</option>";
		}
		
		$callback = array('list_products'=>$lists);
		echo json_encode($callback);			
    }
	public function jsonGetCustomers()
    {
        $id_master_customers = request()->get('id_master_customers');
        
		$datas = DB::table('master_customers')
			->select('name','id')
			->get();
			
		$lists = "<option value='' disabled='' selected=''>** Please Select A Customers</option>";		
		foreach($datas as $data){
			$selected = $data->id==$id_master_customers?'selected':'';
			$lists .= "<option value='".$data->id."' ".$selected.">".$data->name."</option>";
		}
		
		$callback = array('list_customers'=>$lists);
		echo json_encode($callback);			
    }
	public function jsonGetBarcode()
    {
        $key = request()->get('barcode_number');
        
		$datas = DB::table('barcode_detail')
			->select('*')
			->get();
			
		$lists = "<option value='' disabled='' selected=''>** Please Select A Barcodes</option>";		
		foreach($datas as $data){
			$selected = $data->barcode_number==$key?'selected':'';
			$lists .= "<option value='".$data->barcode_number."' ".$selected.">".$data->barcode_number."</option>";
		}
		
		$callback = array('list_barcode'=>$lists);
		echo json_encode($callback);			
    }
	
	public function production_entry_report_blow_save(Request $request){
        if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('save')) {
            $pesan = [
                'id_work_orders.required' => 'Cannot Be Empty',
                'date.required' => 'Cannot Be Empty',
                'id_master_products.required' => 'Cannot Be Empty',
                'id_master_customers.required' => 'Cannot Be Empty',
                'id_master_work_centers.required' => 'Cannot Be Empty',
                'id_master_regus.required' => 'Cannot Be Empty',                
                'shift.required' => 'Cannot Be Empty',                
                'id_known_by.required' => 'Cannot Be Empty',                
            ];

            $validatedData = $request->validate([
                'id_work_orders' => 'required',
                'date' => 'required',
                'id_master_products' => 'required',
                'id_master_customers' => 'required',
                'id_master_work_centers' => 'required',
                'id_master_regus' => 'required',
                'shift' => 'required',
                'id_known_by' => 'required',

            ], $pesan);			
			
			$validatedData['report_number'] = $this->production_entry_report_blow_create_code();
			$validatedData['know_by'] = $_POST['id_known_by'];
			$validatedData['order_name'] = $_POST['id_master_products'];
			$validatedData['status'] = 'Un Posted';
			
            $response = ProductionEntryReportBlow::create($validatedData);
			
			if(!empty($response)){
				$dataHygiene = array(
									'id_report_blows' => $response->id,
									'guide_rubber_roll' => 'Ok',
									'bak_resin' => 'Ok',
									'mixer_resin' => 'Ok',
									'ember_resin' => 'Ok',
									'body_mesin' => 'Ok',
									'lantai_mesin' => 'Ok',
									'created_at' => date('Y-m-d H:i:s'),
									'updated_at' => date('Y-m-d H:i:s')
								);
				ProductionEntryReportBlowHygiene::create($dataHygiene);
				
				$dataPreparation = array(
									'id_report_blows' => $response->id,
									'material' => 'Ok',
									'ukuran' => 'Ok',
									'ratio_camp_resin' => 'Ok',
									'temp_heater' => 'Ok',
									'guide_roll' => 'Ok',
									'rubber_roll' => 'Ok',
									'saringan_resin' => 'Ok',
									'created_at' => date('Y-m-d H:i:s'),
									'updated_at' => date('Y-m-d H:i:s')
								);
				ProductionEntryReportBlowPreparation::create($dataPreparation);
			}
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Save Entry Report Blow ID="'.$response->id.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
            return Redirect::to('/production-ent-report-blow-detail/'.sha1($response->id))->with('pesan', 'Add Successfuly.');
        }
    }
	public function production_entry_report_blow_detail($response_id){
		$data = ProductionEntryReportBlow::select("report_blows.*")
				->whereRaw( "sha1(report_blows.id) = '$response_id'")
                ->get();
		$order_name = explode('|', $data[0]->order_name);
		
		if(!empty($data[0])){
			/*
			$ms_work_orders = DB::table('work_orders')
							->select('id_master_process_productions','wo_number','id')
							->get();
			$ms_work_centers = DB::table('master_work_centers')
							//->where( "id_master_process_productions" , "=", $data[0]->id_master_process_productions)
							->where('id_master_process_productions', '2')
							->select('work_center_code','work_center','id')
							->get();	
			$ms_regus = DB::table('master_regus')
							->where( "id" , "=", $data[0]->id_master_regus)
							->select('id', 'regu')
							->get();	
			*/
			if($data[0]->status=="Un Posted"){
				if(count($order_name)>1){
					$ms_work_orders = DB::table('work_orders AS a')
							->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
							->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
							->select('a.id_master_process_productions','a.wo_number','a.id','c.id AS id_master_customers')
							->whereRaw( "left(wo_number,5) = 'WOBGM'")
							->get();
					$data_detail_preparation = DB::table('report_blow_preparation_checks')
							->select('report_blow_preparation_checks.*')
							->whereRaw( "sha1(id_report_blows) = '$response_id'")
							->get();
					$data_detail_hygiene = DB::table('report_blow_hygiene_checks')
							->select('report_blow_hygiene_checks.*')
							->whereRaw( "sha1(id_report_blows) = '$response_id'")
							->get();
					$data_detail_production = DB::table('report_blow_production_results')
							->select('report_blow_production_results.*')
							->whereRaw( "sha1(id_report_blows) = '$response_id'")
							->get();
					$data_detail_waste = DB::table('report_blow_wastes')
							->select('report_blow_wastes.*')
							->whereRaw( "sha1(id_report_blows) = '$response_id'")
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
					$activity='Detail Entry Report Blow ID="'.$data[0]->id.'"';
					$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

					return view('production.entry_report_blow_detail',compact('data','ms_work_orders','data_detail_preparation','data_detail_hygiene','data_detail_production','data_detail_waste','ms_known_by'));
				}else{
					return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'Data Report Blow Versi Aplikasi Sebelumnya Tidak Bisa Menampilkan Detail');
				}
			}else{
				return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error.');
			}
		}else{
			return Redirect::to('/production-ent-report-blow');
		}
		
    }   
	public function  production_entry_report_blow_update(Request $request){
		//print_r($_POST);exit;
		if ($request->has('rb_update')) {            
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryReportBlow::whereRaw( "sha1(report_blows.id) = '$request_id'")
				->select('id')
				->get();
			
            $pesan = [
                'id_work_orders.required' => 'Cannot Be Empty',
                'date.required' => 'Cannot Be Empty',
                'id_master_products.required' => 'Cannot Be Empty',
                'id_master_customers.required' => 'Cannot Be Empty',
                'id_master_work_centers.required' => 'Cannot Be Empty',
                'id_master_regus.required' => 'Cannot Be Empty',                
                'shift.required' => 'Cannot Be Empty',                
                'id_known_by.required' => 'Cannot Be Empty',                
            ];

            $validatedData = $request->validate([
                'id_work_orders' => 'required',
                'date' => 'required',
                'id_master_products' => 'required',
                'id_master_customers' => 'required',
                'id_master_work_centers' => 'required',
                'id_master_regus' => 'required',
                'shift' => 'required',
                'id_known_by' => 'required',

            ], $pesan);			
			
			$validatedData['know_by'] = $_POST['id_known_by'];		
			$validatedData['order_name'] = $_POST['id_master_products'];
			unset($validatedData["id_known_by"]);
			unset($validatedData["id_master_products"]);
			
            ProductionEntryReportBlow::where('id', $data[0]->id)
				->update($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Update Entry Report Blow ID="'.$data[0]->id.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
			return Redirect::to('/production-ent-report-blow-detail/'.$request_id)->with('pesan', 'Update Successfuly.');
        } elseif ($request->has('pc_update')) {
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryReportBlowPreparation::whereRaw( "sha1(report_blow_preparation_checks.id_report_blows) = '$request_id'")
				->select('id', 'id_report_blows')
				->get();
			
            $pesan = [
                'pc_material.required' => 'Cannot Be Empty',
                'pc_ukuran.required' => 'Cannot Be Empty',
                'pc_ratio_camp_resin.required' => 'Cannot Be Empty',
                'pc_temp_heater.required' => 'Cannot Be Empty',
                'pc_guide_roll.required' => 'Cannot Be Empty',
                'pc_rubber_roll.required' => 'Cannot Be Empty',                
                'pc_saringan_resin.required' => 'Cannot Be Empty',            
            ];

            $validatedData = $request->validate([
                'pc_material' => 'required',
                'pc_ukuran' => 'required',
                'pc_ratio_camp_resin' => 'required',
                'pc_temp_heater' => 'required',
                'pc_guide_roll' => 'required',
                'pc_rubber_roll' => 'required',
                'pc_saringan_resin' => 'required',

            ], $pesan);	
			
            $updatedData = [
                'material' => $validatedData['pc_material'],
                'ukuran' => $validatedData['pc_ukuran'],
                'ratio_camp_resin' => $validatedData['pc_ratio_camp_resin'],
                'temp_heater' => $validatedData['pc_temp_heater'],
                'guide_roll' => $validatedData['pc_guide_roll'],
                'rubber_roll' => $validatedData['pc_rubber_roll'],
                'saringan_resin' => $validatedData['pc_saringan_resin'],
            ] ;
			
            ProductionEntryReportBlowPreparation::where('id', $data[0]->id)
				->where('id_report_blows', $data[0]->id_report_blows)
				->update($updatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Update Report Blow Preparation Check ID="'.$data[0]->id.'", ID Report Blow="'.$data[0]->id_report_blows.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-report-blow-detail/'.$request_id)->with('pesan', 'Update Successfuly.');
		} elseif ($request->has('hc_update')) {
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryReportBlowHygiene::whereRaw( "sha1(report_blow_hygiene_checks.id_report_blows) = '$request_id'")
				->select('id', 'id_report_blows')
				->get();
			
            $pesan = [
                'hc_guide_rubber_roll.required' => 'Cannot Be Empty',
                'hc_bak_resin.required' => 'Cannot Be Empty',
                'hc_mixer_resin.required' => 'Cannot Be Empty',
                'hc_ember_resin.required' => 'Cannot Be Empty',
                'hc_body_mesin.required' => 'Cannot Be Empty',
                'hc_lantai_mesin.required' => 'Cannot Be Empty',               
            ];

            $validatedData = $request->validate([
                'hc_guide_rubber_roll' => 'required',
                'hc_bak_resin' => 'required',
                'hc_mixer_resin' => 'required',
                'hc_ember_resin' => 'required',
                'hc_body_mesin' => 'required',
                'hc_lantai_mesin' => 'required',

            ], $pesan);			
			
			$updatedData = [
                'guide_rubber_roll' => $validatedData['hc_guide_rubber_roll'],
                'bak_resin' => $validatedData['hc_bak_resin'],
                'mixer_resin' => $validatedData['hc_mixer_resin'],
                'ember_resin' => $validatedData['hc_ember_resin'],
                'body_mesin' => $validatedData['hc_body_mesin'],
                'lantai_mesin' => $validatedData['hc_lantai_mesin'],
            ] ;
			
            ProductionEntryReportBlowHygiene::where('id', $data[0]->id)
				->where('id_report_blows', $data[0]->id_report_blows)
				->update($updatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Update Report Blow Hygiene Check ID="'.$data[0]->id.'", ID Report Blow="'.$data[0]->id_report_blows.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-report-blow-detail/'.$request_id)->with('pesan', 'Update Successfuly.');
		} else {
			return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error.');
		}
    }
	public function production_entry_report_blow_material_use($response_id)//sampe sini
    {
		$data = DB::table('work_orders')
				->select('*')
				->whereRaw( "sha1(id) = '$response_id'")
				->get();
		
        //Audit Log		
        $username= auth()->user()->email; 
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $location='0';
        $access_from=Browser::browserName();
        $activity='View List Entry Report Blow - Material Use';
        $this->auditLogs($username,$ipAddress,$location,$access_from,$activity);		

        return view('production.entry_report_blow_material_use', compact('data'));
    }
	public function production_entry_report_blow_material_use_json()
    {
		
		$where = request()->get('work_order');
		//echo $where;
        $datas = ProductionEntryMaterialUse::leftJoin('work_orders AS b', 'report_material_uses.id_work_orders', '=', 'b.id')
				->leftJoin('master_regus AS c', 'report_material_uses.id_master_regus', '=', 'c.id')
				->leftJoin('master_work_centers AS d', 'report_material_uses.id_master_work_centers', '=', 'd.id')
                ->select('report_material_uses.*', 'b.wo_number', 'c.regu', 'd.work_center')
				->whereRaw( "b.wo_number = '$where'")
                ->orderBy('report_material_uses.created_at', 'desc')
                ->get();
				
		return DataTables::of($datas)
			->addColumn('action', function ($data) {
				$return_approve = "return confirm('Are you sure to approve this item ?')";
				$return_hold = "return confirm('Are you sure to hold this item ?')";
				$return_delete = "return confirm('Are you sure to delete this item ?')";
				$tombol = '
						<a target="_blank" href="/production-ent-material-use-print/'.sha1($data->id).'" class="btn btn-dark waves-effect waves-light">
							<i class="bx bx-printer" title="Print"></i> PRINT
						</a>
					</center>						
				';
				return $tombol;
			})
		->make(true);
    }	
	public function production_entry_report_blow_detail_production_result_add(Request $request){
        if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('save')) {
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryReportBlow::whereRaw( "sha1(report_blows.id) = '$request_id'")
				->select('id')
				->get();
				
            $pesan = [
                'start.required' => 'Cannot Be Empty',
                'finish.required' => 'Cannot Be Empty',
                'id_master_barcode.required' => 'Cannot Be Empty',
                'thickness.required' => 'Cannot Be Empty',
                'length.required' => 'Cannot Be Empty',
                'width.required' => 'Cannot Be Empty',                
                'weight.required' => 'Cannot Be Empty',                
                'status.required' => 'Cannot Be Empty',                
            ];

            $validatedData = $request->validate([
                'start' => 'required',
                'finish' => 'required',
                'id_master_barcode' => 'required',
                'thickness' => 'required',
                'length' => 'required',
                'width' => 'required',
                'weight' => 'required',
                'status' => 'required',

            ], $pesan);			
			
			$validatedData['start_time'] = $_POST['start'];		
			$validatedData['finish_time'] = $_POST['finish'];		
			$validatedData['barcode'] = $_POST['id_master_barcode'];		
			unset($validatedData["start"]);
			unset($validatedData["finish"]);
			unset($validatedData["id_master_barcode"]);
			
			$validatedData['id_report_blows'] = $data[0]->id;
			
            $response = ProductionEntryReportBlowProductionResult::create($validatedData);
			
			if(!empty($response)){
				//Audit Log		
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Add Production Result Entry Report Blow ID ="'.$response->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
				return Redirect::to('/production-ent-report-blow-detail/'.$request_id)->with('pesan', 'Add Successfuly.');
			}else{
				return Redirect::to('/production-ent-report-blow-detail/'.$request_id)->with('pesan_danger', 'There Is An Error.');
			}
        }
    }
	public function production_entry_report_blow_detail_production_result_edit($response_id_rb, $response_id_rb_pr){
		//echo $response_id_rb.' - '.$response_id_rb_pr; exit;
		//print_r($_POST);exit;
		$data = DB::table('report_blow_production_results as a')
			->leftJoin('report_blows as b', 'a.id_report_blows', '=', 'b.id')
			->select('a.*')
			->whereRaw( "sha1(a.id_report_blows) = '$response_id_rb'")
			->whereRaw( "sha1(a.id) = '$response_id_rb_pr'")
			->get();
		//print_r($data);exit;
		if(!empty($data[0])){			
			return view('production.entry_report_blow_detail_edit_production_result', compact('data'));			
		}else{
			return Redirect::to('/production-ent-report-blow-detail/'.$response_id_rb)->with('pesan_danger', 'There Is An Error.');
		}
    } 
	public function production_entry_report_blow_detail_production_result_edit_save(Request $request){
		//print_r($_POST);exit;
		
		$response_id_rb = $_POST['token_rb'];
		$response_id_rb_pr = $_POST['token_rb_pr'];
		
		$data = DB::table('report_blow_production_results as a')
			->select('a.*')
			->whereRaw( "sha1(a.id_report_blows) = '$response_id_rb'")
			->whereRaw( "sha1(a.id) = '$response_id_rb_pr'")
			->get();
		//print_r($data);exit;
		if(!empty($data[0])){			
			$pesan = [
                'start.required' => 'Cannot Be Empty',
                'finish.required' => 'Cannot Be Empty',
                'id_master_barcode.required' => 'Cannot Be Empty',
                'thickness.required' => 'Cannot Be Empty',
                'length.required' => 'Cannot Be Empty',
                'width.required' => 'Cannot Be Empty',                
                'weight.required' => 'Cannot Be Empty',                
                'status.required' => 'Cannot Be Empty',                
            ];

            $validatedData = $request->validate([
                'start' => 'required',
                'finish' => 'required',
                'id_master_barcode' => 'required',
                'thickness' => 'required',
                'length' => 'required',
                'width' => 'required',
                'weight' => 'required',
                'status' => 'required',

            ], $pesan);			
			
			$validatedData['start_time'] = $_POST['start'];		
			$validatedData['finish_time'] = $_POST['finish'];		
			$validatedData['barcode'] = $_POST['id_master_barcode'];		
			unset($validatedData["start"]);
			unset($validatedData["finish"]);
			unset($validatedData["id_master_barcode"]);		
			
			ProductionEntryReportBlowProductionResult::where('id', $data[0]->id)
				->where('id_report_blows', $data[0]->id_report_blows)
				->update($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Save Edit Detail Production Result Entry Report Blow '.$data[0]->id;
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-report-blow-detail/'.$response_id_rb)->with('pesan', 'Update Successfuly.');  			
		}else{
			return Redirect::to('/production-ent-report-blow-detail/'.$response_id_rb)->with('pesan', 'There Is An Error.');
		}
		
    }
	public function production_entry_report_blow_detail_production_result_delete(Request $request){	
		//print_r($_POST);exit;
		
		$id_rb = $_POST['token_rb'];
		$id = $_POST['hapus_detail'];
		
		$data = ProductionEntryReportBlowProductionResult::select("*")
				->whereRaw( "sha1(id) = '$id'")
                ->get();
						
		if(!empty($data[0])){
			
			$delete = ProductionEntryReportBlowProductionResult::whereRaw( "sha1(id) = '$id'" )->delete();
			//echo $delete; exit;
			
			if($delete){
				//Audit Log		
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Delete Entry Report Blow Detail Production Result ID="'.$data[0]->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
				return Redirect::to('/production-ent-report-blow-detail/'.$id_rb)->with('pesan', 'Delete Successfuly.');
			}else{
				return Redirect::to('/production-ent-report-blow-detail/'.$id_rb)->with('pesan_danger', 'There Is An Error.');
			}
			
		}else{
			return Redirect::to('/production-ent-report-blow-detail/'.$id_rb)->with('pesan_danger', 'There Is An Error.');
		}
	}
	public function production_entry_report_blow_detail_waste_add(Request $request){
        if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('save')) {
			//print_r($_POST);exit;
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryReportBlow::whereRaw( "sha1(report_blows.id) = '$request_id'")
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
			
			$validatedData['id_report_blows'] = $data[0]->id;
			
            $response = ProductionEntryReportBlowWaste::create($validatedData);
			
			if(!empty($response)){
				//Audit Log		
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Add Waste Entry Report Blow ID ="'.$response->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
				return Redirect::to('/production-ent-report-blow-detail/'.$request_id)->with('pesan', 'Add Successfuly.');
			}else{
				return Redirect::to('/production-ent-report-blow-detail/'.$request_id)->with('pesan_danger', 'There Is An Error.');
			}
        }
    }
	public function production_entry_report_blow_detail_waste_edit($response_id_rb, $response_id_rb_w){
		//echo $response_id_rb.' - '.$response_id_rb_pr; exit;
		//print_r($_POST);exit;
		$data = DB::table('report_blow_wastes as a')
			->leftJoin('report_blows as b', 'a.id_report_blows', '=', 'b.id')
			->select('a.*')
			->whereRaw( "sha1(a.id_report_blows) = '$response_id_rb'")
			->whereRaw( "sha1(a.id) = '$response_id_rb_w'")
			->get();
		//print_r($data);exit;
		if(!empty($data[0])){			
			return view('production.entry_report_blow_detail_edit_waste', compact('data'));			
		}else{
			return Redirect::to('/production-ent-report-blow-detail/'.$response_id_rb)->with('pesan_danger', 'There Is An Error.');
		}
    } 
	public function production_entry_report_blow_detail_waste_edit_save(Request $request){
		//print_r($_POST);exit;
		
		$response_id_rb = $_POST['token_rb'];
		$response_id_rb_w = $_POST['token_rb_w'];
		
		$data = DB::table('report_blow_wastes as a')
			->select('a.*')
			->whereRaw( "sha1(a.id_report_blows) = '$response_id_rb'")
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
			
			ProductionEntryReportBlowWaste::where('id', $data[0]->id)
				->where('id_report_blows', $data[0]->id_report_blows)
				->update($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Save Edit Detail Waste Entry Report Blow '.$data[0]->id;
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-report-blow-detail/'.$response_id_rb)->with('pesan', 'Update Successfuly.');  			
		}else{
			return Redirect::to('/production-ent-report-blow-detail/'.$response_id_rb)->with('pesan', 'There Is An Error.');
		}
		
    }
	public function production_entry_report_blow_detail_waste_delete(Request $request){	
		//print_r($_POST);exit;
		
		$id_rb = $_POST['token_rb'];
		$id = $_POST['hapus_detail'];
		
		$data = ProductionEntryReportBlowWaste::select("*")
				->whereRaw( "sha1(id) = '$id'")
                ->get();	
		
		if(!empty($data[0])){
			
			$delete = ProductionEntryReportBlowWaste::whereRaw( "sha1(id) = '$id'" )->delete();
			//echo $delete; exit;
			
			if($delete){
				//Audit Log		
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Delete Entry Report Blow Detail Waste ID="'.$data[0]->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
				return Redirect::to('/production-ent-report-blow-detail/'.$id_rb)->with('pesan', 'Delete Successfuly.');
			}else{
				return Redirect::to('/production-ent-report-blow-detail/'.$id_rb)->with('pesan_danger', 'There Is An Error.');
			}
			
		}else{
			return Redirect::to('/production-ent-report-blow-detail/'.$id_rb)->with('pesan_danger', 'There Is An Error.');
		}
	}	
	public function production_entry_report_blow_print($response_id)
    {
		$data = ProductionEntryReportBlow::select("report_blows.*", "b.wo_number", "c.name", "d.work_center_code", "d.work_center")
				->leftJoin('work_orders AS b', 'report_blows.id_work_orders', '=', 'b.id')
				->leftJoin('master_customers AS c', 'report_blows.id_master_customers', '=', 'c.id')
				->leftJoin('master_work_centers AS d', 'report_blows.id_master_work_centers', '=', 'd.id')
				->whereRaw( "sha1(report_blows.id) = '$response_id'")
                ->get();
		$order_name = explode('|', $data[0]->order_name);
		
		if(!empty($data[0])){
			if($data[0]->status=="Un Posted"){
				if(count($order_name)>1){
					/*
					$ms_work_orders = DB::table('work_orders AS a')
							->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
							->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
							->select('a.id_master_process_productions','a.wo_number','a.id','c.id AS id_master_customers')
							->whereRaw( "left(wo_number,5) = 'WOBGM'")
							->get();     
					$ms_known_by = DB::table('master_employees')
							->select('id','name')
							->whereRaw( "id_master_bagians IN('3','4')")
							->get();
					*/
					$data_detail_preparation = DB::table('report_blow_preparation_checks')
							->select('report_blow_preparation_checks.*')
							->whereRaw( "sha1(id_report_blows) = '$response_id'")
							->get();
					$data_detail_hygiene = DB::table('report_blow_hygiene_checks')
							->select('report_blow_hygiene_checks.*')
							->whereRaw( "sha1(id_report_blows) = '$response_id'")
							->get();
					$data_detail_waste = DB::table('report_blow_wastes')
							->select('report_blow_wastes.*')
							->whereRaw( "sha1(id_report_blows) = '$response_id'")
							->get();      
					$data_detail_production = DB::table('report_blow_production_results')
							->select('report_blow_production_results.*')
							->whereRaw( "sha1(id_report_blows) = '$response_id'")
							->get();
							
					$table_product = $order_name[0] == 'WIP' ? 'master_wips' : 'master_product_fgs';
				    //echo $table_product;exit;
					$data_product = DB::table($table_product)
							->select('*')
							->where('id', $order_name[1])
							->get();
							
					//print_r($data_product );exit;	
					//echo $data_product[0]->wip_type;exit;	
					
					//Audit Log
					$username= auth()->user()->email; 
					$ipAddress=$_SERVER['REMOTE_ADDR'];
					$location='0';
					$access_from=Browser::browserName();
					$activity='Print Entry Report Blow ID="'.$data[0]->id.'"';
					$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

					return view('production.entry_report_blow_print',compact('data','data_product','data_detail_preparation','data_detail_hygiene','data_detail_production','data_detail_waste'));
				}else{
					return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'Data Report Blow Versi Aplikasi Sebelumnya Tidak Bisa Menampilkan Detail');
				}
			}else{
				return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error.');
			}
		}else{
			return Redirect::to('/production-ent-report-blow');
		}
    }
	public function production_entry_report_blow_update_stock($response_id){
	//public function production_entry_report_blow_update_stock(Request $request){
		//echo "disini";exit;
		//print_r($_POST);exit;
		//echo $response_id;exit;
		$id_rb = $response_id;
		
		$data_update = ProductionEntryReportBlowProductionResult::select('b.report_number','report_blow_production_results.id_report_blows', 'report_blow_production_results.id')
				->selectRaw('SUM(IF(report_blow_production_results.status="Good", 1, 0)) AS good')
				->selectRaw('SUM(IF(report_blow_production_results.status="Hold", 1, 0)) AS hold')
				->selectRaw('SUM(IF(report_blow_production_results.status="Reject", 1, 0)) AS reject')
				->rightJoin('report_blows AS b', 'report_blow_production_results.id_report_blows', '=', 'b.id')
				->whereRaw( "sha1(report_blow_production_results.id_report_blows) = '$id_rb'")
				->groupBy('id_report_blows')
                ->get();
		print_r($data_update);exit;
		
		/*
		$response_id_rb = $_POST['token_rb'];
		$response_id_rb_w = $_POST['token_rb_w'];
		
		$data = DB::table('report_blow_wastes as a')
			->select('a.*')
			->whereRaw( "sha1(a.id_report_blows) = '$response_id_rb'")
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
			
			ProductionEntryReportBlowWaste::where('id', $data[0]->id)
				->where('id_report_blows', $data[0]->id_report_blows)
				->update($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Save Edit Detail Waste Entry Report Blow '.$data[0]->id;
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-report-blow-detail/'.$response_id_rb)->with('pesan', 'Update Successfuly.');  			
		}else{
			return Redirect::to('/production-ent-report-blow-detail/'.$response_id_rb)->with('pesan', 'There Is An Error.');
		}
		*/
    }
	//END ENTRY REPORT BLOW
}
