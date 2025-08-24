<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){
        $reportRaw = [
            'Active' => DB::table('master_raw_materials')->where('status', 'Active')->count(),
            'total' => DB::table('master_raw_materials')->count(),
        ];
        $reportAux = [
            'Request' => DB::table('request_tool_auxiliaries')->where('status', 'Request')->count(),
            'Approve' => DB::table('request_tool_auxiliaries')->where('status', 'Approve')->count(),
            'Hold' => DB::table('request_tool_auxiliaries')->where('status', 'Hold')->count(),
            'total' => DB::table('request_tool_auxiliaries')->count(),
        ];
        $reportBlow = [
            'unposted' => DB::table('report_blows')->where('status', 'un posted')->count(),
            'posted' => DB::table('report_blows')->where('status', 'Posted')->count(),
            'closed' => DB::table('report_blows')->where('status', 'Closed')->count(),
            'total' => DB::table('report_blows')->count(),
        ];
        $reportSlt = [
            'unposted' => DB::table('report_sfs')->where('status', 'un posted')->where('type', 'slitting')->count(),
            'posted' => DB::table('report_sfs')->where('status', 'Posted')->where('type', 'slitting')->count(),
            'closed' => DB::table('report_sfs')->where('status', 'Closed')->where('type', 'slitting')->count(),
            'total' => DB::table('report_sfs')->where('type', 'slitting')->count(),
        ];
        $reportFld = [
            'unposted' => DB::table('report_sfs')->where('status', 'un posted')->where('type', 'folding')->count(),
            'posted' => DB::table('report_sfs')->where('status', 'Posted')->where('type', 'folding')->count(),
            'closed' => DB::table('report_sfs')->where('status', 'Closed')->where('type', 'folding')->count(),
            'total' => DB::table('report_sfs')->where('type', 'folding')->count(),
        ];
        $reportBag = [
            'unposted' => DB::table('report_bags')->where('status', 'un posted')->count(),
            'posted' => DB::table('report_bags')->where('status', 'Posted')->count(),
            'closed' => DB::table('report_bags')->where('status', 'Closed')->count(),
            'total' => DB::table('report_bags')->count(),
        ];


        return view('dashboard.index', compact(
            'reportBlow',
            'reportSlt',
            'reportFld',
            'reportAux',
            'reportRaw',
            'reportBag'
        ));
    }
    public function dashboard()
    {
        // Report Blow
        
    }
}
