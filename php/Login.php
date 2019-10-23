<?php
/**
 * 登录API
 */

include "./DbBase.php";
$isGet = isset($_GET['action']);
$action = ($isGet ? $_GET['action'] : $_POST['action']);
$number = ($isGet ? $_GET['number'] : $_POST['number']);

switch ($action) {
    case 'CheckUserName':
        CheckUserName($isGet);
        break;
    case 'Login':
        Login($isGet);
        break;
    case 'CheckStatus':
        CheckStatus($isGet);
        break;
}

//检查用户名
function CheckUserName($isGet)
{
    $DbContext = ConnDB();
    $number = ($isGet ? $_GET['number'] : $_POST['number']);
    $action = ($isGet ? $_GET['action'] : $_POST['action']);
    $sql = "SELECT * FROM Operators WHERE Number = '$number'";
    $rs = $DbContext->query($sql)->fetch(PDO::FETCH_ASSOC);
    $returnValue = $rs != null;
    echo Success($sql, $returnValue, 0);
}

//检查用户名密码
// typeId 0为操作员 其他为读者
function Login($isGet)
{
    $number = $_POST['number'];
    $password = $_POST['password'];
    $typeId = $_POST['typeId'];
    $DbContext = ConnDB();

    if ($number == null) {
        echo Error("用户名不能为空");
        return;
    }

    if ($password == null) {
        echo Error("密码不能为空");
        return;
    }

    if ($typeId != 0 && $typeId == null) {
        echo Error("类型不能为空");
        return;
    }

    $sql = $typeId == 0 ? "SELECT Number, Name FROM Operators WHERE Number ='$number' AND Password = '$password'"
    : "SELECT Number, Name, TypeId, Barcode, Sex, EnrollmentYear, RegistrationDate FROM Readers WHERE Number ='$number' AND Password = '$password'";

    $rs = $DbContext->query($sql)->fetch(PDO::FETCH_ASSOC);

    $returnValue = $rs != null;
    if ($returnValue) {
        $_SESSION['Name'] = $rs['Name'];
        $_SESSION['Number'] = $rs['Number'];
        $tempId = $typeId == 0 ? 0 : $rs['TypeId'];
        $_SESSION['Barcode'] = $typeId != 0 ? $rs['Barcode'] : "";
        $_SESSION['TypeId'] = $tempId;
        $rs["TypeId"] = $tempId;
        echo Success($sql, $rs, 0);
    } else {
        echo Error("用户名或密码不正确", $sql);
    }
}

//检查是否登录
function CheckStatus()
{
    if (isset($_SESSION['Name'])) {
        echo Success(null, $_SESSION['Name'], 0);
    } else {
        echo Error("");
    }
}
