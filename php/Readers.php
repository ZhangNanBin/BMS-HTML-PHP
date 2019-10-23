<?php
/**
 * 读者API
 */
include "./DbBase.php";
$isGet = isset($_GET['action']);
$action = ($isGet ? $_GET['action'] : $_POST['action']);

if ($_SESSION['Number'] == null) {
    echo Error("未登录或登录已失效,请重新登录!");
    return;
}

switch ($action) {
    case 'GetAll':
        GetAllReaders($isGet);
        break;
    case 'Get':
        GetByNumber($isGet);
        break;
    case 'Create':
        CreateReader($isGet);
        break;
    case 'Update':
        UpdateReader($isGet);
        break;
    case 'Loss':
        Loss($isGet);
        break;
    case 'Disabled':
        Disabled($isGet);
        break;
}

function GetAllReaders($isGet)
{
    $sql = "SELECT * FROM Readers WHERE 1 = 1";
    $number = $isGet ? $_GET['number'] : $_POST['number'];
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    $barcode = $isGet ? $_GET['barcode'] : $_POST['barcode'];
    LikeScreen($sql, "Number", $number);
    LikeScreen($sql, "Name", $name);
    LikeScreen($sql, "Barcode", $barcode);

    echo GetAll($sql, ($isGet ? $_GET['page'] : $_POST['page']), ($isGet ? $_GET['limit'] : $_POST['limit']));
}

function GetByNumber($isGet)
{
    $number = ($isGet ? $_GET['number'] : $_POST['number']);
    $sql = "SELECT Number, Name, TypeId, Barcode, Sex, EnrollmentYear, RegistrationDate, Loss, Disabled FROM Readers WHERE Number = '$number'";
    echo Get($sql);
}

function CreateReader($isGet)
{
    $number = $isGet ? $_GET['number'] : $_POST['number'];
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    $password = $isGet ? $_GET['password'] : $_POST['password'];
    $typeId = $isGet ? $_GET['typeId'] : $_POST['typeId'];
    $barcode = $isGet ? $_GET['barcode'] : $_POST['barcode'];
    $sex = $isGet ? $_GET['sex'] : $_POST['sex'];
    $enrollmentYear = $isGet ? $_GET['enrollmentYear'] : $_POST['enrollmentYear'];
    $registrationDate = $isGet ? $_GET['registrationDate'] : $_POST['registrationDate'];
    $loss = $isGet ? $_GET['loss'] : $_POST['loss'];
    $disabled = $isGet ? $_GET['disabled'] : $_POST['disabled'];
    $loss = $loss != null ? $loss : 0;
    $disabled = $disabled != null ? $disabled : 0;

    if ($number == null) {
        echo Error("编号不能为空");
        return;
    }

    if ($name == null) {
        echo Error("姓名不能为空");
        return;
    }

    if ($password == null) {
        echo Error("密码不能为空");
        return;
    }

    if ($typeId == null) {
        echo Error("读者类型不能为空");
        return;
    }
    if ($enrollmentYear == null) {
        echo Error("入学年份不能为空");
        return;
    }
    if ($registrationDate == null) {
        echo Error("注册日期不能为空");
        return;
    }

    if ($loss != true && $loss != false) {
        echo Error("loss值不合法");
        return;
    }

    if ($disabled != true && $disabled != false) {
        echo Error("disabled值不合法");
        return;
    }

    $sql = "INSERT INTO Readers
            (Number, Name, PassWord, TypeId, Barcode, Sex, EnrollmentYear, RegistrationDate, Loss, Disabled)
            VALUES
            ('$number', '$name', '$password', '$typeId', '$barcode', '$sex', '$enrollmentYear', '$registrationDate', $loss, $disabled)";
    echo Create($sql);
}

function UpdateReader($isGet)
{
    $number = $isGet ? $_GET['number'] : $_POST['number'];
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    $password = $isGet ? $_GET['password'] : $_POST['password'];
    $typeId = $isGet ? $_GET['typeId'] : $_POST['typeId'];
    $barcode = $isGet ? $_GET['barcode'] : $_POST['barcode'];
    $sex = $isGet ? $_GET['sex'] : $_POST['sex'];
    $enrollmentYear = $isGet ? $_GET['enrollmentYear'] : $_POST['enrollmentYear'];
    $registrationDate = $isGet ? $_GET['registrationDate'] : $_POST['registrationDate'];
    $loss = $isGet ? $_GET['loss'] : $_POST['loss'];
    $disabled = $isGet ? $_GET['disabled'] : $_POST['disabled'];
    $loss = $loss != null ? $loss : 0;
    $disabled = $disabled != null ? $disabled : 0;

    if ($number == null) {
        echo Error("编号不能为空");
        return;
    }

    if ($name == null) {
        echo Error("姓名不能为空");
        return;
    }

    if ($password == null) {
        echo Error("密码不能为空");
        return;
    }

    if ($typeId == null) {
        echo Error("读者类型不能为空");
        return;
    }
    if ($enrollmentYear == null) {
        echo Error("入学年份不能为空");
        return;
    }
    if ($registrationDate == null) {
        echo Error("注册日期不能为空");
        return;
    }

    if ($loss != true && $loss != false) {
        echo Error("loss值不合法");
        return;
    }

    if ($disabled != true && $disabled != false) {
        echo Error("disabled值不合法");
        return;
    }

    $sql = "UPDATE Readers SET Name = '$name', PassWord = '$password', TypeId = '$typeId', Barcode = '$barcode', Sex = '$sex',  EnrollmentYear = '$enrollmentYear', RegistrationDate = '$registrationDate', Loss = $loss, Disabled = $disabled WHERE  Number = '$number'";
    echo Update($sql);
}

function Loss($isGet)
{
    $number = $isGet ? $_GET['number'] : $_POST['number'];
    $loss = $isGet ? $_GET['loss'] : $_POST['loss'];
    $sql = "UPDATE Readers SET Loss = $loss, WHERE  Number = '$number'";
    echo Update($sql);
}

function Disabled($isGet)
{
    $number = $isGet ? $_GET['number'] : $_POST['number'];
    $disabled = $isGet ? $_GET['disabled'] : $_POST['disabled'];
    $sql = "UPDATE Readers SET Disabled = $disabled WHERE  Number = '$number'";
    echo Update($sql);
}
