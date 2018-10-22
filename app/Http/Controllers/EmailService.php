<?php
namespace App\Http\Controllers;

use App\Models\JobMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailService extends Controller
{

    protected $previousJob = '5 minutes';

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmail(Request $request)
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
}