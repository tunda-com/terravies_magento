define([
    'ko',
    'Terravives_Fee/js/model/fee',
    'Magento_Ui/js/form/element/select'
], function (ko, fee, Select) {
    'use strict';

    var hasImage = ko.observable(false);

    return Select.extend({

        hasImage: hasImage,

        getProjectDescription: function () {
            var value = this.value(),
                projects = fee.allData().projects;

            if(value in projects) {
                return projects[value]['short_description'];
            }
        },

        imagePath: function () {
            var value = this.value(),
                projects = fee.allData().projects;

            hasImage(false);
            if(value in projects) {
                hasImage(true);
                return projects[value]['cover_url'];
            }
        },

        getUrl: function () {
            var value = this.value(),
                projects = fee.allData().projects;

            if(value in projects) {
                return projects[value]['url'];
            }
        }

    });
});
