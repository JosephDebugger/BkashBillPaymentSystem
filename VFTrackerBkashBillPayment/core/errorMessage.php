<?php
class ErrorMessage
{
    public function mandatoryFieldError()
    {
        $response ='';
        $response = array(
            "ErrorCode" => '406',
            "ErrorMsg" => 'Mandatory Field missing'
        );
        echo  json_encode($response);
        exit;
    }
    public function authenticationError()
    {
        $response = array(
            "ErrorCode" => '403',
            "ErrorMsg" => 'Authentication failed'
        );
        echo json_encode($response);
        exit;
    }
    public function notFoundError(){
        $response = array(
            "ErrorCode" => '404',
            "ErrorMsg" => 'Data not found'
        );
        echo json_encode($response);
    }
    public function successResponse(){
        $response = array(
            "ErrorCode" => '200',
            "ErrorMsg" => 'Successful'
        );
        echo json_encode($response);
    }
}
