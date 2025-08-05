<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Block\Product\Renderer\Listing;

use FeWeDev\Base\Json;
use Infrangible\CatalogProductPriceCalculation\Helper\Config;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Catalog\Model\Product;
use Magento\Framework\Locale\Format;
use Magento\Framework\Stdlib\ArrayUtils;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Simple extends AbstractView
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

    public function setProduct(Product $product): Simple
    {
        $this->product = $product;

        return $this;
    }

    public function getPriceConfigJson(): string
    {
        $product = $this->getProduct();

        $config = [
            'priceFormat' => $this->localeFormat->getPriceFormat(),
            'prices'      => $this->getPrices()
        ];

        $config = $this->configHelper->processConfig(
            $config,
            $product
        );

        return $this->json->encode($config);
    }

    private function getPrices(): array
    {
        $priceInfo = $this->getProduct()->getPriceInfo();

        $regularPrice = $priceInfo->getPrice('regular_price');
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
        ];
    }
}
