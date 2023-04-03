<?php

declare(strict_types=1);

namespace App\Entity\Return;

use App\Entity\Shipping\ShippingMethod;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Plus\Returns\Domain\Model\ReturnRequest as BaseReturnRequest;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_plus_return_request')]
/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_plus_return_request")
 */
class ReturnRequestDecorated extends BaseReturnRequest
{
    #[ORM\OneToOne(targetEntity: ShippingMethod::class)]
    #[ORM\JoinColumn(name: 'shipping_method_id')]
    protected ShippingMethod $shippingMethod;

    public function getShippingMethod(): ShippingMethod
    {
        return $this->shippingMethod;
    }
    public function setShippingMethod(ShippingMethod $shippingMethod): void
    {
        $this->shippingMethod = $shippingMethod;
    }
}
