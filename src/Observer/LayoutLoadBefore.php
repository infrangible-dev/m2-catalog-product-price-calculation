<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Observer;

use Infrangible\Core\Helper\Stores;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Page\Config;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class LayoutLoadBefore implements ObserverInterface
{
    /** @var Stores */
    protected $storeHelper;

    /** @var Config */
    protected $pageConfig;

    public function __construct(Stores $storeHelper, Config $pageConfig)
    {
        $this->storeHelper = $storeHelper;
        $this->pageConfig = $pageConfig;
    }

    public function execute(Observer $observer): void
    {
        if ($this->storeHelper->getStoreConfigFlag(
            'infrangible_catalogproductpricecalculation/general/avoid_price_jump'
        )) {
            $this->pageConfig->addBodyClass('price-calculation-visibility');
        }
    }
}
