<?php

declare(strict_types=1);

namespace Commerz\SwishPlugin\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Convert;
use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class ConvertPaymentAction implements ActionInterface
{
    use GatewayAwareTrait;

    private PaymentDescriptionProviderInterface $paymentDescriptionProvider;

    public function __construct(PaymentDescriptionProviderInterface $paymentDescriptionProvider)
    {
        $this->paymentDescriptionProvider = $paymentDescriptionProvider;
    }

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        $paymentData = $this->getPaymentData($payment);
        $customerData = $this->getCustomerData($order);
        $shoppingList = $this->getShoppingList($order);

        $details = array_merge($paymentData, $customerData, $shoppingList);

        $request->setResult($details);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            'array' === $request->getTo()
        ;
    }

    private function getPaymentData(PaymentInterface $payment): array
    {
        $paymentData = [];

        $paymentData['swish_amount'] = $payment->getAmount();
        $paymentData['swish_currency'] = $payment->getCurrencyCode();
        $paymentData['swish_description'] = $this->paymentDescriptionProvider->getPaymentDescription($payment);

        return $paymentData;
    }

    private function getCustomerData(OrderInterface $order): array
    {
        $customerData = [];

        $customerData['swish_language'] = $order->getLocaleCode();

        if (null !== $customer = $order->getCustomer()) {
            $customerData['swish_email'] = $customer->getEmail();
        }

        if (null !== $address = $order->getShippingAddress()) {
            $customerData['swish_address'] = $address->getStreet();
            $customerData['swish_zip'] = $address->getPostcode();
            $customerData['swish_city'] = $address->getCity();
            $customerData['swish_country'] = $address->getCountryCode();
            $customerData['swish_phone'] = $address->getPhoneNumber();
            $customerData['swish_client'] = $address->getFullName();
        }

        return $customerData;
    }

    private function getShoppingList(OrderInterface $order): array
    {
        $shoppingList = [];

        $index = 1;

        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            $shoppingList['swish_name_' . $index] = $item->getProduct()->getName();
            $shoppingList['swish_quantity_' . $index] = $item->getQuantity();
            $shoppingList['swish_price_' . $index] = $item->getUnitPrice();

            ++$index;
        }

        return $shoppingList;
    }
}
