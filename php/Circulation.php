<?php
/**
 * 图书流通API
 */

include "./DbBase.php";
$isGet = isset($_GET['action']);
$action = ($isGet ? $_GET['action'] : $_POST['action']);

if ($_SESSION['Number'] == null) {
    echo Error("未登录或登录已失效,请重新登录!");
    return;
}

switch ($action) {
    case 'GetAllBorrowing':
        GetAllBorrowing($isGet);
        break;
    case 'GetAllBorrowingRecords':
        GetAllBorrowingRecords($isGet);
        break;
    case 'Borrowing':
        Borrowing($isGet);
        break;
    case 'ReNew':
        ReNew($isGet);
        break;
    case 'SendBack':
        SendBack($isGet);
        break;
    case 'GetAllSendBack':
        GetAllSendBack($isGet);
        break;
    case 'GetAllFine':
        GetAllFine($isGet);
        break;
    case 'PaymentById':
        PaymentById($isGet);
        break;
    case 'PaymentByReader':
        PaymentByReader($isGet);
        break;
}

// 获取未还的借阅记录
function GetAllBorrowing($isGet)
{
    $sql = "SELECT *, BookInfos.Name as BName, Readers.Name as RName FROM
        Borrowing INNER JOIN Books INNER JOIN BookInfos INNER JOIN Readers
        ON BookBarcode = Books.Barcode AND CallNo = BookCallNo AND Readers.Barcode = RBarcode
        WHERE 1 = 1";

    if ($_SESSION['TypeId'] != 0) { // 若是读者登录，则只显示自己的借阅记录
        $barcode = $_SESSION['Barcode'];
        $sql = "$sql AND Borrowing.RBarcode = '$barcode' ";
    }

    $callNo = ($isGet ? $_GET['callNo'] : $_POST['callNo']);
    $bName = $isGet ? $_GET['bName'] : $_POST['bName'];
    $rName = $isGet ? $_GET['rName'] : $_POST['rName'];
    LikeScreen($sql, "CallNo", $callNo);
    LikeScreen($sql, "BookInfos.Name", $bName);
    LikeScreen($sql, "Readers.Name", $rName);
    echo GetAll($sql, ($isGet ? $_GET['page'] : $_POST['page']), ($isGet ? $_GET['limit'] : $_POST['limit']));
}

// 获取已还的借阅记录
function GetAllBorrowingRecords($isGet)
{
    $sql = "SELECT *, BookInfos.Name as BName, Readers.Name as RName FROM
        BorrowingRecords INNER JOIN Books INNER JOIN BookInfos INNER JOIN Readers
        INNER JOIN SendBack
        ON BorrowingRecords.BookBarcode = Books.Barcode AND CallNo = BookCallNo AND Readers.Barcode = BorrowingRecords.RBarcode AND SendBack.BRId = BorrowingRecords.Id
        WHERE 1 = 1";

    if ($_SESSION['TypeId'] != 0) { // 若是读者登录，则只显示自己的借阅记录
        $barcode = $_SESSION['Barcode'];
        $sql = "$sql AND BorrowingRecords.RBarcode = '$barcode' ";
    }

    $callNo = ($isGet ? $_GET['callNo'] : $_POST['callNo']);
    $bName = $isGet ? $_GET['bName'] : $_POST['bName'];
    $rName = $isGet ? $_GET['rName'] : $_POST['rName'];
    LikeScreen($sql, "CallNo", $callNo);
    LikeScreen($sql, "BookInfos.Name", $bName);
    LikeScreen($sql, "Readers.Name", $rName);
    echo GetAll($sql, ($isGet ? $_GET['page'] : $_POST['page']), ($isGet ? $_GET['limit'] : $_POST['limit']));
}

// 借阅图书
function Borrowing($isGet)
{
    $bBarcode = ($isGet ? $_GET['bBarcode'] : $_POST['bBarcode']);
    $rBarcode = ($isGet ? $_GET['rBarcode'] : $_POST['rBarcode']);

    if ($bBarcode == null) {
        echo Error("图书条形码不能为空");
        return;
    }

    if ($rBarcode == null) {
        echo Error("读者条形码不能为空");
        return;
    }

    $DbContext = ConnDB();
    $otherSql = "SELECT * FROM Books WHERE Barcode = '$bBarcode'";
    $rs = $DbContext->query($otherSql)->fetch(PDO::FETCH_ASSOC);
    if ($rs == null) {
        echo Error("图书不存在");
        return;
    }

    if ($rs["State"] == 1) {
        echo Error("图书已外借");
        return;
    }

    if ($rs["Disabled"]) {
        echo Error("图书已禁用");
        return;
    }

    $bookCallNo = $rs["BookCallNo"];
    $otherSql = "SELECT * FROM Readers INNER JOIN ReaderType ON TypeId = ReaderType.Id WHERE Barcode = '$rBarcode'";
    $rs = $DbContext->query($otherSql)->fetch(PDO::FETCH_ASSOC);
    $otherSql = "SELECT SUM(Price) as Total FROM Fine WHERE Payment = False AND  RBarcode = '$rBarcode'";
    $rs1 = $DbContext->query($otherSql)->fetch(PDO::FETCH_ASSOC);

    if ($rs == null) {
        echo Error("读者不存在");
        return;
    }

    if ($rs["Loss"]) {
        echo Error("读者已挂失");
        return;
    }

    if ($rs["Disabled"]) {
        echo Error("读者已禁用");
        return;
    }

    if ($rs1["Total"] >= $rs["DebitAmount"]) {
        echo Error("已达到欠费额度，请缴费！");
        return;
    }

    $startTime = date('Y-m-d H:i:s');
    $day = $rs['LoanPeriod'];
    $expiryTime = date('Y-m-d H:i:s', strtotime("+$day day"));
    $operator = $_SESSION['Number'];

    $sql = "INSERT INTO Borrowing
            (BookBarcode, RBarcode, StartTime, ExpiryTime, DisabledRenew, Operator)
            VALUES
            ('$bBarcode', '$rBarcode', '$startTime', '$expiryTime', False, '$operator')";

    $result = Create($sql);

    $temp = json_decode($result);

    // 若借阅成功则更新图书信息
    if ($temp->code == 0) {
        // 更新图书状态
        $otherSql = "UPDATE Books SET State = True WHERE  Barcode = '$bBarcode'";
        Update($otherSql);
        // 更新图书库存信息
        $otherSql = "UPDATE BookStatistics SET Surplus =  Surplus - 1, NumOfLoans = NumOfLoans + 1 WHERE BookCallNo = '$bookCallNo'";
        Update($otherSql);
    }

    echo $result;
}

// 归还图书
function SendBack($isGet)
{
    $bBarcode = ($isGet ? $_GET['bBarcode'] : $_POST['bBarcode']);

    if ($bBarcode == null) {
        echo Error("图书条形码不能为空");
        return;
    }

    $DbContext = ConnDB();
    $otherSql = "SELECT * FROM Books WHERE Barcode = '$bBarcode'";
    $rs = $DbContext->query($otherSql)->fetch(PDO::FETCH_ASSOC);
    if ($rs == null) {
        echo Error("图书不存在");
        return;
    }

    if ($rs["State"] == 0) {
        echo Error("图书未外借");
        return;
    }

    $operator = $_SESSION['Number'];
    $bookCallNo = $rs["BookCallNo"]; // 图书索书号用于更新库存

    $otherSql = "SELECT * FROM Borrowing WHERE BookBarcode = '$bBarcode'";
    $copySql = "INSERT INTO BorrowingRecords SELECT * FROM Borrowing WHERE BookBarcode = '$bBarcode'";

    // 把数据转移到借阅记录表中
    Update($copySql);

    // 查询借阅信息
    $rs1 = SelectSql($otherSql);

    $rBarcode = $rs1["RBarcode"];
    $brId = $rs1["Id"]; // 归还表BRId

    // 添加归还记录
    $time = date('Y-m-d H:i:s');
    $sql = "INSERT INTO SendBack
                (BookBarcode, RBarcode, ReturnTime, Operator, BRId)
                VALUES
                ('$bBarcode', '$rBarcode', '$time', '$operator', $brId)";

    $result = Create($sql);

    $temp = json_decode($result);

    // 若归还成功则更新图书信息
    if ($temp->code == 0) {
        // 更新图书状态
        $otherSql = "UPDATE Books SET State = False WHERE  Barcode = '$bBarcode'";
        Update($otherSql);
        // 更新图书库存信息
        $otherSql = "UPDATE BookStatistics SET Surplus =  Surplus + 1 WHERE BookCallNo = '$bookCallNo'";
        Update($otherSql);

        // 进行超期罚款
        if (strtotime($rs1["ExpiryTime"]) < strtotime($time)) {
            $otherSql = "SELECT * FROM SendBack WHERE BRId = $brId";
            $rs = SelectSql($otherSql);
            $sbId = $rs["Id"];

            $datetime_start = new DateTime($time);
            $datetime_end = new DateTime($rs1["ExpiryTime"]);
            $days = $datetime_start->diff($datetime_end)->days;
            echo $days;
            if ($days >= 1) {
                $price = $days * 0.1;
                $otherSql = "INSERT INTO Fine
                        (BookBarcode, RBarcode, SBId, Price, Payment)
                        VALUES
                        ('$bBarcode', '$rBarcode', $sbId, $price, False)";
                echo $otherSql;
                Update($otherSql);
            }
        }

        $otherSql = "DELETE FROM Borrowing WHERE BookBarcode = '$bBarcode'";
        Update($otherSql);
    } else {
        $otherSql = "DELETE FROM BorrowingRecords WHERE BookBarcode = '$bBarcode'";
        Update($otherSql);
    }

    echo $result;
}

// 续借
function ReNew($isGet)
{
    $id = $isGet ? $_GET['id'] : $_POST['id'];

    if ($id == null) {
        echo Error("参数错误");
        return;
    }

    $otherSql = "SELECT * FROM Borrowing WHERE Id = '$id'";
    $DbContext = ConnDB();
    $rs = $DbContext->query($otherSql)->fetch(PDO::FETCH_ASSOC);

    if ($rs["DisabledRenew"]) {
        echo Error("不能再次续借");
        return;
    }

    $expiryTime = $rs["ExpiryTime"];
    $expiryTime = date('Y-m-d H:i:s', strtotime("{$expiryTime} +15 day"));
    $sql = "UPDATE Borrowing SET DisabledRenew = True, ExpiryTime = '$expiryTime' WHERE Id = '$id'";
    echo Update($sql);
}

function GetAllSendBack($isGet)
{
    $sql = "SELECT *, BookInfos.Name as BName, Readers.Name as RName FROM
        SendBack INNER JOIN Books INNER JOIN BookInfos INNER JOIN Readers
        ON BookBarcode = Books.Barcode AND CallNo = BookCallNo AND Readers.Barcode = RBarcode
        WHERE 1 = 1";

    if ($_SESSION['TypeId'] != 0) { // 若是读者登录，则只显示自己的借阅记录
        $barcode = $_SESSION['Barcode'];
        $sql = "$sql AND SendBack.RBarcode = '$barcode'";
    }

    echo GetAll($sql, ($isGet ? $_GET['page'] : $_POST['page']), ($isGet ? $_GET['limit'] : $_POST['limit']));
}

function GetAllFine($isGet)
{
    $sql = "SELECT *, BookInfos.Name as BName, Readers.Name as RName, Fine.Price as Price FROM
        Fine INNER JOIN Books INNER JOIN BookInfos INNER JOIN Readers
        ON BookBarcode = Books.Barcode AND CallNo = BookCallNo AND Readers.Barcode = RBarcode
        WHERE 1 = 1";

    if ($_SESSION['TypeId'] != 0) { // 若是读者登录，则只显示自己的借阅记录
        $barcode = $_SESSION['Barcode'];
        $sql = "$sql AND Fine.RBarcode = '$barcode'";
    }

    echo GetAll($sql, ($isGet ? $_GET['page'] : $_POST['page']), ($isGet ? $_GET['limit'] : $_POST['limit']));
}

// 归还单条欠款
function PaymentById($isGet)
{
    $id = $isGet ? $_GET['id'] : $_POST['id'];

    if ($id == null) {
        echo Error("参数错误");
        return;
    }
    $operator = $_SESSION['Number'];

    $sql = "UPDATE Fine SET Payment = True, Operator='$operator' WHERE Id = '$id'";
    echo Update($sql);
}

// 归还读者欠款
function PaymentByReader($isGet)
{
    $rBarcode = $isGet ? $_GET['rBarcode'] : $_POST['rBarcode'];
    $operator = $_SESSION['Number'];

    $sql = "UPDATE Fine SET Payment = True, Operator='$operator' WHERE RBarcode = '$rBarcode'";
    echo Update($sql);
}
