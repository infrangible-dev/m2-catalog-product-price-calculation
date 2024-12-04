<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Block\Product\Renderer\Listing;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\ConfigurableProduct\Helper\Data;
use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Variations\Prices;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Locale\Format;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Swatches\Helper\Data as SwatchData;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Model\SwatchAttributesProvider;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Configurable extends \Magento\Swatches\Block\Product\Renderer\Listing\Configurable
{
    /** @var \Infrangible\CatalogProductPriceCalculation\Helper\Data */
    protected $calculationHelper;

    public function __construct(
        Context $context,
        ArrayUtils $arrayUtils,
        EncoderInterface $jsonEncoder,
        Data $helper,
        Product $catalogProduct,
        CurrentCustomer $currentCustomer,
        PriceCurrencyInterface $priceCurrency,
        ConfigurableAttributeData $configurableAttributeData,
        SwatchData $swatchHelper,
        Media $swatchMediaHelper,
        \Infrangible\CatalogProductPriceCalculation\Helper\Data $calculationHelper,
        array $data = [],
        SwatchAttributesProvider $swatchAttributesProvider = null,
        Format $localeFormat = null,
        Prices $variationPrices = null,
        Resolver $layerResolver = null
    ) {
        parent::__construct(
            $context,
            $arrayUtils,
            $jsonEncoder,
            $helper,
            $catalogProduct,
            $currentCustomer,
            $priceCurrency,
            $configurableAttributeData,
            $swatchHelper,
            $swatchMediaHelper,
            $data,
            $swatchAttributesProvider,
            $localeFormat,
            $variationPrices,
            $layerResolver
        );

        $this->calculationHelper = $calculationHelper;
    }

    public function getCalculatedPricesJson(): string
    {
        $calculatedPrices = [];

        $product = $this->getProduct();

        foreach ($this->calculationHelper->getCalculations() as $calculation) {
            if ($calculation->hasProductCalculation($product)) {
                $calculatedPrices[ $calculation->getCode() ] = $this->calculationHelper->getProductCalculation(
                    $calculation,
                    $product
                );
            }
        }

        return $this->jsonEncoder->encode($calculatedPrices);
    }
}
