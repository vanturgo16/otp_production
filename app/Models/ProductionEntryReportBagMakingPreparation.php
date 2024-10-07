<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionEntryReportBagMakingPreparation extends Model
{
    use HasFactory;
	protected $table = 'report_bag_preparation_checks';
    protected $guarded=[
        'id'
    ];
}
