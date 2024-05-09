<?php
//headers
header('Allow-Control-Allow-Origin: *');
header('Content-Type: application/json');

//initializing our api
require_once($_SERVER['DOCUMENT_ROOT'] .'/VFTrackerBkashBillPayment/core/initialize.php');

//initializing post
$post = new Post($db);
$errorMessage = new ErrorMessage();

$post->username           = (isset($_POST['UserName']) && $_POST['UserName'] != '') ? $_POST['UserName'] : $errorMessage->mandatoryFieldError();
$post->password           = (isset($_POST['Password']) && $_POST['Password'] != '') ? $_POST['Password'] : $errorMessage->mandatoryFieldError();
$post->customerNo         = (isset($_POST['CustomerNo']) && $_POST['CustomerNo'] != '') ? $_POST['CustomerNo'] : $errorMessage->mandatoryFieldError();
$post->getOptionalAmount  = isset($_POST['Amount']) ? $_POST['Amount'] : '';

$post->authentication();

if ($post->msg == 'Successful') {
    $post->get_customer();
    if ($post->msg == "success_getCustomer") {
        $post_arr = array(
            'CustomerNo'   => $post->customerNo,
            'ConsumerName' => $post->name,
            'QueryTime'    => date('Y:m:d h:i:s'),
            'Amount'       => $post->getOptionalAmount,
            'ErrorCode'    => "200",
            'ErrorMsg'     => 'Successful'
        );
        print_r(json_encode($post_arr));
    } else {
        $errorMessage->notFoundError();
    }
} else {
    $errorMessage->authenticationError();
}

//make a json
