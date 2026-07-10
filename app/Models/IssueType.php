<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueType extends Model
{
    protected $fillable = ['name', 'description'];
    public $timestamps = false;
}
