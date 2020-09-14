<?php

namespace App\Models;

class MailTemplate extends Model
{
    const STATUS_ACTIVE = 1; //Đang sử dụng
    const STATUS_LOCK = 2; //Đang khóa

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mail_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'code', 'description', 'keys', 'status', 'created_by', 'updated_by'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|unique:mail_templates,code',
        'description' => 'string',
        'keys' => 'string',
        'status' => 'required|integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    /**
     * @return HasMany
     */
    public function mail_template_locales()
    {
        return $this->hasMany('App\Models\MailTemplateLocale');
    }
}
