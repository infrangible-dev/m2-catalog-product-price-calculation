<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Helper;

use Infrangible\CatalogProductPriceCalculation\Model\CalculationInterface;
use Infrangible\CatalogProductPriceCalculation\Model\Calculations;
use Infrangible\CatalogProductPriceCalculation\Model\CalculationsFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Data
{
    /** @var CalculationsFactory */
    protected $calculationsFactory;

    /** @var ManagerInterface */
    protected $eventManager;

    /** @var Calculations|null */
    private $calculations;

    public function __construct(CalculationsFactory $calculationsFactory, ManagerInterface $eventManager)
    {
        $this->calculationsFactory = $calculationsFactory;
        $this->eventManager = $eventManager;
    }

    /**
     * @return CalculationInterface[]
     */
    public function getCalculations(): array
    {
        if ($this->calculations === null) {
            $this->calculations = $this->calculationsFactory->create();

            $this->eventManager->dispatch(
                'catalog_product_price_calculation',
                ['calculations' => $this->calculations]
            );
        }

        return $this->calculations->getCalculations();
    }

    public function getProductCalculation(CalculationInterface $calculation, Product $product): array
    {
        $calculationPrices = $calculation->getProductPrices($product);

        $calculationData = [
            'basePrice'  => [
                'amount'      => $calculationPrices->getFinalPrice()->getBaseAmount(),
                'adjustments' => []
            ],
            'finalPrice' => [
                'amount'      => $calculationPrices->getFinalPrice()->getValue(),
                'adjustments' => []
            ]
        ];

        if ($calculationPrices->getOldPrice() !== null) {
            $calculationData[ 'baseOldPrice' ] = [
                'amount'      => $calculationPrices->getOldPrice()->getBaseAmount(),
                'adjustments' => []
            ];

            $calculationData[ 'oldPrice' ] = [
                'amount'      => $calculationPrices->getOldPrice()->getValue(),
                'adjustments' => []
            ];
        }

        return $calculationData;
    }

    /**
     * @throws LocalizedException
     */
    public function updateItemPrice(Item $item)
    {
        if ($item->isDeleted()) {
            return;
        }

        $calculations = $this->getCalculations();

        usort(
            $calculations,
            function (CalculationInterface $calculation1, CalculationInterface $calculation2) {
                return $calculation2->getPriority() <=> $calculation1->getPriority();
            }
        );

        $product = $item->getProduct();

        foreach ($calculations as $calculation) {
            if ($calculation->isActive() && $calculation->hasProductCalculation($product)) {
                $prices = $calculation->getProductPrices($product);

                $item->setCustomPrice($prices->getFinalPrice()->getValue());
                $item->setOriginalCustomPrice($prices->getFinalPrice()->getValue());
                $item->addOption(
                    new DataObject(
                        [
                            'product' => $item->getProduct(),
                            'code'    => 'price_calculation',
                            'value'   => $calculation->getQuoteItemOptionCode()
                        ]
                    )
                );

                return;
            }
        }

        $item->setData('custom_price');
        $item->setData('original_custom_price');
        $item->setData('calculation_price');
        $item->setData('base_calculation_price');
        $item->removeOption('price_calculation');
    }
}
