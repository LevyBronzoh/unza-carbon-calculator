<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tip extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'category',
        'is_featured',
        'sort_order'
    ];

    public function scopeByCategory($query, $category)
    {
        return $query->when($category, function($q) use ($category) {
            return $q->where('category', $category);
        });
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
