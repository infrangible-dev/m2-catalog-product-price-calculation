/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'Infrangible_CatalogProductPriceCalculation/js/model/calculation',
    'Magento_Catalog/js/price-box'
], function ($, calculation) {
    'use strict';

    return $.widget('infrangible.priceBox', $.mage.priceBox, {
        options: {
            /**
             * The configuration of the price box, with the prices of the calculations by their codes.
             *
             * @type {?{calculatedPrices: Object, calculatedTierPrices: Object, tierPrices: Array}}
             */
            priceConfig: null
        },

        activeCalculationCode: null,
        activeCalculationCodes: null,

        _init: function () {
            if (!this.options.priceConfig) {
                this.options.priceConfig = {};
            }

            if (!this.options.priceConfig.calculatedPrices) {
                this.options.priceConfig.calculatedPrices = {};
            }

            this.options.priceConfig.calculatedPrices['default'] = this.options.prices;

            calculation.getActiveCalculations().subscribe(function (activeCalculations) {
                this.activeCalculationCodes = activeCalculations.map(function (activeCalculation) {
                    return activeCalculation.code;
                });

                this.updatePrice();
                this.reloadPrice();
            }, this);

            this.options.priceTemplateInit = this.options.priceTemplate;
            this.options.priceTemplateCalculated = '<span class="price calculated"><%- data.formatted %></span>';

            this._super();
        },

        updatePrice: function (newPrices) {
            const selectedCalculationCode = this.updateCalculationPrices();

            if (newPrices !== undefined || this.activeCalculationCode !== selectedCalculationCode) {
                this._super(newPrices);

                this.activeCalculationCode = selectedCalculationCode;
            }
        },

        updateProductTierPrice: function () {
            const selectedCalculationCode = this.updateCalculationPrices();

            this._super();

            this.activeCalculationCode = selectedCalculationCode;
        },

        /**
         * Uses the prices of the active calculation with the highest priority that has prices of the product.
         */
        updateCalculationPrices: function () {
            if (!Array.isArray(this.activeCalculationCodes)) {
                return undefined;
            }

            if (this.activeCalculationCodes.length === 0) {
                this.options.priceTemplate = this.options.priceTemplateInit;

                return undefined;
            }

            this.options.priceTemplate = this.options.priceTemplateCalculated;

            const calculatedPrices = this.options.priceConfig.calculatedPrices || {},
                calculatedTierPrices = this.options.priceConfig.calculatedTierPrices,
                activeCalculationCode = this.activeCalculationCodes.find(function (code) {
                    return Object.prototype.hasOwnProperty.call(calculatedPrices, code);
                });

            if (activeCalculationCode === undefined) {
                return undefined;
            }

            this.options.prices = calculatedPrices[activeCalculationCode];

            if (calculatedTierPrices) {
                this.options.priceConfig.tierPrices = calculatedTierPrices[activeCalculationCode];
            }

            return activeCalculationCode;
        }
    });
});
