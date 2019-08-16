<?php

namespace UnofficialApi;

use Unirest\Request;
use Unirest\Request\Body;

class Badoo extends AbstractFormate
{
    /** User Agent, load session or generate Faker */
    public $UserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:68.0) Gecko/20100101 Firefox/68.0';

    /** Session ID used as Token API */
    public $session;

    /** Session file name */
    private $sessionFile = 'session';

    /** Device id used mobile API */
    public $device_id;

    /** Mobile api url */
    private $mobileAPI = 'https://badoo.com/mwebapi.phtml?';

    /** Desctop api url */
    private $desctopAPI = 'https://badoo.com/bmaapi.phtml?';

    /** Default header */
    public $defaultHeader = [
        'accept' => '*/*',
        'accept-language' => 'ru',
        'content-type' => 'json',
        'sec-fetch-mode' => 'cors',
        'sec-fetch-site' => 'same-origin',
        'referrer' => 'https://badoo.com/encounters?f=top',
        'referrerPolicy' => 'origin-when-cross-origin',
        'x-message-type' => '',
        'user-agent' => '',
        'x-user-agent' => '',
        'x-session-id' => '',
        'x-user-id' => '',
    ];

    /**
     * start API
     *
     * @param string $login
     * @param string $password
     */
    public function __construct(string $login, string $password)
    {
        $this->login = $login;
        $this->password = $password;
        /**  */
        Request::verifyPeer(false);
        Request::verifyHost(false);
    }

    /**
     * Set proxy http/https
     * @example setProxysetProxy('12.0.0.1', 8080, 'user', 'password')
     * @param string $ip
     * @param integer $port
     * @param string $login
     * @param string $password
     * @return boolean
     */
    public function setProxy(string $ip, int $port, string $login = null, string $password = null)
    {
        Unirest\Request::proxy($ip, $port, CURLPROXY_HTTP);
        if ($login && $password) {
            Unirest\Request::proxyAuth($login, $password);
        }
    }

    /**
     * Load session used
     * specify the file name of your session, all sessions are stored in the session root directory
     * @param string $session - name session file
     * @return boolean
     */
    public function loadSession(string $session = null): bool
    {
        $this->sessionFile = $session ?: 'session';
        if ($this->sessionFile && \file_exists(__DIR__ . '/session/' . $this->sessionFile)) {
            $temp = unserialize(file_get_contents(__DIR__ . '/session/' . $this->sessionFile));
            $this->device_id = $temp['device_id'];
            $this->user = $temp['user'];
            $this->session = $temp['session'];
            $this->UserAgent = $temp['ua'];
            $this->defaultHeader['user-agent'] = $temp['ua'];
            $this->defaultHeader['x-user-agent'] = $temp['ua'];
            $this->defaultHeader['x-session-id'] = $temp['session'];
            $this->defaultHeader['x-user-id'] = $temp['user'];
            return true;
        }
        return false;
    }

    /**
     * get Device ID
     *
     * @return string|null
     */
    public function getDeviceId(): ?string
    {
        $response = Request::get('https://badoo.com/signin/?f=top', ['user-agent' => $this->UserAgent]);
        foreach ($response->headers['Set-Cookie'] as $k) {
            if (strpos($k, 'id=') !== false) {
                preg_match('/id\=(.*?);/i', $k, $device_id);
                return $device_id[1] ?? null;
            }
        }
    }

    /**
     * get Session mobile api SERVER_APP_STARTUP
     *
     * @return void
     */
    public function createSession()
    {
        $data = parent::createSession();
        $response = $this->callApi('SERVER_APP_STARTUP', $data['header'], $data['param'], true);
        $this->session = \urldecode($response['headers']['X-Session-id']);
        if (!$this->device_id || !$this->session) {
            throw new Exception("Error get session or device_id");
        }
    }

    /**
     * User authorization
     *
     * @return array user session
     */
    public function userAuth()
    {
        $this->device_id = $this->getDeviceId();
        $this->createSession();
        $this->defaultHeader['x-session-id'] = $this->session;
        $this->defaultHeader['user-agent'] = $this->UserAgent;
        $this->defaultHeader['x-user-agent'] = $this->UserAgent;
        $response = $this->callApi('SERVER_LOGIN_BY_PASSWORD', $this->defaultHeader, parent::{__FUNCTION__}());
        foreach ($response['headers']['Set-Cookie'] as $k) {
            if (strpos($k, 'session=') !== false) {
                preg_match('/session=(.*?);/i', $k, $session);
                $this->session = $session[1];
            }
        }
        if (!$response['headers']['X-User-id']) {
            return ['error' => 'Authorization false'];
        }
        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'session' . DIRECTORY_SEPARATOR . $this->sessionFile, serialize([
            'session' =>  $this->session,
            'user' => $response['headers']['X-User-id'],
            'ua' => $this->UserAgent,
            'device_id' => $this->device_id
        ]));

        return ['user' => $response['headers']['X-User-id']];
    }

    /**
     * Get all visitors for the week
     *
     * @return [array] users
     */
    public function getVisitors()
    {
        $response = $this->callApi('SERVER_GET_USER_LIST', $this->defaultHeader, parent::{__FUNCTION__}());
        return $response['data'][0]['client_user_list']['section'][1]['users'] ?? [];
    }

    /**
     * Search User
     *
     * @param integer $gender - (1- Men, 2 - women)
     * @param integer $start - initial age
     * @param integer $end - the final age
     * @param integer $count - get count result list
     * @param integer $offset - offset count list
     * @param string $country - countryId_regionId_cityId
     * @return array
     */
    public function searchUser($gender = 1, $start = 18, $end = 60, $count = 150, $offset = 1, $country = ''): array
    {
        $response = $this->callApi(
            'SERVER_SAVE_SEARCH_SETTINGS_AND_GET_USER_LIST',
            $this->defaultHeader,
            parent::{__FUNCTION__}(
                $gender,
                $start,
                $end,
                $count,
                $offset,
                $country
            )
        );
        return $response['data'][1]['client_user_list']['section'][0]['users'] ?? [];
    }

    /**
     * set Like user
     *
     * @param string $userId
     * @return int true|false
     * @example $this->likeUser(1234567);
     */
    public function likeUser($userId)
    {
        $response = $this->callApi('SERVER_ENCOUNTERS_VOTE', $this->defaultHeader, parent::{__FUNCTION__}($userId));
        return  $response['data'][0]['client_vote_response']['vote_response_type'] ?? 0;
    }

    /**
     * sendMessage
     * if the message send returns the message uid, if not then false
     * @param string $userId
     * @param string $text
     * @return int uid message
     * @example $this->sendMessage(1234567, 'hello');
     */
    public function sendMessage($userId, $text)
    {
        $response = $this->callApi('SERVER_SEND_CHAT_MESSAGE', $this->defaultHeader, parent::{__FUNCTION__}($userId, $text));
        return  $response['data'][0]['chat_message_received']['chat_message']['uid'] ?? false;
    }


    /**
     * get User
     *
     * @param string $user - user id or null return info current user session
     * @return array
     */
    public function getUser($user = ''): array
    {
        $response = $this->callApi('SERVER_GET_USER', $this->defaultHeader, parent::{__FUNCTION__}($user));
        return $response['data'][0]['user'] ?? [];
    }

    /**
     * Search city id
     *
     * @param string $city - Moscow|Москва
     * @return string countryId_regionId_cityId
     */
    public function getCity($city): ?string
    {
        $response = $this->callApi('SERVER_SEARCH_LOCATIONS', $this->defaultHeader, parent::{__FUNCTION__}($city));
        if (isset($response['data'][0]['client_locations']['locations'][0])) {
            $location = $response['data'][0]['client_locations']['locations'][0];
            return "{$location['country']['id']}_{$location['region']['id']}_{$location['city']['id']}";
        }
        return null;
    }


    /**
     * CURL use library Unirest\Request
     *
     * @param string $method
     * @param array $headers
     * @param array $param
     * @param boolean $desctop
     * @return array
     */
    public function callApi(string $method, array $headers, array $param, $desctop = false): array
    {
        try {
            $response = Request::post(($desctop ? $this->desctopAPI : $this->mobileAPI) . $method, $headers, Body::Json($param));
            if ($response->code !== 200) {
                return ['error' => "http connect status code {$response->code}"];
            }
            $data = \json_decode($response->raw_body, true);
            if (json_last_error()) {
                throw new Exception("json parse result: {$response->raw_body}");
            }
            if (isset($data['body'])) {
                $data = $data['body'];
            }

            if (isset($data[0]['server_error_message'])) {
                throw new Exception(json_encode($data[0]['server_error_message']), 1);
            }
            return [
                'data' => $data,
                'headers' => $response->headers,
                'status' => $response->code
            ];
        } catch (UnofficialApi\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
