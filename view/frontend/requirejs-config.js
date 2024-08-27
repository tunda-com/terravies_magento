/**
 * Copyright © Terravives. All rights reserved.
 * See LICENSE.txt for license details.
 */
var config = {
    config: {
        mixins: {
            'Magento_Braintree/js/view/payment/method-renderer/paypal': {
                'Terravives_Fee/js/mixin/paypal-renderer-mixin': true
            }
        }
    }
};