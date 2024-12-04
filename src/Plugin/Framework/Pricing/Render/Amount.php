<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Plugin\Framework\Pricing\Render;

use Magento\Catalog\Model\Product;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Amount
{
    public function beforeToHtml(\Magento\Framework\Pricing\Render\Amount $subject): void
    {
        $cssClasses = $subject->hasData('css_classes') ? explode(
            ' ',
            $subject->getData('css_classes')
        ) : [];

        $saleableItem = $subject->getSaleableItem();

        if ($saleableItem instanceof Product) {
            $cssClasses[] = 'price-calculation';
        }

        $subject->setData(
            'css_classes',
            join(
                ' ',
                $cssClasses
            )
        );
    }
}
