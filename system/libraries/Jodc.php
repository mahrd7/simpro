<?php

require_once 'HTTP/Request.php';

class CI_Jodc {

    var $url = "http://localhost:8080/converter/service";

	public function __construct()
	{
	}
	
    function convert($inputData, $inputType, $outputType) {
        $request = new HTTP_Request($this->url);
        $request->setMethod("POST");
        $request->addHeader("Content-Type", $inputType);
        $request->addHeader("Accept", $outputType);
        $request->setBody($inputData);
        $request->sendRequest();
        return $request->getResponseBody();
    }
}