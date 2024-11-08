<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionEntryReportBagMakingProductionResultDetail extends Model
{
    use HasFactory;
	protected $table = 'report_bag_production_result_details';
    protected $guarded=[
        'id'
    ];
}
