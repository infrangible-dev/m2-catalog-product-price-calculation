<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Plugin\Catalog\Block\Product;

use FeWeDev\Base\Json;
use Infrangible\CatalogProductPriceCalculation\Helper\Data;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class View
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

    public function afterGetJsonConfig(\Magento\Catalog\Block\Product\View $subject, string $result): string
    {
        $config = $this->json->decode($result);

        $config[ 'calculatedPrices' ] = [];

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
