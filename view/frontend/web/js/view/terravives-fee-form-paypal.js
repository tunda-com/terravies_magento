/**
 * Copyright © Terravives. All rights reserved.
 
 */

/*
 global define
 */
define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/form',
    'Terravives_Fee/js/model/fee',
    'Terravives_Fee/js/action/apply-fee-paypal',
    'Terravives_Fee/js/action/delete-fee-paypal',
    'Terravives_Fee/js/model/fee-messages',
    'Magento_Catalog/js/price-utils',
    'mage/translate'
], function ($,
             ko,
             Component,
             fee,
             applyFeeAction,
             deleteFeeAction,
             messageContainer,
             priceUtils,
             $t
) {
    'use strict';

    var errorMessage = $t('ERROR'),
        isLoading = ko.observable(false);

    return Component.extend({
        defaults: {
            defaultShowButtonAdd: ko.computed(function () {
                if (fee.allData().is_fee_use == true) {
                    return false;
                }

                return true;
            }, this)
        },

        initialize: function () {
            this._super();
            this.showButtonAdd = ko.observable(this.defaultShowButtonAdd);

            /* default initialize */
            var valueFee = fee.allData().fee;
            var formatPrice = priceUtils.formatPrice(valueFee, fee.allData().price_format);
            this.getValueFee = ko.observable(formatPrice);

            return this;
        },

        isLoading: isLoading,

        /**
         * Form submit add fee
         *
         */
        onSubmitAdd: function () {
            var self = this;
            this.source.set('params.invalid', false);
            this.source.trigger('terravivesFeeForm.data.validate');

            if (!this.source.get('params.invalid')) {
                isLoading(true);
                var formData = [];
                
                if (this.source.get('predefinedFee') == 'custom_fee') {
                    formData['fee'] = this.source.get('terravivesFeeForm');
                } else {
                    formData['fee'] = this.source.get('predefinedFee');
                }

                applyFeeAction(formData, isLoading);
            } else {
                messageContainer.addErrorMessage({'message': errorMessage});
            }
        },

        /**
         * Form submit delete fee
         *
         */
        onSubmitDelete: function () {
            this.source.set('params.invalid', false);
            this.source.trigger('terravivesFeeForm.data.validate');

            if (!this.source.get('params.invalid')) {
                isLoading(true);
                var formData = [];
                formData['fee'] = this.source.get('terravivesFeeForm');

                deleteFeeAction(formData, isLoading);
            } else {
                messageContainer.addErrorMessage({'message': errorMessage});
            }
        },

        /**
         * Form submit add fee
         *
         */
        onAddressSave: function () {
            this.source.set('params.invalid', false);
            this.source.trigger('terravivesFeeForm.data.validate');

            if (!this.source.get('params.invalid')) {
                isLoading(true);
                var formData = [];
                formData['fee'] = this.source.get('terravivesFeeForm');

            } else {
                messageContainer.addErrorMessage({'message': errorMessage});
            }
        },

        isSaveAddressVisible: function () {
            return fee.allData().is_save_address_visible;
        },

        /**
         * Is fee use (has in session)
         *
         * @return bool
         */
        isFeeUse: function () {
            return fee.allData().is_fee_use;
        },

        /**
         * Show fees
         *
         * @return bool
         */
        isEnableFees: function () {
            return fee.allData().is_enable_fees;
        },

        /**
         * Show label with minimum fee
         *
         * @return bool
         */
        isActivateMinimumFee: function () {
            if (fee.allData().minimum_fee > 0) {
                return true
            }
            return false;

        },

        /**
         * @return bool
         */
        isShowMinimumFee: function () {
            return fee.allData().is_show_minimum_fee;
        },

        /**
         * Get default fee
         *
         * @return {String}
         */
        getDefaultFee: function () {
            return fee.allData().default_description_fee;
        },

        /**
         * Show title fee
         *
         * @returns bool
         */
        isDisplayTitle: function () {
            return fee.allData().is_display_title;
        },

        /**
         * Show block default fee
         *
         * @returns bool
         */
        isDisplayBlockDefaultFee: function () {
            if (fee.allData().default_description_fee != null) {
                return true;
            }
            return false;
        },

        /**
         *
         * @returns bool
         */
        isDisplayed: function () {
            return fee.allData().is_enable;
        }
    });
});
