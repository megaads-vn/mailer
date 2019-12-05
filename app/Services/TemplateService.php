<?php 
namespace App\Services;

use App\Models\TemplateContent;
use DateTime;

class TemplateService
{
    public function find($filter) {
        $query = TemplateContent::query();
        $query->orderBy('created_at', 'DESC');
        return $query->get();
    }

    public function create($params) {
        $params = $this->buildParams($params);
        $template = new TemplateContent;
        $template->fill($params);
        return $template->save();
    }

    public function delete($id) {
        TemplateContent::where('id', $id)->delete();
    }

    protected function buildParams($params) {
        if (!array_key_exists('created_at', $params)) {
            $params['created_at'] = new DateTime();
        }
        if (!array_key_exists('status', $params)) {
            $params['status'] = 'active';
        }
        return $params;
    }
}