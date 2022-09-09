<?php

namespace Shahdah\Sadded\Providers;

use GuzzleHttp\Client;

class Tap
{

    public array $customerData, $credentials, $response;
    public string $paymentUrl, $callbackUrl;

    /**
     * Set Customer data .
     *
     * @param  array  $customerData
     * @return void
     */

    public function setCustomerData($customerData)
    {
        $this->customerData['full_name'] = $customerData['full_name'];
        $this->customerData['email'] = $customerData['email'];
        $this->customerData['country_code'] = $customerData['country_code'];
        $this->customerData['phone_number'] = $customerData['phone_number'];
    }

    public function setCredentials($credentials)
    {
        $this->credentials['token'] = $credentials['token'];
    }
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
    }    
    /**
     * charge
     *
     * @param  mixed $amount
     * @param  mixed $currency
     * @return object
     */
    public function charge(float $amount, string $currency): ?object
    {
        $data['amount'] = $amount;
        $data['currency'] = $currency;
        $data['customer']['first_name'] = $this->customerData['full_name'];
        $data['customer']['email'] = $this->customerData["email"];
        $data['customer']['phone']['country_code'] = $this->customerData["country_code"];
        $data['customer']['phone']['number'] = $this->customerData["phone_number"];
        $data['redirect']['url'] = $this->callbackUrl;

        $data['source']['id'] = 'src_all';
        $client = new Client(['base_uri' => 'https://api.tap.company/v2/']);
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->credentials['token']
        ];
        $request = $client->post('charges', ['json' => $data, 'headers' => $headers]);
        $data = json_decode($request->getBody()->getContents(), true);
        $this->setResponse($data);
        $this->setPaymentUrl($data['transaction']['url']);
        return $this;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setPaymentUrl($url)
    {
        $this->paymentUrl = $url;
    }

    public function getPaymentUrl()
    {
        return $this->paymentUrl;
    }

    public function getChargeResponse($tap_id)
    {
        $client = new Client(['base_uri' => 'https://api.tap.company/v2/']);
        $headers = [
            'Authorization' => 'Bearer ' . $this->credentials['token']
        ];
        $request = $client->get('charges/' . $tap_id, ['headers' => $headers]);
        $response = json_decode($request->getBody()->getContents(), true);
        return $response;
    }
}
