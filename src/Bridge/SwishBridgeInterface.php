<?php

declare(strict_types=1);

namespace Commerz\SwishPlugin\Bridge;

interface SwishBridgeInterface
{
    public const SANDBOX_ENVIRONMENT = 'sandbox';

    public const PRODUCTION_ENVIRONMENT = 'production';

    public const SANDBOX_HOST = 'https://mss.cpc.getswish.net/swish-cpcapi/api/';

    public const PRODUCTION_HOST = 'https://cpc.getswish.net/swish-cpcapi/api/';

    public const SWISH_API_VERSION = '1';

    public const COMPLETED_STATUS = 'completed';

    public const FAILED_STATUS = 'failed';

    public const CANCELLED_STATUS = 'cancelled';

    public const CREATED_STATUS = 'new';

    public function getRegisterUrl(string $instructionUUID): string;

    public function getCallbackUrl(): string;

    public function getHostForEnvironment(): string;

    public function getPaymentReference(object $details): string;

    public function getPayeePaymentReference(object $order): string;

    public function setAuthorizationData(
        string $payeeAlias,
        string $merchantCertificate,
        string $smcPass,
        string $environment = self::SANDBOX_ENVIRONMENT
    ): void;

    public function createSign(array $parameters): string;

    public function setRegister(object $posData, array $swishData): array;

    public function request(array $posData, string $url): array;
}
