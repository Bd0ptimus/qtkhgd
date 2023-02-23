<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EbookCategoryItem extends Model
{
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'ebook_category_id',
        'ebook_id',
    ];
}
