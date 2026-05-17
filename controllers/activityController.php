<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["ok" => false, "error" => "Unauthenticated"]);
    exit();
}
include "../models/db.php";
$project_id = $_GET["project_id"] ?? "";
$filter_user_id = $_GET["user_id"] ?? "all";

if (empty($project_id)) {
    echo json_encode(["ok" => false, "error" => "Missing project ID"]);
    exit();
}
$database = new db();
$connection = $database->connection();
