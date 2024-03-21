<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ReviewCategory extends Model
{
    use GlobalStatus, Searchable;

    public function scopeActive($query)
    {
        return $query->where("status", 1);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->status == Status::ENABLE) {
                $html = '<span class="badge badge--success">' . trans('Active') . '</span>';
            } else {
                $html = '<span><span class="badge badge--warning">' . trans('Inactive') . '</span></span>';
            }
            return $html;
        });
    }
}
