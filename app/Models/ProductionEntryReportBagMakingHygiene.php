<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionEntryReportBagMakingHygiene extends Model
{
    use HasFactory;
	protected $table = 'report_bag_hygiene_checks';
    protected $guarded=[
        'id'
    ];
}
