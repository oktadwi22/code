<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Searchable, GlobalStatus;

    public function category()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeParent($query)
    {
        $query->where('parent_id', 0);
    }

    public function scopeTopMenu($query)
    {
        $query->active()->where('parent_id', 0)->where('top_menu', Status::YES);
    }

    public function scopeFeatured($query)
    {
        $query->active()->where('featured', Status::YES);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->statusBadgeData(),
        );
    }

    public function statusBadgeData()
    {
        $html = '';
        if ($this->status == Status::ENABLE) {
            $html = '<span class="badge badge--success">' . trans('Active') . '</span>';
        } else {
            $html = '<span><span class="badge badge--warning">' . trans('Inactive') . '</span></span>';
        }
        return $html;
    }


    public function featuredBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->featuredBadgeData(),
        );
    }

    public function featuredBadgeData()
    {
        $html = '';
        if ($this->featured == Status::YES) {
            $html = '<span class="badge badge--success">' . trans('Yes') . '</span>';
        } else {
            $html = '<span><span class="badge badge--warning">' . trans('No') . '</span></span>';
        }
        return $html;
    }
}
