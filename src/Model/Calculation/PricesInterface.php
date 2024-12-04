<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Model\Calculation;

use Magento\Framework\Pricing\Amount\AmountInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
interface PricesInterface
{
    public function getFinalPrice(): AmountInterface;

    public function getOldPrice(): ?AmountInterface;

    public function getMinimalPrice(): ?AmountInterface;

    public function getMaximalPrice(): ?AmountInterface;
}
