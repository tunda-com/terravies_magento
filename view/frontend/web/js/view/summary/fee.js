/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

define(
    [
        'ko',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals'
    ],
    function (ko, Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({

            defaults: {
                template: 'Terravives_Fee/summary/fee'
            },

            totals: quote.getTotals(),

            isDisplayed: function () {
                if (this.isFullMode()) {
                    var price = 0;
                    if (this.totals() && totals.getSegment('terravives_fee')) {
                        price = totals.getSegment('terravives_fee').value;
                        if (price > 0) {
                            return true;
                        }
                    }
                    return false;
                }
                return false;
            },

            getValue: function () {
                var price = 0;
                if (this.totals() && totals.getSegment('terravives_fee')) {
                    price = totals.getSegment('terravives_fee').value;
                }
                return this.getFormattedPrice(price);
            },

            getBaseValue: function () {
                var price = 0;
                if (this.totals()) {
                    price = this.totals().base_fee;
                }
                return priceUtils.formatPrice(price, quote.getBasePriceFormat());
            },

            formatPrice: function (price) {
                return this.getFormattedPrice(price);
            },

            getDetails: function () {
                var feeSegment = totals.getSegment('terravives_fee');
                if (feeSegment && feeSegment.extension_attributes) {
                    return feeSegment.extension_attributes.terravives_fees_details;
                }
                return [];
            }
        });
    }
);