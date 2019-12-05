<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateContent extends Model 
{
    public $timestamps = false;
    protected $fillable = [
        'name', 'content', 'status', 'created_at', 'updated_at'
    ];
}