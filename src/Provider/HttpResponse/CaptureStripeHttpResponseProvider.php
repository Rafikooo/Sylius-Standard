<?php

declare(strict_types=1);

namespace App\Provider\HttpResponse;

use Sylius\Bundle\PaymentBundle\Provider\HttpResponseProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag('sylius.payment_request.provider.http_response.stripe', ['action' => 'capture'])]
final class CaptureStripeHttpResponseProvider implements HttpResponseProviderInterface
{
    public function supports(RequestConfiguration $requestConfiguration, PaymentRequestInterface $paymentRequest,): bool
    {
        return $paymentRequest->getAction() === PaymentRequestInterface::ACTION_CAPTURE;
    }

    public function getResponse(RequestConfiguration $requestConfiguration, PaymentRequestInterface $paymentRequest): Response
    {
        $data = $paymentRequest->getResponseData();

        return new RedirectResponse($data['requestUri']);
    }

}
