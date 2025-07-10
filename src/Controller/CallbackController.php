<?php

declare(strict_types=1);

namespace Commerz\SwishPlugin\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Payum\Core\Exception\UnsupportedApiException;
use Symfony\Component\HttpFoundation\Response;

class CallbackController
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }
    
    public function setPayment(string $paymentReference = "", array $data = []): void
    {
        if (empty($paymentReference) || empty($data)) {
            throw new UnsupportedApiException('Empty data or payment.');
        }

        $url = $_SERVER['HTTP_HOST']."/payment/notify/".$paymentReference; 

        try {
            $this->client->put($url, ['json' => $data]);
        } catch (RequestException $e) {
            throw new UnsupportedApiException("Error creating payment request: " . $e->getMessage());
        }
    }

    public function callback(): Response
    {
        $data = [];
        $swishRequest = file_get_contents('php://input');
        $swishRequest = (string) $swishRequest;
        $swishRequest = json_decode($swishRequest);

        if (empty($swishRequest->payeePaymentReference))

            return new Response('Payment Reference Empty!');

        if (empty($swishRequest->paymentReference))

            return new Response('Callback Identifier Missing!');

        if ($swishRequest->status !== null && $swishRequest->status === "PAID") { 
            $data['status'] = 'completed';
            $this->setPayment($swishRequest->paymentReference, $data);

            return new Response('COMPLETED');
        }

        if ($swishRequest->status !== null && $swishRequest->status === "DECLINED") { 
            $data['status'] = 'cancelled';
            $this->setPayment($swishRequest->paymentReference, $data);

            return new Response('CANCELLED');
        }

        if ($swishRequest->status !== null && $swishRequest->status === "ERROR") { 
            $data['status'] = 'cancelled';
            $this->setPayment($swishRequest->paymentReference, $data);

            return new Response('FAILED');
        }

        return new Response('UNCHANGED');
    }
}