<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Observer;

use Infrangible\CatalogProductPriceCalculation\Helper\Data;
use Infrangible\CatalogProductPriceCalculation\Model\CalculationInterface;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CheckoutCartProductAddBefore implements ObserverInterface
{
    /** @var Session */
    protected $checkoutSession;

    /** @var Data */
    protected $calculationHelper;

    public function __construct(Data $calculationHelper, Session $checkoutSession)
    {
        $this->calculationHelper = $calculationHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        /** @var array $requestInfo */
        $requestInfo = $observer->getData('info');

        $qty = $requestInfo[ 'qty' ];

        /** @var Product $product */
        $product = $observer->getData('product');

        $calculations = $this->calculationHelper->getCalculations();

        usort(
            $calculations,
            function (CalculationInterface $calculation1, CalculationInterface $calculation2) {
                return $calculation2->getPriority() <=> $calculation1->getPriority();
            }
        );

        foreach ($calculations as $calculation) {
            if ($calculation->hasProductCalculation($product) && $calculation->isAvailableForProduct() &&
                ! $calculation->hasProductQty((float)$qty)) {

                throw new LocalizedException(__('Not all of your products are available in the requested quantity.'));
            }
        }
    }
}
