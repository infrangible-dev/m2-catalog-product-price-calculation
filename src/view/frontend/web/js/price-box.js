/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'Infrangible_CatalogProductPriceCalculation/js/model/calculation',
    'Magento_Catalog/js/price-box'
], function ($, calculation) {
    'use strict';

    let activeCalculationCodes;

    $.widget('infrangible.priceBox', $.mage.priceBox, {
        activeCalculationCode: null,

        _init: function initPriceBox() {
            if (! this.options.priceConfig.hasOwnProperty('calculatedPrices')) {
                this.options.priceConfig.calculatedPrices = [];
            }
            this.options.priceConfig.calculatedPrices['default'] = this.options.prices;

            calculation.getActiveCalculations().subscribe(function (activeCalculations) {
                console.debug(activeCalculations);

                activeCalculationCodes = [];

                for (let i = 0; i < activeCalculations.length; i++) {
                    const activeCalculation = activeCalculations[i];
                    console.debug(activeCalculation);

                    activeCalculationCodes.push(activeCalculation.code);
                }

                this.updatePrice();
                this.reloadPrice();
            }, this);

            this.options.priceTemplateInit = this.options.priceTemplate;
            this.options.priceTemplateCalculated = '<span class="price calculated"><%- data.formatted %></span>';

            this._super();
        },

        updatePrice: function updatePrice(newPrices) {
            const selectedCalculationCode = this.updateCalculationPrices();

            if (newPrices !== undefined || this.activeCalculationCode !== selectedCalculationCode) {
                this._super(newPrices);

                this.activeCalculationCode = selectedCalculationCode;
            }
        },

        updateProductTierPrice: function updateProductTierPrice() {
            const self = this;

            const selectedCalculationCode = self.updateCalculationPrices();

            this._super();

            this.activeCalculationCode = selectedCalculationCode;
        },

        updateCalculationPrices: function updateCalculationPrices() {
            let selectedCalculationCode;

            console.debug(activeCalculationCodes);

            if ($.isArray(activeCalculationCodes)) {
                if (activeCalculationCodes.length === 0) {
                    this.options.priceTemplate = this.options.priceTemplateInit;
                    return;
                } else {
                    this.options.priceTemplate = this.options.priceTemplateCalculated;
                }

                console.debug(this.options.priceConfig.calculatedPrices);
                console.debug(this.options.priceConfig.calculatedTierPrices);

                for (let i = 0; i < activeCalculationCodes.length; i++) {
                    const activeCalculationCode = activeCalculationCodes[i];

                    if (activeCalculationCode in this.options.priceConfig.calculatedPrices) {
                        const calculationPrices = this.options.priceConfig.calculatedPrices[activeCalculationCode];
                        console.debug(calculationPrices);
                        this.options.prices = calculationPrices;

                        if (this.options.priceConfig.calculatedTierPrices) {
                            const calculationTierPrices =
                                this.options.priceConfig.calculatedTierPrices[activeCalculationCode];
                            console.debug(calculationTierPrices);
                            this.options.priceConfig.tierPrices = calculationTierPrices;
                        }

                        selectedCalculationCode = activeCalculationCode;

                        break;
                    }
                }
            }

            return selectedCalculationCode;
        }
    });

    return $.infrangible.priceBox;
});
