<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class AuthorLevel extends Model
{
    use GlobalStatus, Searchable;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
