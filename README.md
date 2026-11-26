# Infrangible Catalog Product Price Calculation

The base of personal prices: other modules (for example
[Infrangible Catalog Product Customer Price](https://www.infrangible.dev/modules/catalog-product-customer-price)) add
calculations of product prices, and this module shows the calculated prices on product pages and in product lists and
sets them as prices of the items in the cart. The calculations of the visitor come from the private content of Magento,
so pages in the full page cache stay the same for every visitor.

## Calculations

A module adds calculations with an observer of the event `catalog_product_price_calculation`, which gets the object
`calculations` (`Infrangible\CatalogProductPriceCalculation\Model\Calculations`). A calculation implements
`Model\CalculationInterface`, usually by extending `Model\Calculation\Base`:

- code and priority: if several calculations of a product are available, the one with the highest priority is used
- the prices and tier prices of a product (`getProductPrices()`, `getProductTierPrices()`)
- whether it is available for the customer and for a quantity in the cart, and the value that the order item gets as
  product option `price_calculation`

`Helper\Data::calculatePrices()` calculates the prices of a product with a fixed price (in the base currency) or a
discount in percent (`Model\CalculationDataInterface`). The prices are shown like the prices of Magento, in the currency
of the store and with the tax if the store shows prices with tax; the cart gets the calculated catalog price.

Supported are simple, virtual, downloadable, configurable (also the prices of the selected product) and bundle products.

## Configuration

Stores > Configuration > Infrangible Solutions > Catalog Product Price Calculation > General:

- Avoid Price Jump: hides the prices until the calculated prices of the visitor are loaded

## Requirements

- Magento Open Source or Adobe Commerce 2.4.6 or newer
- PHP 8.1 or newer

## Installation

```bash
composer require infrangible/m2-catalog-product-price-calculation
bin/magento module:enable Infrangible_CatalogProductPriceCalculation
bin/magento setup:upgrade
```

In production mode, also run `bin/magento setup:di:compile` and `bin/magento setup:static-content:deploy`.

## License

Infrangible Catalog Product Price Calculation is licensed under the MIT License - see the LICENSE file for details.
