<?php

namespace App\Models;

class MailTemplateLocale extends Model
{


    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mail_template_locales';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mail_template_id', 'locale', 'subject', 'content', 'created_by', 'updated_by'];


    protected $casts = [
        'keys' => 'object',
    ];

    protected $rules = [
        'mail_template_id' => 'required|integer|exists:mail_templates,id',
        'locale' => 'string|max:50',
        'subject' => 'required|string',
        'content' => 'required|string',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Get the mail_templates record associated with the mail_template_locales.
     */
    public function mail_template()
    {
        return $this->belongsTo('App\Models\MailTemplate');
    }
}
