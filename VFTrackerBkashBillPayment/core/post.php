<?php

class Post
{
    //db stuff
    private $conn;
    private $table = 'api_user_login';
    private $tableTarty = 'tbl_party';

    //post properties
    public $id;
    public $username;
    public $password;
    public $getOptionalAmount;
    public $msg;

    //post property for custpmer
    public $customerNo;
    public $name;
    public $email;
    public $creditLimit;
    public $address;

    // post properties for payment voucher
    public $CustomerNo;
    public $paymentDate;
    public $Amount;
    public $partyId;
    public $UserMobileNumber;
    public $payMethod = "Bkash";

    // search quert
    public $TrxId;
    public $bkash_RefNumber;

    public function __construct($db)
    {
        $this->conn = $db;
        $timezone = 'Asia/Dhaka';
        date_default_timezone_set($timezone);
    }

    public function authentication()
    {
        $query = 'SELECT username, password from ' . $this->table . '  
         where username = :username LIMIT 1';

        $stmt = $this->conn->prepare($query);
        //binding param
        $stmt->bindParam(':username', $this->username);
        //execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $pass = trim($this->password);

        if ($row != '') {
            if (password_verify($pass , $row['password'])) {
                return $this->msg = 'Successful';
            } else {
                return $this->msg = 'not found';
            }
        } else {
            return $this->msg = 'not found';
        }
    }

    public function get_customer()
    {
        $query = 'SELECT * from ' . $this->tableTarty . ' 
          where partyPhone = :phone or partyPhone = :code LIMIT 1';
        $stmt = $this->conn->prepare($query);
        //binding param
        $stmt->bindParam(':phone', $this->customerNo);
        $stmt->bindParam(':code', $this->customerNo);

        //execute query
        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row != '') {
                $this->id = $row['id'];
                $this->name = $row['partyName'];
                $this->email = $row['partyEmail'];
                $this->address = $row['partyAddress'];
                $this->msg = 'success_getCustomer';
            } else {
                $this->msg = 'not_found';
            }
        }
    }
    public function payBill()
    {
        //get party Id
        $query = 'SELECT id from ' . $this->tableTarty . ' where partyPhone LIKE  :phone LIMIT 1';
        $stmt = $this->conn->prepare($query);
        //binding param
        $customerNo = '%' . $this->customerNo . '%';
        $stmt->bindParam(':phone',  $customerNo, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row != "") {
            $this->partyId = $row['id'];
        } else {
            return false;
        }

        $voucherNo = '';

        $sql = 'SELECT LPAD(IFNULL(max(voucherNo),0)+1, 6, 0) as voucherCode, LPAD(IFNULL(max(voucherNo),0)+2, 6, 0) as voucherReceiveCode FROM tbl_paymentVoucher WHERE tbl_partyId=' . $row['id'] . ' AND customerType = "Party"';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        while ($prow = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $voucherNo = $prow['voucherCode'];
        }

        if ($voucherNo == "") {
            $voucherNo = "000001";
        }

        $sql = "INSERT INTO tbl_paymentvoucher (paymentDate, amount, tbl_partyId, paymentMethod,bkash_trxId,voucherType,entryDate,voucherNo,remarks,receivedType,bkash_RefNumber) VALUES (:paymentDate,:amount,:partyId,:payMethod,:bkash_trxId,:voucherType,:entryDate,:voucherNo,:remarks,'MMB',:bkash_RefNumber)"; //voucherType
        $stmt = $this->conn->prepare($sql);

        $this->paymentDate = htmlspecialchars(strip_tags($this->paymentDate));
        $this->Amount      = htmlspecialchars(strip_tags($this->Amount));
        $this->partyId     = htmlspecialchars(strip_tags($this->partyId));
        $this->TrxId       = htmlspecialchars(strip_tags($this->TrxId));

        $today = date('Y:m:d h:i:s');
        $voucherType = "PartySale";
        $this->bkash_RefNumber = $this->partyId . $voucherNo;
        $remarks = 'Bkash api Bill Pay For TranxId : ' . $this->TrxId . ' and Voucher No :' . $voucherNo;
        $stmt->bindParam(':paymentDate', $this->paymentDate);
        $stmt->bindParam(':amount', $this->Amount);
        $stmt->bindParam(':partyId', $this->partyId);
        $stmt->bindParam(':payMethod', $this->payMethod);
        $stmt->bindParam(':bkash_trxId', $this->TrxId);
        $stmt->bindParam(':entryDate', $today, PDO::PARAM_STR);
        $stmt->bindParam(':voucherType',  $voucherType, PDO::PARAM_STR);
        $stmt->bindParam(':voucherNo',  $voucherNo, PDO::PARAM_STR);
        $stmt->bindParam(':remarks',  $remarks, PDO::PARAM_STR);
        $stmt->bindParam(':bkash_RefNumber',  $this->bkash_RefNumber, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function searchTransaction()
    {
        //query
        $query = 'SELECT * from tbl_paymentvoucher Where paymentMethod = "Bkash" AND bkash_trxId = :bkash_trxId order by id DESC';

        //prepare statement
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':bkash_trxId', $this->TrxId);

        //execute query
        $stmt->execute();
        //     $row = $stmt->fetch(PDO::FETCH_ASSOC);
        //if we found the user

        return $stmt;
    }
}
