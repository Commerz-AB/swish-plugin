<?php

declare(strict_types=1);

namespace Commerz\SwishPlugin\Action;

use Commerz\SwishPlugin\Bridge\SwishBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\GetStatusInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class StatusAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    private SwishBridgeInterface $swishBridge;

    public function __construct(SwishBridgeInterface $swishBridge)
    {
        $this->swishBridge = $swishBridge;
    }

    public function setApi($api): void
    {
        if (false === is_array($api)) {
            throw new UnsupportedApiException('Not supported.Expected to be set as array.');
        }

        if($api['smc_pass'] === null){
            $api['smc_pass'] = "";
        }

        $this->swishBridge->setAuthorizationData($api['payee_alias'], $api['merchant_certificate'], $api['smc_pass'], $api['environment']);
    }

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        $details = $payment->getDetails();

        $this->gateway->execute($httpRequest = new GetHttpRequest());
        if (isset($httpRequest->query['status']) &&
            SwishBridgeInterface::CANCELLED_STATUS === $httpRequest->query['status']
        ) {
            $details['swish_status'] = SwishBridgeInterface::CANCELLED_STATUS;
            $request->markCanceled();

            return;
        }

        if (false === isset($details['swish_status'])) {
            $request->markNew();

            return;
        }
        
        if (SwishBridgeInterface::COMPLETED_STATUS === $details['swish_status']) {
            $request->markCaptured();

            return;
        }

        if (SwishBridgeInterface::CREATED_STATUS === $details['swish_status']) {
            $request->markPending();

            return;
        }

        if (SwishBridgeInterface::FAILED_STATUS === $details['swish_status']) {
            $request->markFailed();
            
            return;
        }

        if (SwishBridgeInterface::CANCELLED_STATUS === $details['swish_status']) {
            $request->markCanceled();
            
            return;
        }

        $request->markUnknown();
    }

    public function supports($request): bool
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof PaymentInterface
        ;
    }
}
