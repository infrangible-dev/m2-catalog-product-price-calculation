<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\CustomerData;

use Infrangible\CatalogProductPriceCalculation\Helper\Data;
use Magento\Customer\CustomerData\SectionSourceInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Section implements SectionSourceInterface
{
    /** @var Data */
    protected $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    public function getSectionData(): array
    {
        $activeCalculationCodes = [];

        foreach ($this->helper->getCalculations() as $calculation) {
            if ($calculation->isAvailableForProduct()) {
                $activeCalculationCodes[] = $calculation->getCode();
            }
        }

        return [
            'activeCalculationCodes' => $activeCalculationCodes
        ];
    }
}
