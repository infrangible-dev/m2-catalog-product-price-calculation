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

    var activeCalculationCodes;

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

                for (var i = 0; i < activeCalculations.length; i++) {
                    var activeCalculation = activeCalculations[i];
                    console.debug(activeCalculation);

                    activeCalculationCodes.push(activeCalculation.code);
                }

                this.updatePrice();
            }, this);

            this.options.priceTemplateInit = this.options.priceTemplate;
            this.options.priceTemplateCalculated = '<span class="price calculated"><%- data.formatted %></span>';

            this._super();
        },

        updatePrice: function updatePrice(newPrices) {
            var selectedCalculationCode = this.updateCalculationPrices();

            if (newPrices !== undefined || this.activeCalculationCode !== selectedCalculationCode) {
                this._super(newPrices);

                this.activeCalculationCode = selectedCalculationCode;
            }
        },

        updateProductTierPrice: function updateProductTierPrice() {
            var self = this;

            var selectedCalculationCode = self.updateCalculationPrices();

            this._super();

            this.activeCalculationCode = selectedCalculationCode;

            if (selectedCalculationCode) {
                if (self.options.priceConfig.tierPrices) {
                    var tierPricesDisplay = $('ul.prices-tier.items', self.element);

                    if (tierPricesDisplay.length > 0) {
                        console.log(tierPricesDisplay);
                    }
                }
            }
        },

        updateCalculationPrices: function updateCalculationPrices() {
            var selectedCalculationCode;

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

                for (var i = 0; i < activeCalculationCodes.length; i++) {
                    var activeCalculationCode = activeCalculationCodes[i];

                    if (activeCalculationCode in this.options.priceConfig.calculatedPrices) {
                        var calculationPrices = this.options.priceConfig.calculatedPrices[activeCalculationCode];
                        console.debug(calculationPrices);
                        this.options.prices = calculationPrices;

                        if (this.options.priceConfig.calculatedTierPrices) {
                            var calculationTierPrices =
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
