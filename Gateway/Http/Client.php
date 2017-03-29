<?php

namespace Dibs\Flexwin\Gateway\Http;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Message\ManagerInterface;

class Client implements ClientInterface 
{
    private $curlClient;
    private $message;

    public function __construct(
        Curl $curlClient,
        ManagerInterface $message
    ) {
        $this->curlClient = $curlClient;
        $this->message = $message;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     */
    public function placeRequest(TransferInterface $transferObject)
    {
               $curlOptions = [CURLOPT_SSLVERSION => 1,
                               CURLOPT_SSL_VERIFYPEER => false];
               $this->curlClient->setHeaders($transferObject->getHeaders());
               $this->curlClient->setOptions($curlOptions);
               try {
                  $this->curlClient->post($transferObject->getUri(), $transferObject->getBody());
               } catch(\Exception $ex) {
                   throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
               }
               $statusCode = $this->curlClient->getStatus();
               if($statusCode == 401 ) {
	           throw new \Magento\Framework\Exception\LocalizedException(__('Authorization failed'));
               }
               if($statusCode == 404 ) {
	          throw new \Magento\Framework\Exception\LocalizedException(__('404 Error'));
               }
               if($statusCode != 200 ) {
                   $msg = 'DIBS error occured. Response HTTP status code:';
                   $msg .= $this->curlClient->getStatus();
                   throw new \Magento\Framework\Exception\LocalizedException(__($msg));
               }
               $body = $this->curlClient->getBody();
               
               $match = [];
               preg_match('/status=(.*?)&/', $body, $match);
               $status = $match[1];
               $error = [];
               if(\Dibs\Flexwin\Model\Method::API_OPERATION_FAILURE == $status) {
                   $error = $this->processError($body);
               }
               return ['status'=> $status,
                        'body' => $body,
                        'error'=> $error];
    }
    
    protected function processError($responseBody) {
       $error = [];
       $match = [];
       preg_match('/message=(.*?)$/', $responseBody, $match);
       if(isset($match[1])) {
           $error['message'] = trim($match[1]);
       }
       preg_match('/reason=(.*?)&/', $responseBody, $match);
       if(isset($match[1])) {
           $error['reason'] = trim($match[1]);
       }
       return $error;
    }
            
}
