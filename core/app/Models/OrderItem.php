<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use Searchable;
    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopePaid($query)
    {
        return $query->whereHas('order', function ($query) {
            $query->where('payment_status', 1);
        });
    }

    public function scopeRefunded($query)
    {
        return $query->where('is_refunded', Status::YES);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function licenseBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->license == Status::PERSONAL_LICENSE) {
                $html = '<span class="badge badge--primary">' . trans('Personal') . '</span>';
            } else {
                $html = '<span class="badge badge--success">' . trans('Commercial') . '</span>';
            }
            return $html;
        });
    }

    public function refundedBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->is_refunded == Status::YES) {
                $html = '<span class="badge badge--danger">' . trans('Yes') . '</span>';
            } else {
                $html = '<span class="badge badge--success">' . trans('No') . '</span>';
            }
            return $html;
        });
    }

}
