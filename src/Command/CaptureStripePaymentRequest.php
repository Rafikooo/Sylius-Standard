<?php

declare(strict_types=1);

namespace App\Command;

use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareInterface;
use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareTrait;

class CaptureStripePaymentRequest implements PaymentRequestHashAwareInterface
{
    use PaymentRequestHashAwareTrait;

    public function __construct(
        protected ?string $hash,
        public string $email,
        public int $amount,
        public string $currency,
        public string $source,
    ) {
    }
}
