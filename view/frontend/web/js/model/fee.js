/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(
    [
        'ko'
    ],
    function (ko) {
        'use strict';
        var tempAllFeeData = window.terravivesFeeData,
            allData = ko.observable(tempAllFeeData);

        return {
            allData: allData,

            getData: function () {
                return allData;
            },

            setData: function (data) {
                allData(data);
            },

            validate: function () {
                if (this.allData().is_enable) {
                    return this.allData().is_valid;
                }
                return true;
            }
        }
    }
);
