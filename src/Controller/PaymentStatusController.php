<?php

namespace Commerz\SwishPlugin\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Payum\Core\Exception\UnsupportedApiException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentStatusController
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function paymentStatus(string $swish_url_status = "", array $data = []): string
    {
        if (empty($swish_url_status) || empty($data)) {
            throw new UnsupportedApiException('Error trying to connect to payment notification: Missing session path');
        }

        try {
            $response = $this->client->post($swish_url_status , ['json' => $data]);
        
            return (string) $response->getBody()->getContents();
          
        } catch (RequestException $e) {
            throw new UnsupportedApiException('Error trying to connect to payment notification: '.$e->getMessage());
        }
    }

    public function checkStatus(Request $request): JsonResponse
    {
        $response = [];
        $data['status'] = 'status';
        $payment = $request->request->get('payment');
        
        // Fetch the payment status here
        $status = $this->paymentStatus($_SESSION['swish_url_status'][$payment], $data);
        
        $response['status'] = $status;

        return new JsonResponse($response);
    }
}