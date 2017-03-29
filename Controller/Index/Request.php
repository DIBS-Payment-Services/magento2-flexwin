<?php

namespace Dibs\Flexwin\Controller\Index;

use Magento\Framework\DataObject;

class Request extends \Dibs\Flexwin\Controller\Index
{
    public function execute()
    {
        if ($this->checkPost()) {
            $result = new DataObject();
            $response = $this->getResponse();
            $result->addData($this->method->collectRequestParams());
            return $response->representJson($result->toJson());
        }

    }
}