define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function($, storage) {
    'use strict';

    var cacheKey = 'checkout-data';

    var getData = function () {
        return storage.get(cacheKey)();
    };

    var saveData = function (checkoutData) {
        storage.set(cacheKey, checkoutData);
    };

    return function(checkoutData) {
        return $.extend(checkoutData, {
            setPaytypeId: function(data) {
                var obj = getData();
                obj.paytypeId = String(data);
                saveData(obj);
            },

            getPaytypeId: function() {
                return getData().paytypeId;
            }
        });
    };
});
