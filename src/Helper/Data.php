<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Helper;

use Infrangible\CatalogProductPriceCalculation\Model\Calculation\Prices\SimpleFactory;
use Infrangible\CatalogProductPriceCalculation\Model\Calculation\PricesInterface;
use Infrangible\CatalogProductPriceCalculation\Model\CalculationDataInterface;
use Infrangible\CatalogProductPriceCalculation\Model\CalculationInterface;
use Infrangible\CatalogProductPriceCalculation\Model\Calculations;
use Infrangible\CatalogProductPriceCalculation\Model\CalculationsFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Framework\Pricing\Amount\AmountInterface;
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

    /** @var SimpleFactory */
    protected $pricesFactory;

    /** @var AmountFactory */
    protected $amountFactory;

    /** @var Calculations|null */
    private $calculations;

    public function __construct(
        CalculationsFactory $calculationsFactory,
        ManagerInterface $eventManager,
        SimpleFactory $pricesFactory,
        AmountFactory $amountFactory
    ) {
        $this->calculationsFactory = $calculationsFactory;
        $this->eventManager = $eventManager;
        $this->pricesFactory = $pricesFactory;
        $this->amountFactory = $amountFactory;
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
     * @param Item[] $calculatedItems
     *
     * @throws LocalizedException
     */
    public function updateItemPrice(Item $item, array $calculatedItems): bool
    {
        $calculations = $this->getCalculations();

        return $this->updateItemPriceWithCalculations(
            $item,
            $calculatedItems,
            $calculations
        );
    }

    /**
     * @param Item[]                 $calculatedItems
     * @param CalculationInterface[] $calculations
     *
     * @throws LocalizedException
     */
    public function updateItemPriceWithCalculations(Item $item, array $calculatedItems, array $calculations): bool
    {
        if ($item->isDeleted()) {
            return false;
        }

        usort(
            $calculations,
            function (CalculationInterface $calculation1, CalculationInterface $calculation2) {
                return $calculation2->getPriority() <=> $calculation1->getPriority();
            }
        );

        $product = $item->getProduct();

        foreach ($calculations as $calculation) {
            if ($calculation->hasProductCalculation($product) && $calculation->isAvailableForQuoteItem(
                    $item,
                    $calculatedItems
                ) && $calculation->hasQuoteItemQty(
                    $item,
                    $calculatedItems
                )) {

                $prices = $calculation->getProductPrices($product);

                $price = $prices->getFinalPrice();

                $this->setItemCustomPrice(
                    $item,
                    $price
                );

                $item->addOption(
                    new DataObject(
                        [
                            'product' => $item->getProduct(),
                            'code'    => 'price_calculation',
                            'value'   => $calculation->getQuoteItemOptionCode()
                        ]
                    )
                );

                return true;
            }
        }

        $this->removeItemCustomPrice($item);
        $item->removeOption('price_calculation');

        return false;
    }

    public function setItemCustomPrice(Item $item, AmountInterface $price): void
    {
        $item->setCustomPrice($price->getValue());
        $item->setOriginalCustomPrice($price->getValue());
    }

    public function removeItemCustomPrice(Item $item): void
    {
        $item->setData('custom_price');
        $item->setData('original_custom_price');
        $item->setData('calculation_price');
        $item->setData('base_calculation_price');
    }

    /**
     * @throws \Exception
     */
    public function calculatePrices(
        Product $product,
        CalculationDataInterface $calculationData
    ): PricesInterface {
        if ($calculationData->getPrice() === null && $calculationData->getDiscount() === null) {
            throw new \Exception('No fixed priced or discount in price calculation');
        }

        $prices = $this->pricesFactory->create();

        $price = $product->getPriceInfo()->getPrice('final_price');

        if ($price instanceof FinalPrice) {
            if ($calculationData->getPrice() !== null) {
                $prices->setFinalPrice(
                    $this->amountFactory->create(
                        round(
                            $calculationData->getPrice(),
                            2
                        )
                    )
                );
            } else {
                $prices->setFinalPrice(
                    $this->amountFactory->create(
                        round(
                            $price->getValue() * ((100 - $calculationData->getDiscount()) / 100),
                            2
                        )
                    )
                );
            }
        }

        if ($price instanceof \Magento\Bundle\Pricing\Price\FinalPrice) {
            if ($calculationData->getPrice() !== null) {
                $prices->setMinimalPrice(
                    $this->amountFactory->create(
                        round(
                            $price->getMinimalPrice()->getValue(),
                            2
                        )
                    )
                );
            } else {
                $prices->setMinimalPrice(
                    $this->amountFactory->create(
                        round(
                            $price->getMinimalPrice()->getValue() * ((100 - $calculationData->getDiscount()) / 100),
                            2
                        )
                    )
                );
            }

            if ($calculationData->getPrice() !== null) {
                $prices->setMaximalPrice(
                    $this->amountFactory->create(
                        round(
                            $price->getMaximalPrice()->getValue(),
                            2
                        )
                    )
                );
            } else {
                $prices->setMaximalPrice(
                    $this->amountFactory->create(
                        round(
                            $price->getMaximalPrice()->getValue() * ((100 - $calculationData->getDiscount()) / 100),
                            2
                        )
                    )
                );
            }
        }

        $prices->setOldPrice($this->amountFactory->create($product->getFinalPrice()));

        return $prices;
    }
}
