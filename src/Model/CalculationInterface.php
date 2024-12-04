<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Model;

use Infrangible\CatalogProductPriceCalculation\Model\Calculation\PricesInterface;
use Magento\Catalog\Model\Product;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
interface CalculationInterface
{
    public function getCode(): string;

    public function getPriority(): int;

    public function hasProductCalculation(Product $product): bool;

    public function getProductPrices(Product $product): PricesInterface;

    public function isActive(): bool;

    public function getQuoteItemId(): string;
}