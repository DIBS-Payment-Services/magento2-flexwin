/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        rendererList.push(
            {
                type: 'dibs_flexwin',
                component: 'Dibs_Flexwin/js/view/payment/method-renderer/dibs_flexwin'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
