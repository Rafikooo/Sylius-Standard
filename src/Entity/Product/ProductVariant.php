<?php

declare(strict_types=1);

namespace App\Entity\Product;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product_variant")
 */
#[ApiResource(normalizationContext: ['groups' => ['shop:product_variant:read']])]
class ProductVariant extends BaseProductVariant
{
    protected function createTranslation(): ProductVariantTranslationInterface
    {
        return new ProductVariantTranslation();
    }

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Product\ProductComposition")
     * @ORM\JoinColumn(name="product_composition_id", referencedColumnName="id", nullable=true)
     */
    #[ApiProperty(readableLink: false)]
    #[Groups(['shop:product_variant:read'])]
    private ProductComposition $productComposition;

    public function getProductComposition(): ProductComposition
    {
        return $this->productComposition;
    }

    public function setProductComposition(ProductComposition $productComposition): void
    {
        $this->productComposition = $productComposition;
    }


}
