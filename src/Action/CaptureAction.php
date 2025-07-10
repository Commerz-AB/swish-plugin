<?php

declare(strict_types=1);

namespace Commerz\SwishPlugin\Action;

use Commerz\SwishPlugin\Controller\PendingPageController;
use Commerz\SwishPlugin\Bridge\SwishBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Response;

final class CaptureAction implements ActionInterface, ApiAwareInterface, GenericTokenFactoryAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    private ?GenericTokenFactoryInterface $tokenFactory;

    public function __construct(
        private SwishBridgeInterface $swishBridge
    ){}

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

    public function setGenericTokenFactory(GenericTokenFactoryInterface $genericTokenFactory = null): void
    {
        $this->tokenFactory = $genericTokenFactory;
    }

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = $request->getModel();

        if (isset($details['swish_status'])) {
            return;
        }

        /** @var TokenInterface $token */
        $token = $request->getToken();

        $paymentID = $token->getDetails()->getId();
        $payment = md5((string) "payment_".$paymentID);

        /** @var OrderInterface $order */
        $order = $request->getFirstModel()->getOrder();
  
        $details['swish_status']            = SwishBridgeInterface::CREATED_STATUS;
        $details['instructionUUID']         = strtoupper(bin2hex(openssl_random_pseudo_bytes(16))); // this should not be random if used
        $details['swish_session_id']        = strtoupper(md5((string)$token->getDetails()->getId()));
        $details['swish_url_return']        = $token->getAfterUrl();
        $details['swish_url_cancel']        = $token->getAfterUrl() . '&' . http_build_query(['status' => SwishBridgeInterface::CANCELLED_STATUS]);
        $details['swish_notify_url']        = $this->tokenFactory->createNotifyToken($token->getGatewayName(), $token->getDetails())->getTargetUrl();
        $details['swish_wait_for_result']   = '1';

        // Adding status fetch referense
        $_SESSION['swish_url_status'][$payment] = $details['swish_notify_url'];

        // Adding order reference 
        $swishData['payeePaymentReference'] = $this->swishBridge->getPayeePaymentReference($order); 

        // Adding notify hash 
        $swishData['paymentReference']      = $this->swishBridge->getPaymentReference($details); 

        // Adding callback 
        $swishData['callbackUrl']           = $this->swishBridge->getCallbackUrl(); 

        // Message can be changed
        $swishData['message']               =  $swishData['payeePaymentReference'];

        // Translate to decimals
        $swishData['amount']                =  (($details['swish_amount'])*0.01);  

        // Connect to Swish and send order data.
        $this->swishBridge->setRegister($details, $swishData);

        // Render template for pending payment
        $pendingPageController = new PendingPageController();
        $pendingPageController->index($request);
        
        // To see the debug data, scroll down on pendig page.
        if($_SERVER['APP_DEBUG']){
            echo "<br />OrderRef: ".$order->getNumber();
            echo "<br />PaymentID: ".$token->getDetails()->getId();
            echo "<br />paymentReference: ".$swishData['paymentReference'];
            echo "<br />swish_url_return: ".$details['swish_url_return'];
            echo "<br />swish_url_cancel: ".$details['swish_url_cancel'];
            echo "<br />swish_url_status: ".$_SESSION['swish_url_status'][$payment];
            echo "<br />swish_notify_url: ".$details['swish_notify_url'];
            echo "<br /><br />";
            echo "<pre>";  
                var_dump($swishData,$details);
            echo "</pre>";
        }

        // Wait for result
        if($details['swish_wait_for_result']){
            $response = new Response(
                '',
                Response::HTTP_OK,
                ['content-type' => 'text/html']
            );
            $response->send();
        }
    }
    
    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
