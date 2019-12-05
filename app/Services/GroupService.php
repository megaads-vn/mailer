<?php 
namespace App\Services;

use App\Models\Group;
use DateTime;

class GroupService 
{
    public function find($filter) {
        $query = Group::query();
        if (array_key_exists('group_name', $filter)) {
            $query->where('name', $filter['group_name']);
        }
        return $query->get();
    }

    public function create($params) {
        $params = $this->buildParams($params);
        $group = new Group;
        $group->fill($params);
        return $group->save();
    }

    public function delete($id) {
        Group::where('id', $id)->delete();
    }

    public function checkExists($groupName) {
        $retval = true;
        $find = Group::where('name', $groupName)->count();
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