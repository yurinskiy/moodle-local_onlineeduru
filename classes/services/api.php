<?php

namespace local_onlineeduru\services;

class api
{
    const METHOD_TEST = '/connections/check';

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

        $this->curl = new \curl();
    }

    public function test()
    {
        $url = $this->getUrlMethod(self::METHOD_TEST);

        $this->curl->setHeader($this->getDefaultHeader());

        return $this->curl->get($url);
    }

    public function getInfo()
    {
        return $this->curl->get_info();
    }

    public function getResponse()
    {
        return $this->curl->get_raw_response();
    }

    private function getUrlMethod(string $method): string
    {
        $url = $this->endpoint;

        if (mb_substr($url, -1) === '/') {
            $url = mb_substr($url, 0, -1);
        }

        return $url . $method;
    }

    private function getDefaultHeader(): array
    {
        return [
            'Accept: */*',
            sprintf('%s: %s', self::HEADER_KEY, $this->key)
        ];
    }
}