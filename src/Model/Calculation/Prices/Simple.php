<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Model\Calculation\Prices;

use Infrangible\CatalogProductPriceCalculation\Model\Calculation\PricesInterface;
use Magento\Framework\Pricing\Amount\AmountInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Simple implements PricesInterface
{
    /** @var AmountInterface */
    private $finalPrice;

    /** @var AmountInterface|null */
    private $oldPrice;

    /** @var AmountInterface|null */
    private $minimalPrice;

    /** @var AmountInterface|null */
    private $maximalPrice;

    public function getFinalPrice(): AmountInterface
    {
        return $this->finalPrice;
    }

    public function setFinalPrice(AmountInterface $finalPrice): void
    {
        $this->finalPrice = $finalPrice;
    }

    public function getOldPrice(): ?AmountInterface
    {
        return $this->oldPrice;
    }

    public function setOldPrice(?AmountInterface $oldPrice = null): void
    {
        $this->oldPrice = $oldPrice;
    }

    public function getMinimalPrice(): ?AmountInterface
    {
        return $this->minimalPrice;
    }

    public function setMinimalPrice(?AmountInterface $minimalPrice): void
    {
        $this->minimalPrice = $minimalPrice;
    }

    public function getMaximalPrice(): ?AmountInterface
    {
        return $this->maximalPrice;
    }

    public function setMaximalPrice(?AmountInterface $maximalPrice): void
    {
        $this->maximalPrice = $maximalPrice;
    }
}
