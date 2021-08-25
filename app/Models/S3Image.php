<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class S3Image extends Model
{
    use HasFactory;

    protected $table = 's3images';
    protected $fillable = [
        'name'
    ];
}
