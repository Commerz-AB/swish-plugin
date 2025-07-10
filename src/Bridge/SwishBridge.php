<?php

declare(strict_types=1);

namespace Commerz\SwishPlugin\Bridge;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

final class SwishBridge implements SwishBridgeInterface
{
    private string $payeeAlias = '';

    private string $merchantCertificate = '';

    private string $smcPass = '';

    private string $environment = self::SANDBOX_ENVIRONMENT;

    public function __construct(
        private Client $client
    ){}

    public function setAuthorizationData(
        string $payeeAlias,
        string $merchantCertificate,
        string $smcPass,
        string $environment = self::SANDBOX_ENVIRONMENT
    ): void {
        $this->payeeAlias = $payeeAlias;
        $this->merchantCertificate = $merchantCertificate;
        $this->smcPass = $smcPass;
        $this->environment = $environment;
    }

    public function getRegisterUrl(string $instructionUUID): string
    {
        return $this->getHostForEnvironment() ."v".self::SWISH_API_VERSION. "/paymentrequests/";
    }

    public function getCallbackUrl(): string
    {
        return (self::SANDBOX_ENVIRONMENT === $this->environment || !$_SERVER['HTTPS']) ?
            "https://example.com/api/swishcb/paymentrequests" : 'https://'.$_SERVER['HTTP_HOST'].'/payment/swish/callback'
        ;
    }

    public function getHostForEnvironment(): string
    {
        return self::SANDBOX_ENVIRONMENT === $this->environment ?
            self::SANDBOX_HOST : self::PRODUCTION_HOST
        ;
    }

    public function createSign(array $parameters): string
    {
        return md5(implode('|', array_merge($parameters, [$this->smcPass])));
    }

    public function getPaymentReference(object $details): string
    {
        return $_SESSION['swish_notify_url'] = current(array_reverse(explode("/", (string)$details['swish_notify_url'])));
    }

    public function getPayeePaymentReference(object $order): string
    {   
        return self::SANDBOX_ENVIRONMENT === $this->environment ?
            "0123456789" : $order->getId()."#".$order->getNumber()
        ;
    }

    public function setRegister(object $details, array $swishData): array
    {
        $instructionUUID             = $details['instructionUUID'];
        $details['swish_merchant_certificate'] = $this->merchantCertificate;
        $details['swish_merchant_certificate_password'] = $this->smcPass;
        $details['swish_api_version'] = self::SWISH_API_VERSION;
        $details['swish_api_url'] = $this->getRegisterUrl($instructionUUID);

        $swishData['payeeAlias']            = $this->payeeAlias;
        $swishData['payerAlias']            = $details['swish_phone'];
        $swishData['currency']              = $details['swish_currency']; 

        $swishData['cert'] = [
            $details['swish_merchant_certificate'],
            $details['swish_merchant_certificate_password']
        ];

        return $this->request($swishData, $details['swish_api_url']);
    }

    public function request(array $swishData, string $url): array
    {
        $response = "";
        try {
            $response = (string) $this->client->request('POST', $url, [
                'cert' => $swishData['cert'],
                'json' => $swishData
            ])->getBody();
        } catch (RequestException $e) {
            throw new \Exception("Error creating payment request: " . $e->getMessage());
        }

        return [$response];
    }
}