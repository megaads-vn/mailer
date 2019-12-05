<?php 
namespace App\Services;

use App\Models\EmailUser;
use DateTime;

class EmailUserService 
{
    public function find($filter) {
        $query = EmailUser::from('email_users as u');
        if (array_key_exists('group_id', $filter)) {
            $query->join('email_groups as eg', 'eg.email_id', '=', 'u.id');
            $query->join('groups as g', 'g.id', '=', 'eg.group_id');
            $query->where('g.id', $filter['group_id']);
        }
        return $query->get();
    }

    public function create($params) {
        $params = $this->buildParams($params);
        $emailUser = new EmailUser;
        $emailUser->fill($params);
        return $emailUser->save();
    }

    public function createGetId($params) {
        $params = $this->buildParams($params);
        return EmailUser::insertGetId($params);
    }

    public function delete($id) {
        EmailUser::where('id', $id)->delete();
    }

    public function checkExists($email) {
        $retval = true;
        $find  = EmailUser::where('email', $email)->count();
        if ($find > 0) {
            $retval = false;
        }
        return $retval;
    }

    protected function buildParams($params) {
        if (!array_key_exists('created_at', $params)) {
            $params['created_at'] = new DateTime();
        }
        return $params;
    }
}