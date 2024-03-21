<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use Searchable;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(ReviewCategory::class, 'review_category_id', 'id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'review_id');
    }
}
