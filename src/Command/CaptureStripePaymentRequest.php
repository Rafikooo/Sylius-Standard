<?php

declare(strict_types=1);

namespace App\Command;

use Sylius\Bundle\ApiBundle\Attribute\PaymentRequestHashAware;
use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareInterface;
use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareTrait;

#[PaymentRequestHashAware]
// An Idea: Dedicated PHP Attribute #[CapturePaymentRequest(['gateway' => 'stripe'])]
class CaptureStripePaymentRequest implements PaymentRequestHashAwareInterface
{
    use PaymentRequestHashAwareTrait;

    public function __construct(
        protected ?string $hash,
        public readonly string $email,
        public readonly int $amount,
        public readonly string $currency,
        public readonly array $lineItems,
    ) {
    }
}
