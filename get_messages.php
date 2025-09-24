<?php
require "db_connect.php";

// 支持增量拉取：只获取上次之后的新消息
$lastId = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';

try {
    // 构建查询条件
    $where = [];
    $params = [];
    
    // 增量拉取条件（优先于分页）
    if ($lastId > 0) {
        $where[] = "id > :last_id";
        $params[':last_id'] = $lastId;
    }
    
    // 搜索条件
    if (!empty($search)) {
        $where[] = "content LIKE :search";
        $params[':search'] = "%{$search}%";
    }
    
    $whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";
    
    // 构建SQL
    $sql = "SELECT id, username, content, DATE_FORMAT(timestamp, '%H:%i:%s %p') AS time 
            FROM chat_messages 
            {$whereSql}
            ORDER BY timestamp DESC 
            " . ($lastId > 0 ? "" : "LIMIT :limit OFFSET :offset");
    
    $stmt = $pdo->prepare($sql);
    
    // 绑定参数
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    
    // 绑定分页参数（只有非增量拉取时需要）
    if ($lastId == 0) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $messages = $stmt->fetchAll();
    
    // 反转数组，按时间升序显示
    $messages = array_reverse($messages);
    
    header("Content-Type: application/json");
    echo json_encode($messages);
} catch (PDOException $e) {
    die(json_encode(["error" => "获取消息失败: " . $e->getMessage()]));
}
?>