<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionEntryReportBagMakingProductionResult extends Model
{
    use HasFactory;
	protected $table = 'report_bag_production_results';
    protected $guarded=[
        'id'
    ];
}
