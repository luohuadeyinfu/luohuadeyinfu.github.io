<?php
require "db_connect.php";

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['device_id'], $data['nickname'])) {
    die(json_encode(["status" => "error", "message" => "参数错误"]));
}

try {
    // 检查设备是否已存在
    $stmt = $pdo->prepare("SELECT * FROM users WHERE device_id = ?");
    $stmt->execute([$data['device_id']]);
    $user = $stmt->fetch();
    
    if ($user) {
        // 更新昵称
        $oldNickname = $user['nickname'];
        $newNickname = $data['nickname'];
        
        // 更新用户表
        $stmt = $pdo->prepare("UPDATE users SET nickname = ?, updated_at = NOW() WHERE device_id = ?");
        $stmt->execute([$newNickname, $data['device_id']]);
        
        // 更新历史消息中的昵称
        $stmt = $pdo->prepare("UPDATE chat_messages SET username = ? WHERE username = ?");
        $stmt->execute([$newNickname, $oldNickname]);
    } else {
        // 创建新用户
        $stmt = $pdo->prepare("INSERT INTO users (device_id, nickname, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$data['device_id'], $data['nickname']]);
    }
    
    echo json_encode(["status" => "success"]);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => $e->getMessage()]));
}
?>