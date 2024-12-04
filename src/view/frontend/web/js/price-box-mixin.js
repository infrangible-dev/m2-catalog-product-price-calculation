/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'Infrangible_CatalogProductPriceCalculation/js/model/calculation'
], function ($, calculation) {
    'use strict';

    var activeCalculationCodes = [];

    var priceBoxMixin = {
        activeCalculationCode: null,

        _init: function initPriceBox() {
            if (! this.options.priceConfig.hasOwnProperty('calculatedPrices')) {
                this.options.priceConfig.calculatedPrices = [];
            }
            this.options.priceConfig.calculatedPrices['default'] = this.options.prices;

            calculation.getActiveCalculations().subscribe(function (activeCalculations) {
                console.log(activeCalculations);

                activeCalculationCodes = [];

                for (var i = 0; i < activeCalculations.length; i++) {
                    var activeCalculation = activeCalculations[i];
                    console.log(activeCalculation);

                    activeCalculationCodes.push(activeCalculation.code);
                }

                this.updatePrice();
            }, this);

            this.options.priceTemplate = '<span class="price calculated"><%- data.formatted %></span>';

            this._super();
        },

        updatePrice: function updatePrice(newPrices) {
            console.log(activeCalculationCodes);
            if (activeCalculationCodes.length === 0) {
                return;
            }
            console.log(this.options.priceConfig.calculatedPrices);

            var selectedCalculationCode;

            for (var i = 0; i < activeCalculationCodes.length; i++) {
                var activeCalculationCode = activeCalculationCodes[i];
                console.log(activeCalculationCode);

                if (activeCalculationCode in this.options.priceConfig.calculatedPrices) {
                    var calculationPrices = this.options.priceConfig.calculatedPrices[activeCalculationCode];
                    console.log(calculationPrices);

                    this.options.prices = calculationPrices;
                    selectedCalculationCode = activeCalculationCode;

                    break;
                }
            }

            if (this.activeCalculationCode !== selectedCalculationCode) {
                this._super(newPrices);
                this.activeCalculationCode = selectedCalculationCode;
            }
        }
    };

    return function (targetWidget) {
        $.widget('mage.priceBox', targetWidget, priceBoxMixin);

        return $.mage.priceBox;
    };
});
