<?php

declare(strict_types=1);

namespace Commerz\SwishPlugin;

use Commerz\SwishPlugin\Bridge\SwishBridgeInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

final class SwishGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'swish',
            'payum.factory_title' => 'Swish',
        ]);

        if (false === (bool) $config['payum.api']) {
            $config['payum.default_options'] = [
                'environment' => SwishBridgeInterface::SANDBOX_ENVIRONMENT,
                'payee_alias' => null,
                'merchant_certificate' => null,
                'merchant_key' => null,
                'smc_pass' => null,
            ];

            $config->defaults($config['payum.default_options']);

            $config['payum.required_options'] = [
                'merchant_certificate',
                'merchant_key',
            ];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return [
                    'environment' => $config['environment'],
                    'payee_alias' => $config['payee_alias'],
                    'merchant_certificate' => $config['merchant_certificate'],
                    'merchant_key' => $config['merchant_key'],
                    'smc_pass' => $config['smc_pass'],
                    'payum.http_client' => $config['payum.http_client'],
                ];
            };
        }
    }
}
