<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table = 'email_templates';

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $guarded = ['id'];

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'name',
        'lang',
        'type',
        'category',
        'subject',
        'body',
        'variables',
        'status',
        'language',
        'real_template',
        'show_enabled',
    ];

    public function parse($section = 'body', $data)
    {
        //validate
        if (!is_array($data) || !in_array($section, ['body', 'subject'])) return $this->body;

        //set the content
        $content = $section == 'body'?$this->body : $this->subject;

        //parse the content and inject actual data
        $parsed = preg_replace_callback('/{(.*?)}/', function ($matches) use ($data) {
            list($shortcode, $index) = $matches;
            //if shortcode is found, replace or return as is
            if (isset($data[$index])) {
                return $data[$index];
            } else {
                return $shortcode;
            }
        }, $content);

        return $parsed;
    }
}
