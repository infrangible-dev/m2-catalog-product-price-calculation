<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Plugin\Catalog\Block\Product;

use FeWeDev\Base\Json;
use Infrangible\CatalogProductPriceCalculation\Helper\Config;
use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\RegularPrice;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class View
{
    /** @var Json */
    protected $json;

    /** @var Config */
    protected $configHelper;

    public function __construct(Json $json, Config $configHelper)
    {
        $this->json = $json;
        $this->configHelper = $configHelper;
    }

    public function afterGetJsonConfig(\Magento\Catalog\Block\Product\View $subject, string $result): string
    {
        $config = $this->json->decode($result);

        $product = $subject->getProduct();

        if (! $subject->hasOptions()) {
            $priceInfo = $product->getPriceInfo();

            /** @var RegularPrice $regularPriceModel */
            $regularPriceModel = $priceInfo->getPrice('regular_price');

            /** @var FinalPrice $finalPriceModel */
            $finalPriceModel = $priceInfo->getPrice('final_price');

            $config[ 'prices' ] = [
                'baseOldPrice' => [
                    'amount'      => $regularPriceModel->getAmount()->getBaseAmount() * 1,
                    'adjustments' => []
                ],
                'oldPrice'     => [
                    'amount'      => $regularPriceModel->getAmount()->getValue() * 1,
                    'adjustments' => []
                ],
                'basePrice'    => [
                    'amount'      => $finalPriceModel->getAmount()->getBaseAmount() * 1,
                    'adjustments' => []
                ],
                'finalPrice'   => [
                    'amount'      => $finalPriceModel->getAmount()->getValue() * 1,
                    'adjustments' => []
                ]
            ];
        }

        if ($product->getTypeId() === Type::TYPE_CODE) {
            $priceInfo = $product->getPriceInfo();

            /** @var \Magento\Bundle\Pricing\Price\FinalPrice $finalPriceModel */
            $finalPriceModel = $priceInfo->getPrice('final_price');

            $minimalPrice = $finalPriceModel->getMinimalPrice();
            $maximalPrice = $finalPriceModel->getMaximalPrice();

            $config[ 'prices' ][ 'minPrice' ] = [
                'amount'      => $minimalPrice->getValue() * 1,
                'adjustments' => []
            ];

            $config[ 'prices' ][ 'maxPrice' ] = [
                'amount'      => $maximalPrice->getValue() * 1,
                'adjustments' => []
            ];
        }

        $config = $this->configHelper->processConfig(
            $config,
            $product
        );

        return $this->json->encode($config);
    }
}
