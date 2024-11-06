<?php

namespace App\Controller\Webhook;

use Psr\Log\LoggerInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class StripeController extends AbstractController
{
    public function __construct()
    {
    }

    // I created a dedicated route for the webhook endpoint
    // However, it turned out that PaymentRequests have the infrastructure to handle webhooks.
    // But I haven't checked this implementation yet.
    //
    //    sylius_payment_method_notify:
    //        path: /payment-methods/{code}
    //        defaults:
    //            _controller: sylius.controller.payment_method_notify
    //
    //    sylius_payment_request_notify:
    //        path: /payment-requests/{hash}
    //        defaults:
    //            _controller: sylius.controller.payment_request_notify
    //
    #[Route('/api/v2/shop/stripe/payment/complete', name: 'app_stripe_payment_complete', methods: ['POST'])]
    public function completePayment(
        Request $request,
        StateMachineInterface $stateMachine,
        LoggerInterface $logger,
    ): JsonResponse {
        // To finalize the payment, we should update the payment status below and return a response

        $payload = $request->getContent();
        $logger->info($payload);

//        TODO: Implement the logic to update the payment status
//        $stateMachine->...;

        return $this->json(['responseData' => json_decode($payload, true)]);
    }
}
