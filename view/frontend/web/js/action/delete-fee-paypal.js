/**
 * Copyright © Terravives. All rights reserved.
 
 */

define(
    [
        'ko',
        'jquery',
        'Terravives_Fee/js/model/fee',
        'Terravives_Fee/js/model/fee-messages',
        'mage/translate'
    ],
    function (
        ko,
        $,
        fee,
        messageContainer,
        $t
    ) {
        'use strict';
        return function (formData, isLoading) {
            var data = {
                    'fee': formData['fee'],
                    'deleteFee': true
                },
                errorMessage = $t('Could not apply fee');

            $.ajax({
                url: fee.allData().url,
                data: data,
                type: 'post',
                dataType: 'json'
            })
                .done(function (response) {
                    if (response) {
                        location.reload();
                    } else {
                        isLoading(false);
                        messageContainer.addErrorMessage({'message': errorMessage});
                    }
                })
                .fail(function () {
                    isLoading(false);
                    messageContainer.addErrorMessage({'message': errorMessage});
                });
        };
    }
);
