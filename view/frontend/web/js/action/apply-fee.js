/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */

/*global define,alert*/
define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Terravives_Fee/js/model/fee',
        'Terravives_Fee/js/model/fee-messages',
        'mage/translate',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/cart/totals-processor/default'
    ],
    function (
        ko,
        $,
        quote,
        fee,
        messageContainer,
        $t,
        getPaymentInformationAction,
        totals,
        stepnav,
        totalsProcessor
    ) {
        'use strict';
        return function (formData, isLoading, isFeeAddSuccess) {
            var data = {
                    'fee': formData['fee']
                },
                successMessage = $t('Fee was successfully applied.'),
                errorMessage = $t('Could not apply fee');

            $.ajax({
                url: fee.allData().url,
                data: data,
                type: 'post',
                dataType: 'json'
            })
                .done(function (response) {
                    if (response) {
                        if (response['result'] == 'false') {
                            isFeeAddSuccess(false);
                            isLoading(false);
                            totals.isLoading(false);
                            messageContainer.addErrorMessage({'message': response['error']});
                        } else {
                            isLoading(false);
                            totals.isLoading(true);
                            updateTotals();
                            messageContainer.addSuccessMessage({'message': successMessage});
                            totals.isLoading(false);
                            isFeeAddSuccess(true);
                        }
                    } else {
                        isLoading(false);
                        totals.isLoading(false);
                        messageContainer.addErrorMessage({'message': errorMessage});
                    }
                })
                .fail(
                    function (response) {
                        isLoading(false);
                        totals.isLoading(false);
                        messageContainer.addErrorMessage({'message': errorMessage});
                    }
                );

            function updateTotals() {
                isLoading(false);

                // Reload totals if all done (cart page only)
                if (_.isEmpty(stepnav.steps())) {
                    try {
                        require(
                            [
                                'Magento_Checkout/js/model/cart/cache',
                                'Magento_Checkout/js/model/cart/totals-processor/default'
                            ],
                            function (cartCache, totalsProcessor) {
                                cartCache.clear('cartVersion');
                                totalsProcessor.estimateTotals(quote.shippingAddress());
                            }
                        );
                    } catch (e) {
                        totalsProcessor.estimateTotals(quote.shippingAddress());
                    }

                    if (!require.defined('Magento_Checkout/js/model/cart/cache')) {
                        totalsProcessor.estimateTotals(quote.shippingAddress());
                    }
                } else {
                    var deferred = $.Deferred();
                    totals.isLoading(true);
                    getPaymentInformationAction(deferred);
                    $.when(deferred).done(function () {
                        totals.isLoading(false);
                    });
                }
            }
        };
    }
);
