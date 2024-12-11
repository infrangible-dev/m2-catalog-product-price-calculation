/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'Infrangible_CatalogProductPriceCalculation/js/model/calculation'
], function ($, Component, customerData, calculation) {
    'use strict';

    return Component.extend({
        defaults: {
            calculations: []
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();

            var reloadSection = false;

            var cartObservable = customerData.get('cart');
            if (cartObservable) {
                var cart = cartObservable();
                if (cart && cart.summary_count > 0) {
                    reloadSection = true;
                }
            }

            var customerObservable = customerData.get('customer');
            if (customerObservable) {
                var customer = customerObservable();
                if (customer && customer.fullname) {
                    reloadSection = true;
                }
            }

            if (reloadSection) {
                $(document).on('customer-data-reload', function(event, sectionNames) {
                    for (var i = 0; i < sectionNames.length; i++) {
                        if (sectionNames[i] === 'catalog-product-price-calculation') {
                            setTimeout(function () {
                                calculation.addActiveCalculation('default', 0);
                            }, 100);
                        }
                    }
                });
                customerData.reload(['catalog-product-price-calculation']);
            } else {
                var self = this;

                $(document).ready(function() {
                    var catalogProductPriceSection = customerData.get('catalog-product-price-calculation');
                    var catalogProductPriceData = catalogProductPriceSection();
                    if (! $.isEmptyObject(catalogProductPriceData)) {
                        console.log(catalogProductPriceData);
                        if (catalogProductPriceData.activeCalculationCodes) {
                            for (var i = 0; i < catalogProductPriceData.activeCalculationCodes.length; i++) {
                                var activeCalculationCode = catalogProductPriceData.activeCalculationCodes[i];
                                self.setActiveCalculation(activeCalculationCode);
                            }
                        }
                    }
                    calculation.addActiveCalculation('default', 0);
                });
            }

            customerData.get('catalog-product-price-calculation').subscribe(function (sectionInfo) {
                if (sectionInfo.activeCalculationCodes) {
                    var activeCalculations = calculation.getActiveCalculations()();
                    var activeCalculation;
                    var activeCalculationCode;
                    var activeCalculationCodes = [];
                    var i;

                    for (i = 0; i < activeCalculations.length; i++) {
                        activeCalculation = activeCalculations[i];
                        activeCalculationCode = activeCalculation.code;
                        activeCalculationCodes.push(activeCalculationCode);
                    }

                    for (i = 0; i < sectionInfo.activeCalculationCodes.length; i++) {
                        activeCalculationCode = sectionInfo.activeCalculationCodes[i];
                        if ($.inArray(activeCalculationCode, activeCalculationCodes) === -1) {
                            this.setActiveCalculation(activeCalculationCode);
                        }
                    }

                    for (i = 0; i < activeCalculations.length; i++) {
                        activeCalculation = activeCalculations[i];
                        activeCalculationCode = activeCalculation.code;
                        if (activeCalculationCode !== 'default' &&
                            $.inArray(activeCalculationCode, sectionInfo.activeCalculationCodes) === -1) {
                            calculation.removeActiveCalculation(activeCalculationCode);
                        }
                    }
                } else {
                    calculation.resetActiveCalculations();
                }
            }, this);
        },

        setActiveCalculation: function (activeCalculationCode) {
            if (this.calculations.hasOwnProperty(activeCalculationCode)) {
                calculation.addActiveCalculation(activeCalculationCode, this.calculations[activeCalculationCode]);
            }
        }
    });
});
