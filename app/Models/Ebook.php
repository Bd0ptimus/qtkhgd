<?php

namespace App\Models;

use App\Admin\Admin;
use Illuminate\Database\Eloquent\Model;

class Ebook extends Model
{
    //
    
    protected $table = 'ebook';
    protected $dateFormat = 'Y-m-d H:i:s';
    const PAGE_SIZE = 25;    
    const PARAM_LEVEL = 'level';
    const PARAM_GRADE = 'grade';
    
    protected $fillable = [
        'name',
        'description',
        'publisher',
        'publishing_company',
        'authors',
        'd_o_p',
        'n_o_p',
        'total_page',
        'assemblage',
        'cover_type',
        'size',
        'grade',
        'subject_id',
        'creator_id'
    ];
    
    public static function boot ()
    {
        parent::boot();
        static::creating(function ($ebook) {
            $ebook->creator_id = Admin::user()->id;
        });
    }
    
    public function subject () {
        return $this->belongsTo(Subject::class,'subject_id',  'id');
    }

    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function ebookCategories()
    {
        return $this->belongsToMany(EbookCategory::class, 'ebook_category_items', 'ebook_id', 'ebook_category_id');
    }
}
