<?php

namespace local_onlineeduru\services;

class api
{
    const METHOD_TEST = '/connections/check';
    const METHOD_CREATE_COURSE = '/registry/courses';
    const METHOD_UPDATE_COURSE = '/registry/courses';
    const METHOD_GET_USER_ID = '/users/id';
    const METHOD_USER_PARTICIPATION = '/courses/participation';

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

        $this->curl = new \curl(['debug' => true]);
    }

    public function getStatus()
    {
        $info = $this->curl->get_info();

        $code = $info['http_code'] ?? '';

        return mb_strlen($code) > 3 ? mb_substr($code, 0, 3) : $code;
    }

    public function test()
    {
        $url = $this->getUrlMethod(self::METHOD_TEST);

        $this->curl->setHeader($this->getDefaultHeader());

        return $this->curl->get($url);
    }

    public function createCourse(string $data)
    {
        $url = $this->getUrlMethod(self::METHOD_CREATE_COURSE);

        $this->curl->setHeader($this->getDefaultHeader());
        $this->curl->setHeader(['Content-type: application/json']);
        $this->curl->setHeader(['Accept: application/json']);

        $request_body = sprintf('{"partner_id":"%s", "package": { "items": [%s] } }', $this->partner_id, $data);

        return $this->curl->post($url, $request_body);
    }
    public function updateCourse(string $data)
    {
        $url = $this->getUrlMethod(self::METHOD_UPDATE_COURSE);

        $this->curl->setHeader($this->getDefaultHeader());
        $this->curl->setHeader(['Content-type: application/json']);
        $this->curl->setHeader(['Accept: application/json']);

        $request_body = sprintf('{"partner_id":"%s", "package": { "items": [%s] } }', $this->partner_id, $data);

        return $this->curl->put($url, $request_body);
    }

    public function getUserID(string $email) {

        $url = $this->getUrlMethod(self::METHOD_GET_USER_ID);

        $this->curl->setHeader($this->getDefaultHeader());

        $response = $this->curl->get($url, ['email' => $email]);

        try {
            $data = json_decode($response, true);
        } catch (\Throwable $e) {
            debugging("Ошибка при разборе response: {$e->getMessage()}");
            $data = [];
        }

        return $data['user_id'] ?? null;
    }

    public function createUser(string $data)
    {
        $url = $this->getUrlMethod(self::METHOD_USER_PARTICIPATION);

        $this->curl->setHeader($this->getDefaultHeader());
        $this->curl->setHeader(['Content-type: application/json']);
        $this->curl->setHeader(['Accept: application/json']);

        return $this->curl->post($url, $data);
    }


    private function getDefaultHeader(): array
    {
        return [
            'Accept: */*',
            sprintf('%s: %s', self::HEADER_KEY, $this->key)
        ];
    }

    private function getUrlMethod(string $method): string
    {
        $url = $this->endpoint;

        if (mb_substr($url, -1) === '/') {
            $url = mb_substr($url, 0, -1);
        }

        return $url . $method;
    }
}