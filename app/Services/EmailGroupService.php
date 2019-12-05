<?php 
namespace App\Services;

use App\Models\EmailGroup;
use DateTime;

class EmailGroupService
{
    public function create($params) {
        $checkExists = $this->checkExists($params['group_id'], $params['email_id']);
        if ($checkExists) {
            $emailGroup = new EmailGroup;
            $emailGroup->fill($params);
            return $emailGroup->save();
        }
        return $checkExists;
    }

    protected function checkExists($groupId, $emailId) {
        $retval = true;
        $find = EmailGroup::where('group_id', $groupId)
                            ->where('email_id', $emailId)
                            ->count();
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