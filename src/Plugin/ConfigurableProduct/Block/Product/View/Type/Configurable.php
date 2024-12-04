<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Plugin\ConfigurableProduct\Block\Product\View\Type;

use FeWeDev\Base\Json;
use Infrangible\CatalogProductPriceCalculation\Helper\Data;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Configurable
{
    /** @var Json */
    protected $json;

    /** @var Data */
    protected $helper;

    public function __construct(Json $json, Data $helper)
    {
        $this->json = $json;
        $this->helper = $helper;
    }

    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        string $result
    ): string {
        $config = $this->json->decode($result);

        $config['calculatedPrices'] = [];

        $product = $subject->getProduct();

        foreach ($this->helper->getCalculations() as $calculation) {
            if ($calculation->hasProductCalculation($product)) {
                $config[ 'calculatedPrices' ][ $calculation->getCode() ] = $this->helper->getProductCalculation(
                    $calculation,
                    $product
                );
            }
        }

        return $this->json->encode($config);
    }
}
