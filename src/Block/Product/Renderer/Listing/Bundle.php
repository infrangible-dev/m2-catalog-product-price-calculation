<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Block\Product\Renderer\Listing;

use FeWeDev\Base\Json;
use Infrangible\CatalogProductPriceCalculation\Helper\Config;
use Magento\Bundle\Pricing\Price\BundleRegularPrice;
use Magento\Bundle\Pricing\Price\FinalPrice;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\RegularPrice;
use Magento\Framework\Locale\Format;
use Magento\Framework\Stdlib\ArrayUtils;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Bundle extends AbstractView
{
    /** @var Json */
    protected $json;

    /** @var Format */
    protected $localeFormat;

    /** @var Config */
    protected $configHelper;

    /** @var Product|null */
    private $product;

    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        Json $json,
        Format $localeFormat,
        Config $configHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );

        $this->json = $json;
        $this->localeFormat = $localeFormat;
        $this->configHelper = $configHelper;
    }

    public function getProduct(): ?Product
    {
        if (! $this->product) {
            $this->product = parent::getProduct();
        }

        return $this->product;
    }

    public function setProduct(Product $product): Bundle
    {
        $this->product = $product;

        return $this;
    }

    public function getPriceConfigJson(): string
    {
        $config = [
            'priceFormat' => $this->localeFormat->getPriceFormat(),
            'prices'      => $this->getPrices()
        ];

        $product = $this->getProduct();

        $config = $this->configHelper->processConfig(
            $config,
            $product
        );

        return $this->json->encode($config);
    }

    private function getPrices(): array
    {
        $priceInfo = $this->getProduct()->getPriceInfo();

        /** @var BundleRegularPrice $regularPrice */
        $regularPrice = $priceInfo->getPrice('regular_price');
        /** @var FinalPrice $finalPrice */
        $finalPrice = $priceInfo->getPrice('final_price');

        return [
            'baseOldPrice' => [
                'amount' => $this->localeFormat->getNumber($regularPrice->getAmount()->getBaseAmount()),
            ],
            'oldPrice'     => [
                'amount' => $this->localeFormat->getNumber($regularPrice->getAmount()->getValue()),
            ],
            'basePrice'    => [
                'amount' => $this->localeFormat->getNumber($finalPrice->getAmount()->getBaseAmount()),
            ],
            'finalPrice'   => [
                'amount' => $this->localeFormat->getNumber($finalPrice->getAmount()->getValue()),
            ],
            'minPrice'     => [
                'amount' => $this->localeFormat->getNumber($finalPrice->getMinimalPrice()->getValue()),
            ],
            'maxPrice'     => [
                'amount' => $this->localeFormat->getNumber($finalPrice->getMaximalPrice()->getValue()),
            ]
        ];
    }
}
