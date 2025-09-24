<?php
require "db_connect.php";

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['username'], $data['content'])) {
    die(json_encode(["error" => "参数错误"]));
}

try {
    $stmt = $pdo->prepare("INSERT INTO chat_messages (username, content, timestamp) VALUES (?, ?, NOW())");
    $stmt->execute([$data['username'], $data['content']]);
    
    echo json_encode(["status" => "success"]);
} catch (PDOException $e) {
    die(json_encode(["error" => "发送消息失败: " . $e->getMessage()]));
}
?>