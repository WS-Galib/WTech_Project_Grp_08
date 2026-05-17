<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["ok" => false, "error" => "Unauthenticated request."]);
    exit();
}
include "../models/db.php";
include "../config/helpers.php";
 
if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $comment_id = $_GET["id"] ?? "";
    $active_user_id = $_SESSION["user_id"];

    if (empty($comment_id)) {
        echo json_encode(["ok" => false, "error" => "Invalid comment ID."]);
        exit();
    }
    $database = new db();
    $connection = $database->connection();
    