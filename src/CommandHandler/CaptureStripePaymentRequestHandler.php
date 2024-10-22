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
        private PaymentRequestProviderInterface $paymentRequestProvider,
    ) {
    }

    public function __invoke(CaptureStripePaymentRequest $captureStripePaymentRequest): void
    {
        $stripe = new StripeClient('sk_test_51QC0TK09pCMnsIMgZPaqpWAP9U8Gw883sdaHMU6RNBWn5ynecPptMR9TI0ObvHq5XlBOGZWBEeentQIyDtFjcvtd00IngsWK0d');

        $paymentMethods = $stripe->paymentMethods->all();
        dd($paymentMethods);
        $paymentIntent = $stripe->paymentIntents->create([
            'payment_method' => $paymentMethods->data[0]->id,
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
