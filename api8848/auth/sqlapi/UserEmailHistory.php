<?php
/**
 * 名称：recordEmailChange
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：插入邮箱变更记录到数据库
 * 说明：用户的邮箱更新记录，记录用户对邮箱的相关操作，用于安全审计
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $user_id 用户ID
 * @param string $newEmail 新邮箱地址
 * @param string $ipAddress IP地址
 * @param string $operatorID 操作者ID
 * @param string|null $remarks 备注，可选
 * @param string|null $changeReason 变更原因，可选
 * @throws Exception 如果$user_id，$newEmail，$ipAddress，$operatorID出现空则抛出异常
 * 
 * 示例:
 * 
 * $result = insertEmailHistory($pdo, 用户ID, '新邮箱地址', 'IP地址', '操作者ID', '备注', '变更原因');
 *  if ($result) {
 *     echo '记录插入成功';
 *  } else {
 *     echo '记录插入插入失败';
 *  }
 * 
 */

function recordEmailChange(PDO $pdo, int $user_id, string $newEmail, string $ipAddress, string $operatorID, ?string $remarks = null, ?string $changeReason = null): void
{
    if (!$pdo) {
        echo "数据库连接失败！";
        exit;
    }
    if (!$user_id || !$newEmail || !$ipAddress || !$operatorID) {
        echo "参数错误！";
        exit;
    }

    try {
        // 准备SQL语句
        $sql = "INSERT INTO UserEmailHistory (user_id, NewEmail, Remarks, IPAddress, operatorID, ChangeReason) 
                VALUES (:user_id, :newEmail, :remarks, :ipAddress, :operatorID, :changeReason)";
        
        // 预处理语句
        $stmt = $pdo->prepare($sql);
        
        // 绑定参数
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':newEmail', $newEmail, PDO::PARAM_STR);
        $stmt->bindParam(':remarks', $remarks, PDO::PARAM_STR, 255); // 允许NULL值
        $stmt->bindParam(':ipAddress', $ipAddress, PDO::PARAM_STR, 45);
        $stmt->bindParam(':operatorID', $operatorID, PDO::PARAM_STR);
        $stmt->bindParam(':changeReason', $changeReason, PDO::PARAM_STR, 255); // 允许NULL值
        
        // 执行插入操作
        $stmt->execute();
        
    } catch (PDOException $e) {
        // 错误处理
        echo "Error inserting email history: " . $e->getMessage();
    }
}

/**
 * 名称：getEmailHistoryByUserId
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：查询用户的邮箱修改历史记录
 * 说明：根据用户ID查询UserEmailHistory表中所有相关的邮箱修改记录
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * 
 * 返回:
 * @return array 包含用户所有邮箱修改记录的数组，如果没有记录则返回空数组
 * 
 * 示例:
 * 
 * $emailHistory = getEmailHistoryByUserId($pdo, 123);
 * if ($emailHistory) {
 *     foreach ($emailHistory as $record) {
 *         print_r($record);
 *     }
 * } else {
 *     echo '该用户没有邮箱修改记录';
 * }
 * 
 */

function getEmailHistoryByUserId(PDO $pdo, int $userId): array 
{
    try {
        // SQL查询语句，根据$user_id获取UserEmailHistory表中的所有记录
        $sql = "SELECT UserEmailHistoryID, NewEmail, Remarks, IPAddress, OperatorID, 
                ChangeTimestamp, ChangeReason 
                FROM UserEmailHistory 
                WHERE user_id = :userId 
                ORDER BY ChangeTimestamp DESC";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        // 执行查询
        $stmt->execute();
        
        // 获取所有结果并返回
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询用户邮箱修改历史失败！" . $e->getMessage());
        return [];
    }
}