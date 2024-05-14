<?php
/**
 * 名称：recordUsernameChange
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：插入用户名变更记录到数据库
 * 说明：用户的用户名更新记录，记录用户对用户名的相关操作，用于安全审计
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $user_id 用户ID
 * @param string $newUsername 新用户名
 * @param string $ipAddress IP地址
 * @param string $operatorID 操作者ID
 * @param string|null $remarks 备注，可选
 * @param string|null $changeReason 变更原因，可选
 * @throws Exception 如果$user_id，$newUsername，$ipAddress，$operatorID出现空则抛出异常
 * 
 * 示例:
 * 
 * $result = insertEmailHistory($pdo, 用户ID, '新用户名', 'IP地址', '操作者ID', '备注', '变更原因');
 *  if ($result) {
 *     echo '记录插入成功';
 *  } else {
 *     echo '记录插入插入失败';
 *  }
 * 
 */

function recordUsernameChange(PDO $pdo, int $user_id, string $newUsername, string $ipAddress, int $operatorID, ?string $remarks = null, ?string $changeReason = null): void
{

    if (!$pdo) {
        echo "数据库连接失败！";
        exit;
    }
    if (!$user_id || !$newUsername || !$ipAddress || !$operatorID) {
        echo "参数错误！";
        exit;
    }
    
    try {
        // 准备SQL语句
        $sql = "INSERT INTO UsernameHistory (user_id, NewUsername, Remarks, IPAddress, OperatorID, ChangeReason,) 
                VALUES (:user_id, :newUsername, :remarks, :ipAddress, :operatorID, :changeReason,)";
        
        // 预处理语句
        $stmt = $pdo->prepare($sql);
        
        // 绑定参数
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':newUsername', $newUsername, PDO::PARAM_STR);
        $stmt->bindParam(':remarks', $remarks, PDO::PARAM_STR, 255); // 允许NULL值
        $stmt->bindParam(':ipAddress', $ipAddress, PDO::PARAM_STR, 45);
        $stmt->bindParam(':operatorID', $operatorID, PDO::PARAM_STR, 255);
        $stmt->bindParam(':changeReason', $changeReason, PDO::PARAM_STR, 255); // 允许NULL值
        
        // 执行插入
        $stmt->execute();
        
    } catch (PDOException $e) {
        // 错误处理
        echo "用户名插入处理错误！" . $e->getMessage();
    }
}

/**
 * 名称：getUsernamesHistory
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：查询用户的用户名修改记录
 * 说明：根据用户ID查询UsernameHistory表中所有的用户名修改记录
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $user_id 用户ID
 * 
 * 返回:
 * @return array 包含用户所有用户名修改记录的数组，如果没有记录则返回空数组
 * 
 * 示例:
 * 
 * $usernameHistory = getUsernamesHistory($pdo, 123);
 * if ($usernameHistory) {
 *     foreach ($usernameHistory as $record) {
 *         print_r($record);
 *     }
 * } else {
 *     echo '该用户没有用户名修改记录';
 * }
 * 
 */

function getUsernamesHistory(PDO $pdo, int $user_id): array 
{
    try {
        // SQL查询语句，根据$user_id获取UsernameHistory表中的所有记录
        $sql = "SELECT UsernameHistoryID, NewUsername, Remarks, IPAddress, OperatorID, 
                ChangeTimestamp, ChangeReason 
                FROM UsernameHistory 
                WHERE user_id = :user_id 
                ORDER BY ChangeTimestamp DESC";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        // 执行查询
        $stmt->execute();
        
        // 获取所有结果并返回
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询用户用户名修改历史失败！" . $e->getMessage());
        return [];
    }
}