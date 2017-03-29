/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'jquery',
        'underscore',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'mage/url'
    ],
    function (ko, $, _, Component, selectPaymentMethodAction, quote, checkoutData, url) {
        'use strict';
        return Component.extend({
            redirectAfterPlaceOrder: false,
            placeOrderResult: null,

            defaults: {
                template: 'Dibs_Flexwin/payment/dibs_flexwin',
                requestData: [],
                imgWidth: window.checkoutConfig.payment.dibsFlexwin.logoWith
            },

            initialize: function() {
                this._super();

                // when Dibs is preselected as the only available payment method, make sure paytype is selected
                if (!checkoutData.getPaytypeId()) {
                    var types = this.getEnabledPaytypes();
                    if (types) {
                        checkoutData.setPaytypeId(types[0].id);
                    }
                }

                this.isChecked = ko.computed(function() {
                    return (quote.paymentMethod() && quote.paymentMethod().method == this.item.method) ?
                        checkoutData.getPaytypeId() : null;
                }, this);

                return this;
            },

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

            getPlaceOrderDeferredObject: function () {
                return this._super().done(function(data) {
                    // store response for use in afterPlaceOrder
                    this.placeOrderResult = data;
                }.bind(this));
            },

            afterPlaceOrder: function() {
                var paytype = _.find(this.getEnabledPaytypes(), function (card) {
                    return card.id == checkoutData.getPaytypeId();
                }).paytype;

                $.ajax({
                    method: "POST",
                    url: window.checkoutConfig.payment.dibsFlexwin.getPlaceOrderUrl,
                    data: {
                        paytype: paytype,
                        cartid: quote.getQuoteId(),
                        orderid: this.placeOrderResult
                    },
                    dataType: 'json'
                })
                    .done(function (jsonResponse) {
                        if (jsonResponse.result == 'success') {
                            var requestDataArr = [];
                            this.requestData.subscribe(function () {
                                $('#payment-form-dibs').submit();
                            });
                            $.each(jsonResponse.params, function (name, value) {
                                requestDataArr.push({'name': name, 'value': value});
                            });
                            this.requestData(requestDataArr);
                        } else {
                            alert(jsonResponse.message);
                            window.location.href = url.build('checkout/cart');
                        }
                    }.bind(this));
            },

            setCustomPaymentMethod: function (data, event) {
                var paytypeId = event.target.id;

                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                checkoutData.setPaytypeId(paytypeId);

                return true;
            },

            getInstructions: function () {
                return window.checkoutConfig.payment.instructions[this.item.method];
            }
        });
    }
);