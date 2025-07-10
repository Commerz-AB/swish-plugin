<?php

declare(strict_types=1);

namespace Commerz\SwishPlugin\Action;

use Commerz\SwishPlugin\Bridge\SwishBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Payum\Core\Security\TokenInterface;
use Symfony\Component\HttpFoundation\Response;

final class NotifyAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    private SwishBridgeInterface $swishBridge;

    public function __construct(SwishBridgeInterface $swishBridge)
    {
        $this->swishBridge = $swishBridge;
    }

    public function setApi($api): void
    {
        if (!is_array($api)) {
            throw new UnsupportedApiException('Not supported. Expected to be set as array.');
        }

        if($api['smc_pass'] === null){
            $api['smc_pass'] = "";
        }
        
        $this->swishBridge->setAuthorizationData($api['payee_alias'], $api['merchant_certificate'], $api['smc_pass'], $api['environment']);
    }

    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest());
 
        if (false === isset($httpRequest->request['status'])) {
            throw new InvalidArgumentException('Not supported. Invalid argument.');
        }

        /** @var TokenInterface $token */
        $token = $request->getToken();

        $paymentId = $token->getDetails()->getId();

        if($httpRequest->request['status'] === "status" ){

            // Retrieve payments from the order
            $paymentItems = $request->getFirstModel()->getOrder()->getPayments();
            $orderPayments = array();
            if ($paymentItems && is_object($paymentItems)) {
                foreach ($paymentItems as $item) {
                    if($paymentId === $item->getId()){
                        $orderPayments[$item->getId()] = $item;
                    } 
                }
            }

            // Send current payment state
            $response = new Response(
                $orderPayments[$paymentId]->getState(),
                Response::HTTP_OK,
                ['content-type' => 'text/html']
            );

            $response->send();
        }

        if($httpRequest->request['status'] === SwishBridgeInterface::COMPLETED_STATUS ){
            $details['swish_status'] = SwishBridgeInterface::COMPLETED_STATUS;

            return;
        }

        if($httpRequest->request['status'] === SwishBridgeInterface::CANCELLED_STATUS ){
            $details['swish_status'] = SwishBridgeInterface::CANCELLED_STATUS;
            
            return;
        }

        if($httpRequest->request['status'] === SwishBridgeInterface::FAILED_STATUS ){
            $details['swish_status'] = SwishBridgeInterface::FAILED_STATUS;
            
            return;
        }
    }

    public function supports($request): bool
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
