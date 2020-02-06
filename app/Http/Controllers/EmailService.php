<?php
namespace App\Http\Controllers;

use App\Models\JobMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailService extends Controller
{

    protected $previousJob = '5 minutes';

    /**
     * Send error notification to a specified group
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function exceptionEmail(Request $request)
    {
        $response['status'] = 'failed';
        $input = (object) $request->all();
        $toGroups = $this->getMailGroups($input->data);
        $previousJob = $this->checkPreviousJob($input->data, $input->is_html);
        if ( $toGroups != NULL && $previousJob != NULL) {
            try {
                $data = [
                    'subject' => "Error from " . $input->url,
                    'name' => 'Trap Mail',
                    'html' => $previousJob->content
                ];

                foreach( $toGroups as $group ) {
                    $data['to'] = $group;
                    Mail::send([], [], function ($m) use ($data) {
                                $m->from(env('MAIL_USERNAME'), $data['name']);
                                $m->to($data['to']);
                                $m->subject($data['subject']);
                                $m->setBody($data['html'], 'text/html');
                            });
                }
                $this->updateMailStatus($previousJob->id);
                $response['status'] = 'successful';
                $response['message'] = 'send mail successful';
            } catch (\Exception $ex) {
                $response['message'] = $ex->getMessage();
                return response()->json($response);
            }
        }
        return $response;
    }

    /**
     * Email to a group or a list of recipients
     *
     * @param Request $request
     * @return void
     */
    public function notifyEmail(Request $request) 
    {
        $response['status'] = 'failed';
        $input = $request->all();
        $recipients = [];
        $groupRecipients = [];
        if ( array_key_exists('content', $input) ) {
            if (array_key_exists('group', $input) || array_key_exists('to', $input) ) {
                if (array_key_exists('group', $input)) {
                    $groupRecipients = explode(',', $input['group']);
                    foreach ($groupRecipients as $groupRecipient) {
                        $result = $this->getMailGroup($groupRecipient);
                        $recipients = array_merge($recipients, $result);
                    }
                } 
                if (array_key_exists('to', $input))  {
                    $extractToAttribute = explode(',', $input['to']);
                    $recipients = array_merge($recipients, $extractToAttribute);
                }
                if (!empty($recipients)) {
                    $this->processNotifyEmail($recipients, $input);
                }
                $response['status'] = 'successful';
            } else {
                $response['message'] = 'Please , provide at least one recipients';
            }  
        } else {
            $response['message'] = 'Group and mail content is required. Please check again.';
        }
        return response()->json($response);
    }

    protected function processNotifyEmail($recipients, $input) {
        $data = [
            'subject' => (array_key_exists('subject', $input)) ? $input['subject'] : 'You have new job',
            'name' => (array_key_exists('name', $input)) ? $input['name'] : 'Job Mail',
            'html' => $input['content'],
            'to' => $recipients
        ];
        try {
            Mail::send([], [], function ($m) use ($data) {
                $m->from(env('MAIL_USERNAME'), $data['name']);
                $m->to($data['to']);
                $m->subject($data['subject']);
                $m->setBody($data['html'], 'text/html');
            });
            $response['status'] = 'successful';
            $response['message'] = 'Email sent';
        } catch (\Exception $ex) {
            $response['message'] = $ex->getMessage();
            return response()->json($response);
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @return void
     */
    public function notifyJobs(Request $request) {
        $response['status'] = 'failed';
        $input = $request->all();
        if ( array_key_exists('content', $input) ) {
            if (array_key_exists('group', $input)) {
                $extractGroup = explode(',', $input['group']);
                foreach ($extractGroup as $item) {
                    $this->sendGroupEmail($item, $input);
                }
            }
        } else {
            $response['message'] = 'Group and mail content is required. Please check again.';
        }
        return response()->json($response);
    }

    protected function sendGroupEmail($groupName, $input) {
        $groupMail = $this->getMailGroup($groupName);
        if (empty($groupMail)) {
            $groupMail = explode(',', $input['to']);
        }
        if ( !empty($groupMail) ) {
            $buildContent = $this->buildNotifyJobContent($input['content'], 'group');
            $data = [
                'subject' => $input['subject'],
                'name' => (array_key_exists('name', $input)) ? $input['name'] : 'Trap Mail',
                'html' => $buildContent,
                'to' => $groupMail
            ];
            try {
                Mail::send([], [], function ($m) use ($data) {
                    $m->from(env('MAIL_USERNAME'), $data['name']);
                    $m->to($data['to']);
                    $m->subject($data['subject']);
                    $m->setBody($data['html'], 'text/html');
                });
                $response['status'] = 'successful';
                $response['message'] = 'Email sent';
            } catch (\Exception $ex) {
                $response['message'] = $ex->getMessage();
                return response()->json($response);
            }
        }
    }

    /**
     * @param $groupName
     * @return mixed
     */
    private function getMailGroups($statusCode)
    {
        $retval = [];
        $codeToGroups = config('status_code');
        $groups = config('groups');
        $codeGroups = [];
        $statusKey = array_keys($statusCode);
        if ( !in_array('404', $statusKey) ) {
            foreach( $statusCode as $key => $status ) {
                if ( $key != NULL ) {
                    $code = $codeToGroups[$key];
                    if ( $code != NULL ) {
                        $codeGroups[$key] = $code;
                    }
                }
            }
            if ( $codeGroups != NULL ) {
                foreach( $groups as $key => $group ) {
                    foreach ( $codeGroups as $codeKey => $codeVal ) {
                        if ( in_array($key, $codeVal)) {
                            foreach ($group as $emailItem) {
                                $retval[$codeKey][] = $emailItem;
                            }
                        }
                    } 
                } 
            }
        }

        return $retval;
    }


    /**
     * @param $data
     * @return string
     */
    private function buildEmailBodyContent($code, $data)
    {  
        $retval = "";
        $retval .= "<p>";
        $retval .= "<h4>Error code: " . $code . "</h4>";
        foreach( $data as $content) {
            if ( isset ($content["message"]) && $content["message"] != "") {
                $retval .= "<strong>" . $content["message"] . "</strong>";
                $retval .= " <p>In file " . $content["file"] . " at line " . $content["line"] . "</p>";
            }
        }
        $retval .="</p>";
        return $retval;
    }

    /**
     * @param $jobData
     * @return JobMail|\Illuminate\Database\Eloquent\Model|null
     */
    private function saveJobs($jobData, $isHtml)
    {
        foreach($jobData as $code => $value) {
            $job["error_code"] = $code;
            $job["content"] = $this->buildEmailBodyContent($code, $value["items"]);
            $job["created_at"] = date('Y-m-d H:i:s');
            $job["updated_at"] = date('Y-m-d H:i:s');
            $newMail = new JobMail;
            $newMail->fill($job);
            $newMail->save();
        }

        $getNewJob = JobMail::query()->where('error_code', '=', '500')
            ->orderBy('created_at', 'DESC')
            ->first();

        return $getNewJob;
    }

    /**
     * @param $jobData
     * @return JobMail|\Illuminate\Database\Eloquent\Model|null
     */
    private function checkPreviousJob($jobData, $isHtml)
    {
        $retval = NULL;
        $findJob = JobMail::query()->where('error_code', '=', '500')
                            ->orderBy('created_at', 'DESC')
                            ->first();
        if ( !empty($findJob) ) {
            $nextTime = date("Y-m-d H:i:s", strtotime('+'. $this->previousJob, strtotime($findJob->updated_at)));
            $now = date('Y-m-d H:i:s');
            if (strtotime($now) >= strtotime($nextTime)) {
                $retval = $this->saveJobs($jobData, $isHtml);
            }
        } else {
            $retval = $this->saveJobs($jobData, $isHtml);
        }
        return $retval;
    }

    /**
     * @param $mailId
     */
    private function updateMailStatus($mailId)
    {
        $findMail = JobMail::query()->where('id', '=', $mailId)
                            ->first();

        if ( !empty($findMail) ) {
            try {
                JobMail::query()->where('id', '=', $mailId)
                    ->update(['status' => 'sent', 'updated_at' => date('Y-m-d H:i:s')]);
            } catch (\Exception  $ex) {

            }
        }
    }

    protected function getMailGroup($groupName) {
        $retval = [];
        $findGroup = $this->groupService->find(['group_name' => $groupName]);
        if (count($findGroup) > 0) {
            $groupId = $findGroup[0]->id;
            $getAllEmailGroup = $this->emailUserService->find(['group_id' => $groupId]);
            if (count($getAllEmailGroup) > 0) {
                foreach ($getAllEmailGroup as $item) {
                    $retval[] = $item->email;
                }
            }
        }
        return $retval;
    }

    protected function buildNotifyJobContent($requestContent, $type = 'single') {
        $retval = $requestContent;
        $getTemplate = $this->templateService->find([]);
        if (count($getTemplate) > 0) {
            $tmpContent = $getTemplate[0]->content;
            if ($type == 'group') {
                $tmpContent = str_replace('#name', '', $tmpContent);
            }
            $retval = str_replace('#content', $retval, $tmpContent);
        }
        return $retval;
    }
}