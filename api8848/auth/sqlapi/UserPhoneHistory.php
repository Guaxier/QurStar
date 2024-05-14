<?php
/**
 * 名称：recordPhoneChange
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：插入手机号变更记录到数据库
 * 说明：用户的手机号更新记录，记录用户对密码的相关操作，用于安全审计
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $user_id 用户ID
 * @param string $newPhone 新手机号
 * @param string $ipAddress IP地址
 * @param string $operatorID 操作者ID
 * @param string|null $remarks 备注，可选
 * @param string|null $changeReason 变更原因，可选
 * @throws Exception 如果$user_id，$newPhone，$ipAddress，$operatorID不能为空，否则抛出异常
 *  
 * 示例:
 * 
 * $result = insertEmailHistory($pdo, 用户ID, '新手机号', 'IP地址', '操作者ID', '备注', '变更原因');
 *  if ($result) {
 *     echo '记录插入成功';
 *  } else {
 *     echo '记录插入插入失败';
 *  }
 * 
 */

function recordPhoneChange(PDO  $pdo, int $user_id, string $newPhone, string $ipAddress, string $operatorID, ?string $changeReason = null, ?string $remarks = null): void
{
    if (!$pdo) {
        echo "数据库连接失败！";
        exit;
    }
    if (!$user_id || !$newPhone || !$ipAddress || !$operatorID) {
        echo "参数错误！";
        exit;
    }

    try{

        // 构建SQL插入语句
        $sql = "INSERT INTO UserPhoneHistory (user_id, NewPhone, Remarks, IPAddress, operatorID, ChangeReason) 
        VALUES (:user_id, :newPhone, :remarks, :ipAddress, :operatorID, :changeReason)";

        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); 
        $stmt->bindParam(':newPhone', $newPhone, PDO::PARAM_STR);
        $stmt->bindParam(':remarks', $remarks, PDO::PARAM_STR | PDO::PARAM_NULL);
        $stmt->bindParam(':ipAddress', $ipAddress, PDO::PARAM_STR);
        $stmt->bindParam(':operatorID', $operatorID, PDO::PARAM_STR);
        $stmt->bindParam(':changeReason', $changeReason, PDO::PARAM_STR | PDO::PARAM_NULL);

        // 执行插入操作
        $stmt->execute();
        
    } catch (PDOException $e) {
        // 错误处理
        echo "手机号插入处理错误！ " . $e->getMessage();
}
}

/**
 * 名称：getUserPhoneHistory
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：查询用户的手机号修改记录
 * 说明：根据用户ID查询UserPhoneHistory表中所有的手机号修改记录
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $user_id 用户ID
 * 
 * 返回:
 * @return array 包含用户所有手机号修改记录的数组，如果没有记录则返回空数组
 * 
 * 示例:
 * 
 * $phoneHistory = getUserPhoneHistory($pdo, 123);
 * if ($phoneHistory) {
 *     foreach ($phoneHistory as $record) {
 *         print_r($record);
 *     }
 * } else {
 *     echo '该用户没有手机号修改记录';
 * }
 * 
 */

function getUserPhoneHistory(PDO $pdo, int $user_id): array 
{
    try {
        // SQL查询语句，根据$user_id获取UserPhoneHistory表中的所有记录
        $sql = "SELECT UserPhoneHistoryID, NewPhone, Remarks, IPAddress, OperatorID, 
                ChangeTimestamp, ChangeReason 
                FROM UserPhoneHistory 
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
        error_log("查询用户手机号修改历史失败！" . $e->getMessage());
        return [];
    }
}