<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class JobMail extends Model
{
    public $timestamps = false;
    protected $table = 'job_mail';
    protected $fillable = [
        'error_code', 'content', 'created_at', 'updated_at'
    ];
}