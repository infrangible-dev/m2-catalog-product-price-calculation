<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Block\Product\Renderer\Listing;

use FeWeDev\Base\Json;
use Infrangible\CatalogProductPriceCalculation\Helper\Data;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Catalog\Model\Product;
use Magento\Framework\Locale\Format;
use Magento\Framework\Pricing\PriceInfo\Base;
use Magento\Framework\Stdlib\ArrayUtils;

/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Simple extends AbstractView
{
    /** @var Json */
    protected $json;

    /** @var Format */
    protected $localeFormat;

    /** @var Data */
    protected $helper;

    /** @var Product|null */
    private $product;

    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        Json $json,
        Format $localeFormat,
        Data $helper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );

        $this->json = $json;
        $this->localeFormat = $localeFormat;
        $this->helper = $helper;
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

    public function getPriceFormatJson(): ?string
    {
        return $this->json->encode($this->localeFormat->getPriceFormat());
    }

    public function getPricesJson(): ?string
    {
        return $this->json->encode(
            $this->getFormattedPrices($this->getProduct()->getPriceInfo())
        );
    }

    public function getFormattedPrices(Base $priceInfo): array
    {
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

    public function getCalculatedPricesJson(): ?string
    {
        $calculatedPrices = [];

        $product = $this->getProduct();

        foreach ($this->helper->getCalculations() as $calculation) {
            if ($calculation->hasProductCalculation($product)) {
                $calculatedPrices[ $calculation->getCode() ] = $this->helper->getProductCalculation(
                    $calculation,
                    $product
                );
            }
        }

        return $this->json->encode($calculatedPrices);
    }
}
