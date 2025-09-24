<?php
// MySQL数据库连接配置，自动创建数据表
$host = 'mysql.sqlpub.com';
$port = '3306';
$dbname = 'luohuadeyinfu1';
$username = 'luohuadeyinfu1';
$password = '7NzobuPb3Yn1Wibi';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => true // 启用持久连接优化性能
    ];
    $pdo = new PDO($dsn, $username, $password, $options);

    // 自动创建聊天消息表（如果不存在），添加索引优化查询
    $createTableSql = "CREATE TABLE IF NOT EXISTS chat_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        content TEXT NOT NULL,
        timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_timestamp (timestamp) 
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($createTableSql);
    
    // 自动创建用户表（如果不存在）
    $createUsersTableSql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        device_id VARCHAR(255) NOT NULL UNIQUE,
        nickname VARCHAR(50) NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($createUsersTableSql);

} catch (PDOException $e) {
    die(json_encode(["error" => "数据库连接失败: " . $e->getMessage()]));
}
?>