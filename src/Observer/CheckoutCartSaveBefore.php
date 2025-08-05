<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Observer;

use Infrangible\CatalogProductPriceCalculation\Helper\Data;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Item;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CheckoutCartSaveBefore implements ObserverInterface
{
    /** @var Data */
    protected $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Cart $cart */
        $cart = $observer->getData('cart');

        $quote = $cart->getQuote();

        $items = $quote->getId() ? $cart->getItems() : $quote->getItemsCollection();

        $calculatedItems = [];

        /** @var Item $item */
        foreach ($items as $item) {
            if ($this->helper->updateItemPrice(
                $item,
                $calculatedItems
            )) {
                $calculatedItems[] = $item;
            }
        }
    }
}
