<?php

namespace local_onlineeduru\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

class log_page implements renderable, templatable
{
    private $log = null;
    private $request = null;
    private $response = null;

    public function __construct($log)
    {
        $this->log = $log;

        $this->request = $this->pretty_json($this->log->request);
        $this->response = $this->pretty_json($this->log->response);
    }

    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->log = $this->log;
        $data->request = $this->request;
        $data->response = $this->response;
        return $data;
    }

    protected function pretty_json($string): string {

        if (empty($string)) {
            return 'Пусто';
        }

        return json_encode(json_decode($string), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?? 'Плохой JSON';
    }
}