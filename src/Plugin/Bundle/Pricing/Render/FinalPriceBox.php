<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Plugin\Bundle\Pricing\Render;

use Magento\Catalog\Model\Product;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class FinalPriceBox
{
    public function aroundToHtml(\Magento\Bundle\Pricing\Render\FinalPriceBox $subject, callable $proceed): string
    {
        $product = $subject->getSaleableItem();

        if ($product instanceof Product) {
            if (! $product->getDataUsingMethod('price_view')) {
                if (! $subject->showRangePrice()) {
                    $subject->setTemplate(
                        'Infrangible_CatalogProductPriceCalculation::bundle/price/final_price.phtml'
                    );
                }
            }
        }

        return $proceed();
    }
}
