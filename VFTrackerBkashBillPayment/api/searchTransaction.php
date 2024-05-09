<?php
//headers
header('Allow-Control-Allow-Origin: *');
header('Content-Type: application/json');
// header('Allow-Control-Allow-Method: GET');
// header('Allow-Control-Allow-Headers: Access-Controll-Allow-Headers,Content-Type,Allow-Control-Allow-Method, Authorization,X-Requested-With');
include_once($_SERVER['DOCUMENT_ROOT'] .'/VFTrackerBkashBillPayment/core/initialize.php');

$post = new Post($db);
$errorMessage = new ErrorMessage();


$post->username           = (isset($_POST['UserName']) && $_POST['UserName'] != '') ? $_POST['UserName'] : $errorMessage->mandatoryFieldError();
$post->password           = (isset($_POST['Password']) && $_POST['Password'] != '') ? $_POST['Password'] : $errorMessage->mandatoryFieldError();
$post->TrxId              = (isset($_POST['TrxId']) && $_POST['TrxId'] != '') ? $_POST['TrxId'] : $errorMessage->mandatoryFieldError();

// echo $post->username;
// exit;
//blog post query

$post->authentication();
$totalAmount = 0;

if ($post->msg == 'Successful') {
    $result = $post->searchTransaction();
    $num = $result->rowCount();
    $post_arr=[];
    if ($num > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $post_item = array(
                'ConsumerName' => $post->name,
                'TotalAmount'   => $totalAmount += $row['amount'],
                'TrxId'         => $row['bkash_trxId'],
                'RefNumber'     => $row['bkash_RefNumber'],
                'CustomMessage' => "",
                'ErrorCode'     => "200",
                'ErrorMsg'      => "Successful"
            );
            array_push($post_arr, $post_item);
        }
        echo json_encode(($post_arr));
    }else{
        $errorMessage->notFoundError();
    }
} else {
    $errorMessage->authenticationError();
}

