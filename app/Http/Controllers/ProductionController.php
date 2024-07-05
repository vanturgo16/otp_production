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
				$return_approve = "return confirm('Are you sure to approve this item ?')";
				$return_hold = "return confirm('Are you sure to hold this item ?')";
				$return_delete = "return confirm('Are you sure to delete this item ?')";
				if($data->status=='Hold'){
					$tombol = '
						<center>
							<a onclick="'.$return_approve.'" href="/production-ent-material-use-approve/'.sha1($data->id).'" class="btn btn-primary waves-effect waves-light">
								<i class="bx bx-check" title="Approve"></i> APPROVE
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
			//->where('id_master_process_productions', $id_master_process_productions)
			->where('id_master_process_productions', '2')
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
			$validatedData['status'] = 'Hold';			
			
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
	public function production_entry_material_use_approve($response_id){		
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
	public function production_entry_material_use_hold($response_id){		
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
                ->select('report_blows.*', 'b.wo_number', 'c.regu', 'd.work_center', 'e.name')
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
				$checklist = '<a href="/production-ent-report-blow-detail/'.sha1($data->id).'" class="btn btn-danger waves-effect btn-label waves-light"><i class="bx bx-check-double label-icon"></i>  Preparation Check</a><br><a href="/production-ent-report-blow-detail/'.sha1($data->id).'" class="btn btn-danger waves-effect btn-label waves-light mt-1"><i class="bx bx-check-double label-icon"></i> Hygiene Check</a>';
				return $checklist;
			})
			->addColumn('update', function ($data) {				
				$update = '<a href="#" class="btn btn-info waves-effect btn-label waves-light"><i class="bx bx-file label-icon"></i>  Stock Updated</a><br><a href="#" class="btn btn-primary waves-effect btn-label waves-light mt-1"><i class="bx bx-reply label-icon"></i> Un Posted</a>';
				return $update;
			})
			->addColumn('action', function ($data) {
				$return_approve = "return confirm('Are you sure to approve this item ?')";
				$return_hold = "return confirm('Are you sure to hold this item ?')";
				$return_delete = "return confirm('Are you sure to delete this item ?')";
				/*
				if($data->status=='Hold'){
					$tombol = '
						<center>
							<a onclick="'.$return_approve.'" href="/production-ent-material-use-approve/'.sha1($data->id).'" class="btn btn-primary waves-effect waves-light">
								<i class="bx bx-check" title="Approve"></i> APPROVE
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
				}else{
					$tombol = '
						<center>
							<a onclick="'.$return_hold.'" href="/production-ent-material-use-hold/'.sha1($data->id).'" class="btn btn-outline-dark waves-effect waves-light">
								<i class="bx bx-file" title="Hold"></i> Material Used
							</a>	
							<a target="_blank" href="/production-ent-material-use-detail/'.sha1($data->id).'" class="btn btn-outline-info waves-effect waves-light">
								<i class="bx bx-edit-alt" title="Edit"></i> EDIT
							</a>
							<a onclick="'.$return_delete.'" href="/production-ent-material-use-delete/'.sha1($data->id).'" class="btn btn-outline-danger waves-effect waves-light" onclick="return confirm('."'Anda yakin mau menghapus item inix ?'".')">
								<i class="bx bx-trash-alt" title="Delete" ></i> DELETE
							</a>
					';
				}
				*/
				$tombol = '
					<center>
						<a target="_blank" href="/production-ent-report-blow-material-use/'.sha1($data->id_work_orders).'" class="btn btn-outline-dark waves-effect waves-light">
							<i class="bx bx-file" title="Material Use"></i> Material Used
						</a>	
						<a target="_blank" href="/production-ent-report-blow-detail/'.sha1($data->id).'" class="btn btn-outline-info waves-effect waves-light">
							<i class="bx bx-edit-alt" title="Edit"></i> EDIT
						</a>
						<a onclick="'.$return_delete.'" href="#" class="btn btn-outline-danger waves-effect waves-light" onclick="return confirm('."'Anda yakin mau menghapus item inix ?'".')">
							<i class="bx bx-trash-alt" title="Delete" ></i> DELETE
						</a>
				';
				$tombol .= '
						<a target="_blank" href="#" class="btn btn-outline-dark waves-effect waves-light">
							<i class="bx bx-printer" title="Print"></i> PRINT
						</a>
					</center>						
				';
				return $tombol;
			})
			->rawColumns(array("report_info", "order_info", "team", "checklist", "update", "action"))
		->make(true);
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
                        ->whereRaw( "left(wo_number,5) = 'WOBGM'")
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
        $key = request()->get('id_master_customers');
        
		$datas = DB::table('barcode_detail')
			->select('*')
			->get();
			
		$lists = "<option value='' disabled='' selected=''>** Please Select A Barcodes</option>";		
		foreach($datas as $data){
			$selected = $data->id==$key?'selected':'';
			$lists .= "<option value='".$data->id."' ".$selected.">".$data->barcode_number."</option>";
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
			
            //return Redirect::to('/production-ent-report-blow')->with('pesan', 'Add Successfuly.');
            return Redirect::to('/production-ent-report-blow-detail/'.sha1($response->id))->with('pesan', 'Add Successfuly.');
        }
    }
	public function production_entry_report_blow_detail($response_id){
		$data = ProductionEntryReportBlow::select("report_blows.*")
				->whereRaw( "sha1(report_blows.id) = '$response_id'")
                ->get();
		//print_r($data);exit;
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
			return Redirect::to('/production-ent-report-blow')->with('pesan', 'Terjadi Kesalahan');
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
				/*
				if($data->status=='Hold'){
					$tombol = '
						<center>
							<a onclick="'.$return_approve.'" href="/production-ent-material-use-approve/'.sha1($data->id).'" class="btn btn-primary waves-effect waves-light">
								<i class="bx bx-check" title="Approve"></i> APPROVE
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
				/
				$tombol = '
						<center>
							<a onclick="'.$return_hold.'" href="/production-ent-material-use-hold/'.sha1($data->id).'" class="btn btn-warning waves-effect waves-light">
								<i class="bx bx-block" title="Hold"></i> HOLD
							</a>						
					';
				*/
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
	//END ENTRY REPORT BLOW
}
