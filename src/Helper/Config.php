<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Helper;

use FeWeDev\Base\Arrays;
use Infrangible\Core\Helper\Product;
use Infrangible\Core\Helper\Stores;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Config
{
    /** @var Data */
    protected $helper;

    /** @var Product */
    protected $productHelper;

    /** @var Arrays */
    protected $arrays;

    /** @var Stores */
    protected $storeHelper;

    public function __construct(
        Data $helper,
        Product $productHelper,
        Arrays $arrays,
        Stores $storeHelper
    ) {
        $this->helper = $helper;
        $this->productHelper = $productHelper;
        $this->arrays = $arrays;
        $this->storeHelper = $storeHelper;
    }

    public function processConfig(array $config, \Magento\Catalog\Model\Product $product): array
    {
        $config[ 'calculatedPrices' ] = [
            'default' => $this->arrays->getValue(
                $config,
                'prices',
                []
            )
        ];

        $config[ 'calculatedTierPrices' ] = [
            'default' => $this->arrays->getValue(
                $config,
                'tierPrices',
                $this->productHelper->getTierPricesByProduct($product)
            )
        ];

        foreach ($this->helper->getCalculations() as $calculation) {
            if ($calculation->hasProductCalculation($product)) {
                $calculationCode = $calculation->getCode();

                $config[ 'calculatedPrices' ][ $calculationCode ] = $this->helper->getProductCalculation(
                    $calculation,
                    $product
                );

                $config[ 'calculatedTierPrices' ][ $calculationCode ] = $this->helper->getProductTierCalculation(
                    $calculation,
                    $product
                );

                $config = $this->processSavePercent(
                    $config,
                    $calculationCode
                );
            }
        }

        return $config;
    }

    private function processSavePercent(array $config, string $code): array
    {
        $productPriceValue = $this->arrays->getValue(
            $config,
            sprintf(
                'calculatedPrices:%s:finalPrice:amount',
                $code
            )
        );

        $tierPrices = $this->arrays->getValue(
            $config,
            sprintf(
                'calculatedTierPrices:%s',
                $code
            ),
            []
        );

        foreach ($tierPrices as $key => $tierPrice) {
            $tierPriceValue = $this->arrays->getValue(
                $tierPrice,
                'price',
                $productPriceValue
            );

            $config = $this->arrays->addDeepValue(
                $config,
                ['calculatedTierPrices', $code, $key, 'savePercent'],
                $this->getSavePercent(
                    $productPriceValue,
                    $tierPriceValue
                )
            );
        }

        return $config;
    }

    private function getSavePercent(float $productPriceValue, float $tierPriceValue): array
    {
        $savePercent = $this->calculateSavePercent(
            $productPriceValue,
            $tierPriceValue
        );

        return [
            'value'     => $savePercent,
            'formatted' => $this->getFormattedSavePercent($savePercent)
        ];
    }

    public function calculateSavePercent(float $productPriceValue, float $tierPriceValue): float
    {
        return round(100 - ((100 / $productPriceValue) * $tierPriceValue));
    }

    public function getFormattedSavePercent(float $savePercent): string
    {
        return $this->storeHelper->formatNumber(
            $this->formatPercent($savePercent),
            0
        );
    }

    private function formatPercent(float $percent): int
    {
        return intval(
            rtrim(
                rtrim(
                    number_format(
                        $percent,
                        2
                    ),
                    '0'
                ),
                '.'
            )
        );
    }
}
