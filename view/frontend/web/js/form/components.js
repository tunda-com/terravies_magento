/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'ko',
    'Magento_Ui/js/lib/core/collection',
    'jquery',
    'jquery/ui'
], function (ko, uiColumns, jQuery) {
    'use strict';

    jQuery(document).ready(function () {
        jQuery('body').on('change', '[name="predefinedFee"] select.select', function () {
            changeFeeAmount();
        });
    });


    function changeFeeAmount() {
        var predefinedFee = require('uiRegistry').get('dataScope = predefinedFee');

        if (typeof (predefinedFee) === 'undefined') {
            return;
        }

        var feeForm = jQuery('[name="terravivesFeeForm"]');
        var inputFeeForm = jQuery('[name="terravivesFeeForm"] input');
        var minimumFeeBlock = jQuery('[class="minimum_fee"]');

        if (predefinedFee.value() == "custom_fee") {
            inputFeeForm.val('');
            feeForm.show();
            minimumFeeBlock.show();
        } else if (predefinedFee.value() == "round_up") {
            feeForm.hide();
            minimumFeeBlock.hide();
            inputFeeForm.val(predefinedFee.value());
        } else {
            feeForm.hide();
            inputFeeForm.val(predefinedFee.value());
            minimumFeeBlock.show();
        }
    }

    return uiColumns.extend({});
});
