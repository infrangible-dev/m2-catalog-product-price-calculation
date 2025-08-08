<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Plugin\ConfigurableProduct\Block\Product\View\Type;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use Infrangible\CatalogProductPriceCalculation\Helper\Config;
use Infrangible\CatalogProductPriceCalculation\Helper\Data;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Configurable
{
    /** @var Json */
    protected $json;

    /** @var Arrays */
    protected $arrays;

    /** @var Data */
    protected $helper;

    /** @var Config */
    protected $configHelper;

    public function __construct(Json $json, Arrays $arrays, Data $helper, Config $configHelper)
    {
        $this->json = $json;
        $this->arrays = $arrays;
        $this->helper = $helper;
        $this->configHelper = $configHelper;
    }

    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        string $result
    ): string {
        $config = $this->json->decode($result);

        $product = $subject->getProduct();

        $config = $this->configHelper->processConfig(
            $config,
            $product
        );

        $config[ 'calculatedOptionPrices' ] = [
            'default' => $this->arrays->getValue(
                $config,
                'optionPrices',
                []
            )
        ];

        foreach ($subject->getAllowProducts() as $usedProduct) {
            foreach ($this->helper->getCalculations() as $calculation) {
                if ($calculation->hasProductCalculation($usedProduct)) {
                    $calculationCode = $calculation->getCode();

                    $prices = $this->helper->getProductCalculation(
                        $calculation,
                        $usedProduct
                    );

                    $config[ 'calculatedOptionPrices' ][ $calculationCode ][ $usedProduct->getId() ] = $prices;

                    $productPriceValue = $this->arrays->getValue(
                        $prices,
                        'finalPrice:amount',
                        0
                    );

                    $tierPrices = $this->helper->getProductTierCalculation(
                        $calculation,
                        $usedProduct
                    );

                    foreach ($tierPrices as $key => $tierPrice) {
                        $tierPriceValue = $this->arrays->getValue(
                            $tierPrice,
                            'price',
                            $productPriceValue
                        );

                        $tierPrices[ $key ][ 'percentage' ] = $this->configHelper->getFormattedSavePercent(
                            $this->configHelper->calculateSavePercent(
                                $productPriceValue,
                                $tierPriceValue
                            )
                        );
                    }

                    $config[ 'calculatedOptionPrices' ][ $calculationCode ][ $usedProduct->getId() ][ 'tierPrices' ] =
                        $tierPrices;
                }
            }
        }

        return $this->json->encode($config);
    }
}
