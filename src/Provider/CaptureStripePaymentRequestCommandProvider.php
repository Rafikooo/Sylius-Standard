<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Provider;

use App\Command\CaptureStripePaymentRequest;
use Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('sylius.command_provider.payment_request.stripe', ['action' => PaymentRequestInterface::ACTION_CAPTURE])]
final class CaptureStripePaymentRequestCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getAction() === PaymentRequestInterface::ACTION_CAPTURE;
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        $order = $paymentRequest->getPayment()->getOrder();

        return new CaptureStripePaymentRequest(
            $paymentRequest->getHash()?->toBinary(),
            $order->getCustomer()->getEmail(),
            $order->getTotal(),
            $order->getCurrencyCode(),
            '',
        );
    }
}
