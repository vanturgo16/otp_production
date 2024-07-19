<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionEntryReportBlowPreparation extends Model
{
    use HasFactory;
	protected $table = 'report_blow_preparation_checks';
    protected $guarded=[
        'id'
    ];
}
