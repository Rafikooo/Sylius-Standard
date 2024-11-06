<?php

declare(strict_types=1);

namespace App\Provider\Command;

use App\Command\CaptureStripePaymentRequest;
use Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(
    name: 'sylius.command_provider.payment_request.stripe',
    attributes: ['action' => PaymentRequestInterface::ACTION_CAPTURE]
)]
#[AutoconfigureTag(
    name: 'sylius.payment_request.command_provider',
    attributes: ['action' => PaymentRequestInterface::ACTION_CAPTURE, 'gateway-factory' => 'stripe']
)]

// An Idea: Instead of using Autoconfigure Tag use Dedicated PHP Attribute
// #[PaymentRequestCommandProvider(['gateway-factory' => 'stripe', 'action' => PaymentRequestInterface::ACTION_CAPTURE])]
final class CaptureStripePaymentRequestCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getAction() === PaymentRequestInterface::ACTION_CAPTURE;
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        $order = $paymentRequest->getPayment()->getOrder();

        /** @var PaymentInterface $payment */
        $payment = $paymentRequest->getPayment();

        // Could be extracted to a dedicated service
        $lineItems = [];

        foreach ($payment->getOrder()->getItems() as $orderItem) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'pln',
                    'product_data' => [
                        'name' => $orderItem->getVariant()->getProduct()->getName(),
                        'description' => $orderItem->getVariant()->getProduct()->getDescription(),
                    ],
                    'unit_amount' => $orderItem->getUnitPrice(),
                ],
                'quantity' => $orderItem->getQuantity(),
            ];
        }

        return new CaptureStripePaymentRequest(
            $paymentRequest->getHash()?->toBinary(),
            $order->getCustomer()->getEmail(),
            $order->getTotal(),
            $order->getCurrencyCode(),
            $lineItems,
        );
    }
}
