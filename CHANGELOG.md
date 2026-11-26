# Changelog

## 4.0.1

### Changed

- Supports PHP 8.1 again, like Magento 2.4.6: the Adobe Commerce Marketplace requires modules to allow every PHP
  version of the Magento versions they support.
- The JavaScript follows the ESLint rules of the Magento coding standard, which the Marketplace checks as well;
  the behavior is unchanged.

## 4.0.0

### Breaking changes

- Requires Magento 2.4.6 or newer and PHP 8.2 or newer. Older versions of Magento and PHP 7.1 to 8.1 are no longer
  supported; use version 3.x for these.
- Requires `infrangible/m2-core` 5.0, `infrangible/m2-foundation` 3.0, `fewedev/base` 2.4 and the Magento modules it
  uses; before, the module used m2-core and fewedev/base without requiring them.
- All classes have native types, and the constructors use constructor property promotion. The constructors of
  `Helper\Data`, `Helper\Config`, the plugins, the observers, `CustomerData\Section` and the renderers of product lists
  changed. The renderers of simple, downloadable and bundle products extend
  `Block\Product\Renderer\Listing\AbstractRenderer`.
- The methods of `CalculationInterface` that check the availability and return prices declare `LocalizedException`.
  `Helper\Data::calculatePrices()` throws a `LocalizedException` instead of an `Exception` if the calculation has
  neither a fixed price nor a discount.
- The plugins of the bundle price box and of the conversion of quote items are before and after plugins instead of
  around plugins.
- `PricesInterface` has the new method `getCatalogPrice()`; own implementations of the interface have to add it.

### Fixed

- In stores that show prices with tax, the calculated prices were shown without tax, unlike the other prices.
- Fixed prices were not converted into the currency of the store.
- A fixed price of a bundle product did not change its minimal and maximal price.
- Products whose final price was not the price model of Magento failed with an error instead of showing the price.
- The saved percentage of tier prices failed for a price of 0 (division by zero).
- A failing calculation broke the product page and the private content; it is skipped and logged now.
- The quantity check when adding products to the cart failed for requests without a quantity.
- The removed price of a cart item stayed in the database, because the values were not saved.
- The active calculations of the storefront were reset to an object instead of a list, and calculations could be
  activated twice.
- The swatches of product lists failed for products without calculated prices.
- Prices of products got the CSS class `price-calculation` once more each time they were rendered.

### Added

- `PricesInterface::getCatalogPrice()`: the calculated price as catalog price, which the cart gets.
- `Helper\Data::getSortedCalculations()` and `scaleAmount()`.
- Translations in 24 languages.
- Unit tests.
- README with the calculations and the configuration.

### Changed

- The storefront no longer writes debug messages to the console of the browser.
- The module is loaded after the modules it uses.
- Checks with PHPStan level 9, the Magento coding standard and unit tests on every push.
