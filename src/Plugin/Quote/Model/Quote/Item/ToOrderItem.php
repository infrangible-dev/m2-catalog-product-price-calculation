<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Plugin\Quote\Model\Quote\Item;

use Magento\Quote\Model\Quote\Item;
use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ToOrderItem
{
    /** @noinspection PhpUnusedParameterInspection */
    public function aroundConvert(
        Item\ToOrderItem $subject,
        \Closure $proceed,
        $item,
        $data = []
    ): OrderItemInterface {
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        $orderItem = $proceed(
            $item,
            $data
        );

        if ($item instanceof Item) {
            $additionalOptions = $item->getOptionByCode('price_calculation');

            if ($additionalOptions && $additionalOptions->getValue()) {
                $options = $orderItem->getProductOptions();

                $options[ 'price_calculation' ] = $additionalOptions->getValue();

                $orderItem->setProductOptions($options);
            }
        }

        return $orderItem;
    }
}