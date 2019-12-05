<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailUser extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'name', 'alias_name', 'email', 'status', 'created_at', 'updated_at'
    ];

    public function groups() {
        return $this->hasManyThrough(EmailGroup::class, Group::class, 'id', 'group_id');
    }
}