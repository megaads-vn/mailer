<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailGroup extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'group_id', 'email_id','created_at', 'updated_at'
    ];
}