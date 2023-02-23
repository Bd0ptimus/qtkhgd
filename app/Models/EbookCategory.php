<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Admin\Admin;

class EbookCategory extends Model
{
    const PAGE_SIZE = 25;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $fillable = [
        'name',
        'slug',
    ];

    public function ebooks()
    {
        return $this->belongsToMany(Ebook::class, 'ebook_category_items', 'ebook_id', 'ebook_category_id');
    }
    public static function boot ()
    {
        parent::boot();
        static::creating(function ($ebookCategory) {
            $ebookCategory->creator_id = Admin::user()->id;
        });
    }
}
