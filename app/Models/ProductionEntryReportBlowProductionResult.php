<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionEntryReportBlowProductionResult extends Model
{
    use HasFactory;
	protected $table = 'report_blow_production_results';
    protected $guarded=[
        'id'
    ];
}
