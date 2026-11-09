/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
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

            let reloadSection = false;

            const cartObservable = customerData.get('cart');
            if (cartObservable) {
                const cart = cartObservable();
                if (cart && cart.summary_count > 0) {
                    reloadSection = true;
                }
            }

            const customerObservable = customerData.get('customer');
            if (customerObservable) {
                const customer = customerObservable();
                if (customer && customer.fullname) {
                    reloadSection = true;
                }
            }

            if (reloadSection) {
                $(document).on('customer-data-reload', function(event, sectionNames) {
                    for (let i = 0; i < sectionNames.length; i++) {
                        if (sectionNames[i] === 'catalog-product-price-calculation') {
                            setTimeout(function () {
                                calculation.addActiveCalculation('default', 0);
                            }, 100);
                        }
                    }
                });
                customerData.reload(['catalog-product-price-calculation']);
            } else {
                const self = this;

                $(document).ready(function() {
                    const catalogProductPriceSection = customerData.get('catalog-product-price-calculation');
                    const catalogProductPriceData = catalogProductPriceSection();
                    if (! $.isEmptyObject(catalogProductPriceData)) {
                        console.debug(catalogProductPriceData);
                        if (catalogProductPriceData.activeCalculationCodes) {
                            for (let i = 0; i < catalogProductPriceData.activeCalculationCodes.length; i++) {
                                const activeCalculationCode = catalogProductPriceData.activeCalculationCodes[i];
                                self.setActiveCalculation(activeCalculationCode);
                            }
                        }
                    }
                    calculation.addActiveCalculation('default', 0);
                });
            }

            customerData.get('catalog-product-price-calculation').subscribe(function (sectionInfo) {
                if (sectionInfo.activeCalculationCodes) {
                    const activeCalculations = calculation.getActiveCalculations()();
                    let activeCalculation;
                    let activeCalculationCode;
                    const activeCalculationCodes = [];
                    let i;

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
        },

        getActiveCalculationCodes: function () {
            const activeCalculationCodes = [];

            const activeCalculations = calculation.getActiveCalculations()();
            for (let i = 0; i < activeCalculations.length; i++) {
                activeCalculationCodes.push(activeCalculations[i].code);
            }

            return activeCalculationCodes;
        },

        hasActiveCalculation: function (code) {
            const activeCalculations = calculation.getActiveCalculations()();
            for (let i = 0; i < activeCalculations.length; i++) {
                if (code === activeCalculations[i].code) {
                    return true;
                }
            }

            return false;
        }
    });
});
