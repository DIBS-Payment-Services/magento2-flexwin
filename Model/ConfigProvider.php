<?php

namespace Dibs\Flexwin\Model;

use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;

class ConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    protected $method;
    protected $urlInterface;
    protected $assetRepo;
    protected $formKey;
    const METHOD_CODE = 'dibs_flexwin';

    /**
     * @var Escaper
     */
    protected $escaper;

    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Data\Form\FormKey $formKey

    ) {
        $this->escaper = $escaper;
        $this->method = $paymentHelper->getMethodInstance(self::METHOD_CODE);
        $this->urlInterface = $urlInterface;
        $this->assetRepo = $assetRepo;
        $this->formKey = $formKey;
    }

    public function getConfig()
    {
        $config = [
            'payment' => [
                'dibsFlexwin' => [
                    'paytype'       => [
                        'visa'      => ['enabled' => $this->method->getConfigData('card_visa'),
                                        'logo'=>$this->getLogo('visa'),
                                         'id' => 'dibs_flexwin_cards_visa',
                                         'title' => __('Visa'),
                                         'paytype' => 'VISA'],
                        'master'    => ['enabled' => $this->method->getConfigData('card_master'),
                                        'logo'=>$this->getLogo('mastercard'),
                                         'id'=> 'dibs_flexwin_cards_master',
                                         'title'=> __('Master'),
                                         'paytype'=> 'MC'],
                        'amex'      => ['enabled' => $this->method->getConfigData('card_amex'),
                                        'logo'=>$this->getLogo('amex'),
                                         'id'=> 'dibs_flexwin_cards_amex',
                                         'title'=> __('Amex'),
                                         'paytype' => 'AMEX'],
                        'diners'    => ['enabled' => $this->method->getConfigData('card_diners'),
                                        'logo'=>$this->getLogo('diners'),
                                        'id' => 'dibs_flexwin_cards_diners',
                                        'title'=> __('Diners'),
                                        'paytype'=> 'DIN'],
                        'dankort'   => ['enabled' => $this->method->getConfigData('card_dankort'),
                                        'logo'=>$this->getLogo('dankort'),
                                         'id' => 'dibs_flexwin_cards_dankort',
                                         'title' => __('Dankort'),
                                         'paytype' => 'DK'],
                        'mobilepay' => ['enabled' => $this->method->getConfigData('mobilepay'),
                                        'logo'=>$this->getLogo('mobilepay'),
                                         'id'=> 'dibs_flexwin_mobilepay',
                                         'title'=> __('MobilePay'),
                                         'paytype'=> 'MPO_Nets'],
                        'swedbank'  => ['enabled' => $this->method->getConfigData('swedbank'),
                                        'logo'=>$this->getLogo('swedbank'),
                                         'id'=> 'dibs_flexwin_fsb',
                                         'title'=> __('Swedbank'),
                                         'paytype'=> $this->method->getConfigData('testmode') == 1 ? 'swd_atest' : 'swd_a'],
                        'seb'       => ['enabled' => $this->method->getConfigData('seb'),
                                        'logo'=>$this->getLogo('seb'),
                                         'id' => 'dibs_flexwin_seb',
                                         'title' => __('SEB Bank'),
                                         'paytype' => $this->method->getConfigData('testmode') == 1 ? 'seb_atest' : 'seb'],
                        'paypal'    => ['enabled' => $this->method->getConfigData('paypal'),
                                        'logo'=>$this->getLogo('paypal'),
                                         'id' => 'dibs_flexwin_paypal',
                                         'title' => __('Paypal'),
                                         'paytype' => $this->method->getConfigData('testmode') == 1 ?'paypaltest':'paypal'],
                        'nordea'    => ['enabled' => $this->method->getConfigData('nordea'),
                                        'logo' => $this->getLogo('nordea'),
                                        'id' => 'dibs_flexwin_nordea',
                                        'title' => __('Nordea'),
                                        'paytype' => 'ndb']],
                        'getPlaceOrderUrl' => $this->urlInterface->getDirectUrl($this->method->getConfigData('place_order_url')),
                        'formActionUrl'    => $this->method->getConfigData('form_action_url'),
                        'logoWith'         => $this->method->getConfigData('logo_with'),
                        'test'             => $this->method->getConfigData('testmode'),
                        'form_key'         => $this->formKey->getFormKey()
                ]
            ]
        ];
        return $config;
    }

    public function getCards()
    {
        return $this->method->isCardsActive();
    }

    protected function getLogo($imgName) {
        $imgPath = 'Dibs_Flexwin::images/'.$imgName.'.png';
        return $this->assetRepo->getUrlWithParams($imgPath,[]);
    }
}
