<?php
/**
 * 操作员API
 * GetAll 获取所有操作员数据
 * Get 根据编号获取数据
 * Create 创建操作员
 * Update 更新操作员
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
        GetAllOperators($isGet);
        break;
    case 'Get':
        GetByNumber($isGet);
        break;
    case 'Create':
        CreateOperator($isGet);
        break;
    case 'Update':
        UpdateOperator($isGet);
        break;
}

// 获取所有操作员信息
function GetAllOperators($isGet)
{
    $sql = "SELECT Number, Name FROM Operators WHERE 1 = 1";
    $number = $isGet ? $_GET['number'] : $_POST['number'];
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    LikeScreen($sql, "Number", $number);
    LikeScreen($sql, "Name", $name);
    echo GetAll($sql, ($isGet ? $_GET['page'] : $_POST['page']), ($isGet ? $_GET['limit'] : $_POST['limit']));
}

function GetByNumber($isGet)
{
    $number = ($isGet ? $_GET['number'] : $_POST['number']);
    $sql = "SELECT Number, Name FROM Operators WHERE Number = '$number'";
    echo Get($sql);
}

// 创建操作员信息
function CreateOperator($isGet)
{
    $number = $isGet ? $_GET['number'] : $_POST['number'];
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    $password = $isGet ? $_GET['password'] : $_POST['password'];

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

    $sql = "INSERT INTO Operators
            (Number, Name, PassWord)
            VALUES
            ('$number', '$name', '$password')";
    echo Create($sql);
}

// 更新操作员信息
function UpdateOperator($isGet)
{
    $number = $isGet ? $_GET['number'] : $_POST['number'];
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    $password = $isGet ? $_GET['password'] : $_POST['password'];

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

    $sql = "UPDATE Operators SET Name = '$name', PassWord = '$password' WHERE  Number = '$number'";
    echo Update($sql);
}
