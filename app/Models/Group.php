<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model 
{
    public $timestamps = false;
    protected $fillable = [
        'name', 'status', 'created_at', 'updated_at'
    ];

    public function emails() {
        // return $this->hasManyThrough(EmailUser::class, EmailGroup::class, 'email_id', 'id');
    }
}