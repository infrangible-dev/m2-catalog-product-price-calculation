/**
 * @author      Andreas Knollmann
 * @copyright   Copyright (c) 2014-2024 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'ko',
    'underscore',
    'domReady!'
], function ($, ko, _) {
    'use strict';

    var activeCalculationsObservable = ko.observable([]);

    return {
        getActiveCalculations: function () {
            return activeCalculationsObservable;
        },

        addActiveCalculation: function (activeCalculationCode, activeCalculationPriority) {
            var activeCalculationsData = activeCalculationsObservable();

            activeCalculationsData.push({code: activeCalculationCode, priority: activeCalculationPriority});

            activeCalculationsData.sort(function(a, b) {
                return b.priority - a.priority;
            });

            console.debug(activeCalculationsData);
            activeCalculationsObservable(activeCalculationsData);
        },

        resetActiveCalculations: function () {
            activeCalculationsObservable({code: 'default', priority: 0});
        },

        removeActiveCalculation: function (activeCalculationCode) {
            var activeCalculationsData = activeCalculationsObservable();

            for (var i = 0; i < activeCalculationsData.length; i++) {
                var activeCalculation = activeCalculationsData[i];

                if (activeCalculation.code === activeCalculationCode) {
                    activeCalculationsData.splice(i, 1);
                    break;
                }
            }

            console.debug(activeCalculationsData);
            activeCalculationsObservable(activeCalculationsData);
        }
    };
});
