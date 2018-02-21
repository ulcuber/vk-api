<?php

namespace Kernel;

use Exception;

class Api
{
    public function messagesSend($peer_id, $message, $attachments = array())
    {
        if (is_array($peer_id)) {
            $array = [
                'user_ids'    => $peer_id,
                'message'    => $message,
                'attachment' => implode(',', $attachments)
            ];
        } else {
            $array = [
                'peer_id'    => $peer_id,
                'message'    => $message,
                'attachment' => implode(',', $attachments)
            ];
        }
        return $this->apiCall('messages.send', $array);
    }

    public function usersGet($user_id, $fields = array())
    {
        return $this->apiCall('users.get', array(
            'user_id' => $user_id,
            'fields' => implode(',', $fields)
        ));
    }

    public function photosGetMessagesUploadServer($peer_id)
    {
        return $this->apiCall('photos.getMessagesUploadServer', array(
            'peer_id' => $peer_id,
        ));
    }

    public function photosSaveMessagesPhoto($photo, $server, $hash)
    {
        return $this->apiCall('photos.saveMessagesPhoto', array(
            'photo'  => $photo,
            'server' => $server,
            'hash'   => $hash,
        ));
    }

    public function docsGetMessagesUploadServer($peer_id, $type)
    {
        return $this->apiCall('docs.getMessagesUploadServer', array(
            'peer_id' => $peer_id,
            'type'    => $type,
        ));
    }

    public function docsSave($file, $title)
    {
        return $this->apiCall('docs.save', array(
            'file'  => $file,
            'title' => $title,
        ));
    }

    public function apiCall($method, $params = array())
    {
        $params['access_token'] = getenv('VK_API_ACCESS_TOKEN');
        $params['v'] = getenv('VK_API_VERSION');

        $query = http_build_query($params);
        $url = getenv('VK_API_ENDPOINT').$method.'?'.$query;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curl);
        $error = curl_error($curl);
        if ($error) {
            throw new Exception("Failed {$method} request");
        }

        curl_close($curl);

        $response = json_decode($json, true);
        if (!$response || !isset($response['response'])) {
            $this->logger->error($json, $response);
            throw new Exception("Invalid response for {$method} request");
        }

        return $response['response'];
    }

    public function upload($url, $file_name)
    {
        if (!file_exists($file_name)) {
            throw new Exception('File not found: '.$file_name);
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array('file' => new CURLfile($file_name)));
        $json = curl_exec($curl);
        $error = curl_error($curl);
        if ($error) {
            throw new Exception("Failed {$url} request");
        }

        curl_close($curl);

        $response = json_decode($json, true);
        if (!$response) {
            $this->logger->error($json, $response);
            throw new Exception("Invalid response for {$url} request");
        }

        return $response;
    }
}
