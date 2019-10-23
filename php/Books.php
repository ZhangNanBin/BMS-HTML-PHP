<?php
/**
 * 图书基础信息API
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
        GetAllBooks($isGet);
        break;
    case 'Get':
        GetByBarcode($isGet);
        break;
    case 'Create':
        CreateBook($isGet);
        break;
    case 'Update':
        UpdateBook($isGet);
        break;
    case 'State':
        State($isGet);
        break;
    case 'Disabled':
        Disabled($isGet);
        break;
}

// 获取所有图书
// bookCallNo 按图书索书号筛选
// name 按图书名称筛选
// barcode 按条形码筛选
// state 按状态 true为已借出 false为在库存筛选
function GetAllBooks($isGet)
{
    $sql = "SELECT * FROM Books INNER JOIN BookInfos ON CallNo = BookCallNo WHERE 1 = 1";
    $bookCallNo = ($isGet ? $_GET['bookCallNo'] : $_POST['bookCallNo']);
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    $barcode = $isGet ? $_GET['barcode'] : $_POST['barcode'];
    $state = $isGet ? $_GET['state'] : $_POST['state'];
    if ($state != null && ($state != true && $state != false)) {
        echo Error("状态值不合法");
        return;
    }

    LikeScreen($sql, "BookCallNo", $bookCallNo);
    LikeScreen($sql, "Name", $name);
    LikeScreen($sql, "Barcode", $barcode);
    StringEqualScreen($sql, "Barcode", $barcode);
    EqualScreen($sql, "State", $state);
    echo GetAll($sql, ($isGet ? $_GET['page'] : $_POST['page']), ($isGet ? $_GET['limit'] : $_POST['limit']));
}

// 通过条形码获取图书
function GetByBarcode($isGet)
{
    $barcode = $isGet ? $_GET['barcode'] : $_POST['barcode'];
    $sql = "SELECT * FROM Books WHERE Barcode = '$barcode'";
    echo Get($sql);
}

// 创建图书
function CreateBook($isGet)
{
    $barcode = ($isGet ? $_GET['barcode'] : $_POST['barcode']);
    $bookCallNo = $isGet ? $_GET['bookCallNo'] : $_POST['bookCallNo'];
    $operator = $_SESSION['Number'];

    if ($barcode == null) {
        echo Error("图书条形码不能为空");
        return;
    }

    if ($bookCallNo == null) {
        echo Error("索书号不能为空");
        return;
    }

    $sql = "INSERT INTO Books
            (Barcode, BookCallNo, Operator, State, Disabled)
            VALUES
            ('$barcode', '$bookCallNo', '$operator', False, False)";

    $otherSql = "UPDATE BookStatistics SET Surplus =  Surplus + 1, Total = Total + 1
                WHERE BookCallNo = '$bookCallNo'";
    $result = Create($sql);

    $temp = json_decode($result);
    if ($temp->code == 0) {
        Update($otherSql);
    }
    echo $result;
}

// 禁用图书
function Disabled($isGet)
{
    $barcode = $isGet ? $_GET['barcode'] : $_POST['barcode'];
    $disabled = $isGet ? $_GET['disabled'] : $_POST['disabled'];
    $sql = "UPDATE Books SET Disabled = $disabled WHERE  Barcode = '$barcode'";
    echo Update($sql);
}
