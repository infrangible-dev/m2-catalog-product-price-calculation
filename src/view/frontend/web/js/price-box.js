/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'Infrangible_CatalogProductPriceCalculation/js/model/calculation',
    'Magento_Catalog/js/price-box'
], function ($, calculation) {
    'use strict';

    var activeCalculationCodes = [];

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

            this.options.priceTemplate = '<span class="price calculated"><%- data.formatted %></span>';

            this._super();
        },

        updatePrice: function updatePrice(newPrices) {
            console.debug(activeCalculationCodes);
            if (activeCalculationCodes.length === 0) {
                return;
            }
            console.debug(this.options.priceConfig.calculatedPrices);

            var selectedCalculationCode;

            for (var i = 0; i < activeCalculationCodes.length; i++) {
                var activeCalculationCode = activeCalculationCodes[i];
                console.debug(activeCalculationCode);

                if (activeCalculationCode in this.options.priceConfig.calculatedPrices) {
                    var calculationPrices = this.options.priceConfig.calculatedPrices[activeCalculationCode];
                    console.debug(calculationPrices);

                    this.options.prices = calculationPrices;
                    selectedCalculationCode = activeCalculationCode;

                    break;
                }
            }

            if (newPrices !== undefined || this.activeCalculationCode !== selectedCalculationCode) {
                this._super(newPrices);
                this.activeCalculationCode = selectedCalculationCode;
            }
        }
    });

    return $.infrangible.priceBox;
});
