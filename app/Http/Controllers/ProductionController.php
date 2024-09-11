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

use App\Models\ProductionEntryReportBlow;
use App\Models\ProductionEntryReportBlowHygiene;
use App\Models\ProductionEntryReportBlowPreparation;
use App\Models\ProductionEntryReportBlowProductionResult;
use App\Models\ProductionEntryReportBlowWaste;
use App\Models\HistoryStock;

//END REQUEST SPAREPART AND AUXILIARIES




class ProductionController extends Controller
{
    use AuditLogsTrait;
	
	//START REQUEST SPAREPART AND AUXILIARIES
    public function production_req_sparepart_auxiliaries()
    {
        $data = ProductionReqSparepartAuxiliaries::leftJoin('master_departements AS b', 'request_tool_auxiliaries.id_master_departements', '=', 'b.id')
                ->select('request_tool_auxiliaries.*', 'b.name')
                ->orderBy('request_tool_auxiliaries.created_at', 'desc')
                ->get();

        //Audit Log		
        $username= auth()->user()->email; 
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $location='0';
        $access_from=Browser::browserName();
        $activity='View List Request Sparepart Auxiliaries';
        $this->auditLogs($username,$ipAddress,$location,$access_from,$activity);		

        return view('production.req_sparepart_auxiliaries',compact('data'));
    }
	public function production_req_sparepart_auxiliaries_json()
    {
        $datas = ProductionReqSparepartAuxiliaries::leftJoin('master_departements AS b', 'request_tool_auxiliaries.id_master_departements', '=', 'b.id')
                ->select('request_tool_auxiliaries.*', 'b.name')
                ->orderBy('request_tool_auxiliaries.created_at', 'desc')
                ->get();
				
		return DataTables::of($datas)
			->addColumn('action', function ($data) {
				$return_approve = "return confirm('Are you sure to approve this item ?')";
				$return_hold = "return confirm('Are you sure to hold this item ?')";
				$return_delete = "return confirm('Are you sure to delete this item ?')";
				if($data->status=='Hold'){
					$tombol = '
						<center>
							<a onclick="'.$return_approve.'" href="/production-req-sparepart-auxiliaries-approve/'.sha1($data->id).'" class="btn btn-primary waves-effect waves-light">
								<i class="bx bx-check" title="Approve"></i> APPROVE
							</a>
					';
				}elseif($data->status=='Approve'){
					$tombol = '
						<center>
							<a onclick="'.$return_hold.'" href="/production-req-sparepart-auxiliaries-hold/'.sha1($data->id).'" class="btn btn-secondary waves-effect waves-light">
								<i class="bx bx-block" title="Hold"></i> HOLD
							</a>						
					';				
				}elseif($data->status=='Request'){
					$tombol = '
						<center>
							<a onclick="'.$return_approve.'" href="/production-req-sparepart-auxiliaries-approve/'.sha1($data->id).'" class="btn btn-primary waves-effect waves-light">
								<i class="bx bx-check" title="Approve"></i> APPROVE
							</a>
							<a onclick="'.$return_hold.'" href="/production-req-sparepart-auxiliaries-hold/'.sha1($data->id).'" class="btn btn-secondary waves-effect waves-light">
								<i class="bx bx-block" title="Hold"></i> HOLD
							</a>	
					';
				}
				$tombol .= '
						<a onclick="'.$return_delete.'" target="_blank" href="/production-req-sparepart-auxiliaries-delete/'.sha1($data->id).'" class="btn btn-danger waves-effect waves-light">
							<i class="bx bx-trash-alt" title="Delete" ></i> DELETE
						</a>
						<a target="_blank" href="/production-req-sparepart-auxiliaries-detail/'.sha1($data->request_number).'" class="btn btn-info waves-effect waves-light">
							<i class="bx bx-edit-alt" title="Edit"></i> EDIT
						</a>
					</center>
				';
				return $tombol;
			})
		->make(true);
    }
	public function production_req_sparepart_auxiliaries_add(){
        $ms_departements = DB::table('master_departements')
                        ->select('name','id')
                        ->get();
        $ms_tool_auxiliaries = DB::table('master_tool_auxiliaries')
                        ->select('description','id')
                        ->get();
        
        $formattedCode = $this->production_req_sparepart_auxiliaries_create_code();
		
        //Audit Log
        $username= auth()->user()->email; 
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $location='0';
        $access_from=Browser::browserName();
        $activity='Add Request Sparepart Auxiliaries';
        $this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

        return view('production.req_sparepart_auxiliaries_add',compact('ms_departements','ms_tool_auxiliaries','formattedCode'));
    }
	private function production_req_sparepart_auxiliaries_create_code(){
		$lastCode = ProductionReqSparepartAuxiliaries::orderBy('created_at', 'desc')
        ->value(DB::raw('RIGHT(request_number, 6)'));
    
        // Jika tidak ada nomor urut sebelumnya, atur ke 0
        $lastCode = $lastCode ? $lastCode : 0;

        // Tingkatkan nomor urut
        $nextCode = $lastCode + 1;

        // Format kode dengan panjang 7 karakter
        $formattedCode = 'TA'. date('y') . date('m') . str_pad($nextCode, 6, '0', STR_PAD_LEFT);
		
		return $formattedCode;
	}	
	public function production_req_sparepart_auxiliaries_save(Request $request){
        if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('save')) {
            $pesan = [
                'request_number.required' => 'Cannot Be Empty',
                'date.required' => 'Cannot Be Empty',
                'id_master_departements.required' => 'Cannot Be Empty',
                'status.required' => 'Cannot Be Empty',                
            ];

            $validatedData = $request->validate([
                'date' => 'required',
                'id_master_departements' => 'required',
                'status' => 'required',

            ], $pesan);
			$validatedData['request_number'] = $this->production_req_sparepart_auxiliaries_create_code();
			$validatedData['status'] = 'Request';
			
            $request_number = $validatedData['request_number'];
			
            ProductionReqSparepartAuxiliaries::create($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Save Request Sparepart Auxiliaries';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
            return Redirect::to('/production-req-sparepart-auxiliaries-detail/'.sha1($request_number))->with('pesan', 'Add Successfuly.');      
        }        
    }
	public function production_req_sparepart_auxiliaries_hold($response_id){
		
		$data = ProductionReqSparepartAuxiliaries::leftJoin('master_departements AS b', 'request_tool_auxiliaries.id_master_departements', '=', 'b.id')
                ->select('request_tool_auxiliaries.*', 'b.name')
                ->whereRaw( "sha1(request_tool_auxiliaries.id) = '$response_id'")
                ->get();
		
		if(!empty($data[0])){
			
			$validatedData['status'] = 'Hold';			
			
			ProductionReqSparepartAuxiliaries::whereRaw( "sha1(id) = '$response_id'" )
				->update($validatedData);
		
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Hold Request Sparepart Auxiliaries "'.$data[0]->request_number.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-req-sparepart-auxiliaries')->with('pesan', 'Hold Successfuly.');
		}else{
			return Redirect::to('/production-req-sparepart-auxiliaries')->with('pesan', 'There Is An Error.');
		}
	}
	public function production_req_sparepart_auxiliaries_approve($response_id){
		
		$data = ProductionReqSparepartAuxiliaries::leftJoin('master_departements AS b', 'request_tool_auxiliaries.id_master_departements', '=', 'b.id')
                ->select('request_tool_auxiliaries.*', 'b.name')
                ->whereRaw( "sha1(request_tool_auxiliaries.id) = '$response_id'")
                ->get();
		
		if(!empty($data[0])){
			
			$validatedData['status'] = 'Approve';			
			
			ProductionReqSparepartAuxiliaries::whereRaw( "sha1(id) = '$response_id'" )
				->update($validatedData);
		
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Approve Request Sparepart Auxiliaries "'.$data[0]->request_number.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-req-sparepart-auxiliaries')->with('pesan', 'Approve Successfuly.');
		}else{
			return Redirect::to('/production-req-sparepart-auxiliaries')->with('pesan', 'There Is An Error.');
		}
	}
	public function production_req_sparepart_auxiliaries_delete($response_id){
				
		$data = ProductionReqSparepartAuxiliaries::leftJoin('master_departements AS b', 'request_tool_auxiliaries.id_master_departements', '=', 'b.id')
                ->select('request_tool_auxiliaries.*', 'b.name')
                ->whereRaw( "sha1(request_tool_auxiliaries.id) = '$response_id'")
                ->get();
		
		if(!empty($data[0])){
			
			ProductionReqSparepartAuxiliaries::whereRaw( "sha1(id) = '$response_id'" )->delete();
			ProductionReqSparepartAuxiliariesDetail::whereRaw( "sha1(id_request_tool_auxiliaries) = '$response_id'" )->delete();
		
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Delete Request Sparepart Auxiliaries "'.$data[0]->request_number.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-req-sparepart-auxiliaries')->with('pesan', 'Delete Successfuly.');
		}else{
			return Redirect::to('/production-req-sparepart-auxiliaries')->with('pesan', 'There Is An Error.');
		}
	}
	public function production_req_sparepart_auxiliaries_detail($request_number){
		$data = ProductionReqSparepartAuxiliaries::leftJoin('master_departements AS b', 'request_tool_auxiliaries.id_master_departements', '=', 'b.id')
                ->select('request_tool_auxiliaries.*', 'b.name')
				->whereRaw( "sha1(request_tool_auxiliaries.request_number) = '$request_number'")
                ->orderBy('request_tool_auxiliaries.created_at', 'desc')
                ->get();
		if(!empty($data[0])){
			$ms_departements = DB::table('master_departements')
							->select('name','id')
							->get();
			$ms_tool_auxiliaries = DB::table('master_tool_auxiliaries')
							->select('description','id')
							->get();			
					
			$data_detail = DB::table('request_tool_auxiliaries_details as a')
					->leftJoin('request_tool_auxiliaries as b', 'a.id_request_tool_auxiliaries', '=', 'b.id')
					->leftJoin('master_tool_auxiliaries as c', 'a.id_master_tool_auxiliaries', '=', 'c.id')
					->select('a.*', 'c.description')
					->whereRaw( "sha1(b.request_number) = '$request_number'")
					->get();            
				
			//Audit Log
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Detail Request Sparepart Auxiliaries '.$data[0]->request_number;
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

			return view('production.req_sparepart_auxiliaries_detail',compact('ms_departements','ms_tool_auxiliaries','data','data_detail'));
		}else{
			return Redirect::to('/production-req-sparepart-auxiliaries');
		}
    }   
	public function production_req_sparepart_auxiliaries_detail_update(Request $request){
		if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('update')) {
			
			$request_number = $_POST['request_number'];		
			$data = ProductionReqSparepartAuxiliaries::whereRaw( "sha1(request_tool_auxiliaries.request_number) = '$request_number'")
				->select('request_number')
				->get();
			
            $pesan = [
                'id_master_departements.required' => 'Cannot Be Empty',
                'date.required' => 'Cannot Be Empty',         
            ];

            $validatedData = $request->validate([
                'id_master_departements' => 'required',
                'date' => 'required',
            ], $pesan);
			$validatedData['status'] = 'Request';			
			
            ProductionReqSparepartAuxiliaries::where('request_number', $data[0]->request_number)
				->update($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Update Request Sparepart Auxiliaries '.$data[0]->request_number;
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
            return Redirect::to('/production-req-sparepart-auxiliaries-detail/'.$request_number)->with('pesan', 'Update Successfuly.');
        } 
			
    }
	public function production_req_sparepart_auxiliaries_detail_add(Request $request){
		if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('save')) {
			
			$request_number = $_POST['request_number'];		
			$data = ProductionReqSparepartAuxiliaries::whereRaw( "sha1(request_tool_auxiliaries.request_number) = '$request_number'")
				->select('id','request_number')
				->get();
			
            $pesan = [
                'id_master_tool_auxiliaries.required' => 'Cannot Be Empty',
                'qty.required' => 'Cannot Be Empty',         
            ];

            $validatedData = $request->validate([
                'id_master_tool_auxiliaries' => 'required',
                'qty' => 'required',

            ], $pesan);
			$validatedData['remarks'] = !empty($request->input('remarks'))?$request->input('remarks'):'';			
			$validatedData['id_request_tool_auxiliaries'] = $data[0]->id;			
			
            ProductionReqSparepartAuxiliariesDetail::create($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Add Detail Request Sparepart Auxiliaries '.$data[0]->request_number;
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity); 
			 
            return Redirect::to('/production-req-sparepart-auxiliaries-detail/'.$request_number)->with('pesan', 'Add Successfuly.');      
        } 
			
    }
	public function production_req_sparepart_auxiliaries_detail_edit_get(Request $request, $id)
    {
		$data['find'] = ProductionReqSparepartAuxiliariesDetail::find($id);
        $data['ms_tool_auxiliaries'] = DB::select("SELECT master_tool_auxiliaries.description, master_tool_auxiliaries.id FROM master_tool_auxiliaries");
		
		//Audit Log		
		$username= auth()->user()->email; 
		$ipAddress=$_SERVER['REMOTE_ADDR'];
		$location='0';
		$access_from=Browser::browserName();
		$activity='Get Edit Detail Request Sparepart Auxiliaries '.$id;
		$this->auditLogs($username,$ipAddress,$location,$access_from,$activity); 
			
        return response()->json(['data' => $data]);
    }
	public function production_req_sparepart_auxiliaries_detail_edit_save(Request $request, $id){
		$pesan = [
            'id_master_tool_auxiliaries.required' => 'Cannot Be Empty',
            'qty.required' => 'Cannot Be Empty',
            'remarks.required' => 'Cannot Be Empty',
            
        ];

        $validatedData = $request->validate([
            'id_master_tool_auxiliaries' => 'required',
            'qty' => 'required',
            'remarks' => 'required',

        ], $pesan);

        ProductionReqSparepartAuxiliariesDetail::where('id', $id)
			->update($validatedData);

        $request_number = $request->input('request_number');
		
		//Audit Log		
		$username= auth()->user()->email; 
		$ipAddress=$_SERVER['REMOTE_ADDR'];
		$location='0';
		$access_from=Browser::browserName();
		$activity='Save Edit Detail Request Sparepart Auxiliaries '.$id;
		$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
		
		return Redirect::to('/production-req-sparepart-auxiliaries-detail/'.$request_number)->with('pesan', 'Edit Successfuly.');  
    }
	public function production_req_sparepart_auxiliaries_detail_delete(Request $request){
		$id_delete = $request->input('hapus_detail');
		$request_number = $request->input('request_number');
		
		ProductionReqSparepartAuxiliariesDetail::whereRaw( "sha1(id) = '$id_delete'" )->delete();
		
		//Audit Log		
		$username= auth()->user()->email; 
		$ipAddress=$_SERVER['REMOTE_ADDR'];
		$location='0';
		$access_from=Browser::browserName();
		$activity='Delete Request Sparepart Auxiliaries Detail';
		$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
		
		return Redirect::to('/production-req-sparepart-auxiliaries-detail/'.$request_number)->with('pesan', 'Delete Successfuly.');
	}
	//END REQUEST SPAREPART AND AUXILIARIES
	
	//START ENTRY MATERIAL USE
	public function production_entry_material_use()
    {        
        //Audit Log		
        $username= auth()->user()->email; 
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $location='0';
        $access_from=Browser::browserName();
        $activity='View List Entry Report Material Use';
        $this->auditLogs($username,$ipAddress,$location,$access_from,$activity);		

        return view('production.entry_material_use');
    }
	public function production_entry_material_use_json()
    {
        $datas = ProductionEntryMaterialUse::leftJoin('work_orders AS b', 'report_material_uses.id_work_orders', '=', 'b.id')
				->leftJoin('master_regus AS c', 'report_material_uses.id_master_regus', '=', 'c.id')
				->leftJoin('master_work_centers AS d', 'report_material_uses.id_master_work_centers', '=', 'd.id')
                ->select('report_material_uses.*', 'b.wo_number', 'c.regu', 'd.work_center')
                ->orderBy('report_material_uses.created_at', 'desc')
                ->get();
				
		return DataTables::of($datas)
			->addColumn('action', function ($data) {
				$id = "'".sha1($data->id)."'";
				$return_hold = "return confirm('Are you sure to hold this item ?')";
				$return_delete = "return confirm('Are you sure to delete this item ?')";
				if($data->status=='Hold'){
					$tombol = '
						<center>
							
							<a data-bs-toggle="modal" onclick="showApprove('.$id.')" data-bs-target="#modal_approve" class="btn btn-primary waves-effect waves-light">
								<i class="bx bx-check" title="Approve"></i>  APPROVE
							</a>
							<a target="_blank" href="/production-ent-material-use-detail/'.sha1($data->id).'" class="btn btn-info waves-effect waves-light">
								<i class="bx bx-edit-alt" title="Edit"></i> EDIT
							</a>
							<a onclick="'.$return_delete.'" href="/production-ent-material-use-delete/'.sha1($data->id).'" class="btn btn-danger waves-effect waves-light" onclick="return confirm('."'Anda yakin mau menghapus item inix ?'".')">
								<i class="bx bx-trash-alt" title="Delete" ></i> DELETE
							</a>
					';
				}elseif($data->status=='Approve'){
					$tombol = '
						<center>
							<a onclick="'.$return_hold.'" href="/production-ent-material-use-hold/'.sha1($data->id).'" class="btn btn-warning waves-effect waves-light">
								<i class="bx bx-block" title="Hold"></i> HOLD
							</a>						
					';
				}
				$tombol .= '
						<a target="_blank" href="/production-ent-material-use-print/'.sha1($data->id).'" class="btn btn-dark waves-effect waves-light">
							<i class="bx bx-printer" title="Print"></i> PRINT
						</a>
					</center>						
				';
				return $tombol;
			})
		->make(true);
    }	
	public function production_entry_material_use_add(){
        $ms_work_orders = DB::table('work_orders')
                        ->select('id_master_process_productions','wo_number','id')
                        ->get();
		/*
        $ms_work_centers = DB::table('master_work_centers')
                        ->select('work_center_code','work_center','id')
                        ->get();
        $ms_regus = DB::table('master_regus')
                        ->select('regu','id')
                        ->get();
        */
		
        //Audit Log
        $username= auth()->user()->email; 
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $location='0';
        $access_from=Browser::browserName();
        $activity='Add Entry Report Material Use';
        $this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

        return view('production.entry_material_use_add',compact('ms_work_orders'));
    }
	public function jsonGetWorkCenter()
    {
        $id_master_process_productions = request()->get('id_master_process_productions');
        $data_work_center = request()->has('data_work_center')?request()->get('data_work_center'):'-';
		
		$datas = DB::table('master_work_centers')
			->where('id_master_process_productions', $id_master_process_productions)
			//->where('id_master_process_productions', '2')
			->select('work_center_code','work_center','id')
			->get();
			
		$lists = "<option value='' disabled='' selected=''>** Please Select A Work Centers</option>";		
		foreach($datas as $data){
			$selected = $data->id==$data_work_center?'selected':'';			
			$lists .= "<option ".$selected." value='".$data->id."'>".$data->work_center_code.' - '.$data->work_center."</option>";
		}
		
		$callback = array('list_work_center'=>$lists);
		echo json_encode($callback);			
    }
	public function jsonGetRegu()
    {
        $id_master_work_centers = request()->get('id_master_work_centers');
        $data_regus = request()->has('data_regus')?request()->get('data_regus'):'-';
        
		$datas = DB::table('master_regus')
			->where('id_master_work_centers', $id_master_work_centers)
			->select('id', 'regu')
			->get();
			
		$lists = "<option value='' disabled='' selected=''>** Please Select A Regu</option>";		
		foreach($datas as $data){
			$selected = $data->id==$data_regus?'selected':'';	
			$lists .= "<option ".$selected." value='".$data->id."'>".$data->regu."</option>";
		}
		
		$callback = array('list_regu'=>$lists);
		echo json_encode($callback);			
    }
	public function production_entry_material_use_save(Request $request){
        if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('save')) {
            $pesan = [
                'id_work_orders.required' => 'Cannot Be Empty',
                'date.required' => 'Cannot Be Empty',
                'id_master_work_centers.required' => 'Cannot Be Empty',
                'id_master_regus.required' => 'Cannot Be Empty',                
                'shift.required' => 'Cannot Be Empty',                
            ];

            $validatedData = $request->validate([
                'id_work_orders' => 'required',
                'date' => 'required',
                'id_master_work_centers' => 'required',
                'id_master_regus' => 'required',
                'shift' => 'required',

            ], $pesan);			
            $validatedData['status'] = 'Hold';
			
            $response = ProductionEntryMaterialUse::create($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Save Entry Report Material Use ID="'.$response->id.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
            return Redirect::to('/production-ent-material-use-detail/'.sha1($response->id))->with('pesan', 'Add Successfuly.');
        }
    }
	public function production_entry_material_use_detail($response_id){
		$data = ProductionEntryMaterialUse::leftJoin('work_orders AS b', 'report_material_uses.id_work_orders', '=', 'b.id')
				->select("report_material_uses.*","b.id_master_process_productions")
				->whereRaw( "sha1(report_material_uses.id) = '$response_id'")
                ->get();
				
		if(!empty($data[0])){
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
			
			/*
			$ms_barcodes = DB::table('good_receipt_note_details')
							->where( "lot_number" , "!=", "0")
							->where( "qc_passed" , "=", "Y")
							->orderBy("created_at", "desc")
							->select('id', 'lot_number')
							->get();//sumber data masih belum ter-definisi dengan jelas sumber nya.
			*/

			$ms_barcodes = DB::table('detail_good_receipt_note_details as a')
					->leftJoin('good_receipt_note_details as b', function ($join) {
						$join->on('a.id_grn_detail', '=', 'b.id');
						$join->on('a.lot_number', '=', 'b.lot_number');
					})
					->leftJoin('master_raw_materials as c', 'b.id_master_products', '=', 'c.id')
					->leftJoin('good_receipt_notes as d', 'a.id_grn', '=', 'd.id')
					->where( "a.qty" , ">", "a.qty_out")
					->whereRaw( "ROUND(a.qty-a.qty_out, 1) > 0")
					->select('c.description', 'a.*')
					->selectRaw('ROUND(a.qty-a.qty_out, 1) as sisa')
					->get();
			
			$data_detail = DB::table('report_material_use_details as a')
					->leftJoin('report_material_uses as b', 'a.id_report_material_uses', '=', 'b.id')
					->leftJoin('good_receipt_note_details as c', 'a.id_good_receipt_note_details', '=', 'c.id')
					->leftJoin('master_raw_materials as d', 'c.id_master_products', '=', 'd.id')
					->leftJoin('detail_good_receipt_note_details as e', 'a.id_detail_good_receipt_note_details', '=', 'e.id')
					->select('a.*', 'c.lot_number', 'd.description', 'e.ext_lot_number')
					->whereRaw( "sha1(b.id) = '$response_id'")
					->get();            
				        
			//Audit Log
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Detail Entry Report Material Use ID="'.$data[0]->id.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

			return view('production.entry_material_use_detail',compact('ms_work_orders','ms_work_centers','ms_regus','ms_barcodes','data','data_detail'));
		}else{
			return Redirect::to('/production-ent-material-use');
		}
		
    }   
	
	public function jsonGetMaterialInfo()
	{			
		$lot_number = request()->get('lot_number');
		
		$data = DB::table('detail_good_receipt_note_details as a')
				->leftJoin('good_receipt_note_details as b', function ($join) {
					$join->on('a.id_grn_detail', '=', 'b.id');
					$join->on('a.lot_number', '=', 'b.lot_number');
				})
				->leftJoin('master_raw_materials as c', 'b.id_master_products', '=', 'c.id')
				->select('c.rm_code', 'c.description')
				->selectRaw('SUM(ROUND(a.qty-a.qty_out, 1)) as stok_ext_all')
				->whereRaw( "a.lot_number = '$lot_number'")
				->groupBy('a.lot_number','c.id','c.rm_code','c.description')
                ->get();
				
		$sData = array();
		
		if($data){
			foreach($data as $rs){
				$sData['rm_code'] = $rs->rm_code;
				$sData['description'] = $rs->description;
				$sData['stok_ext_all'] = $rs->stok_ext_all;
			}
		}else{
			$sData['rm_code'] = 'Tidak Ditemukan';
			$sData['description'] = 'Tidak Ditemukan';
			$sData['stok_ext_all'] = 'Tidak Ditemukan';
		}
		
		header('Content-Type: application/json');
		echo json_encode($sData, JSON_PRETTY_PRINT);
	}
	public function production_entry_material_use_update(Request $request){
		if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('update')) {
			$request_id = $_POST['request_id'];		
			$data = ProductionEntryMaterialUse::whereRaw( "sha1(report_material_uses.id) = '$request_id'")
				->select('id')
				->get();
			
            $pesan = [
                'id_work_orders.required' => 'Cannot Be Empty',
                'date.required' => 'Cannot Be Empty',
                'id_master_work_centers.required' => 'Cannot Be Empty',
                'id_master_regus.required' => 'Cannot Be Empty',                
                'shift.required' => 'Cannot Be Empty',                
            ];

            $validatedData = $request->validate([
                'id_work_orders' => 'required',
                'date' => 'required',
                'id_master_work_centers' => 'required',
                'id_master_regus' => 'required',
                'shift' => 'required',

            ], $pesan);	
			//$validatedData['status'] = 'Hold';			
			
            ProductionEntryMaterialUse::where('id', $data[0]->id)
				->update($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Update Entry Report Material Use ID="'.$data[0]->id.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
			return Redirect::to('/production-ent-material-use-detail/'.$request_id)->with('pesan', 'Update Successfuly.');
        } 
			
    }
	
	public function production_entry_report_material_use_json_approve(Request $request){
		
		/*
		?>
		Test
		<?php
		*/
		
		$id_rm = request()->get('id');
		//echo $id_rm;exit;
		$data = ProductionEntryMaterialUseDetail::select('id_report_material_uses')
				->selectRaw('COUNT(report_material_use_details.id) AS jumlah_detail')
				->whereRaw( "sha1(report_material_use_details.id_report_material_uses) = $id_rm")
				//->groupBy('id_report_material_uses')
                ->get();
		//print_r($data);exit;
		if(!empty($data[0]->id_report_material_uses)){
		?>					
			<!--form method="post" action="/production-entry-report-blow-update-stock" class="form-material m-t-40" enctype="multipart/form-data"-->
				
				<div class="card-body">
					<div class="row g-4">
						<div class="col-sm-12">
							<div class="alert alert-success alert-dismissible fade show px-4 mb-0 text-center" role="alert">
								<i class="mdi mdi-check-all d-block display-4 mt-2 mb-3 text-success"></i>
								<h5 class="text-success"><b><?= $data[0]->jumlah_detail; ?></b></h5>
								<p>Data Detail Yang Akan Di APPROVE</p>
							</div>
						</div>
					</div><!-- end row -->
				</div>
				<div class="modal-footer">
					<a href="/production-ent-material-use-approve/<?= sha1($data[0]->id_report_material_uses); ?>" type="submit" class="btn btn-primary btn-lg"><i class="bx bx-save" title="Approve"></i> APPROVE</a>
					<!--a href="#" type="submit" class="btn btn-primary btn-lg"><i class="bx bx-save" title="Update"></i> UPDATE</a-->
				</div>
			<!--/form-->
		<?php
		}else{
		?>
			<div class="card-body">
				<div class="row g-4">
					<div class="col-sm-12">
						<div class="alert alert-danger alert-dismissible fade show px-4 mb-0 text-center" role="alert">
							<i class="mdi mdi-block-helper d-block display-4 mt-2 mb-3 text-danger"></i>
							<!--h5 class="text-danger">Tidak Ada Detail Yang Dapat Di APPROVE</h5-->
							<p>Tidak Ada Detail Yang Dapat Di APPROVE</p>
						</div>
					</div><!-- end col -->
				</div><!-- end row -->
			</div>
		<?php
		}
	}	
	public function production_entry_material_use_approve($response_id){		
		//QUERY UNTUK UPDATE REPORT MATERIAL USES
		$data = ProductionEntryMaterialUse::leftJoin('work_orders AS b', 'report_material_uses.id_work_orders', '=', 'b.id')
				->select("report_material_uses.*","b.id_master_process_productions")
				->whereRaw( "sha1(report_material_uses.id) = '$response_id'")
                ->get();
		
		if(!empty($data[0])){
			$validatedData['status'] = 'Approve';			
			
			$response_material_uses = ProductionEntryMaterialUse::whereRaw( "sha1(id) = '$response_id'" )
										->update($validatedData);
			
			if($response_material_uses){
				
		
				//QUERY UNTUK UPDATE MASTER RM
				$data_update_master = ProductionEntryMaterialUseDetail::select('report_material_use_details.id','report_material_use_details.id_master_products')
					->selectRaw('SUM(taking) AS taking')//MENGURANGI STOCK DI TABLE MASTER
					->whereRaw( "sha1(report_material_use_details.id_report_material_uses) = '$response_id'")
					->groupBy('report_material_use_details.id_master_products')
					->get();
				
				if(!empty($data_update_master)){//UPDATE STOCK MASTER
					foreach($data_update_master as $datas){					
						$data_master = DB::table('master_raw_materials')
							->select('*')
							->whereRaw( "id = '".$datas->id_master_products."'")
							->get();
							
						$stock_akhir = $data_master[0]->stock - $datas->taking;							
						DB::table('master_raw_materials')->where('id', $datas->id_master_products)->update(array('stock' => $stock_akhir)); 
					}
					
					//QUERY UNTUK INSERT HISTORY STOCK
					$data_insert_history = ProductionEntryMaterialUseDetail::select('report_material_use_details.id', 'report_material_use_details.id_report_material_uses', 'report_material_use_details.id_master_products', 'report_material_use_details.id_good_receipt_note_details', 'report_material_use_details.id_detail_good_receipt_note_details')
						->selectRaw('SUM(sisa_camp) AS sisa_camp')
						->selectRaw('SUM(taking) AS taking')
						->selectRaw('SUM(`usage`) AS `usage`')
						->selectRaw('SUM(remaining) AS remaining')//TAMBAHKAN KE FIELD REMARK "Id Material Used | Id Detail Material Used | Id Detail GRN Detail | Remaining"
						->whereRaw( "sha1(report_material_use_details.id_report_material_uses) = '$response_id'")
						->groupBy('report_material_use_details.id')
						->groupBy('report_material_use_details.id_report_material_uses')
						->groupBy('report_material_use_details.id_master_products')
						->get();
					
					if(!empty($data_insert_history)){//UPDATE INSERT HISTORY
						foreach($data_insert_history as $datas){					
							if($datas->sisa_camp>0){
								$validatedData = ([
									'id_good_receipt_notes_details' => $datas->id_good_receipt_note_details,
									'type_product' => 'RM',
									'id_master_products' => $datas->id_master_products,
									'qty' => $datas->sisa_camp,
									'type_stock' => 'SISA_CAMP',
									'date' => date("Y-m-d"),
									'remarks' => $datas->id_report_material_uses.'|'. $datas->id.'|'.$datas->id_detail_good_receipt_note_details.'|Sisa Camp'
									//TAMBAHKAN KE FIELD REMARK "Id Material Used | Id Detail Material Used | Id Detail GRN Detail | Sisa Camp"
								]);	
								$responseTaking = HistoryStock::create($validatedData);
							}					
							if($datas->taking>0){
								$validatedData = ([
									'id_good_receipt_notes_details' => $datas->id_good_receipt_note_details,
									'type_product' => 'RM',
									'id_master_products' => $datas->id_master_products,
									'qty' => $datas->taking,
									'type_stock' => 'OUT',
									'date' => date("Y-m-d"),
									'remarks' => $datas->id_report_material_uses.'|'. $datas->id.'|'.$datas->id_detail_good_receipt_note_details.'|Taking'
									//TAMBAHKAN KE FIELD REMARK "Id Material Used | Id Detail Material Used | Id Detail GRN Detail | Taking"
								]);	
								$responseTaking = HistoryStock::create($validatedData);
							}				
							if($datas->usage>0){
								$validatedData = ([
									'id_good_receipt_notes_details' => $datas->id_good_receipt_note_details,
									'type_product' => 'RM',
									'id_master_products' => $datas->id_master_products,
									'qty' => $datas->usage,
									'type_stock' => 'USAGE',
									'date' => date("Y-m-d"),
									'remarks' => $datas->id_report_material_uses.'|'. $datas->id.'|'.$datas->id_detail_good_receipt_note_details.'|Usage'
									//TAMBAHKAN KE FIELD REMARK "Id Material Used | Id Detail Material Used | Id Detail GRN Detail | Usage"
								]);	
								$responseTaking = HistoryStock::create($validatedData);
							}
							if($datas->remaining>0){
								$validatedData = ([
									'id_good_receipt_notes_details' => $datas->id_good_receipt_note_details,
									'type_product' => 'RM',
									'id_master_products' => $datas->id_master_products,
									'qty' => $datas->remaining,
									'type_stock' => 'REMAINING',
									'date' => date("Y-m-d"),
									'remarks' => $datas->id_report_material_uses.'|'. $datas->id.'|'.$datas->id_detail_good_receipt_note_details.'|Remaining|0'
									//TAMBAHKAN KE FIELD REMARK "Id Material Used | Id Detail Material Used | Id Detail GRN Detail | Remaining"
									//0 Digunakan sebagai parameter penyesuaian stock opname / move stock proins
								]);	
								$responseRemaining = HistoryStock::create($validatedData);
							}
						}
							
						//Audit Log		
						$username= auth()->user()->email; 
						$ipAddress=$_SERVER['REMOTE_ADDR'];
						$location='0';
						$access_from=Browser::browserName();
						$activity='Approve Entry Report Material Use ID="'.$data[0]->id.'"';
						$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
						
						return Redirect::to('/production-ent-material-use')->with('pesan', 'Approve Successfuly.');						
					}else{
						return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
					}
					
				}else{
					return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
				}
					
				
			}else{
				return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
			}
			
		}else{
			return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
		}
	}
	public function production_entry_material_use_hold($response_id){
		//TINGGAL DELETED HISTORY NYA SAJA. 
		//QUERY UNTUK UPDATE REPORT MATERIAL USES
		$data = ProductionEntryMaterialUse::leftJoin('work_orders AS b', 'report_material_uses.id_work_orders', '=', 'b.id')
				->select("report_material_uses.*","b.id_master_process_productions")
				->whereRaw( "sha1(report_material_uses.id) = '$response_id'")
                ->get();
		/*		
		$get_history = HistoryStock::select('history_stocks.remarks')
					->whereRaw( "SUBSTRING_INDEX(remarks, '|', '1') = '".$data[0]->id."'")
					->get();
		echo "<pre>";
		print_r($get_history);
		echo "</pre>";exit;
		*/
		if(!empty($data[0])){
			$validatedData['status'] = 'Hold';			
			
			$response_material_uses = ProductionEntryMaterialUse::whereRaw( "sha1(id) = '$response_id'" )
										->update($validatedData);
			
			if($response_material_uses){
				
		
				//QUERY UNTUK UPDATE MASTER RM
				$data_update_master = ProductionEntryMaterialUseDetail::select('report_material_use_details.id','report_material_use_details.id_master_products')
					->selectRaw('SUM(taking) AS taking')//TAMBAH STOCK DI TABLE MASTER KARENA TIDAK JADI DI APPROVE
					->whereRaw( "sha1(report_material_use_details.id_report_material_uses) = '$response_id'")
					->groupBy('report_material_use_details.id_master_products')
					->get();
				
				if(!empty($data_update_master)){//UPDATE STOCK MASTER
					foreach($data_update_master as $datas){					
						$data_master = DB::table('master_raw_materials')
							->select('*')
							->whereRaw( "id = '".$datas->id_master_products."'")
							->get();
							
						$stock_akhir = $data_master[0]->stock + $datas->taking;							
						DB::table('master_raw_materials')->where('id', $datas->id_master_products)->update(array('stock' => $stock_akhir)); 
					}
					//QUERY UNTUK DELETE HISTORY STOCK
					$get_history = HistoryStock::select('history_stocks.remarks')
						->whereRaw( "SUBSTRING_INDEX(remarks, '|', '1') = '".$data[0]->id."'")
						->get();
					
					if(!empty($get_history)){//UPDATE INSERT HISTORY
						HistoryStock::whereRaw( "SUBSTRING_INDEX(remarks, '|', '1') = '".$data[0]->id."'" )->delete();
						
						//Audit Log		
						$username= auth()->user()->email; 
						$ipAddress=$_SERVER['REMOTE_ADDR'];
						$location='0';
						$access_from=Browser::browserName();
						$activity='Approve Entry Report Material Use ID="'.$data[0]->id.'"';
						$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
						
						return Redirect::to('/production-ent-material-use')->with('pesan', 'Approve Successfuly.');
					}else{
						return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
					}				
				}else{
					return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
				}			
			}else{
				return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
			}
			
		}else{
			return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
		}
	}
	public function production_entry_material_use_delete($response_id){
		$data = ProductionEntryMaterialUse::leftJoin('work_orders AS b', 'report_material_uses.id_work_orders', '=', 'b.id')
				->select("report_material_uses.*","b.id_master_process_productions")
				->whereRaw( "sha1(report_material_uses.id) = '$response_id'")
                ->get();
		
		if(!empty($data[0])){
			//QUERY UNTUK UPDATE MASTER RM
			$data_update_master = ProductionEntryMaterialUseDetail::select('report_material_use_details.id','report_material_use_details.id_master_products')
				->selectRaw('SUM(taking) AS taking')//TAMBAH STOCK DI TABLE MASTER KARENA TIDAK JADI DI APPROVE
				->whereRaw( "sha1(report_material_use_details.id_report_material_uses) = '$response_id'")
				->groupBy('report_material_use_details.id_master_products')
				->get();
			
			if(!empty($data_update_master)){//UPDATE STOCK MASTER
				foreach($data_update_master as $datas){					
					$data_master = DB::table('master_raw_materials')
						->select('*')
						->whereRaw( "id = '".$datas->id_master_products."'")
						->get();
						
					$stock_akhir = $data_master[0]->stock + $datas->taking;							
					//DB::table('master_raw_materials')->where('id', $datas->id_master_products)->update(array('stock' => $stock_akhir)); 
				}
				ProductionEntryMaterialUse::whereRaw( "sha1(id) = '$response_id'" )->delete();
				ProductionEntryMaterialUseDetail::whereRaw( "sha1(id_report_material_uses) = '$response_id'" )->delete();
			
				//Audit Log		
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Delete Entry Report Material Use ID="'.$data[0]->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
				return Redirect::to('/production-ent-material-use')->with('pesan', 'Delete Successfuly.');				
			}else{
				return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
			}	
			
		}else{
			return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
		}
	}
	/*
	public function production_entry_material_use_approve_old($response_id){		
		$data = ProductionEntryMaterialUse::leftJoin('work_orders AS b', 'report_material_uses.id_work_orders', '=', 'b.id')
				->select("report_material_uses.*","b.id_master_process_productions")
				->whereRaw( "sha1(report_material_uses.id) = '$response_id'")
                ->get();
		
		if(!empty($data[0])){
			$validatedData['status'] = 'Approve';			
			
			ProductionEntryMaterialUse::whereRaw( "sha1(id) = '$response_id'" )
				->update($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Approve Entry Report Material Use ID="'.$data[0]->id.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-material-use')->with('pesan', 'Approve Successfuly.');
		}else{
			return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
		}
	}
	public function production_entry_material_use_hold_old($response_id){		
		$data = ProductionEntryMaterialUse::leftJoin('work_orders AS b', 'report_material_uses.id_work_orders', '=', 'b.id')
				->select("report_material_uses.*","b.id_master_process_productions")
				->whereRaw( "sha1(report_material_uses.id) = '$response_id'")
                ->get();
		
		if(!empty($data[0])){
			$validatedData['status'] = 'Hold';			
			
			ProductionEntryMaterialUse::whereRaw( "sha1(id) = '$response_id'" )
				->update($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Hold Entry Report Material Use ID="'.$data[0]->id.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-material-use')->with('pesan', 'Hold Successfuly.');
		}else{
			return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
		}
	}
	public function production_entry_material_use_delete($response_id){
		$data = ProductionEntryMaterialUse::leftJoin('work_orders AS b', 'report_material_uses.id_work_orders', '=', 'b.id')
				->select("report_material_uses.*","b.id_master_process_productions")
				->whereRaw( "sha1(report_material_uses.id) = '$response_id'")
                ->get();
		
		if(!empty($data[0])){
			
			ProductionEntryMaterialUse::whereRaw( "sha1(id) = '$response_id'" )->delete();
			ProductionEntryMaterialUseDetail::whereRaw( "sha1(id_report_material_uses) = '$response_id'" )->delete();
		
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Delete Entry Report Material Use ID="'.$data[0]->id.'"';
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-material-use')->with('pesan', 'Delete Successfuly.');
		}else{
			return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
		}
	}
	*/
	
	public function production_entry_material_use_print($response_id)
    {
        $data = ProductionEntryMaterialUse::leftJoin('work_orders AS b', 'report_material_uses.id_work_orders', '=', 'b.id')
				->leftJoin('master_work_centers AS c', 'report_material_uses.id_master_work_centers', '=', 'c.id')
				->select("report_material_uses.*","b.wo_number","c.work_center")
				->whereRaw( "sha1(report_material_uses.id) = '$response_id'")
                ->get();
				
		if(!empty($data[0])){
			$data_detail = ProductionEntryMaterialUse::rightJoin('report_material_use_details AS b', 'report_material_uses.id', '=', 'b.id_report_material_uses')
				->leftJoin('good_receipt_note_details AS c', 'b.id_good_receipt_note_details', '=', 'c.id')
				->leftJoin('detail_good_receipt_note_details AS d', 'b.id_detail_good_receipt_note_details', '=', 'd.id')
				->select("b.*","c.lot_number", "d.ext_lot_number")
				->whereRaw( "sha1(report_material_uses.id) = '$response_id'")
                ->get();
			return view('production.entry_material_use_print',compact('data','data_detail'));
		}else{
			return Redirect::to('/production-ent-material-use')->with('pesan', 'There Is An Error.');
		}
		
    }
	public function production_entry_material_use_detail_add(Request $request){		
		if ($request->has('savemore')) {
            return "Tombol Save & Add More diklik.";
        } elseif ($request->has('save')) {
			
			$id_rmu = $_POST['token_rmu'];
			$data_rmu = ProductionEntryMaterialUse::whereRaw( "sha1(report_material_uses.id) = '$id_rmu'")
				->select('id')
				->get();
			
			$id = $_POST['token'];
			$data = DB::table('detail_good_receipt_note_details as a')
				->leftJoin('good_receipt_note_details as b', 'a.lot_number', '=', 'b.lot_number')
				->leftJoin('master_raw_materials as c', 'b.id_master_products', '=', 'c.id')
				->select('c.id', 'c.description AS rm_name', 'b.id AS id_good_receipt_note_details', 'a.id AS id_detail_good_receipt_note_details', 'a.qty_out')
				->selectRaw('SUM(ROUND(a.qty-a.qty_out, 1)) as stok_ext_all')
				->whereRaw( "sha1(a.id) = '$id'")
				->groupBy('a.qty_out','a.id','c.id','c.description','b.id')
				->groupBy('a.qty_out','a.id','c.id','c.description','b.id')
                ->get();
			
            $pesan = [
                'taking.required' => 'Cannot Be Empty',
                'usage.required' => 'Cannot Be Empty',                  
                'sisa_campuran.required' => 'Cannot Be Empty',                  
            ];

            $validatedData = $request->validate([
                'taking' => 'required|lte:sisa_ext',
                'usage' => 'required|lte:taking',
                'sisa_campuran' => 'required|lte:taking',

            ], $pesan);
			
			$validatedData['remaining'] = $_POST['taking']-$_POST['usage']+$_POST['sisa_campuran'];			
			$validatedData['id_report_material_uses'] = $data_rmu[0]->id;			
			$validatedData['id_master_products'] = $data[0]->id;			
			$validatedData['rm_name'] = $data[0]->rm_name;			
			$validatedData['sisa_camp'] = $_POST['sisa_campuran'];			
			$validatedData['id_good_receipt_note_details'] = $data[0]->id_good_receipt_note_details;		
			$validatedData['id_detail_good_receipt_note_details'] = $data[0]->id_detail_good_receipt_note_details;			
						
            $create = ProductionEntryMaterialUseDetail::create($validatedData);
			if($create){
				$update_qty['qty_out'] = $data[0]->qty_out + $_POST['taking'];			
				
				DB::table('detail_good_receipt_note_details')
					->where("id", "=", $data[0]->id_detail_good_receipt_note_details)
					->update($update_qty);
			}
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Add Entry Report Material Use '.$data_rmu[0]->id;
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity); 
			
            return Redirect::to('/production-ent-material-use-detail/'.$id_rmu)->with('pesan', 'Add Successfuly.');      
        } 
			
    }
	public function production_entry_material_use_detail_edit($response_id_rm, $response_id_rm_detail){
		
		$ms_barcodes = DB::table('detail_good_receipt_note_details as a')
			->leftJoin('good_receipt_note_details as b', function ($join) {
				$join->on('a.id_grn_detail', '=', 'b.id');
				$join->on('a.lot_number', '=', 'b.lot_number');
			})
			->leftJoin('master_raw_materials as c', 'b.id_master_products', '=', 'c.id')
			->leftJoin('good_receipt_notes as d', 'a.id_grn', '=', 'd.id')
			->where( "a.qty" , ">", "a.qty_out")
			->whereRaw( "ROUND(a.qty-a.qty_out, 1) > 0")
			->select('c.description', 'a.*')
			->selectRaw('ROUND(a.qty-a.qty_out, 1) as sisa')
			->get();
		
		$data = DB::table('report_material_use_details as a')
			->leftJoin('report_material_uses as b', 'a.id_report_material_uses', '=', 'b.id')
			->leftJoin('good_receipt_note_details as c', 'a.id_good_receipt_note_details', '=', 'c.id')
			->leftJoin('master_raw_materials as d', 'c.id_master_products', '=', 'd.id')
			->leftJoin('detail_good_receipt_note_details as e', 'a.id_detail_good_receipt_note_details', '=', 'e.id')
			->select('a.*', 'c.lot_number', 'd.description', 'e.ext_lot_number')
			->whereRaw( "sha1(a.id_report_material_uses) = '$response_id_rm'")
			->whereRaw( "sha1(a.id) = '$response_id_rm_detail'")
			->get();
		
		if(!empty($data[0])){			
			return view('production.entry_material_use_detail_edit', compact('ms_barcodes', 'data'));			
		}else{
			return Redirect::to('/production-ent-material-use');
		}
    } 
	public function production_entry_material_use_detail_edit_save(Request $request){
		$response_id_rm = $_POST['token_rm'];
		$response_id_rm_detail = $_POST['token_rm_detail'];
		
		$data = DB::table('report_material_use_details as a')
			->leftJoin('report_material_uses as b', 'a.id_report_material_uses', '=', 'b.id')
			->leftJoin('good_receipt_note_details as c', 'a.id_good_receipt_note_details', '=', 'c.id')
			->leftJoin('master_raw_materials as d', 'c.id_master_products', '=', 'd.id')
			->leftJoin('detail_good_receipt_note_details as e', 'a.id_detail_good_receipt_note_details', '=', 'e.id')
			->select('a.*', 'c.lot_number', 'd.description', 'e.ext_lot_number')
			->whereRaw( "sha1(a.id_report_material_uses) = '$response_id_rm'")
			->whereRaw( "sha1(a.id) = '$response_id_rm_detail'")
			->get();
		
		if(!empty($data[0])){			
			$pesan = [
				'usage.required' => 'Cannot Be Empty',
				'sisa_campuran.required' => 'Cannot Be Empty',				
			];

			$validatedData = $request->validate([
				'usage' => 'required|lte:'.$data[0]->taking,
				'sisa_campuran' => 'required|lte:'.$data[0]->taking,
			], $pesan);
			
			$validatedData['remaining'] = $data[0]->taking-$_POST['usage']+$_POST['sisa_campuran'];			
			$validatedData['sisa_camp'] = $_POST['sisa_campuran'];			
			
			unset($validatedData["sisa_campuran"]);
			
			ProductionEntryMaterialUseDetail::where('id', $data[0]->id)
				->update($validatedData);
			
			//Audit Log		
			$username= auth()->user()->email; 
			$ipAddress=$_SERVER['REMOTE_ADDR'];
			$location='0';
			$access_from=Browser::browserName();
			$activity='Save Edit Detail Entry Report Material Use '.$data[0]->id;
			$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
			
			return Redirect::to('/production-ent-material-use-detail/'.$response_id_rm)->with('pesan', 'Edit Detail Successfuly.');  			
		}else{
			return Redirect::to('/production-ent-material-use-detail/'.$response_id_rm)->with('pesan', 'There Is An Error.');
		}
    }
	
	public function production_entry_material_use_detail_delete(Request $request){	
		//print_r($_POST);exit;
		
		$id_rmu = $_POST['token_rmu'];
		$id = $_POST['hapus_detail'];
		
		$data = ProductionEntryMaterialUseDetail::select("*")
				->whereRaw( "sha1(id) = '$id'")
                ->get();
		
		$data_detail_grn_detail = DB::table('detail_good_receipt_note_details')
				->select('qty_out')
				->where( "id", "=", $data[0]->id_detail_good_receipt_note_details)
                ->get();
				
		//print_r($data[0]); exit;
		//print_r($data_detail_grn_detail[0]); exit;
		//echo $data_detail_grn_detail[0]->qty_out; exit;	
		
		if(!empty($data[0] && $data_detail_grn_detail[0])){
			
			$delete = ProductionEntryMaterialUseDetail::whereRaw( "sha1(id) = '$id'" )->delete();
			//echo $delete; exit;
			
			if($delete){
				$update_qty['qty_out'] = $data_detail_grn_detail[0]->qty_out - $data[0]->taking;			
				
				DB::table('detail_good_receipt_note_details')
					->where("id", "=", $data[0]->id_detail_good_receipt_note_details)
					->update($update_qty);
				
				//Audit Log		
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Delete Entry Report Material Use Detail ID="'.$data[0]->id.'"';
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
				return Redirect::to('/production-ent-material-use-detail/'.$id_rmu)->with('pesan', 'Delete Successfuly.');
			}else{
				return Redirect::to('/production-ent-material-use-detail/'.$id_rmu)->with('pesan', 'There Is An Error.');
			}
			
		}else{
			return Redirect::to('/production-ent-material-use-detail/'.$id_rmu)->with('pesan', 'There Is An Error.');
		}
	}	
	//END ENTRY MATERIAL USE
	
	//START ENTRY REPORT BLOW
	public function production_entry_report_blow()
    {
        $datas = ProductionEntryReportBlow::leftJoin('work_orders AS b', 'report_blows.id_work_orders', '=', 'b.id')
                ->select('report_blows.*', 'b.wo_number')
                ->orderBy('report_blows.created_at', 'desc')
                ->get();

        //Audit Log		
        $username= auth()->user()->email; 
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $location='0';
        $access_from=Browser::browserName();
        $activity='View List Entry Report Blow';
        $this->auditLogs($username,$ipAddress,$location,$access_from,$activity);		

        return view('production.entry_report_blow', compact('datas'));
    }
	public function production_entry_report_blow_json()
    {
        $datas = ProductionEntryReportBlow::leftJoin('work_orders AS b', 'report_blows.id_work_orders', '=', 'b.id')
				->leftJoin('master_regus AS c', 'report_blows.id_master_regus', '=', 'c.id')
				->leftJoin('master_work_centers AS d', 'report_blows.id_master_work_centers', '=', 'd.id')
				->leftJoin('master_customers AS e', 'report_blows.id_master_customers', '=', 'e.id')
				//->leftJoin('report_blows_production_results AS f', 'report_blows.id', '=', 'f.id_report_blows')
                ->select('report_blows.*', 'b.wo_number', 'c.regu', 'd.work_center', 'e.name')
                //->selectRaw('SUM(IF(f.status="Good", 1, 0)) AS good')
                ->orderBy('report_blows.report_number', 'desc')
                ->get();
		//print_r($datas);exit;
		return DataTables::of($datas) 			
			->addColumn('report_info', function ($data) {			
				$report_info = '<p>Report Number : <b>'.$data->report_number.'</b><br><code>Work Order : '.$data->wo_number.'</code><br><footer class="blockquote-footer">Date : <cite>'.$data->date.'</cite></footer></p>';
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
				$order_name = explode('|', $data->order_name);	
				
				$return_unposted = "return confirm('Are you sure to un posted this item ?')";
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
					if(count($order_name)>1){
						$update = '
							<a data-bs-toggle="modal" onclick="showUpdateStockInfo('.$id.')" data-bs-target="#modal_update_stock_info" class="btn btn-info waves-effect btn-label waves-light"><i class="bx bx-info-circle  label-icon"></i>  Stock Updated</a><br>						
							<a onclick="'.$return_unposted.'" href="/production-entry-report-blow-unposted/'.sha1($data->id).'" class="btn btn-primary waves-effect btn-label waves-light mt-1" onclick="return confirm('."'Anda yakin unposted data ?'".')">
								<i class="bx bx-reply label-icon"></i> Un Posted
							</a>
						';
					}else{
						$update = '
							<a class="btn btn-dark waves-effect btn-label waves-light"><i class="bx bx-time-five label-icon"></i>  Data Lama</a>
							</a>
						';
					}
				}
				
				return $update;
			})
			->addColumn('action', function ($data) {
				$order_name = explode('|', $data->order_name);	
				
				$return_approve = "return confirm('Are you sure to approve this item ?')";
				$return_hold = "return confirm('Are you sure to hold this item ?')";
				$return_delete = "return confirm('Are you sure to delete this item ?')";
				
				$tombol = '<center>';
					
				if(count($order_name)>1){
					$tombol .= '
						<a target="_blank" href="/production-ent-report-blow-material-use/'.sha1($data->id_work_orders).'" class="btn btn-outline-dark waves-effect waves-light">
							<i class="bx bx-file" title="Material Use"></i> Material Used
						</a>
					';
				}
				if($data->status=='Un Posted'){
					$tombol .= '
							<a target="_blank" href="/production-ent-report-blow-detail/'.sha1($data->id).'" class="btn btn-outline-info waves-effect waves-light">
								<i class="bx bx-edit-alt" title="Edit"></i> EDIT
							</a>
							<a onclick="'.$return_delete.'" href="/production-ent-report-blow-delete/'.sha1($data->id).'" class="btn btn-outline-danger waves-effect waves-light" onclick="return confirm('."'Anda yakin mau menghapus item ini ?'".')">
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
					<a href="/production-entry-report-blow-update-stock/<?= sha1($data[0]->id_report_blows); ?>" type="submit" class="btn btn-primary btn-lg"><i class="bx bx-save" title="Update"></i> UPDATE</a>
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
	public function production_entry_report_blow_json_update_stock_info(Request $request){
		
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
			->select('*')
			->whereRaw( "id = '$id_master_products'")
			->get();
			
		$lists = "<option value='' disabled='' selected=''>** Please Select A Product</option>";		
		foreach($datas as $data){
			$ukuran = $type_product=="FG"?$data->thickness." x ".$data->width." x ".$data->height:$data->thickness." x ".$data->width." x ".$data->length;
			$selected = $data->id==$id_master_products?'selected':'';
			$lists .= "<option value='".$type_product.'|'.$data->id.'|'.$data->description.'|'.$ukuran."' ".$selected.">".$data->description."</option>";
			//HARUS DIPERBAIKI TAMBAHKAN SIZE UNTUK DISPLAY UKURAN DI REPORT SLITTING
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
		$where = request()->get('where');
		$key = request()->get('barcode_number');
        
		if($where == "BLOW"){
			$where_query = "a.status IS NULL AND b.id_master_process_productions = '2'";
			
			if(!empty($key)){
				$where_query .= " OR a.barcode_number = '$key'";
			}
			
			$datas = DB::table('barcode_detail as a')
				->leftJoin('barcodes as b', function($join) {
					$join->on('a.id_barcode', '=', 'b.id');
				})
				->select('a.*')
				->whereRaw($where_query)
				->get();				
		}else if($where == "SLITTING START"){			
			$datas = DB::table('barcode_detail as a')
				->leftJoin('barcodes as b', 'a.id_barcode', '=', 'b.id')
				->leftJoin('report_sf_production_results as c', function($join) {
					$join->on('a.barcode_number', '=', 'c.barcode_start')
						 ->whereNotIn('a.barcode_number', function($query) {
							 $query->select('barcode_start')
								   ->from('report_sf_production_results');
						 })
						 ->where('c.type_result', '=', 'Slitting');
				})
				//Cek Status Report BLW START
				->leftJoin('report_blow_production_results as d', 'a.barcode_number', '=', 'd.barcode')
				->leftJoin('report_blows as e', 'd.id_report_blows', '=', 'e.id')
				->where('e.status', 'Closed')
				//Cek Status Report BLW END
				->where('a.status', 'In Stock BLW')
				->select('a.*')
				->get();
		}else if($where == "SLITTING"){
			$where_query = "a.status IS NULL AND b.id_master_process_productions = '4'";
			
			if(!empty($key)){
				$where_query .= " OR a.barcode_number = '$key'";
			}
			
			$datas = DB::table('barcode_detail as a')
				->leftJoin('barcodes as b', function($join) {
					$join->on('a.id_barcode', '=', 'b.id');
				})
				->select('a.*')
				->whereRaw($where_query)
				->get();	
		}else{
			$datas = DB::table('barcode_detail')
					->select('*')
					->get();
		}
		
		
				
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
			$validatedData['type'] = $_POST['type'];
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
		
		if(!empty($data[0])){
			$order_name = explode('|', $data[0]->order_name);
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
					/*
					$ms_work_orders = DB::table('work_orders AS a')
							->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
							->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
							->select('a.id_master_process_productions','a.wo_number','a.id','c.id AS id_master_customers')
							->whereRaw( "left(wo_number,5) = 'WOBGM'")
							->get();
					*/
					$ms_work_orders = DB::table('work_orders AS a')
							->leftJoin('sales_orders AS b', 'a.id_sales_orders', '=', 'b.id')
							->leftJoin('master_customers AS c', 'b.id_master_customers', '=', 'c.id')
							->select('a.*','c.id AS id_master_customers')
							->whereRaw( "left(wo_number,5) = 'WOBLW'")
							->whereRaw( "a.type_product = 'WIP'")
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
				$updatedData['status'] = $_POST['status']=="Good"?'In Stock BLW':$_POST['status'];
				
				DB::table('barcode_detail')
				->where('barcode_number', $response->barcode)
				->update($updatedData);
			
				//Audit Log		
				$username= auth()->user()->email; 
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$location='0';
				$access_from=Browser::browserName();
				$activity='Add Production Result Entry Report Blow ID ="'.$response->id.'", Barcode ID = "'.$validatedData['barcode'].'"';
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
			
			$response = ProductionEntryReportBlowProductionResult::where('id', $data[0]->id)
				->where('id_report_blows', $data[0]->id_report_blows)
				->update($validatedData);
			
			if($response){
				$updatedData['status'] = $_POST['status']=="Good"?'In Stock BLW':$_POST['status'];
			
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
				$activity='Save Edit Detail Production Result Entry Report Blow '.$data[0]->id;
				$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
				return Redirect::to('/production-ent-report-blow-detail/'.$response_id_rb)->with('pesan', 'Update Successfuly.');  	
			}else{
				return Redirect::to('/production-ent-report-blow-detail/'.$response_id_rb)->with('pesan', 'There Is An Error.');
			}
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
				//Jika Barcode Bisa Digunakan Lagi, Sesuaikan status data barcode menjadi NULL
				$updatedData['status'] = null;
				
				//$updatedData['status'] = 'Un Used';
				
				DB::table('barcode_detail')
				->where('barcode_number', $data[0]->barcode)
				->update($updatedData);
				
				
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
			//if($data[0]->status=="Un Posted"){
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
					
					$data_product = DB::table($table_product)
							->select('*')
							->where('id', $order_name[1])
							->get();
					
					//Audit Log
					$username= auth()->user()->email; 
					$ipAddress=$_SERVER['REMOTE_ADDR'];
					$location='0';
					$access_from=Browser::browserName();
					$activity='Print Entry Report Blow ID="'.$data[0]->id.'"';
					$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);

					return view('production.entry_report_blow_print',compact('data','data_product','data_detail_preparation','data_detail_hygiene','data_detail_production','data_detail_waste'));
				}else{
					return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'Data Report Blow Versi Aplikasi Sebelumnya Tidak Bisa Di Print');
				}
			/*
			}else{
				return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error.');
			}
			*/
		}else{
			return Redirect::to('/production-ent-report-blow');
		}
    }
	public function production_entry_report_blow_update_stock($response_id){
	
		$id_rb = $response_id;
		
		$data_update = ProductionEntryReportBlowProductionResult::select('b.report_number','c.type_product','b.order_name','report_blow_production_results.id_report_blows', 'report_blow_production_results.id')
			->selectRaw('SUM(IF(report_blow_production_results.status="Good", 1, 0)) AS good')
			->selectRaw('SUM(IF(report_blow_production_results.status="Hold", 1, 0)) AS hold')
			->selectRaw('SUM(IF(report_blow_production_results.status="Reject", 1, 0)) AS reject')
			->rightJoin('report_blows AS b', 'report_blow_production_results.id_report_blows', '=', 'b.id')
			->rightJoin('work_orders AS c', 'b.id_work_orders', '=', 'c.id')
			->whereRaw( "sha1(report_blow_production_results.id_report_blows) = '$id_rb'")
			->groupBy('id_report_blows')
			->get();
		
		$order_name = explode('|', $data_update[0]->order_name);		
		
		if(!empty($data_update[0])){	
			$data_product = DB::table('master_wips')
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
						'type_stock' => 'IN',
						'date' => date("Y-m-d"),
					]);	
					$responseGood = HistoryStock::create($validatedData);
					
					
				}
				if($data_update[0]->hold>0){
					$validatedData = ([
						'id_good_receipt_notes_details' => $data_update[0]->report_number,
						'type_product' => $data_update[0]->type_product,
						'id_master_products' => $order_name[1],
						'qty' => $data_update[0]->hold,
						'type_stock' => 'HOLD',
						'date' => date("Y-m-d"),
					]);	
					$responseHold = HistoryStock::create($validatedData);
				}
				if($data_update[0]->reject>0){
					$validatedData = ([
						'id_good_receipt_notes_details' => $data_update[0]->report_number,
						'type_product' => $data_update[0]->type_product,
						'id_master_products' => $order_name[1],
						'qty' => $data_update[0]->reject,
						'type_stock' => 'REJECT',
						'date' => date("Y-m-d"),
					]);	
					$responseReject = HistoryStock::create($validatedData);
				}
				
				if($responseGood or $responseHold or $responseReject){
					if($responseGood){					
						$stock_akhir = $data_product[0]->stock + $data_update[0]->good;				
						
						DB::table('master_wips')->where('id', $order_name[1])->update(array('stock' => $stock_akhir)); 						
					}
					
					$validatedData = ([
						'status' => 'Closed',
					]);				
					
					ProductionEntryReportBlow::where('report_number', $data_update[0]->report_number)
						->update($validatedData);
					
					//Audit Log
					$username= auth()->user()->email; 
					$ipAddress=$_SERVER['REMOTE_ADDR'];
					$location='0';
					$access_from=Browser::browserName();
					$activity='Update Histori Stock Blow Report Number ="'.$data_update[0]->report_number.'" (Good : '.$data_update[0]->good.', Hold : '.$data_update[0]->hold.', Reject : '.$data_update[0]->reject.')';
					$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
						
					return Redirect::to('/production-ent-report-blow')->with('pesan', 'Update Stock Successfuly.');
				}else{
					return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error.');
				}
			}else{
				return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error. Data Produk Not Found.');
			}
		}else{
			return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error.');
		}
    }
	public function production_entry_report_blow_unposted($response_id){
	
		$id_rb = $response_id;
		
		$data_update = ProductionEntryReportBlowProductionResult::select('b.report_number','c.type_product','b.order_name','report_blow_production_results.id_report_blows', 'report_blow_production_results.id')
			->selectRaw('SUM(IF(report_blow_production_results.status="Good", 1, 0)) AS good')
			->selectRaw('SUM(IF(report_blow_production_results.status="Hold", 1, 0)) AS hold')
			->selectRaw('SUM(IF(report_blow_production_results.status="Reject", 1, 0)) AS reject')
			->rightJoin('report_blows AS b', 'report_blow_production_results.id_report_blows', '=', 'b.id')
			->rightJoin('work_orders AS c', 'b.id_work_orders', '=', 'c.id')
			->whereRaw( "sha1(report_blow_production_results.id_report_blows) = '$id_rb'")
			->groupBy('id_report_blows')
			->get();
		
		$order_name = explode('|', $data_update[0]->order_name);			
				
		if(!empty($data_update[0])){	
			$data_product = DB::table('master_wips')
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
						'type_stock' => 'Un Posted',
						'date' => date("Y-m-d"),
						'remarks' => 'From GOOD Posted'
					]);	
					$responseGood = HistoryStock::create($validatedData);
					
					if($responseGood){					
						$stock_akhir = $data_product[0]->stock - $data_update[0]->good;				
						
						DB::table('master_wips')->where('id', $order_name[1])->update(array('stock' => $stock_akhir)); 						
					}
				}
				if($data_update[0]->hold>0){
					$validatedData = ([
						'id_good_receipt_notes_details' => $data_update[0]->report_number,
						'type_product' => $data_update[0]->type_product,
						'id_master_products' => $order_name[1],
						'qty' => $data_update[0]->hold,
						'type_stock' => 'Un Posted',
						'date' => date("Y-m-d"),
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
						'remarks' => 'From REJECT Posted'
					]);	
					$responseReject = HistoryStock::create($validatedData);
				}
				
				if($responseGood or $responseHold or $responseReject){
					
					$validatedData = ([
						'status' => 'Un Posted',
					]);				
					
					ProductionEntryReportBlow::where('report_number', $data_update[0]->report_number)
						->update($validatedData);
					
					//Audit Log
					$username= auth()->user()->email; 
					$ipAddress=$_SERVER['REMOTE_ADDR'];
					$location='0';
					$access_from=Browser::browserName();
					$activity='Un Posted Histori Stock Blow Report Number ="'.$data_update[0]->report_number.'" (Good : '.$data_update[0]->good.', Hold : '.$data_update[0]->hold.', Reject : '.$data_update[0]->reject.')';
					$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
						
					return Redirect::to('/production-ent-report-blow')->with('pesan', 'Update Stock Successfuly.');
				}else{
					return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error.');
				}
			}else{
				return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error. Data Produk Not Found.');
			}
		}else{
			return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error.');
		}
    }
	public function production_entry_report_blow_delete($response_id){
		//echo 'disini';exit;
		$id_rb = $response_id;
		
		$data_update = ProductionEntryReportBlowProductionResult::select('b.report_number','c.type_product','b.order_name','report_blow_production_results.id_report_blows', 'report_blow_production_results.id')
			->selectRaw('b.id AS id_rb')
			->selectRaw('SUM(IF(report_blow_production_results.status="Good", 1, 0)) AS good')
			->selectRaw('SUM(IF(report_blow_production_results.status="Hold", 1, 0)) AS hold')
			->selectRaw('SUM(IF(report_blow_production_results.status="Reject", 1, 0)) AS reject')
			->rightJoin('report_blows AS b', 'report_blow_production_results.id_report_blows', '=', 'b.id')
			->rightJoin('work_orders AS c', 'b.id_work_orders', '=', 'c.id')
			->whereRaw( "sha1(report_blow_production_results.id_report_blows) = '$id_rb'")
			->groupBy('report_blow_production_results.id_report_blows')
			->get();
		
		if(!empty($data_update[0])){	
			$order_name = explode('|', $data_update[0]->order_name);
			
			$data_product = DB::table('master_wips')
				->select('*')
				->whereRaw( "id = '".$order_name[1]."'")
				->get();
			
			if(!empty($data_product[0])){	
			
				$data_detail = ProductionEntryReportBlowProductionResult::select('*')
					->whereRaw( "sha1(report_blow_production_results.id_report_blows) = '$id_rb'")
					->get();
				
				if($data_detail){
					$deleteHistori = HistoryStock::whereRaw( "id_good_receipt_notes_details = '".$data_update[0]->report_number."'" )->delete();
					
					$deleteWaste = ProductionEntryReportBlowWaste::whereRaw( "id_report_blows = '".$data_update[0]->id_rb."'" )->delete();
					$deleteHygiene = ProductionEntryReportBlowHygiene::whereRaw( "id_report_blows = '".$data_update[0]->id_rb."'" )->delete();
					$deletePreparation = ProductionEntryReportBlowPreparation::whereRaw( "id_report_blows = '".$data_update[0]->id_rb."'" )->delete();
					$deleteProductionResult = ProductionEntryReportBlowProductionResult::whereRaw( "id_report_blows = '".$data_update[0]->id_rb."'" )->delete();
					$deleteBlow = ProductionEntryReportBlow::whereRaw( "id = '".$data_update[0]->id_rb."'" )->delete();
					//echo $delete; exit;
					
					if($deleteBlow){
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
						$activity='Deleted Blow Report Number ="'.$data_update[0]->report_number.'" (Good : '.$data_update[0]->good.', Hold : '.$data_update[0]->hold.', Reject : '.$data_update[0]->reject.')';
						$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
					
						return Redirect::to('/production-ent-report-blow')->with('pesan', 'Delete Successfuly.');
					}else{
						return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error.');
					}						
				}else{
					return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error.');
				}
			}else{
				return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error. Data Produk Not Found.');
			}
		}else{
			$data_blow = DB::table('report_blows')
				->selectRaw('id AS id_rb')
				->selectRaw('report_number')
				->whereRaw( "sha1(id) = '".$id_rb."'")
				->get();
			
			//print_r($data_blow);exit;
			if($data_blow){
				$report_number = $data_blow[0]->report_number;
				
				$deleteWaste = ProductionEntryReportBlowWaste::whereRaw( "id_report_blows = '".$data_blow[0]->id_rb."'" )->delete();
				$deleteHygiene = ProductionEntryReportBlowHygiene::whereRaw( "id_report_blows = '".$data_blow[0]->id_rb."'" )->delete();
				$deletePreparation = ProductionEntryReportBlowPreparation::whereRaw( "id_report_blows = '".$data_blow[0]->id_rb."'" )->delete();
				$deleteProductionResult = ProductionEntryReportBlowProductionResult::whereRaw( "id_report_blows = '".$data_blow[0]->id_rb."'" )->delete();
				$deleteBlow = ProductionEntryReportBlow::whereRaw( "id = '".$data_blow[0]->id_rb."'" )->delete();
				
				if($deleteBlow){
					//Audit Log
					$username= auth()->user()->email; 
					$ipAddress=$_SERVER['REMOTE_ADDR'];
					$location='0';
					$access_from=Browser::browserName();
					$activity='Deleted Blow Report Number ="'.$report_number.'" (Good : "-", Hold : "-", Reject : "-")';
					$this->auditLogs($username,$ipAddress,$location,$access_from,$activity);
				
					return Redirect::to('/production-ent-report-blow')->with('pesan', 'Delete Successfuly.');
				}else{
					return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error.');
				}	
			}else{
				return Redirect::to('/production-ent-report-blow')->with('pesan_danger', 'There Is An Error.');
			}
		}
    }
	//END ENTRY REPORT BLOW
}
