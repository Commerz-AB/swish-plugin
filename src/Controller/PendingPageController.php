<?php

namespace Commerz\SwishPlugin\Controller;

use Payum\Core\Request\Capture;
use Symfony\Component\HttpFoundation\Response;
use Commerz\SwishPlugin\Bridge\SwishBridgeInterface;
use Payum\Core\Security\TokenInterface;

final class PendingPageController
{
    public function index(Capture $request): Response
    {
        $capture = $request->getModel()->getArrayCopy();
        $swish_url_cancel = $capture['swish_url_cancel'];
        $swish_url_return = $capture['swish_url_return'];

        /** @var TokenInterface $token */
        $token = $request->getToken();
        
        $paymentID = $token->getDetails()->getId();
        $payment = md5((string) "payment_".$paymentID);

        ?>
        <!DOCTYPE html>
        <html lang="en"> 
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Loading...</title>
                <script>
                    let postData = {
                        payment: "<?php echo $payment; ?>"
                    };
                    setInterval(function() {
                        postJSON('/payment/swish/status', postData, 
                            function(err, data) {
                                if (err !== null) {
                                    console.log(err);
                                } else {
                                    if(data.status && data.status !== "<?php echo SwishBridgeInterface::CREATED_STATUS; ?>"){

                                        if(data.status === "<?php echo SwishBridgeInterface::COMPLETED_STATUS; ?>"){
                                            window.location.href = "<?php echo $swish_url_return; ?>";
                                        }

                                        if(data.status === "<?php echo SwishBridgeInterface::CANCELLED_STATUS; ?>"){
                                            window.location.href = "<?php echo $swish_url_return; ?>";
                                        }

                                        if(data.status === "<?php echo SwishBridgeInterface::FAILED_STATUS; ?>"){
                                            window.location.href = "<?php echo $swish_url_return; ?>";
                                        }
                                    }
                                }
                            }
                        );
                    }, 3000); // Checks order status every 3 seconds.

                    // The Swish order will automatically cancel after 3 minutes.
                    setTimeout(function() {
                        window.location.href = "<?php echo $swish_url_cancel;  ?>";
                    }, 180000); // Redirect after 3 minutes.
                    
                    let xhr;
                    let status;
                    var postJSON = function(url, data, callback) {
                        xhr = new XMLHttpRequest();
                        xhr.open('POST', url, true);
                        xhr.setRequestHeader('Content-Type', 'application/json');
                        xhr.responseType = 'json';

                        xhr.onload = function() {
                            status = xhr.status;
                            if (status === 200) {
                                callback(null, xhr.response);
                            } else {
                                callback(status, xhr.response);
                            }
                        };
                        xhr.send(JSON.stringify(data));
                    };
                </script>
                <style>
                    .text-wrap {
                        font-size: 16px;
                        color: #272934;
                        line-height: 24px;
                        width: 100%;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                    }
                    .swish-payment-sub-text {
                        color: #272934;
                        font-weight: 600;
                        font-size: 24px;
                    }
                    .b64img{
                        max-height:279px;
                    }
                    @media (min-width: 768px) {
                        .d-md-flex {
                            display: -ms-flexbox!important;
                            display: flex !important;
                        }
                    }
                    @media (min-width: 768px) {
                        .ml-md-5 {
                            margin-left: 3rem !important;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="text-wrap">
                    <div class="d-md-flex pt-md-4" id="myImg">
                        <?php
                            $dataImage = "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";
                            echo '<img src="data:image/webp;base64,' . $dataImage . '" alt="Swish payment image" class="b64img d-md-flex" />';
                        ?>
                        <div class="ml-md-5">
                            <p class="mb-1 swish-payment-sub-text">Starta din Swishapp och</p>
                            <p class="mb-2 swish-payment-sub-text">godkänn betalningen</p>
                            <ol>
                                <li class="swish-payment-step mb-1 pt-md-1">Öppna appen</li>
                                <li class="swish-payment-step mb-1">Klicka på betala</li>
                                <li class="swish-payment-step">Signera med BankID</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </body>
        </html>
        <?php

        return new Response(<<<HTML
        <!DOCTYPE html>
        HTML);
        
    }
}