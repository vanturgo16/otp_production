<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionEntryReportBlowWaste extends Model
{
    use HasFactory;
	protected $table = 'report_blow_wastes';
    protected $guarded=[
        'id'
    ];
}
