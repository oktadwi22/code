<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class ProductCollection extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'collection_product');
    }

    public function scopePublic($query) {
        return $query->where('is_public', Status::YES);
    }
}
