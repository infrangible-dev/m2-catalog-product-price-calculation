/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'Infrangible_CatalogProductPriceCalculation/js/model/calculation'
], function ($, calculation) {
    'use strict';

    /**
     * The configuration of the configurable product, with the prices of the calculations by their codes.
     *
     * @typedef {Object} SwatchJsonConfig
     * @property {?Object} calculatedPrices the prices of the product by calculation code
     * @property {?Object} calculatedOptionPrices the prices of the products of the options by calculation code
     * @property {Object} prices the prices that the price box shows
     * @property {Object} optionPrices the prices of the products of the options that the price box shows
     */

    return function (widget) {
        return $.widget('mage.SwatchRenderer', widget, {
            activeCalculationCodes: null,

            _init: function () {
                calculation.getActiveCalculations().subscribe(function (activeCalculations) {
                    this.activeCalculationCodes = activeCalculations.map(function (activeCalculation) {
                        return activeCalculation.code;
                    });

                    this._UpdatePrice();
                }, this);

                this._super();
            },

            _OnClick: function ($this, $widget) {
                const $priceBox = $widget.element.parents(this.options.selectorProduct)
                    .find(this.options.selectorProductPrice);

                if (!$priceBox.is(':data(mage-priceBox)')) {
                    $priceBox.data('mage-priceBox', true);
                }

                this._super($this, $widget);
            },

            _UpdatePrice: function () {
                this.activeCalculationCode = this.updateCalculationPrices();

                this._super();
            },

            /**
             * Uses the prices of the active calculation with the highest priority that has prices of the product and
             * of its products.
             */
            updateCalculationPrices: function () {
                if (!Array.isArray(this.activeCalculationCodes)) {
                    return undefined;
                }

                /** @type {SwatchJsonConfig} */
                const jsonConfig = this.options.jsonConfig,
                    calculatedPrices = jsonConfig.calculatedPrices || {},
                    calculatedOptionPrices = jsonConfig.calculatedOptionPrices || {},
                    priceCode = this.activeCalculationCodes.find(function (code) {
                        return Object.prototype.hasOwnProperty.call(calculatedPrices, code);
                    }),
                    optionPriceCode = this.activeCalculationCodes.find(function (code) {
                        return Object.prototype.hasOwnProperty.call(calculatedOptionPrices, code);
                    });
                let selectedCalculationCode;

                if (priceCode !== undefined) {
                    jsonConfig.prices = calculatedPrices[priceCode];
                    selectedCalculationCode = priceCode;
                }

                if (optionPriceCode !== undefined) {
                    jsonConfig.optionPrices = calculatedOptionPrices[optionPriceCode];
                    selectedCalculationCode = optionPriceCode;
                }

                return selectedCalculationCode;
            }
        });
    };
});
