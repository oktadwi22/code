<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class ProductData extends Model
{
    use GlobalStatus;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
