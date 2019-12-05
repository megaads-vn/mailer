<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        return view('admin.dashboard.index');
    }

    /*
    |              GROUP EMAIL
    |-------------------------------------------------------------------
    */

    public function listGroups(Request $request)
    {
        $groups = $this->groupService->find([]);
        return view('admin.groups.index')->with(compact('groups'));
    }

    public function createGroup(Request $request)
    {
        $response = [
            'status' => 'fail'
        ];

        if ($request->has('name')) {
            $checkExists = $this->groupService->checkExists($request->get('name'));
            if ($checkExists) {
                $result = $this->groupService->create($request->all());
                if ($result) {
                    $findAllGroups = $this->groupService->find([]);
                    $generateTable = view('admin.groups.list-groups', ['groups' => $findAllGroups])->render();
                    $response['status'] = 'successful';
                    $response['data'] = $generateTable;
                }
            } else {
                $response['message'] = 'Group exists';
            }
        } else {
            $response['message'] = 'Invalid params.';
        }

        return response()->json($response);
    }

    public function deleteGroup(Request $request)
    {
        $response = [
            'status' => 'fail'
        ];
        if ($request->has('id')) {
            $this->groupService->delete($request->get('id'));
            $findAllGroups = $this->groupService->find([]);
            $generateTable = view('admin.groups.list-groups', ['groups' => $findAllGroups])->render();
            $response['status'] = 'successful';
            $response['data'] = $generateTable;
        } else {
            $response['message'] = 'Invalid params';
        }
        return response()->json($response);
    }

    /*
    |              EMAIL USER
    |-------------------------------------------------------------------
    */

    public function listEmailUser(Request $request)
    {
        $emails = $this->emailUserService->find([]);
        $groups = $this->groupService->find([]);
        return view('admin.email.index')->with(compact('emails', 'groups'));
    }

    public function createEmail(Request $request)
    {
        $response = [
            'status' => 'fail'
        ];

        if ($request->has('email')) {
            $checkExists = $this->emailUserService->checkExists($request->get('email'));
            if ($checkExists) {
                $params = $request->all();
                unset($params['group_id']);
                $result = $this->emailUserService->createGetId($params);
                if ($result) {
                    if ($request->has('group_id')) {
                        $assignParams = [
                            'group_id' => $request->get('group_id'),
                            'email_id' => $result
                        ];
                        $assignToGroup = $this->emailGroupService->create($assignParams);
                    }
                    $findAllEmails = $this->emailUserService->find([]);
                    $generateTable = view('admin.email.list-emails', ['emails' => $findAllEmails])->render();
                    $response['status'] = 'successful';
                    $response['data'] = $generateTable;
                }
            } else {
                $response['message'] = 'Email exists';
            }
        } else {
            $response['message'] = 'Invalid email.';
        }

        return response()->json($response);
    }

    public function deleteEmailUser(Request $request) {
        $response = [
            'status' => 'fail'
        ];
        if ($request->has('id')) {
            $this->emailUserService->delete($request->get('id'));
            $findAllEmails = $this->emailUserService->find([]);
            $generateTable = view('admin.email.list-emails', ['emails' => $findAllEmails])->render();
            $response['status'] = 'successful';
            $response['data'] = $generateTable;
        } else {
            $response['message'] = 'Invalid param';
        }
        return response()->json($response);
    }


    /*
    |              TEMPLATE EMAIL CONTENT
    |-------------------------------------------------------------------
    */

    public function listTemplateContent(Request $request)
    {
        $templates = $this->templateService->find([]);
        return view('admin.content.index')->with(compact('templates'));
    }

    public function createTemplateContent(Request $request)
    {
        $response = [
            'status' => 'fail',
        ];
        if ($request->has('content')) {
            $result = $this->templateService->create($request->all());
            if ($result) {
                $getAllTemplates = $this->templateService->find([]);
                $generateTable = view('admin.content.list-template', ['templates' => $getAllTemplates])->render();
                $response['status'] = 'successful';
                $response['data'] = $generateTable;
            }
        } else {
            $response['message'] = 'Invalid param content';
        }
        return response()->json($request->all());
    }

    public function deleteTemplate(Request $request)
    {
        $response = [
            'status' => 'fail'
        ];

        if ($request->has('id')) {
            $this->templateService->delete($request->get('id'));
            $getAllTemplates = $this->templateService->find([]);
            $generateTable = view('admin.content.list-template', ['templates' => $getAllTemplates])->render();
            $response['status'] = 'successful';
            $response['data'] = $generateTable;
        } else {
            $response['message'] = 'Invalid params';
        }
        return response()->json($response);
    }
}
