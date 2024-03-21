<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Product extends Model
{
    use Searchable;

    protected $casts = ['attribute_info' => 'object', 'tags' => 'object'];

    public function getMyProductAttribute()
    {
        return auth()->id() == $this->getAttribute('user_id');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', Status::ENABLE);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', Status::PRODUCT_PENDING);
    }

    public function scopeCountComment($query)
    {
        return $query->withCount([
            'comments' => function ($query) {
                $query->where('review_id', 0)->where('parent_id', 0);
            }
        ]);
    }

    public function scopeAllActive($query)
    {
        $query->whereHas('category', function ($q) {
            $q->active();
        })->whereHas('subcategory', function ($q) {
            $q->active();
        })->whereHas('author', function ($q) {
            $q->active();
        });
    }

    public function scopeApproved($query)
    {
        return $query->where('status', Status::PRODUCT_APPROVED);
    }

    public function scopeHardRejected($query)
    {
        return $query->where('status', Status::PRODUCT_HARD_REJECTED);
    }

    public function scopeSoftRejected($query)
    {
        return $query->where('status', Status::PRODUCT_SOFT_REJECTED);
    }

    public function scopeDown($query)
    {
        return $query->where('status', Status::PRODUCT_DOWN);
    }

    public function scopeFileUpdated($query)
    {
        return $query->where('product_updated', 1);
    }

    public function scopeUpdatePending($query)
    {
        return $query('product_updated', Status::PRODUCT_UPDATE_PENDING);
    }

    public function scopeUpdateApproved($query)
    {
        return $query('product_updated', Status::PRODUCT_UPDATE_APPROVED);
    }

    public function scopeUpdateSoftRejected($query)
    {
        return $query('product_updated', Status::PRODUCT_UPDATE_SOFT_REJECT);
    }

    public function scopeUpdateHardRejected($query)
    {
        return $query('product_updated', Status::PRODUCT_UPDATE_HARD_REJECT);
    }

    public function scopePermanentDown($query)
    {
        return $query->where('status', Status::PRODUCT_PERMANENT_DOWN);
    }
    public function scopeWaiting($query)
    {
        return $query->whereIn('status', [Status::PRODUCT_PENDING,Status::PRODUCT_UPDATE_PENDING]);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class)->latest();
    }

    public function productData()
    {
        return $this->hasMany(ProductData::class, 'product_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function collections()
    {
        return $this->belongsToMany(ProductCollection::class, 'collection_product');
    }

    public function rejections()
    {
        return $this->hasMany(Rejection::class);
    }

    public function screenshots()
    {
        $slug = $this->slug;
        $extractedPath = getFilePath('screenshots') . '/' . $slug . '/screenshots';
        
        if(!is_dir($extractedPath)) return [];

        $files = File::allFiles($extractedPath);

        return collect($files)->map(function ($file) use ($extractedPath) {
            return $extractedPath . '/' . $file->getRelativePathname();
        });
    }

    public function updateStatusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->product_updated == Status::PRODUCT_UPDATE_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } elseif ($this->product_updated == Status::PRODUCT_UPDATE_APPROVED) {
                $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
            } elseif ($this->product_updated == Status::PRODUCT_UPDATE_SOFT_REJECT) {
                $html = '<span class="badge badge--warning">' . trans('Soft Rejected') . '</span>';
            } elseif ($this->product_updated == Status::PRODUCT_UPDATE_HARD_REJECT) {
                $html = '<span class="badge badge--danger">' . trans('Hard Rejected') . '</span>';
            } elseif ($this->product_updated == Status::PRODUCT_NO_UPDATE) {
                $html = '<span class="badge bg-secondary">' . trans('No Update') . '</span>';
            }
            return $html;
        });
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function () {
            $html = '';
            if ($this->status == Status::PRODUCT_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } elseif ($this->status == Status::PRODUCT_APPROVED) {
                $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
            } elseif ($this->status == Status::PRODUCT_SOFT_REJECTED) {
                $html = '<span class="badge badge--warning">' . trans('Soft Rejected') . '</span>';
            } elseif ($this->status == Status::PRODUCT_HARD_REJECTED) {
                $html = '<span class="badge badge--danger">' . trans('Hard Rejected') . '</span>';
            } elseif ($this->status == Status::PRODUCT_DOWN) {
                $html = '<span class="badge badge--warning">' . trans('Soft Disabled') . '</span>';
            } elseif ($this->status == Status::PRODUCT_PERMANENT_DOWN) {
                $html = '<span class="badge badge--danger">' . trans('Permanent Disabled') . '</span>';
            }
            return $html;
        });
    }
}
