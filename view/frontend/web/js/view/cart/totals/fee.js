/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(
    [
        'Terravives_Fee/js/view/summary/fee'
    ],
    function (Component) {
        'use strict';

        return Component.extend({

            /**
             * @override
             */
            isDisplayed: function () {
                return true;
            }
        });
    }
);
