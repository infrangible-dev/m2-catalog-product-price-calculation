<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Model;

use Infrangible\CatalogProductPriceCalculation\Model\Calculation\PricesInterface;
use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote\Item;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
interface CalculationInterface
{
    public function getCode(): string;

    public function getPriority(): int;

    public function getProductPrices(Product $product): PricesInterface;

    /**
     * @return PricesInterface[]
     */
    public function getProductTierPrices(Product $product): array;

    public function hasProductCalculation(Product $product): bool;

    public function isAvailableForProduct(): bool;

    public function hasProductQty(float $qty): bool;

    /**
     * @param Item[] $calculatedItems
     */
    public function isAvailableForQuoteItem(Item $item, array $calculatedItems): bool;

    /**
     * @param Item[] $calculatedItems
     */
    public function hasQuoteItemQty(Item $item, array $calculatedItems): bool;

    public function getQuoteItemOptionCode(): string;
}
