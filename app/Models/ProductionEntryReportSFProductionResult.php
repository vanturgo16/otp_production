<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionEntryReportSFProductionResult extends Model
{
    use HasFactory;
	protected $table = 'report_sf_production_results';
    protected $guarded=[
        'id'
    ];
}
