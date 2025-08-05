<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Model;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
interface CalculationDataInterface
{
    public function getPrice(): ?float;

    public function getDiscount(): ?int;
}
