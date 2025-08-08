/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

var config = {
    map: {
        '*': {
            priceBox: 'Infrangible_CatalogProductPriceCalculation/js/price-box'
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Infrangible_CatalogProductPriceCalculation/js/swatch-renderer-mixin': true
            }
        }
    }
};
