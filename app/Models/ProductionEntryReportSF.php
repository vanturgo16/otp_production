<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionEntryReportSF extends Model
{
    use HasFactory;
	protected $table = 'report_sfs';
    protected $guarded=[
        'id'
    ];
}