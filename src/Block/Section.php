<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Block;

use FeWeDev\Base\Json;
use Infrangible\CatalogProductPriceCalculation\Helper\Data;
use Magento\Framework\View\Element\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Section extends Template
{
    /** @var Data */
    protected $helper;

    /** @var Json */
    protected $json;

    public function __construct(Template\Context $context, Data $helper, Json $json, array $data = [])
    {
        parent::__construct(
            $context,
            $data
        );

        $this->helper = $helper;
        $this->json = $json;
    }

    public function getCalculationsConfig(): string
    {
        $calculationConfig = [];

        foreach ($this->helper->getCalculations() as $calculation) {
            $calculationConfig[ $calculation->getCode() ] = $calculation->getPriority();
        }

        return $this->json->encode($calculationConfig);
    }
}
