<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionEntryReportBagMakingWaste extends Model
{
    use HasFactory;
	protected $table = 'report_bag_wastes';
    protected $guarded=[
        'id'
    ];
}
