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

                $config = $this->calculateSavePercent(
                    $config,
                    $calculationCode
                );
            }
        }

        return $config;
    }

    private function calculateSavePercent(array $config, string $code): array
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

            $savePercent = round(100 - ((100 / $productPriceValue) * $tierPriceValue));

            $config = $this->arrays->addDeepValue(
                $config,
                ['calculatedTierPrices', $code, $key, 'savePercent'],
                [
                    'value' => $savePercent,
                    'formatted' => $this->storeHelper->formatNumber(
                        $this->formatPercent($savePercent),
                        0
                    )
                ]
            );
        }

        return $config;
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
