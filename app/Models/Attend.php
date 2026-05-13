<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attend extends Model
{
    use HasFactory;


    protected $fillable = [
        "user_id",
        "selfie_url",
        "clock_in",
        "clock_out",
        "latitude",
        "longitude",
        "status"
    ];

    protected $guarded = [];
}
