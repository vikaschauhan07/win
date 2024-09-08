<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetToekns extends Model
{
    use HasFactory;
    protected $table = "reset_codes";

}
