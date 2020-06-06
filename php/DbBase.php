<?php
if (!session_id()) {
    session_start();
}


error_reporting(E_ALL & ~E_NOTICE);

function ConnDB()
{
    $db = new PDO("mysql:host=localhost;dbname=BMS", "root", "root");
    if (!$db) {
        echo "数据库连接失败！";
    }
    $db->query("set names utf8");
    return $db;
}

function Success($sql, $data, $count)
{
    return json_encode(array("data" => $data, "code" => 0, "msg" => "Success", "count" => $count, "sql" => $sql));
}

function Error($msg, $sql)
{
    return json_encode(array("code" => 1, "msg" => $msg, "sql" => $sql));
}

function GetAll($sql, $page, $limit)
{
    $DbContext = ConnDB();
    $temp = ($page - 1) * $limit;
    $tempsql = "$sql  LIMIT $temp, $limit";

    $rs = $DbContext->query($sql);
    $count = $rs->rowCount();

    $result = $DbContext->query($tempsql);
    $data = $result->fetchAll(PDO::FETCH_ASSOC);


    $arr = $DbContext->errorInfo();
    return Base($arr, $tempsql, $data, $count);
}

function GetFull($sql)
{
    $DbContext = ConnDB();
    $result = $DbContext->query($sql);
    $arr = $DbContext->errorInfo();
    $data = $result->fetchAll(PDO::FETCH_ASSOC);
    $arr = $DbContext->errorInfo();
    return Base($arr, $sql, $data, 0);
}

function Get($sql)
{
    $DbContext = ConnDB();
    $result = $DbContext->query($sql);
    $data = $result->fetch(PDO::FETCH_ASSOC);
    $count = $result->rowCount();
    $arr = $DbContext->errorInfo();
    return Base($arr, $sql, $data, $count);
}

function Update($sql)
{
    $DbContext = ConnDB();
    $result = $DbContext->query($sql);
    $arr = $DbContext->errorInfo();
    return Base($arr, $sql, null, null);
}

function Create($sql)
{
    $DbContext = ConnDB();
    $result = $DbContext->query($sql);
    $arr = $DbContext->errorInfo();

    return Base($arr, $sql, null, null);
}

function Base($arr, $sql, $data, $count)
{
    if ($arr[0] == "00000") {
        return Success($sql, $data, $count);
    }

    return Error($arr[2], $sql);
}

function LikeScreen(&$sql, $lable, $value)
{
    $sql = $value != null ? "$sql AND $lable LIKE '%$value%'" : $sql;
}

function StringEqualScreen(&$sql, $lable, $value)
{
    $temp = $value != null ? "'$value'" : $value;
    EqualScreen($sql, $lable, $temp);
}

function EqualScreen(&$sql, $lable, $value)
{
    $sql = $value != null ? "$sql AND $lable = $value" : $sql;
}

function SelectSql($sql)
{
    $DbContext = ConnDB();
    $result = $DbContext->query($sql);
    return $result->fetch(PDO::FETCH_ASSOC);
}
