/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */

define([
    'jquery',
    'ko',
    'underscore',
    'domReady!'
], function ($, ko, _) {
    'use strict';

    const activeCalculationsObservable = ko.observable([]);

    return {
        getActiveCalculations: function () {
            return activeCalculationsObservable;
        },

        addActiveCalculation: function (activeCalculationCode, activeCalculationPriority) {
            const activeCalculationsData = activeCalculationsObservable();

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
            const activeCalculationsData = activeCalculationsObservable();

            for (let i = 0; i < activeCalculationsData.length; i++) {
                const activeCalculation = activeCalculationsData[i];

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
