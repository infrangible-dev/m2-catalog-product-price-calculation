/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'Infrangible_CatalogProductPriceCalculation/js/model/calculation'
], function ($, calculation) {
    'use strict';

    var activeCalculationCodes;

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            _init: function() {
                calculation.getActiveCalculations().subscribe(function (activeCalculations) {
                    console.debug(activeCalculations);

                    activeCalculationCodes = [];

                    for (var i = 0; i < activeCalculations.length; i++) {
                        var activeCalculation = activeCalculations[i];
                        console.debug(activeCalculation);

                        activeCalculationCodes.push(activeCalculation.code);
                    }

                    this._UpdatePrice();
                }, this);

                this._super();
            },

            _OnClick: function($this, $widget) {
                var $priceBox = $widget.element.parents(this.options.selectorProduct)
                    .find(this.options.selectorProductPrice);

                if (! $priceBox.is(':data(mage-priceBox)')) {
                    $priceBox.data('mage-priceBox', true);
                }

                this._super($this, $widget);
            },

            _UpdatePrice: function() {
                this.activeCalculationCode = this.updateCalculationPrices();

                this._super();
            },

            updateCalculationPrices: function() {
                var selectedCalculationCode;

                console.debug(activeCalculationCodes);

                if ($.isArray(activeCalculationCodes)) {
                    console.debug(this.options.jsonConfig.calculatedPrices);
                    console.debug(this.options.jsonConfig.calculatedOptionPrices);

                    var i, activeCalculationCode;

                    for (i = 0; i < activeCalculationCodes.length; i++) {
                        activeCalculationCode = activeCalculationCodes[i];

                        if (activeCalculationCode in this.options.jsonConfig.calculatedPrices) {
                            var calculatedPrices = this.options.jsonConfig.calculatedPrices[activeCalculationCode];
                            console.debug(calculatedPrices);
                            this.options.jsonConfig.prices = calculatedPrices;

                            selectedCalculationCode = activeCalculationCode;

                            break;
                        }
                    }

                    for (i = 0; i < activeCalculationCodes.length; i++) {
                        activeCalculationCode = activeCalculationCodes[i];

                        if (activeCalculationCode in this.options.jsonConfig.calculatedOptionPrices) {
                            var calculatedOptionPrices =
                                this.options.jsonConfig.calculatedOptionPrices[activeCalculationCode];
                            console.debug(calculatedOptionPrices);
                            this.options.jsonConfig.optionPrices = calculatedOptionPrices;

                            selectedCalculationCode = activeCalculationCode;

                            break;
                        }
                    }
                }

                return selectedCalculationCode;
            }
        });

        return $.mage.SwatchRenderer;
    };
});
