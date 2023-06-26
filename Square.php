<?php

use Square\Exceptions\ApiException;
use Square\SquareClient;
use Square\Environment;
use Square\Models\ObtainTokenRequest;
use Square\Models\RevokeTokenRequest;
use Square\Models\RefundPaymentRequest;
use Square\Models\DeviceCodeStatus;
use Square\Models\DeviceCode;
use Square\Models\CreateDeviceCodeRequest;
use Square\Models\Money;
use Square\Models\Currency;
use Square\Models\DeviceCheckoutOptions;
use Square\Models\TipSettings;
use Square\Models\TerminalCheckout;
use Square\Models\CreateTerminalCheckoutRequest;
use Square\Models\TerminalRefund;
use Square\Models\CreateTerminalRefundRequest;
use Square\Models\Merchant;
use Square\Models\RetrieveMerchantResponse;
use Square\Models\Error;

class Square
{
    public $user_id;
    public $parent_user_id;
    public $access_id;
    public $ownerId;

    protected $appId;
    protected $appSecret;
    protected $env;
    protected $CI;
    protected $accessToken;
    protected $refreshToken;
    protected $merchantId;
    protected $currency;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->user_id = $this->CI->session->userdata('user_id');
        $this->parent_user_id = $this->CI->session->userdata('parent_id');
        $this->access_id = $this->CI->session->userdata('access_id');
        $this->ownerId = empty($this->parent_user_id) ? $this->user_id : $this->parent_user_id;
        $this->CI->load->model('square_settings_model');
        $this->appId = $this->CI->config->item('square_app_id');
        $this->appSecret = $this->CI->config->item('square_app_secret');
        $this->env = $this->CI->config->item('square_env') == "sandbox" ? Environment::SANDBOX : Environment::PRODUCTION;
        $this->checkToken();
    }

    /**
     * Function to check square access token
     * normal access token only valid for 30 days from authorization
     * we need to refresh the token before expire
     */
    private function checkToken()
    {
        $tokens = $this->CI->square_settings_model->getSquareSettings();
        if (!empty($tokens)) {
            $createdDate = new DateTime($tokens->created_at);
            $today = new DateTime();
            $interval = $createdDate->diff($today);
            $days = $interval->days;
            if ($days > 20 && $days < 30) {
                $tokens = $this->getAccessToken('refresh', true);
                if (!empty($tokens)) {
                    $data = [
                        'access_token' => $tokens[0],
                        'refresh_token' => $tokens[1],
                        'expires_at' => $tokens[2],
                        'merchant_id' => $tokens[3],
                        'created_at' => date('Y-m-d')
                    ];
                    $this->CI->square_settings_model->saveSetting($data);
                }
            } elseif ($days > 30) {
                $this->revokeTokens();
                $this->CI->session->set_flashdata('err_msg', 'Square Tokens expired please connect again');
                redirect('settings/integrations');
            }
        }
    }

    /**
     * Function to get auth url
     * @return string
     */
    public function getAuthUrl()
    {
        $state = md5(time());
        setcookie("Auth_State", $state, 0, '/');
        $authUrl = '';
        if ($this->env == "sandbox") {
            $authUrl = "https://connect.squareupsandbox.com";
        } else if ($this->env == "production") {
            $authUrl = "https://connect.squareup.com";
        }
        return $authUrl . '/oauth2/authorize?session=false&scope=DEVICE_CREDENTIAL_MANAGEMENT+PAYMENTS_WRITE+PAYMENTS_READ+MERCHANT_PROFILE_READ&client_id=' . $this->appId . '&state=' . $state;
    }

    /**
     * Function to get square  access token, refresh token
     * @return array|string[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSquareToken()
    {
        try {
            if ($this->appId && $this->appSecret) {
                if ($_COOKIE["Auth_State"] !== $this->CI->input->get('state')) {
                    $this->CI->session->set_flashdata('err_msg', 'Invalid access');
                    return;
                }
                if ("code" === $this->CI->input->get('response_type')) {
                    // Get the authorization code and use it to call the obtainOAuthToken wrapper function.
                    $authCode = $this->CI->input->get('code');
                    $tokens = $this->getAccessToken($authCode);
                    $merchantId = $tokens[3];
                    $data = [
                        'access_token' => $tokens[0],
                        'refresh_token' => $tokens[1],
                        'expires_at' => $tokens[2],
                        'merchant_id' => $tokens[3],
                        'enable_payment' => 1,
                        'enable_pos_payment' => 1
                    ];
                    $this->CI->square_settings_model->saveSetting($data);
                    $this->CI->square_settings_model->manageSquarePaymentMethod(1);
                    $merchantCurrency = 'USD';
                    $currency = $this->retrieveMerchant($merchantId);
                    if (!empty($currency)) {
                        $merchantCurrency = $currency;
                    }
                    $updateData = [
                        'merchant_currency' => $merchantCurrency,
                    ];
                    $this->CI->square_settings_model->saveSetting($updateData);
                } elseif ($this->CI->input->get('error')) {
                    // Check to see if the seller clicked the Deny button and handle it as a special case.
                    if (("access_denied" === $this->CI->input->get('error')) && ("user_denied" === $this->CI->input->get('error_description'))) {
                        logError(['Square Authorization denied for ' . $this->user_id, "You chose to deny access to the app."], true);
                    } // Display the error and description for all other errors.
                    else {
                        logError(['Square Authorization Error for ' . $this->user_id, $this->CI->input->get('error_description')], true);
                    }
                } else {
                    // No recognizable parameters were returned.
                    logError(['Square Unknown parameters', 'Expected parameters were not returned'], true);
                }

            }
            return ['status' => 'success'];

        } catch (Exception $e) {
            logError(['token exception', $e->getMessage()], true);
            return ['status' => 'error', 'msg' => $e->getMessage()];
        }
    }

    /**
     * Function to get access token and refresh token through api
     * @param $authCode
     * @param $refresh
     * @return array|void
     */
    public function getAccessToken($authCode, $refresh = false)
    {
        $apiClient = new SquareClient([
            'environment' => $this->env
        ]);
        $oauthApi = $apiClient->getOAuthApi();
        $grantType = 'authorization_code';
        if ($refresh) {
            $grantType = 'refresh_token';
            $this->setTokens();
        }
        $body = new ObtainTokenRequest(
            $this->appId,
            $this->appSecret,
            $grantType
        );
        if (!$refresh) {
            $body->setCode($authCode);
        } else {
            $body->setRefreshToken($this->refreshToken);
        }
        //$body->setScopes(['DEVICE_CREDENTIAL_MANAGEMENT', 'PAYMENTS_WRITE', 'PAYMENTS_READ', 'MERCHANT_PROFILE_READ', 'MERCHANT_PROFILE_WRITE']);
        try {
            $response = $oauthApi->obtainToken($body);
            if ($response->isError()) {
                $code = $response->getErrors()[0]->getCode();
                $category = $response->getErrors()[0]->getCategory();
                $detail = $response->getErrors()[0]->getDetail();
                logError(['obtain token Error', "Error Processing Request: obtainToken failed!\n" . $code . "\n" . $category . "\n" . $detail], true);
                $statusCode = $response->getStatusCode();
                if (401 == $statusCode && $refresh) {
                    $this->revokeTokens();
                }
                return;
            }
        } catch (ApiException $e) {
            error_log($e->getMessage());
            error_log($e->getHttpResponse()->getRawBody());
            logError(['obtain token Exception', "Error Processing Request: obtainToken failed!\n" . $e->getMessage() . "\n" . $e->getHttpResponse()->getRawBody()], true);
            return;
        }
        $accessToken = urlEncrypt($response->getResult()->getAccessToken());
        $refreshToken = urlEncrypt($response->getResult()->getRefreshToken());
        $expiresAt = urlEncrypt($response->getResult()->getExpiresAt());
        $merchantId = $response->getResult()->getMerchantId();
        return [$accessToken, $refreshToken, $expiresAt, $merchantId];
    }

    /**
     * Function to revoke tokens from Square
     * @return bool
     * @throws ApiException
     */
    public function revokeTokens()
    {

        $apiClient = $this->getApiClient();
        $oauthApi = $apiClient->getOAuthApi();
        $this->setTokens();
        $body = new RevokeTokenRequest;
        $body->setClientId($this->appId);
        $body->setMerchantId($this->merchantId);
        $body->setRevokeOnlyAccessToken(false);
        $apiResponse = $oauthApi->revokeToken($body, "Client " . $this->appSecret);
        if (!$apiResponse->isSuccess()) {
            $errors = $apiResponse->getErrors();
            logError(['token revoke error', $errors], true);
        }
        $this->CI->square_settings_model->removeSquare();
        $this->CI->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);
        $this->CI->cache->delete(getCachePrefix($this->ownerId) . 'squareSettings');
        return true;
    }

    /**
     * Function to get square locations
     * @return bool
     */
    public function getLocations()
    {
        $this->setTokens();
        $apiClient = $this->getApiClient(1);
        try {
            $locationsApi = $apiClient->getLocationsApi();
            $apiResponse = $locationsApi->listLocations();
            if ($apiResponse->isSuccess()) {
                return ['status' => 'success', 'locations' => $apiResponse->getResult()->getLocations()];
            } else {
                $errors = $apiResponse->getErrors()[0];
                $errorCode = $errors->getCode();
                $statusCode = $apiResponse->getStatusCode();
                if (401 == $statusCode && ($errorCode == 'ACCESS_TOKEN_EXPIRED' || $errorCode == 'ACCESS_TOKEN_REVOKED')) {
                    $this->revokeTokens();
                } elseif (500 == $statusCode) {
                    return ['status' => 'error', 'message' => $this->CI->lang->line('something_wrong')];
                } else {
                    logError(['error get locations for ' . $this->user_id, $errors], true);
                    return ['status' => 'error', 'message' => $this->CI->lang->line('something_wrong')];;
                }
            }
        } catch (ApiException $e) {
            logError(['Received error while calling Square list location api for :' . $this->user_id, $e->getMessage()], true);
            return ['status' => 'error', 'message' => $this->CI->lang->line('something_wrong')];;
        }
        return ['status' => 'error', 'message' => $this->CI->lang->line('something_wrong')];;
    }

    /**
     * Function to create square device
     * @param $deviceName
     * @param $locationId
     * @return bool
     */
    public function createDevice($deviceName, $locationId)
    {
        if ($this->env == 'sandbox') {
            return ['9fa747a2-25ff-48ee-b078-04381f7c828f', 'paired', '9fa747a2-25ff-48ee-b078-04381f7c828f', '9fa747a2-25ff-48ee-b078-04381f7c828f'];
        }
        $this->setTokens();
        $apiClient = $this->getApiClient(1);
        try {
            $devicesApi = $apiClient->getDevicesApi();
            $productType = 'TERMINAL_API';
            $deviceCode = new DeviceCode(
                $productType
            );
            $idempotencKey = uniqid(ENV . '_');
            $deviceCode->setName($deviceName);
            $deviceCode->setLocationId($locationId);
            $body = new CreateDeviceCodeRequest(
                $idempotencKey,
                $deviceCode
            );
            $apiResponse = $devicesApi->createDeviceCode($body);
            if ($apiResponse->isSuccess()) {
                $deviceCodeResponse = $apiResponse->getResult()->getDeviceCode();
                logError(['create device', $deviceCodeResponse, $deviceCodeResponse->getDeviceId()], true);
                return [$deviceCodeResponse->getId(), $deviceCodeResponse->getStatus(), $deviceCodeResponse->getCode(), $deviceCodeResponse->getDeviceId()];
            } else {
                logError([$apiResponse], true);
                $errors = $apiResponse->getErrors()[0];
                $errorCode = $errors->getCode();
                $statusCode = $apiResponse->getStatusCode();
                if (401 == $statusCode && ($errorCode == 'ACCESS_TOKEN_EXPIRED' || $errorCode == 'ACCESS_TOKEN_REVOKED')) {
                    $this->revokeTokens();
                } elseif (500 == $statusCode) {
                    return false;
                } else {
                    logError(['error create device for ' . $this->user_id, $errors], true);
                    return false;
                }
            }
        } catch (ApiException $e) {
            logError(['error create device for :' . $this->user_id, $e->getMessage()], true);
            return false;
        }
        return false;
    }

    /**
     * Function to get devices from square dashboard
     * API implementation
     * @return array|bool
     */
    public function getDevices()
    {
        if ($this->env == 'sandbox') {
            return true;
        }
        $this->setTokens();
        $apiClient = $this->getApiClient(1);
        try {
            $devicesApi = $apiClient->getDevicesApi();
            $productType = 'TERMINAL_API';
            $apiResponse = $devicesApi->listDeviceCodes(NULL, NULL, $productType);
            if ($apiResponse->isSuccess()) {
                logError(['list device', $apiResponse->getResult()], true);
                return ['status' => 'success', 'devices' => $apiResponse->getResult()->getDeviceCodes()];
            } else {
                logError([$apiResponse], true);
                $errors = $apiResponse->getErrors()[0];
                $errorCode = $errors->getCode();
                $statusCode = $apiResponse->getStatusCode();
                if (401 == $statusCode && ($errorCode == 'ACCESS_TOKEN_EXPIRED' || $errorCode == 'ACCESS_TOKEN_REVOKED')) {
                    $this->revokeTokens();
                } elseif (500 == $statusCode) {
                    return ['status' => 'error', 'message' => $errors];
                } else {
                    logError(['error create device for ' . $this->user_id, $errors], true);
                    return ['status' => 'error', 'message' => $errors];
                }
            }
        } catch (ApiException $e) {
            logError(['error create device for :' . $this->user_id, $e->getMessage()], true);
            return false;
        }
        return false;
    }

    /**
     * Function to format the amount for terminal checkout and refund
     *
     * @param $amount
     * @return string
     */
    private function getAmount($amount)
    {
        return number_format((float)$amount * 100., 0, '.', '');
    }

    /**
     * Function for terminal checkout API
     *
     * @param $data
     * @return array|string[]
     */
    public function createTerminalCheckout($data)
    {
        $transactionData = [
            'payment_record_id' => $data['payment_id'],
            'payment_method_id' => $data['payment_method_id'],
            'payment_from' => $data['payment_from'],
            'status' => 'pending',
            'transaction_amount' => $data['payment_amount']
        ];
        $transactionId = $this->CI->square_settings_model->manageTransactions($transactionData);
        $this->setTokens();
        $apiClient = $this->getApiClient(1);
        try {
            $terminalApi = $apiClient->getTerminalApi();
            $idempotencyKey = uniqid(ENV . '_');
            $bodyAmountMoney = new Money;
            $amount = $this->getAmount($data['payment_amount']);
            $bodyAmountMoney->setAmount($amount);
            $bodyAmountMoney->setCurrency($this->currency);
            $deviceId = $this->CI->input->post('square_device_id');
            $bodyDeviceOptions = new DeviceCheckoutOptions(
                $deviceId
            );
            $bodyDeviceOptions->setSkipReceiptScreen(false);
            $bodyDeviceOptions->setTipSettings(new TipSettings);
            $bodyDeviceOptions->getTipSettings()->setAllowTipping(false);
            $bodyDeviceOptions->getTipSettings()->setSeparateTipScreen(false);
            $bodyDeviceOptions->getTipSettings()->setCustomTipField(false);
            $bodyCheckout = new TerminalCheckout(
                $bodyAmountMoney,
                $bodyDeviceOptions
            );
            $bodyCheckout->setId($transactionId);
            $bodyCheckout->setReferenceId($transactionId);
            $note = (platform() == 'betterclinics') ? 'Reckon Better Clinics [Invoice #'.$data['invoiceId'].']' : 'Reckon BetterHQ [Invoice #'.$data['invoiceId'].']';
            $bodyCheckout->setNote($note);
            $bodyCheckout->setPaymentType('CARD_PRESENT');
            $body = new CreateTerminalCheckoutRequest(
                $idempotencyKey,
                $bodyCheckout
            );
            $apiResponse = $terminalApi->createTerminalCheckout($body);
            if ($apiResponse->isSuccess()) {
                return ['status' => 'success', 'data' => $apiResponse->getResult(), 'transactionId' => $transactionId];
            } else {
                $errors = $apiResponse->getErrors()[0];
                $errorCode = $errors->getCode();
                $statusCode = $apiResponse->getStatusCode();
                if (401 == $statusCode && ($errorCode == 'ACCESS_TOKEN_EXPIRED' || $errorCode == 'ACCESS_TOKEN_REVOKED')) {
                    $this->revokeTokens();
                    return ['status' => 'error', 'message' => $this->CI->lang->line('something_wrong')];
                } elseif (500 == $statusCode) {
                    return ['status' => 'error', 'message' => $this->CI->lang->line('something_wrong')];
                } else {
                    logError(['error create terminal checkout for ' . $this->user_id, $errors], true);
                    return ['status' => 'error', 'message' => $errors->getDetail()];
                }
            }
        } catch (ApiException $e) {
            logError(['error create terminal checkout for :' . $this->user_id, $e->getMessage()], true);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Something went wrong'];
    }

    /**
     * Function for terminal checkout API
     *
     * @param $data
     * @return array|string[]
     */
    public function cancelTerminalCheckout($checkoutId)
    {
        $this->setTokens();
        $apiClient = $this->getApiClient(1);
        try {
            $terminalApi = $apiClient->getTerminalApi();
            $apiResponse = $terminalApi->cancelTerminalCheckout($checkoutId);
            if ($apiResponse->isSuccess()) {
                return ['status' => 'success', 'data' => $apiResponse->getResult()];
            } else {
                $errors = $apiResponse->getErrors()[0];
                $errorCode = $errors->getCode();
                $statusCode = $apiResponse->getStatusCode();
                if (401 == $statusCode && ($errorCode == 'ACCESS_TOKEN_EXPIRED' || $errorCode == 'ACCESS_TOKEN_REVOKED')) {
                    $this->revokeTokens();
                    return ['status' => 'error', 'message' => $this->CI->lang->line('something_wrong')];
                } elseif (500 == $statusCode) {
                    return ['status' => 'error', 'message' => $this->CI->lang->line('something_wrong')];
                } else {
                    logError(['error cancel terminal checkout for ' . $this->user_id, $errors], true);
                    return ['status' => 'error', 'message' => $errors->getDetail()];
                }
            }
        } catch (ApiException $e) {
            logError(['error cancel terminal checkout for :' . $this->user_id, $e->getMessage()], true);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Something went wrong'];
    }

    /**
     * Function for Refund API
     *
     * @param $data
     * @return array|string[]
     */
    public function createTerminalRefund($data)
    {
        $this->setTokens();
        $apiClient = $this->getApiClient(1);
        try {
            $terminalApi = $apiClient->getTerminalApi();
            $idempotencyKey = uniqid(ENV . '_');
            $body = new CreateTerminalRefundRequest(
                $idempotencyKey
            );
            $refundPaymentId = $data['squarePaymentId'];
            $bodyAmountMoney = new Money;
            $amount = $this->getAmount($data['refundAmt']);
            $bodyAmountMoney->setAmount($amount);
            $bodyAmountMoney->setCurrency($this->currency);
            $body->setRefund(new TerminalRefund(
                $refundPaymentId,
                $bodyAmountMoney
            ));
            $body->getRefund()->setReason($data['refundReason']);
            $deviceId = $data['squareDeviceId'];
            if ($this->env == 'sandbox') {
                $deviceId = 'f72dfb8e-4d65-4e56-aade-ec3fb8d33291';
            }
            $body->getRefund()->setOrderId($data['transactionId']);
            $body->getRefund()->setDeviceId($deviceId);
            $apiResponse = $terminalApi->createTerminalRefund($body);
            if ($apiResponse->isSuccess()) {
                return ['status' => 'success', 'data' => $apiResponse->getResult()];
            } else {
                $errors = $apiResponse->getErrors()[0];
                $errorCode = $errors->getCode();
                $statusCode = $apiResponse->getStatusCode();
                if (401 == $statusCode && ($errorCode == 'ACCESS_TOKEN_EXPIRED' || $errorCode == 'ACCESS_TOKEN_REVOKED')) {
                    $this->revokeTokens();
                    return ['status' => 'error', 'message' => $this->CI->lang->line('something_wrong')];
                } elseif (500 == $statusCode) {
                    return ['status' => 'error', 'message' => $this->CI->lang->line('something_wrong')];
                } else {
                    logError(['error create terminal refund for ' . $this->user_id, $errors], true);
                    return ['status' => 'error', 'message' => $errors->getDetail()];
                }
            }
        } catch (ApiException $e) {
            logError(['error create terminal refund for :' . $this->user_id, $e->getMessage()], true);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Something went wrong'];
    }

    /**
     * Function for Payment Refund API
     *
     * @param $data
     * @return array|string[]
     */
    public function createPaymentRefund($data)
    {
        $this->setTokens();
        $apiClient = $this->getApiClient(1);
        try {
            $refundsApi = $apiClient->getRefundsApi();
            $idempotencyKey = uniqid(ENV . '_');
            $bodyAmountMoney = new Money;
            $amount = $this->getAmount($data['refundAmt']);
            $bodyAmountMoney->setAmount($amount);
            $bodyAmountMoney->setCurrency($this->currency);
            $body = new RefundPaymentRequest(
                $idempotencyKey,
                $bodyAmountMoney,
                $data['squarePaymentId']
            );
            $body->setPaymentId($data['squarePaymentId']);
            $body->setReason($data['refundReason']);
            $apiResponse = $refundsApi->refundPayment($body);
            if ($apiResponse->isSuccess()) {
                return ['status' => 'success', 'data' => $apiResponse->getResult()];
            } else {
                $errors = $apiResponse->getErrors()[0];
                $errorCode = $errors->getCode();
                $statusCode = $apiResponse->getStatusCode();
                if (401 == $statusCode && ($errorCode == 'ACCESS_TOKEN_EXPIRED' || $errorCode == 'ACCESS_TOKEN_REVOKED')) {
                    $this->revokeTokens();
                    return ['status' => 'error', 'message' => $this->CI->lang->line('something_wrong')];
                } elseif (500 == $statusCode) {
                    return ['status' => 'error', 'message' => $this->CI->lang->line('something_wrong')];
                } else {
                    logError(['error create terminal refund for ' . $this->user_id, $errors], true);
                    return ['status' => 'error', 'message' => $errors->getDetail()];
                }
            }
        } catch (ApiException $e) {
            logError(['error create terminal refund for :' . $this->user_id, $e->getMessage()], true);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
        return ['status' => 'error', 'message' => 'Something went wrong'];
    }

    /**
     * Function for retrieve merchant API
     *
     * @param $merchantId
     * @return bool
     */
    public function retrieveMerchant($merchantId)
    {
        $this->setTokens();
        $apiClient = $this->getApiClient(1);
        try {
            $merchantApi = $apiClient->getMerchantsApi();
            $apiResponse = $merchantApi->retrieveMerchant($merchantId);
            logError(['merchant api', $apiResponse->getResult()], true);
            if ($apiResponse->isSuccess()) {
                return $apiResponse->getResult()->getMerchant()->getCurrency();
            } else {
                $errors = $apiResponse->getErrors()[0];
                $errorCode = $errors->getCode();
                $statusCode = $apiResponse->getStatusCode();
                if (401 == $statusCode && ($errorCode == 'ACCESS_TOKEN_EXPIRED' || $errorCode == 'ACCESS_TOKEN_REVOKED')) {
                    $this->revokeTokens();
                } elseif (500 == $statusCode) {
                    return false;
                } else {
                    logError(['error retrieve merchant api  ' . $this->user_id, $errors], true);
                    return false;
                }
            }
        } catch (ApiException $e) {
            logError(['error retrieve merchant api :' . $this->user_id, $e->getMessage()], true);
            return false;
        }
        return false;
    }

    /**
     * Function to get api client
     * @param bool $token
     * @return SquareClient
     */
    private function getApiClient($token = false)
    {
        $param = [
            'environment' => $this->env
        ];
        if ($token) {
            $accessToken = $this->accessToken;
            if ($this->env == "sandbox") {
                $accessToken = $this->CI->config->item('square_sandbox_access_token');
            }
            $param['accessToken'] = $accessToken;
        }
        return new SquareClient($param);
    }

    /**
     * Function set local variables
     * @return bool
     */
    private function setTokens()
    {
        $tokens = $this->CI->square_settings_model->getSquareSettings();
        $this->accessToken = urlDecrypt($tokens->access_token);
        $this->refreshToken = urlDecrypt($tokens->refresh_token);
        $this->merchantId = $tokens->merchant_id;
        $this->currency = $tokens->merchant_currency;
        return true;
    }

}