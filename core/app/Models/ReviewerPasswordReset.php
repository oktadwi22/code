<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewerPasswordReset extends Model
{
    protected $table = "reviewer_password_resets";
    protected $guarded = ['id'];
    public $timestamps = false;
}
