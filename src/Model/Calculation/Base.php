<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductPriceCalculation\Model\Calculation;

use Infrangible\CatalogProductPriceCalculation\Model\Calculation\Prices\SimpleFactory;
use Infrangible\CatalogProductPriceCalculation\Model\CalculationInterface;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Framework\Pricing\Amount\AmountInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
abstract class Base implements CalculationInterface
{
    /** @var SimpleFactory */
    protected $pricesFactory;

    /** @var AmountFactory */
    protected $amountFactory;

    /** @var string */
    private $code;

    /** @var int */
    private $priority;

    public function __construct(
        SimpleFactory $pricesFactory,
        AmountFactory $amountFactory
    ) {
        $this->pricesFactory = $pricesFactory;
        $this->amountFactory = $amountFactory;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    protected function createAmount(float $value): AmountInterface
    {
        return $this->amountFactory->create(
            round(
                $value,
                2
            )
        );
    }
}
