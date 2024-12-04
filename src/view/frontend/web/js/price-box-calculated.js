/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mage.priceBoxCalculation', {
        _init: function initPriceBox() {
            var priceWrapperElement = this.element;

            setTimeout(function () {
                var priceElement = priceWrapperElement.find('.price');
                if (! priceElement.hasClass('calculated')) {
                    priceElement.addClass('calculated');
                }
            }, 1500);
        }
    });

    return $.mage.priceBoxCalculation;
});
