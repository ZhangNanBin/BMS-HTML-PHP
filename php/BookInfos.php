<?php
/**
 * 图书基础信息API
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
        GetAllBookInfos($isGet);
        break;
    case 'Get':
        GetByCallNo($isGet);
        break;
    case 'Create':
        CreateBookInfo($isGet);
        break;
    case 'Update':
        UpdateBookInfo($isGet);
        break;
}

// 获取全部图书信息
function GetAllBookInfos($isGet)
{
    $sql = "SELECT * FROM BookInfos INNER JOIN BookStatistics ON CallNo = BookCallNo  WHERE 1 = 1";
    $callNo = ($isGet ? $_GET['callNo'] : $_POST['callNo']);
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    LikeScreen($sql, "CallNo", $callNo);
    LikeScreen($sql, "Name", $name);
    echo GetAll($sql, ($isGet ? $_GET['page'] : $_POST['page']), ($isGet ? $_GET['limit'] : $_POST['limit']));
}

// 通过索书号获取图书信息
function GetByCallNo($isGet)
{
    $callNo = ($isGet ? $_GET['callNo'] : $_POST['callNo']);
    $sql = "SELECT * FROM BookInfos WHERE CallNo = '$callNo'";
    echo Get($sql);
}

// 创建图书信息
function CreateBookInfo($isGet)
{
    $callNo = ($isGet ? $_GET['callNo'] : $_POST['callNo']);
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    $typeNumber = $isGet ? $_GET['typeNumber'] : $_POST['typeNumber'];
    $author = $isGet ? $_GET['author'] : $_POST['author'];
    $translator = $isGet ? $_GET['translator'] : $_POST['translator'];
    $press = $isGet ? $_GET['press'] : $_POST['press'];
    $datePublication = $isGet ? $_GET['datePublication'] : $_POST['datePublication'];
    $price = $isGet ? $_GET['price'] : $_POST['price'];
    $price = $price != null ? $price : 0;
    $page = $isGet ? $_GET['page'] : $_POST['page'];
    $page = $page != null ? $page : 0;

    if ($callNo == null) {
        echo Error("索书号不能为空");
        return;
    }

    if ($name == null) {
        echo Error("图书名称不能为空");
        return;
    }

    if ($typeNumber == null) {
        echo Error("图书类型不能为空");
        return;
    }

    if ($datePublication == null) {
        echo Error("出版年份不能为空");
        return;
    }

    $sql = "INSERT INTO BookInfos
            (CallNo, Name, TypeNumber, Author, Translator, Press, DatePublication, Price, Page)
            VALUES
            ('$callNo', '$name', '$typeNumber', '$author', '$translator', '$press', '$datePublication','$price', '$page')";

    $otherSql = "INSERT INTO BookStatistics
                (BookCallNo, NumOfLoans, Surplus, Total)
                VALUES
                ('$callNo', 0, 0, 0)";
    $result = Create($sql);

    $temp = json_decode($result);
    if ($temp->code == 0) {
        Create($otherSql);
    }

    echo $result;
}

// 更新图书基本信息
function UpdateBookInfo($isGet)
{
    $callNo = ($isGet ? $_GET['callNo'] : $_POST['callNo']);
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    $typeNumber = $isGet ? $_GET['typeNumber'] : $_POST['typeNumber'];
    $author = $isGet ? $_GET['author'] : $_POST['author'];
    $translator = $isGet ? $_GET['translator'] : $_POST['translator'];
    $press = $isGet ? $_GET['press'] : $_POST['press'];
    $datePublication = $isGet ? $_GET['datePublication'] : $_POST['datePublication'];
    $price = $isGet ? $_GET['price'] : $_POST['price'];
    $price = $price != null ? $price : 0;
    $page = $isGet ? $_GET['page'] : $_POST['page'];
    $page = $page != null ? $page : 0;

    if ($callNo == null) {
        echo Error("索书号不能为空");
        return;
    }

    if ($name == null) {
        echo Error("图书名称不能为空");
        return;
    }

    if ($typeNumber == null) {
        echo Error("图书类型不能为空");
        return;
    }

    if ($datePublication == null) {
        echo Error("出版年份不能为空");
        return;
    }

    $sql = "UPDATE BookInfos SET Name = '$name', TypeNumber = '$typeNumber', Author = '$author', Translator = '$translator', Press = '$press', DatePublication = '$datePublication', Price = '$price', Page = '$page' WHERE CallNo = '$callNo'";
    echo Update($sql);
}
