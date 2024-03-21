<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use GlobalStatus, Searchable;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function reviewers()
    {
        return $this->belongsToMany(Reviewer::class);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->status == Status::ENABLE) {
                $html = '<span class="badge badge--success">' . trans('Active') . '</span>';
            } else {
                $html = '<span class="badge badge--danger">' . trans('Inactive') . '</span>';
            }
            return $html;
        });
    }

}
