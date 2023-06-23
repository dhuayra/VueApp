<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //public $timestamp = false;
    use HasFactory;
    protected $fillable = ['name'];
}
