<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Plugin\ConfigurableProduct\Block\Product\View\Type;

use FeWeDev\Base\Json;
use Infrangible\CatalogProductPriceCalculation\Helper\Config;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Configurable
{
    /** @var Json */
    protected $json;

    /** @var Config */
    protected $configHelper;

    public function __construct(Json $json, Config $configHelper)
    {
        $this->json = $json;
        $this->configHelper = $configHelper;
    }

    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        string $result
    ): string {
        $config = $this->json->decode($result);

        $product = $subject->getProduct();

        $config = $this->configHelper->processConfig(
            $config,
            $product
        );

        return $this->json->encode($config);
    }
}
