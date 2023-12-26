<?php

namespace local_onlineeduru\services;

use core\uuid;

class api
{
    const METHOD_TEST = '/connections/check';
    const METHOD_CREATE_COURSE = '/registry/courses';
    const METHOD_UPDATE_COURSE = '/registry/courses';
    const METHOD_GET_USER_ID = '/users/id';
    const METHOD_USER_PARTICIPATION = '/courses/participation';
    const METHOD_USER_RESULTS = '/courses/results';
    const METHOD_USER_RESULTS_PROGRESS = '/courses/results/progress';

    const HEADER_KEY = 'X-CN-UUID';

    private $curl;
    private $endpoint;
    private $key;

    private $partner_id;
    private $institution;

    public function __construct()
    {
        $this->endpoint = get_config('local_onlineeduru', 'api_endpoint');
        $this->key = get_config('local_onlineeduru', 'api_key');
        $this->partner_id = get_config('local_onlineeduru', 'partner_id');
        $this->institution = get_config('local_onlineeduru', 'institution');

        $this->curl = new \curl(['debug' => false]);
    }

    public function getStatus(): string
    {
        $info = $this->curl->get_info();

        $code = $info['http_code'] ?? '';

        return mb_strlen($code) > 3 ? mb_substr($code, 0, 3) : $code;
    }

    public function test()
    {
        $url = $this->getUrlMethod(self::METHOD_TEST);

        return $this->request($url, 'get');
    }

    public function createCourse(string $key, string $data)
    {
        $url = $this->getUrlMethod(self::METHOD_CREATE_COURSE);

        $request_body = sprintf('{"partner_id":"%s", "package": { "items": [%s] } }', $this->partner_id, $data);

        return $this->request($url, 'post', $request_body, [], [
            'Content-type: application/json',
            'Accept: application/json'
        ], $key);
    }

    public function updateCourse(string $key, string $data)
    {
        $url = $this->getUrlMethod(self::METHOD_UPDATE_COURSE);

        $request_body = sprintf('{"partner_id":"%s", "package": { "items": [%s] } }', $this->partner_id, $data);

        return $this->request($url, 'put', $request_body, [], [
            'Content-type: application/json',
            'Accept: application/json'
        ], $key);
    }

    public function getUserID(string $email)
    {
        $url = $this->getUrlMethod(self::METHOD_GET_USER_ID);

        $response = $this->request($url, 'get', ['email' => $email]);

        try {
            $data = json_decode($response, true);
        } catch (\Throwable $e) {
            debugging("Ошибка при разборе response: {$e->getMessage()}");
            $data = [];
        }

        return $data['user_id'] ?? null;
    }

    public function createParticipation(string $key, string $data)
    {
        $url = $this->getUrlMethod(self::METHOD_USER_PARTICIPATION);

        return $this->request($url, 'post', $data, [], [
            'Content-type: application/json',
            'Accept: application/json'
        ], $key);
    }

    public function deleteParticipation(string $key, string $data)
    {
        $url = $this->getUrlMethod(self::METHOD_USER_PARTICIPATION);

        return $this->request($url, 'delete', [], ['CURLOPT_POSTFIELDS' => $data], [
            'Content-type: application/json',
            'Accept: application/json'
        ], $key);
    }

    public function sendCheckpoint(string $key, string $data)
    {
        $url = $this->getUrlMethod(self::METHOD_USER_RESULTS);

        return $this->request($url, 'post', $data, [], [
            'Content-type: application/json',
            'Accept: application/json'
        ], $key);
    }

    public function sendProgress(string $key, string $data)
    {
        $url = $this->getUrlMethod(self::METHOD_USER_RESULTS_PROGRESS);

        return $this->request($url, 'post', $data, [], [
            'Content-type: application/json',
            'Accept: application/json'
        ], $key);
    }

    private function getUrlMethod(string $method): string
    {
        $url = $this->endpoint;

        if (mb_substr($url, -1) === '/') {
            $url = mb_substr($url, 0, -1);
        }

        return $url . $method;
    }

    private function request($url, $method, $params = null, array $options = [], array $headers = [], string $uuid = null)
    {
        global $DB, $USER;

        if (null === $uuid) {
            $uuid = uuid::generate();
        }

        $this->curl->resetHeader();

        $this->curl->setHeader([
            'Accept: */*',
            sprintf('%s: %s', self::HEADER_KEY, $this->key)
        ]);

        if (!empty($headers)) {
            $this->curl->setHeader($headers);
        }

        switch ($method) {
            case 'get':
            case 'put':
            case 'delete':
                $params = $params ?? [];
                break;
            case 'post':
            case 'patch':
                $params = $params ?? '';
                break;
        }

        $curl = new \stdClass();
        $curl->uuid = $uuid;
        $curl->url = $url;
        $curl->method = $method;
        $curl->request = is_array($params) ? json_encode($params) : $params;
        $curl->usermodified = $USER->id ?? 0;
        $curl->timecreated = $curl->timemodified = time();

        $curl->id = $DB->insert_record('local_onlineeduru_curl', $curl);

        $response = $this->handleRequest($method, $url, $params, $options);

        $curl->response = $response;
        $curl->status = $this->getStatus();
        $curl->timemodified = time();

        $DB->update_record('local_onlineeduru_curl', $curl);

        return $response;
    }

    private function handleRequest($method, $url, $params, $options)
    {
        switch ($method) {
            case 'get':
                $response = $this->curl->get($url, $params, $options);
                break;
            case 'post':
                $response = $this->curl->post($url, $params, $options);
                break;
            case 'put':
                $response = $this->curl->put($url, $params, $options);
                break;
            case 'patch':
                $response = $this->curl->patch($url, $params, $options);
                break;
            case 'delete':
                $response = $this->curl->delete($url, $params, $options);
                break;
            default:
                throw new \LogicException('неизвестный метод ' . $method);
        }

        return $response;
    }
}