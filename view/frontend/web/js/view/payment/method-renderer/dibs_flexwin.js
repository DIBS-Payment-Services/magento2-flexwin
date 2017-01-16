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

            getDibsPaytype: ko.observable(function () {
                return checkoutData.getPaytypeId();

            }),

            getDibsActionFormUrl: function () {
                return window.checkoutConfig.payment.dibsFlexwin.formActionUrl;
            },

            getEnabledPaytypes: function () {
                var configKey = window.checkoutConfig.payment.dibsFlexwin;
                var cardsArr =
                    [
                        {
                            id: 'dibs_flexwin_cards_visa',
                            title: $.mage.__('Visa'),
                            imgNumber: '07',
                            paytype: 'VISA',
                            enabled: configKey.paytype.visa
                        },
                        {
                            id: 'dibs_flexwin_cards_master',
                            title: $.mage.__('Master'),
                            imgNumber: '01',
                            paytype: 'MC',
                            enabled: configKey.paytype.master
                        },
                        {
                            id: 'dibs_flexwin_cards_amex',
                            title: $.mage.__('Amex'),
                            imgNumber: '06',
                            paytype: 'AMEX',
                            enabled: configKey.paytype.amex
                        },
                        {
                            id: 'dibs_flexwin_cards_diners',
                            title: $.mage.__('Diners'),
                            imgNumber: '04',
                            paytype: 'DIN',
                            enabled: configKey.paytype.diners
                        },
                        {
                            id: 'dibs_flexwin_cards_dankort',
                            title: $.mage.__('Dankort'),
                            imgNumber: '03',
                            paytype: 'DK',
                            enabled: configKey.paytype.dankort
                        },
                        {
                            id: 'dibs_flexwin_mobilepay',
                            title: $.mage.__('MobilePay'),
                            imgNumber: '10',
                            paytype: 'MPO_Nets',
                            enabled: configKey.paytype.mobilepay
                        }
                    ];

                return _.filter(cardsArr, function (card) {
                    return card.enabled == 1;
                });
            },

            initObservable: function () {
                this._super().observe('requestData');
                return this;
            },

            imgUrl: function (imgNumber) {
                var urlPrefix = window.checkoutConfig.payment.dibsFlexwin.cdnUrlLogoPrefix + imgNumber + '.png';
                return urlPrefix;
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

            getData: function () {
                return {
                    "method": this.item.method,
                    'po_number': null,
                    "additional_data": null
                };
            },

            customMethodDisabled: function () {
                return false;
            },

            setCustomPaymentMethod: function (data, event) {
                var paytypeId = event.target.id;

                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(paytypeId);
                checkoutData.setPaytypeId(paytypeId);

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