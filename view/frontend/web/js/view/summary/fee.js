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
        'Magento_Checkout/js/model/totals',
        'mage/translate'
    ],
    function (ko, Component, quote, priceUtils, totals, $t) {
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

            /**
             * Get parsed fee details from extension attributes
             */
            getDetails: function () {
                var feeSegment = totals.getSegment('terravives_fee');
                if (feeSegment &&
                    feeSegment.extension_attributes &&
                    feeSegment.extension_attributes.terravives_fee_details) {

                    try {
                        // Parse JSON string
                        var details = JSON.parse(feeSegment.extension_attributes.terravives_fee_details);
                        return details;
                    } catch (e) {
                        console.error('Error parsing terravives_fee_details:', e);
                        return {};
                    }
                }
                return {};
            },

            /**
             * Check if tax should be shown separately
             */
            showTaxSeparately: function () {
                var details = this.getDetails();
                return details && details.show_tax_separately === true;
            },

            /**
             * Get tax amount formatted
             */
            getTaxValue: function () {
                var details = this.getDetails();
                var taxAmount = details && details.tax_amount ? parseFloat(details.tax_amount) : 0;
                return this.getFormattedPrice(taxAmount);
            },

            /**
             * Get tax title with percentage - translatable
             */
            getTaxTitle: function () {
                return $t('Fee Tax');
            },

            /**
             * Get base fee amount (without tax)
             */
            getBaseFeeValue: function () {
                var details = this.getDetails();
                var baseFee = details && details.base_fee_amount ? parseFloat(details.base_fee_amount) : 0;
                return this.getFormattedPrice(baseFee);
            }
        });
    }
);
