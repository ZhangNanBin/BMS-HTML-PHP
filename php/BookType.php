<?php
/**
 * 图书类型API
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
        GetAllBookType($isGet);
        break;
    case 'Get':
        GetByNumber($isGet);
        break;
    case 'Create':
        CreateBookType($isGet);
        break;
    case 'Update':
        UpdateBookType($isGet);
        break;
}

// 获取全部图书类型
function GetAllBookType($isGet)
{
    $sql = "SELECT * FROM BookType WHERE 1 = 1";
    $number = ($isGet ? $_GET['number'] : $_POST['number']);
    LikeScreen($sql, "Number", $number);
    echo GetAll($sql, ($isGet ? $_GET['page'] : $_POST['page']), ($isGet ? $_GET['limit'] : $_POST['limit']));
}

function GetByNumber($isGet)
{
    $number = ($isGet ? $_GET['number'] : $_POST['number']);
    $sql = "SELECT * FROM BookType WHERE Number = '$number'";
    echo Get($sql);
}

// 创建图书类型
function CreateBookType($isGet)
{
    $number = ($isGet ? $_GET['number'] : $_POST['number']);
    $name = $isGet ? $_GET['name'] : $_POST['name'];

    if ($number == null) {
        echo Error("编号不能为空");
        return;
    }

    if ($name == null) {
        echo Error("名称不能为空");
        return;
    }

    $sql = "INSERT INTO BookType
            (Number, Name)
            VALUES
            ('$number', '$name')";
    echo Create($sql);
}

// 更新图书类型
function UpdateBookType($isGet)
{
    $number = ($isGet ? $_GET['number'] : $_POST['number']);
    $name = $isGet ? $_GET['name'] : $_POST['name'];
    if ($number == null) {
        echo Error("编号不能为空");
        return;
    }

    if ($name == null) {
        echo Error("名称不能为空");
        return;
    }
    $sql = "UPDATE BookType SET Name = '$name' WHERE Number = '$number'";
    echo Update($sql);
}
