<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Model;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Calculations
{
    /** @var CalculationInterface[] */
    private $calculations = [];

    /**
     * @return CalculationInterface[]
     */
    public function getCalculations(): array
    {
        return $this->calculations;
    }

    /**
     * @param CalculationInterface[] $calculations
     */
    public function setCalculations(array $calculations): void
    {
        $this->calculations = $calculations;
    }

    public function addCalculation(CalculationInterface $calculation): void
    {
        $this->calculations[] = $calculation;
    }
}
