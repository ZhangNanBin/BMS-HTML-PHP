<?php
/**
 * 读者借阅信息API
 */
include "./DbBase.php";
$isGet = isset($_GET['action']);
$action = ($isGet ? $_GET['action'] : $_POST['action']);

if ($_SESSION['Number'] == null) {
    echo Error("未登录或登录已失效,请重新登录!");
    return;
}

switch ($action) {
    case 'GetReaderBorrowing':
        GetReaderBorrowing($isGet);
        break;
    case 'GetReaderBorrowingStatistics':
        GetReaderBorrowingStatistics($isGet);
        break;

}

function GetReaderBorrowing($isGet)
{
    $sql = "(SELECT *, BookInfos.Name as BName, Readers.Name as RName FROM  BorrowingRecords
        INNER JOIN Books INNER JOIN BookInfos INNER JOIN Readers 
        ON BorrowingRecords.BookBarcode = Books.Barcode AND CallNo = BookCallNo AND Readers.Barcode = BorrowingRecords.RBarcode ORDER BY BorrowingRecords.Id) 
        UNION ALL 
        (SELECT *, BookInfos.Name as BName, Readers.Name as RName FROM Borrowing 
        INNER JOIN Books INNER JOIN BookInfos INNER JOIN Readers
        ON Borrowing.BookBarcode = Books.Barcode AND CallNo = BookCallNo AND Readers.Barcode = Borrowing.RBarcode ORDER BY Borrowing.Id)";
    echo GetFull($sql);
}


function GetReaderBorrowingStatistics($isGet)
{
    $sql = "SELECT *, (SELECT COUNT(*)+(SELECT COUNT(*) FROM BorrowingRecords WHERE Readers.Barcode = BorrowingRecords.RBarcode) FROM Borrowing WHERE Readers.Barcode = Borrowing.RBarcode) as Total FROM Readers WHERE 1 = 1";
    $rName = $isGet ? $_GET['rName'] : $_POST['rName'];
    LikeScreen($sql, "Readers.Name", $rName);
    echo GetAll($sql, ($isGet ? $_GET['page'] : $_POST['page']), ($isGet ? $_GET['limit'] : $_POST['limit']));
}