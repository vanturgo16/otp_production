<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionEntryReportSFPreparation extends Model
{
    use HasFactory;
	protected $table = 'report_sf_preparation_checks';
    protected $guarded=[
        'id'
    ];
}