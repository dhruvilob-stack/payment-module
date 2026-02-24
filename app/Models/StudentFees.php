<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFees extends Model
{
    protected $fillable = [
        "student_id",
        "amount",
        "ref_id",
        "gateway",
        "logs",
        "status"
    ];
}
