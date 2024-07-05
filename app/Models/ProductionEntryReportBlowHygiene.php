<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionEntryReportBlowHygiene extends Model
{
    use HasFactory;
	protected $table = 'report_blow_hygiene_checks';
    protected $guarded=[
        'id'
    ];
}
