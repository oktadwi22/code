<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundActivity extends Model
{
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function refundRequest()
    {
        return $this->belongsTo(RefundRequest::class);
    }
}
