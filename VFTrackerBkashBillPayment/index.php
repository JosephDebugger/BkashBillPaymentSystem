<?php

$request = $_SERVER['REQUEST_URI'];
$viewDir = '/api/';

switch ($request) {
    case '':
    case '/VFTrackerBkashBillPayment/api/queryBill':
        require __DIR__ . $viewDir . 'queryBill.php';
        break;

    case '/VFTrackerBkashBillPayment/api/paybill':
        require __DIR__ . $viewDir . 'paybill.php';
        break;

    case '/VFTrackerBkashBillPayment/api/searchTransaction':
        require __DIR__ . $viewDir . 'searchTransaction.php';
        break;

    default:
        http_response_code(404);
        require __DIR__ . $viewDir . '404.php';
}