<?php

declare(strict_types=1);

namespace App\CommandHandler;

use App\Command\CaptureStripePaymentRequest;
use Stripe\StripeClient;
use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareInterface;
use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareTrait;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'sylius.payment_request.command_bus', handles: CaptureStripePaymentRequest::class)]
class CaptureStripePaymentRequestHandler implements PaymentRequestHashAwareInterface
{
    use PaymentRequestHashAwareTrait;

    public function __construct(
        private readonly PaymentRequestProviderInterface $paymentRequestProvider,
        private readonly string $stripeSecretKey,
    ) {
    }

    public function __invoke(CaptureStripePaymentRequest $captureStripePaymentRequest): void
    {
        dd(":D");
        $stripe = new StripeClient($this->stripeSecretKey);

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $captureStripePaymentRequest->amount,
            'currency' => strtolower($captureStripePaymentRequest->currency),
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        $paymentRequest = $this->paymentRequestProvider->provide($captureStripePaymentRequest);

        $paymentRequest->setResponseData([
            'paymentIntentId' => $paymentIntent->id,
            'nextAction' => $paymentIntent->next_action,
        ]);
    }
}
