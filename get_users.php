<?php
require "db_connect.php";

try {
    // 获取所有用户
    $stmt = $pdo->query("SELECT nickname FROM users ORDER BY updated_at DESC");
    $users = $stmt->fetchAll();
    
    echo json_encode([
        "status" => "success",
        "users" => $users
    ]);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "获取用户列表失败: " . $e->getMessage()]));
}
?>