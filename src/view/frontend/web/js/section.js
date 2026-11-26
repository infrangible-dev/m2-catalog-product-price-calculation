/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2026 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'Infrangible_CatalogProductPriceCalculation/js/model/calculation'
], function ($, Component, customerData, calculation) {
    'use strict';

    const SECTION = 'catalog-product-price-calculation';

    /**
     * Activates the calculations of the private content of the customer, with their priorities of the configuration.
     */
    return Component.extend({
        defaults: {
            calculations: {}
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();

            if (this.hasPrivateContent()) {
                $(document).on('customer-data-reload', function (event, sectionNames) {
                    if (Array.isArray(sectionNames) && sectionNames.indexOf(SECTION) !== -1) {
                        calculation.addActiveCalculation('default', 0);
                    }
                });

                customerData.reload([SECTION], false);
            } else {
                $(function () {
                    const sectionData = customerData.get(SECTION)();

                    if (sectionData && Array.isArray(sectionData.activeCalculationCodes)) {
                        sectionData.activeCalculationCodes.forEach(this.setActiveCalculation, this);
                    }

                    calculation.addActiveCalculation('default', 0);
                }.bind(this));
            }

            customerData.get(SECTION).subscribe(this.updateActiveCalculations, this);
        },

        /**
         * Checks if the customer is logged in or has a cart, so that the section has to be reloaded.
         */
        hasPrivateContent: function () {
            const cart = customerData.get('cart')(),
                customer = customerData.get('customer')();

            return !!(cart && cart.summary_count > 0) || !!(customer && customer.fullname);
        },

        /**
         * Activates the calculations of the section and deactivates the others, except "default".
         */
        updateActiveCalculations: function (sectionData) {
            if (!sectionData || !Array.isArray(sectionData.activeCalculationCodes)) {
                calculation.resetActiveCalculations();

                return;
            }

            sectionData.activeCalculationCodes.forEach(this.setActiveCalculation, this);

            calculation.getActiveCalculations()().forEach(function (activeCalculation) {
                if (activeCalculation.code !== 'default' &&
                    sectionData.activeCalculationCodes.indexOf(activeCalculation.code) === -1) {

                    calculation.removeActiveCalculation(activeCalculation.code);
                }
            });
        },

        /**
         * Activates the calculation if the page knows its priority.
         */
        setActiveCalculation: function (activeCalculationCode) {
            if (Object.prototype.hasOwnProperty.call(this.calculations, activeCalculationCode)) {
                calculation.addActiveCalculation(activeCalculationCode, this.calculations[activeCalculationCode]);
            }
        },

        getActiveCalculationCodes: function () {
            return calculation.getActiveCalculations()().map(function (activeCalculation) {
                return activeCalculation.code;
            });
        },

        hasActiveCalculation: function (code) {
            return calculation.hasActiveCalculation(code);
        }
    });
});
