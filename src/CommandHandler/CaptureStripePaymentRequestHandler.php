<?php

declare(strict_types=1);

namespace App\CommandHandler;

use App\Command\CaptureStripePaymentRequest;
use Stripe\StripeClient;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareInterface;
use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareTrait;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'sylius.payment_request.command_bus', handles: CaptureStripePaymentRequest::class)]
class CaptureStripePaymentRequestHandler implements PaymentRequestHashAwareInterface
{
    use PaymentRequestHashAwareTrait;

    public function __construct(
        private readonly PaymentRequestProviderInterface $paymentRequestProvider,
        private readonly string $stripeSecretKey,
        private readonly StateMachineInterface $stateMachine,
    ) {
    }

    public function __invoke(CaptureStripePaymentRequest $captureStripePaymentRequest): void
    {
        // The code below should be stateless to ensure validity in both synchronous and asynchronous processing
        $stripe = new StripeClient($this->stripeSecretKey);

        $checkoutSession = $stripe->checkout->sessions->create([
            'client_reference_id' => $captureStripePaymentRequest->getHash(),
            'line_items' => $captureStripePaymentRequest->lineItems,
            'mode' => 'payment',
            'payment_method_types' => ['card', 'blik', 'p24'],
            'success_url' => 'http://localhost:8000/en_US/order/thank-you',
            'cancel_url' => 'http://localhost:8000/en_US/order/cancel',
        ]);

        $paymentRequest = $this->paymentRequestProvider->provide($captureStripePaymentRequest);
        $paymentRequest->setPayload([
            'lineItems' => $captureStripePaymentRequest->lineItems,
        ]);

        $paymentRequest->setResponseData(['responseData' => $checkoutSession]);
    }
}
