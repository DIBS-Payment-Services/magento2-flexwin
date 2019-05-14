define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader',
        'mage/url'
        
    ],
    function ($, quote, urlBuilder, storage, errorProcessor, customer, fullScreenLoader, url) {
        'use strict';
        var agreementsConfig = window.checkoutConfig.checkoutAgreements;
        return function (messageContainer, requestData, method) {
            var serviceUrl,
                payload,
                paymentData = quote.paymentMethod();

            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:quoteId/payment-information', {
                    quoteId: quote.getQuoteId()
                });
                payload = {
                    cartId: quote.getQuoteId(),
                    email: quote.guestEmail,
                    paymentMethod: paymentData,
                    billingAddress: quote.billingAddress()
                };
            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/payment-information', {});
                payload = {
                    cartId: quote.getQuoteId(),
                    paymentMethod: paymentData,
                    billingAddress: quote.billingAddress()
                };
            }
             // some copy-paste from place-order-mixin.js for adding agreements...
            if (agreementsConfig.isEnabled) {
                var agreementForm = $('.payment-method._active div[data-role=checkout-agreements] input'),
                agreementData = agreementForm.serializeArray(),
                agreementIds = [];

                agreementData.forEach(function (item) {
                    agreementIds.push(item.value);
                });

                if (paymentData['extension_attributes'] === undefined) {
                    paymentData['extension_attributes'] = {};
                }

                paymentData['extension_attributes']['agreement_ids'] = agreementIds;
            }
            fullScreenLoader.startLoader();
            return storage.post(
                serviceUrl, JSON.stringify(payload)
            ).done(
                function (data) {
                    $.ajax({
                        method: "POST",
                        url: window.checkoutConfig.payment.dibsFlexwin.getPlaceOrderUrl,
                        data: {
                            paytype: method,
                            cartid: quote.getQuoteId(),
                            orderid: data,
                            form_key: $.mage.cookies.get('form_key')
                        },
                        dataType: 'json'
                    })
                        .done(function (jsonResponse) {
                            if (jsonResponse.result == 'success') {
                                var requestDataArr = [];
                                requestData.subscribe(function () {
                                    $('#payment-form-dibs').submit();
                                });
                                $.each(jsonResponse.params, function (name, value) {
                                    requestDataArr.push({'name': name, 'value': value});
                                });
                                requestData(requestDataArr);
                            } else {
                                fullScreenLoader.stopLoader();
                                alert(jsonResponse.message);
                                window.location.href = url.build('checkout/cart');
                            }
                        });
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response, messageContainer);
                    fullScreenLoader.stopLoader();
                }
            );

        };
    }
);