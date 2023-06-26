<?php


class Zoom
{
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->user_id = $this->CI->session->userdata('user_id');
        $this->parent_user_id = $this->CI->session->userdata('parent_id');
        $this->access_id = $this->CI->session->userdata('access_id');
        $this->ownerId = empty($this->parent_user_id) ? $this->user_id : $this->parent_user_id;
        $this->CI->load->model('zoom_settings_model');
        $this->clientKey = $this->CI->config->item('zoom_client_id');
        $this->clientSecret = $this->CI->config->item('zoom_client_secret');
    }

    /**
     * Function to get zoom access token
     * @return array|string[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getZoomToken()
    {
        try {
            if ($this->clientKey && $this->clientSecret) {
                $client = new GuzzleHttp\Client(['base_uri' => 'https://zoom.us']);
                $response = $client->request('POST', '/oauth/token', [
                    "headers" => [
                        "Authorization" => "Basic " . base64_encode($this->clientKey . ':' . $this->clientSecret)
                    ],
                    'form_params' => [
                        "grant_type" => "authorization_code",
                        "code" => $this->CI->input->get('code'),
                        "redirect_uri" => base_url('settings/integrations')
                    ],
                ]);

                $token = json_decode($response->getBody()->getContents(), true);
                $accessToken = $token['access_token'];
                $refreshToken = $token['refresh_token'];
                $token['access_token'] = urlEncrypt($accessToken);
                $token['refresh_token'] = urlEncrypt($refreshToken);
                $data = [
                    'access_token' => json_encode($token)
                ];
                $this->CI->zoom_settings_model->saveSetting($data);
                $this->getUserInfo();
            }
            return ['status' => 'success'];

        } catch (Exception $e) {
            logError(['token exception', $e->getMessage()], true);
            return ['status' => 'error', 'msg' => $e->getMessage()];
        }
    }

    /**
     * The function is to get new access token from zoom account if already existing one expires
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateAccessToken()
    {
        $refreshToken = $this->CI->zoom_settings_model->getRefreshToken();
        $client = new GuzzleHttp\Client(['base_uri' => 'https://zoom.us']);
        try {
            $response = $client->request('POST', '/oauth/token', [
                "headers" => [
                    "Authorization" => "Basic " . base64_encode($this->clientKey . ':' . $this->clientSecret)
                ],
                'form_params' => [
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refreshToken
                ],
            ]);
            $token = json_decode($response->getBody());
            $token->access_token = urlEncrypt($token->access_token);
            $token->refresh_token = urlEncrypt($token->refresh_token);
            $data = [
                'access_token' => json_encode($token)
            ];
            $this->CI->zoom_settings_model->saveSetting($data);
            return true;

        } catch (Exception $e) {
            logError(['update token exception', $e->getMessage()], true);
            //remove zoom settings if get unauthorized response while updating access token with refresh token
            //as this means user uninstalled the app from market place and we did not get the notification
            if (401 == $e->getCode()) {
                $this->CI->zoom_settings_model->removeZoom();
                $this->CI->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);
                $this->CI->cache->delete(getCachePrefix($this->ownerId) . 'zoomEnabled');
            }
            return false;
        }

    }

    /**
     * Function to get zoom account information
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUserInfo()
    {
        $client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
        $arrayToken = $this->CI->zoom_settings_model->getAccessToken();
        $accessToken = $arrayToken->access_token;
        try {
            $response = $client->request('GET', '/v2/users/me', [
                "headers" => [
                    "Authorization" => "Bearer $accessToken"
                ]
            ]);
            $res = json_decode($response->getBody());
            if ($res && $res->id && $res->account_id) {
                $data = [
                    'zoom_user_id' => openssl_encrypt($res->id, "AES-128-ECB", "zoom"),
                    'zoom_account_id' => openssl_encrypt($res->account_id, "AES-128-ECB", "zoom")
                ];
                $this->CI->zoom_settings_model->saveSetting($data);
            }
            return true;
        } catch (Exception $e) {
            if (401 == $e->getCode()) {
                if ($this->updateAccessToken()) {
                    return $this->getUserInfo();
                }
            } else {
                logError(['error get user info', $e->getMessage()], true);
                return false;
            }
        }
    }

    /**
     * Function to get new access token and update to our db
     * @param $zoomId
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMeetingInvitation($zoomId)
    {
        $client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
        $arrayToken = $this->CI->zoom_settings_model->getAccessToken();
        $accessToken = $arrayToken->access_token;
        try {
            $response = $client->request('GET', '/v2/meetings/' . $zoomId . '/invitation', [
                "headers" => [
                    "Authorization" => "Bearer $accessToken"
                ]
            ]);
            $res = json_decode($response->getBody());
            return nl2br($res->invitation);
        } catch (Exception $e) {
            if (401 == $e->getCode()) {
                if ($this->updateAccessToken()) {
                    return $this->getMeetingInvitation($zoomId);
                }
            } else {
                logError(['error get invitation', $e->getMessage()], true);
                return false;
            }
        }
    }

    /**
     * Function to delete zoom meeting via api
     * @param $zoomId
     * @param null $recurrenceId
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteMeeting($zoomId, $recurrenceId = null)
    {
        $client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
        $arrayToken = $this->CI->zoom_settings_model->getAccessToken();
        $accessToken = $arrayToken->access_token;
        try {
            $url = '/v2/meetings/' . $zoomId;
            if ($recurrenceId) {
                $url .= '?occurrence_id=' . $recurrenceId;
            }
            $response = $client->request('DELETE', $url, [
                "headers" => [
                    "Authorization" => "Bearer $accessToken"
                ]
            ]);
            $res = json_decode($response->getBody());
            return $res;
        } catch (Exception $e) {
            if (401 == $e->getCode()) {
                if ($this->updateAccessToken()) {
                    return $this->deleteMeeting($zoomId, $recurrenceId);
                }
            } else {
                logError(['error delete meeting', $e->getMessage()], true);
                return ['status' => 'error', 'msg' => $e->getMessage()];
            }
        }
    }

    /**
     * Function to update meeting
     * @param $zoomId
     * @param array $data
     * @param null $recurrenceId
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updateMeeting($zoomId, $data = [], $recurrenceId = null)
    {
        $client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
        $arrayToken = $this->CI->zoom_settings_model->getAccessToken();
        $accessToken = $arrayToken->access_token;
        try {
            $url = '/v2/meetings/' . $zoomId;
            if ($recurrenceId) {
                $url .= '?occurrence_id=' . $recurrenceId;
            }
            $response = $client->request('PATCH', $url, [
                "headers" => [
                    "Authorization" => "Bearer $accessToken"
                ],
                'json' => $data,
            ]);
            $res = json_decode($response->getBody());
            return $res;
        } catch (Exception $e) {
            if (401 == $e->getCode()) {
                if ($this->updateAccessToken()) {
                    return $this->updateMeeting($zoomId, $data, $recurrenceId);
                }
            } else {
                logError(['error update meeting', $e->getMessage()], true);
                return ['status' => 'error', 'msg' => $e->getMessage()];
            }
        }
    }

    /**
     * Function to create meeting via api
     * @param array $data
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createMeeting($data = [])
    {
        $client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
        $arrayToken = $this->CI->zoom_settings_model->getAccessToken();
        $accessToken = $arrayToken->access_token;
        logError(['accesstoken',$accessToken], true);
        try {
            $response = $client->request('POST', '/v2/users/me/meetings', [
                "headers" => [
                    "Authorization" => "Bearer $accessToken"
                ],
                'json' => $data,
            ]);
            return json_decode($response->getBody());
        } catch (Exception $e) {
            if (401 == $e->getCode()) {
                if ($this->updateAccessToken()) {
                    return $this->createMeeting($data);
                }
            } else {
                logError(['error create meeting', $e->getMessage()], true);
                return ['status' => 'error', 'msg' => $e->getMessage()];
            }
        }
    }

    /**
     * Function to notify zoom about the data compliance after uninstall app from marketplace
     * @param array $data
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function dataCompliance($data = [])
    {
        $client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
        try {
            $response = $client->request('POST', '/oauth/data/compliance', [
                "headers" => [
                    "Authorization" => "Basic " . base64_encode($this->clientKey . ':' . $this->clientSecret)
                ],
                'json' => $data,
            ]);
            return json_decode($response->getBody());
        } catch (Exception $e) {
            logError(['error data Compliance', $e->getMessage()], true);
            return ['status' => 'error', 'msg' => $e->getMessage()];
        }
    }

}