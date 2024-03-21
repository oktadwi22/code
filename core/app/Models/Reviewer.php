<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Reviewer extends Authenticatable
{
    use Searchable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = ['subcategories' => 'array'];

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function subCategories()
    {
        return $this->belongsToMany(SubCategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'assigned_to');
    }

    public function approvedProducts()
    {
        return $this->hasMany(Product::class, 'approved_by');
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->status == Status::ENABLE) {
                $html = '<span class="badge badge--success">' . trans('Active') . '</span>';
            } else {
                $html = '<span class="badge badge--danger">' . trans('Banned') . '</span>';
            }
            return $html;
        });
    }
}
