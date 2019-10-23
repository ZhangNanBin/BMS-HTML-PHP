<?php
/**
 * 读者类型API
 */

session_start();
include "./DbBase.php";
$isGet = isset($_GET['action']);
$action = ($isGet ? $_GET['action'] : $_POST['action']);

if ($_SESSION['Number'] == null) {
    echo Error("未登录或登录已失效,请重新登录!");
    return;
}

switch ($action) {
    case 'GetAll':
        GetAllReaderType($isGet);
        break;
    case 'Get':
        GetById($isGet);
        break;
    case 'Create':
        CreateReaderType($isGet);
        break;
    case 'Update':
        UpdateReaderType($isGet);
        break;
}

function GetAllReaderType($isGet)
{
    $sql = "SELECT * FROM ReaderType";
    echo GetAll($sql, ($isGet ? $_GET['page'] : $_POST['page']), ($isGet ? $_GET['limit'] : $_POST['limit']));
}

function GetById($isGet)
{
    $id = ($isGet ? $_GET['id'] : $_POST['id']);
    $sql = "SELECT * FROM ReaderType WHERE Id = '$id'";
    echo Get($sql);
}

function CreateReaderType($isGet)
{
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    $loanPeriod = $isGet ? $_GET['loanPeriod'] : $_POST['loanPeriod'];
    $debitAmount = $isGet ? $_GET['debitAmount'] : $_POST['debitAmount'];

    if ($name == null) {
        echo Error("类型名称不能为空");
        return;
    }

    if ($loanPeriod == null) {
        echo Error("可借天数不能为空");
        return;
    }

    if ($debitAmount == null) {
        echo Error("欠款额度不能为空");
        return;
    }

    $sql = "INSERT INTO ReaderType
            (Name, LoanPeriod, DebitAmount)
            VALUES
            ('$name', '$loanPeriod', '$debitAmount')";
    echo Create($sql);
}

function UpdateReaderType($isGet)
{
    $id = $isGet ? $_GET['id'] : $_POST['id'];
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    $loanPeriod = $isGet ? $_GET['loanPeriod'] : $_POST['loanPeriod'];
    $debitAmount = $isGet ? $_GET['debitAmount'] : $_POST['debitAmount'];
    if ($id == null || $id <= 0) {
        echo Error("Id不合法");
        return;
    }

    if ($name == null) {
        echo Error("类型名称不能为空");
        return;
    }

    if ($loanPeriod == null) {
        echo Error("可借天数不能为空");
        return;
    }

    if ($debitAmount == null) {
        echo Error("欠款额度不能为空");
        return;
    }

    $sql = "UPDATE ReaderType SET Name = '$name', LoanPeriod = '$loanPeriod', DebitAmount = '$debitAmount' WHERE Id = '$id'";
    echo Update($sql);
}
