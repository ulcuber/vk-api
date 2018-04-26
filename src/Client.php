<?php

namespace Vk;

use Exception;

class Client
{
    const VERSION = '5.67';
    const BASE_URL = 'https://api.vk.com/method/';

    private $token;
    private $method = 'post';

    public function token(string $token)
    {
        $this->token = $token;
        return $this;
    }

    public function method(string $method)
    {
        $this->method = mb_strtolower($method);
        return $this;
    }

    public function __call($method, $params)
    {
        $method = str_replace('_', '.', $method);
        $params = array_values($params);
        if (isset($params[0]) && is_array($params[0])) {
            $params = $params[0];
        }
        $token = $this->token ?: getenv('VK_API_ACCESS_TOKEN');
        $common = [];
        if ($token) {
            $common['access_token'] = $token;
        }
        $common['v'] = static::VERSION;

        if ($method === 'get') {
            $options = [
                CURLOPT_RETURNTRANSFER => true,
            ];
            $params = array_merge($common);
        } else {
            $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $params,
            ];
            $params = $common;
        }

        $query = http_build_query($params);
        $url = static::BASE_URL . $method . '?' . $query;

        $curl = curl_init($url);
        curl_setopt_array($curl, $options);
        $json = curl_exec($curl);
        $error = curl_error($curl);
        if ($error) {
            throw new Exception("Failed {$method} request");
        }

        curl_close($curl);

        $response = json_decode($json, true);
        if (!$response || !isset($response['response'])) {
            throw new Exception("Invalid response for {$method} request: {$json}");
        }

        return $response['response'];
    }
}
