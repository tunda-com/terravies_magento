/**
 * Copyright © Terravives. All rights reserved.
 
 */

define([
    'ko',
    'Magento_Ui/js/form/element/single-checkbox',
    'Terravives_Fee/js/model/fee',
    'jquery',
    'jquery/ui'
], function (ko, Select, fee, jQuery) {
    'use strict';


    var isLoading = ko.observable(false);

    return Select.extend({

        defaults: {
            ignoreChanged: false,
        },

        initialize: function () {
            this._super();
            var self = this;
            self.ignoreChanged = false;

            return this;
        },

        isLoading: isLoading,

        onCheckedChanged: function () {

            if (!this.ignoreChanged) {
                this._super();
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
                   
                    if (self.checked() == true) {
                        self.checked(true);
                    } else {
                        self.checked(false);
                    }
                } else {
                    messageContainer.addErrorMessage({'message': errorMessage});
                }
            }
        }
    });
});
