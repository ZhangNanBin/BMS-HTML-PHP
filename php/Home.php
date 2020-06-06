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

$sql = "SELECT COUNT(*) AS TypeCount, 
    (SELECT COUNT(*) FROM Readers) as ReaderCount, 
    (SELECT COUNT(*) FROM Operators) as OperatorCount, 
    (SELECT COUNT(*) FROM Books) as BookCount, 
    (SELECT SUM(NumOfLoans) FROM BookStatistics) as TotalCount, 
    (SELECT COUNT(*) FROM Borrowing) as BorowingCount 
    FROM BookType";

echo Get($sql);
?>