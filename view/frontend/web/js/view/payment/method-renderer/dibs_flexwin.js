/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/checkout-data',
        'Magento_Customer/js/customer-data',
        'ko',
        'jquery',
        'mage/url'
    ],
    function (Component, selectPaymentMethodAction,
              checkoutData, storage, ko, $, url) {
        'use strict';
        return Component.extend({
            redirectAfterPlaceOrder: false,

            defaults: {
                template: 'Dibs_Flexwin/payment/dibs_flexwin',
                requestData: [],
                imgWidth: window.checkoutConfig.payment.dibsFlexwin.logoWith
            },

            getDibsPaytype: ko.observable(function () {
                var obj = storage.get('checkout-data');
                return obj.paytypeId;

            }),

            getDibsActionFormUrl: function () {
                return window.checkoutConfig.payment.dibsFlexwin.formActionUrl;
            },

            getEnabledPaytypes: function () {
                var paytypes = [];
                _.each(window.checkoutConfig.payment.dibsFlexwin.paytype, function( val, key ) {
                     paytypes.push(val);
                });
                return _.filter(paytypes, function (paytype) {
                    return paytype.enabled == 1;
                });
            },

            initObservable: function () {
                this._super().observe('requestData');
                return this;
            },

            afterPlaceOrder: function() {
                console.log( storage.get('checkout-data'));
                var obj = storage.get('checkout-data');
                var paytypeT = _.find(this.getEnabledPaytypes(), function (card) {
                            return card.id == obj.paytypeId;
                }).paytype;
                var urlredirect = url.build("dibsflexwin/index/request") + '?paytype=' + paytypeT;
                window.location.replace(urlredirect);
            },

            getData: function () {
                return {
                    method: this.item.method,
                    po_number: null,
                    additional_data: null
                };
            },

            customMethodDisabled: function () {
                return false;
            },

            setCustomPaymentMethod: function (data, event) {
                selectPaymentMethodAction({
                    method: this.item.method,
                    po_number: null,
                    additional_data: null
                });
                var obj = storage.get('checkout-data');
                obj.paytypeId = event.target.id;
                this.getDibsPaytype(obj.paytypeId);
                /**
                 * The below instruction causes checkout data to be removed when selecting a payment method
                 * since `obj` refers to a function and not the actual data.
                 *
                 * @todo: set payTypeId properly on the observable data and not the observable function
                 */
                // storage.set('checkout-data', obj);

                return true;
            },

            getMethodCode: function () {
                return this.item.method;
            },

            getInstructions: function () {
                return window.checkoutConfig.payment.instructions[this.item.method];
            }
        });
    }
);