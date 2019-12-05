<?php

namespace App\Http\Controllers;

use App\Services\EmailGroupService;
use App\Services\EmailUserService;
use App\Services\GroupService;
use App\Services\TemplateService;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {
        $this->groupService = new GroupService;
        $this->emailUserService = new EmailUserService;
        $this->emailGroupService = new EmailGroupService;
        $this->templateService = new TemplateService;
    }
}
