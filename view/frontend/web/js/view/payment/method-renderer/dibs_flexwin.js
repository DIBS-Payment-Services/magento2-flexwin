/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Dibs_Flexwin/js/action/set-payment-method',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/checkout-data',
        'Magento_Customer/js/customer-data',
        'ko',
        'jquery',
        'Magento_Checkout/js/model/payment/additional-validators'
    ],
    function (Component, setPaymentMethodAction, selectPaymentMethodAction,
              checkoutData, storage, ko, $, additionalValidators) {
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

            placeOrder: function () {
                var self = this;
                var obj = storage.get('checkout-data');
                if (self.validate() && additionalValidators.validate() ) {
                    self.selectPaymentMethod();
                    setPaymentMethodAction(this.messageContainer, self.requestData,
                        _.find(this.getEnabledPaytypes(), function (card) {
                            return card.id == obj.paytypeId;
                        }).paytype);
                }
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
                checkoutData.setSelectedPaymentMethod(event.target.id);
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