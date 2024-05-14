<?php
/**
 * 名称：insertAccountStatus
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：更新或插入用户状态变更记录到数据库
 * 说明：根据用户ID检查记录是否存在，存在则更新，不存在则插入新的状态变更记录
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $user_id 用户ID
 * @param int $status 状态
 * @param string $start_time 开始时间字符串，格式 'YYYY-MM-DD HH:II:SS'
 * @param string|null $end_time 结束时间字符串，格式 'YYYY-MM-DD HH:II:SS'，默认1999-12-12 23:59:59，"永久"
 * @param string|null $notes 备注，可选
 * @param string $operator_id 操作者ID
 * @param string|null $reason 状态原因，可选
 * 返回：
 * @return bool 操作成功返回true，否则false
 * @throws PDOException 数据库操作异常
 * 
 * 示例:
 * $result = insertAccountStatus($pdo, 123, 1, '2023-10-01 12:00:00', '2023-10-02 12:00:00', '这是备注', 'OPR001', '状态变更原因');
 *  if ($result) {
 *     echo '记录插入/更新成功';
 *  } else {
 *     echo '记录插入插入/更新失败';
 *  }
 * 
 */
function insertAccountStatus(PDO $pdo, int $user_id, int $status, string $start_time, ?string $end_time = null, ?string $notes = null, string $operator_id, ?string $reason = null): bool 
{
    try {
        //设置结束时间为-1，即永久
        $end_time = $end_time ?? '1999-12-31 23:59:59';
        // 首先检查该用户是否有记录
        $checkSql = "SELECT COUNT(*) FROM AccountStatus WHERE user_id = ?";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->bindParam(1, $user_id, PDO::PARAM_INT);
        $checkStmt->execute();
        $rowCount = $checkStmt->fetchColumn();

        if ($rowCount > 0) {
            // 如果有记录，则执行更新操作
            $updateSql = "UPDATE AccountStatus SET 
                           `STATUS` = ?, 
                           reason = ?, 
                           notes = ?, 
                           operator_id = ?, 
                           start_time = ?, 
                           end_time = ?
                         WHERE user_id = ?";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->bindParam(1, $status, PDO::PARAM_INT);
            $updateStmt->bindParam(2, $reason, PDO::PARAM_STR, 255);
            $updateStmt->bindParam(3, $notes, PDO::PARAM_STR);
            $updateStmt->bindParam(4, $operator_id, PDO::PARAM_STR);
            $updateStmt->bindParam(5, $start_time, PDO::PARAM_STR);
            $updateStmt->bindParam(6, $end_time, PDO::PARAM_STR);
            $updateStmt->bindParam(7, $user_id, PDO::PARAM_INT);
            $updateStmt->execute();
        } else {
            // 如果没有记录，则执行插入操作
            $insertSql = "INSERT INTO AccountStatus (user_id, `STATUS`, reason, notes, operator_id, start_time, end_time) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->bindParam(1, $user_id, PDO::PARAM_INT);
            $insertStmt->bindParam(2, $status, PDO::PARAM_INT);
            $insertStmt->bindParam(3, $reason, PDO::PARAM_STR, 255);
            $insertStmt->bindParam(4, $notes, PDO::PARAM_STR);
            $insertStmt->bindParam(5, $operator_id, PDO::PARAM_STR);
            $insertStmt->bindParam(6, $start_time, PDO::PARAM_STR);
            $insertStmt->bindParam(7, $end_time, PDO::PARAM_STR);
            $insertStmt->execute();
        }
        
        return true; // 更新或插入成功
    } catch (PDOException $e) {
        // 记录或处理异常
        error_log("执行状态更新/插入操作失败！" . $e->getMessage());
        return false;
    }
}

/**
 * 名称：getUserStatusInfo
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：检查用户状态信息
 * 说明：根据用户ID检查记录是否存在，存在则返回对应信息，不存在则返回null
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $user_id 用户ID
 * 
 * 返回:
 * @return array|null 包含用户状态信息的数组，如果没有找到则返回null
 * 
 * 示例:
 * 
 * $result = getUserStatusInfo($pdo, 123);
 * if ($result) {
 *     print_r($result);
 * } else {
 *     echo '没有找到该用户的状态信息';
 * }
 * 
 */
function getUserStatusInfo(PDO $pdo, int $user_id): ?array 
{
    try {
        // 预备SQL语句，查询AccountStatus表中指定user_id的所有字段（排除AccountStatusID）
        $sql = "SELECT STATUS, reason, notes, operator_id, start_time, end_time, update_time 
                FROM AccountStatus 
                WHERE user_id = :user_id";
        
        // 使用PDO预处理语句，绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        // 执行查询
        $stmt->execute();
        
        // 获取第一条数据（预期每个user_id只有一条有效记录）
        $statusInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // 如果查询到数据，则返回，否则返回null
        return $statusInfo ?: null;
    } catch (PDOException $e) {
        // 记录或处理数据库错误
        error_log("查询用户状态失败！" . $e->getMessage());
        return null;
    }
}

/**
 * 名称：getUsersByStatus
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：根据状态查询用户列表
 * 说明：根据给定的状态查询AccountStatus表中所有匹配状态的用户记录，返回包含这些用户状态信息的数组
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $status 状态值
 * 
 * 返回:
 * @return array 包含所有匹配状态的用户状态信息的数组，如果没有匹配项则返回空数组
 * 
 * 示例:
 * 
 * $results = getUsersByStatus($pdo, 1);
 * if ($results) {
 *     foreach ($results as $result) {
 *         print_r($result);
 *     }
 * } else {
 *     echo '没有找到该状态的用户信息';
 * }
 * 
 */
function getUsersByStatus(PDO $pdo, int $status): array 
{
    try {
        // 预备SQL语句，查询具有特定状态的所有用户状态信息
        $sql = "SELECT user_id, reason, notes, operator_id, start_time, end_time, update_time 
                FROM AccountStatus 
                WHERE STATUS = :status";
        
        // 使用PDO预处理语句，绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        
        // 执行查询
        $stmt->execute();
        
        // 获取所有数据并以数组形式返回
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // 记录或处理数据库错误
        error_log("查询特定状态用户失败！" . $e->getMessage());
        return [];
    }
}









































