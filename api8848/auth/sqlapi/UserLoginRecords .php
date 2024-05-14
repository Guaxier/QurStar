<?php

/**
 * 名称：insertLoginRecord
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：插入登录记录到数据库
 * 说明：根据传入的参数，向UserLoginRecords表中插入一条新的登录记录
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * @param string $loginMethod 登录方式
 * @param string $device 登录设备
 * @param string $loginTime 登录时间字符串，格式 'YYYY-MM-DD HH:II:SS'
 * @param string $location 登录地点
 * @param string $ipAddress IP地址
 * @param string $browserInfo 浏览器信息
 * @param int $loginStatus 登录状态，tinyint类型
 * @param string $authenticationDetails 验证详情
 * @param string|null $remarks 备注，可选
 * 
 * 返回:
 * @return bool 操作成功返回true，否则false
 * 
 * 示例:
 * 
 * $success = insertLoginRecord($pdo, 123, 'password', 'Desktop', 'Windows 10', '2024-05-15 14:30:00', null, 'Office', '192.168.1.1', 'Chrome 89', 1, '通过验证', '常规登录');
 * if ($success) {
 *     echo '登录记录插入成功';
 * } else {
 *     echo '登录记录插入失败';
 * }
 * 
 */
function insertLoginRecord(PDO $pdo, int $userId, string $loginMethod, string $device, string $loginTime, string $location, string $ipAddress, string $browserInfo, int $loginStatus, string $authenticationDetails, ?string $remarks = null): bool 
{
    try {
        // 准备SQL语句
        $sql = "INSERT INTO UserLoginRecords (user_id, Login_Method, Device, Login_Time, Location, IP_Address, Browser_Info, Login_Status, Authentication_Details, Remarks) 
                VALUES (:userId, :loginMethod, :device, :loginTime, :location, :ipAddress, :browserInfo, :loginStatus, :authenticationDetails, :remarks)";
        
        // 预处理语句
        $stmt = $pdo->prepare($sql);
        
        // 绑定参数
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':loginMethod', $loginMethod, PDO::PARAM_STR);
        $stmt->bindParam(':device', $device, PDO::PARAM_STR);
        $stmt->bindParam(':loginTime', $loginTime, PDO::PARAM_STR);
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->bindParam(':ipAddress', $ipAddress, PDO::PARAM_STR);
        $stmt->bindParam(':browserInfo', $browserInfo, PDO::PARAM_STR);
        $stmt->bindParam(':loginStatus', $loginStatus, PDO::PARAM_INT);
        $stmt->bindParam(':authenticationDetails', $authenticationDetails, PDO::PARAM_STR);
        $stmt->bindParam(':remarks', $remarks, PDO::PARAM_STR, "无", PDO::PARAM_NULL);
        
        // 执行插入操作
        $stmt->execute();
        
        return true; // 插入成功
    } catch (PDOException $e) {
        // 记录或处理异常
        error_log("插入登录记录失败: " . $e->getMessage());
        return false;
    }
}

/**
 * 名称：updateLogoutTimeByUserIdAndStatus
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：根据用户ID和登出时间，更新最后一次登录状态为0的记录的登出时间
 * 说明：查找用户ID对应的登录状态为0的最新记录，若存在则更新其登出时间为指定时间
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * @param string $newLogoutTime 新的登出时间字符串，格式 'YYYY-MM-DD HH:II:SS'
 * 
 * 返回:
 * @return bool 更新成功返回true，否则false
 * 
 * 示例:
 * 
 * $success = updateLogoutTimeByUserIdAndStatus($pdo, 123, '2024-05-15 15:30:00');
 * if ($success) {
 *     echo '登出时间更新成功';
 * } else {
 *     echo '没有符合条件的记录或更新失败';
 * }
 * 
 */
function updateLogoutTimeByUserIdAndStatus(PDO $pdo, int $userId, string $newLogoutTime): bool 
{
    try {
        // 首先，找到用户ID和登录状态为0的最新登录记录
        $findSql = "SELECT LoginRecordID FROM UserLoginRecords 
                   WHERE user_id = :userId AND Login_Status = 0 
                   ORDER BY Login_Time DESC LIMIT 1";
        
        $stmt = $pdo->prepare($findSql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $latestRecordId = $stmt->fetchColumn(); // 获取登录记录ID
        
        // 如果找到了记录，则更新其登出时间
        if ($latestRecordId !== false) {
            $updateSql = "UPDATE UserLoginRecords 
                        SET Logout_Time = :newLogoutTime 
                        WHERE LoginRecordID = :recordId";
            
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->bindParam(':newLogoutTime', $newLogoutTime, PDO::PARAM_STR);
            $updateStmt->bindParam(':recordId', $latestRecordId, PDO::PARAM_INT);
            $updateStmt->execute();
            return true; // 更新成功
        }
        
        return false; // 未找到符合条件的记录
    } catch (PDOException $e) {
        // 记录或处理异常
        error_log("更新登出时间失败: " . $e->getMessage());
        return false;
    }
}

/**
 * 名称：getUserLoginInfoById
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：根据用户ID查询登录信息
 * 说明：根据用户ID从UserLoginRecords表中查询相关的登录记录
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * 
 * 返回:
 * @return array 包含用户登录记录的数组，如果没有记录则返回空数组
 * 
 * 示例:
 * 
 * $loginInfo = getUserLoginInfoById($pdo, 123);
 * if ($loginInfo) {
 *     foreach ($loginInfo as $record) {
 *         print_r($record);
 *     }
 * } else {
 *     echo '该用户没有登录记录';
 * }
 * 
 */

function getUserLoginInfoById(PDO $pdo, int $userId): array 
{
    try {
        // SQL查询语句，根据$user_id获取UserLoginRecords表中的所有记录
        $sql = "SELECT LoginRecordID, Login_Method, Device, Login_Time, Logout_Time, Location, 
                IP_Address, Browser_Info, Login_Status, Authentication_Details, Remarks 
                FROM UserLoginRecords 
                WHERE user_id = :userId 
                ORDER BY Login_Time DESC";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        // 执行查询
        $stmt->execute();
        
        // 获取所有结果并返回
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询用户登录信息失败！" . $e->getMessage());
        return [];
    }
}

/**
 * 名称：getMostFrequentLoginLocationById
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：根据用户ID查询登录状态为0时最常登录的地点
 * 说明：根据用户ID从UserLoginRecords表中筛选登录状态为0的记录，统计各登录地点的频次，
 *       并返回出现次数最多的登录地点。如果有多个地点出现次数相同且最多，则只返回一个。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * 
 * 返回:
 * @return string 最常登录的地点，如果没有记录则返回空字符串
 * 
 * 示例:
 * 
 * $mostFrequentLocation = getMostFrequentLoginLocationById($pdo, 123);
 * if ($mostFrequentLocation) {
 *     echo '最常登录地点: ' . $mostFrequentLocation;
 * } else {
 *     echo '该用户没有登录成功记录或登录地点信息不全';
 * }
 * 
 */
function getMostFrequentLoginLocationById(PDO $pdo, int $userId): string 
{
    try {
        // SQL查询语句，根据$user_id和Login_Status=0分组计数登录地点，按计数降序排列并限制结果为1条
        $sql = "SELECT Location 
                FROM UserLoginRecords 
                WHERE user_id = :userId AND Login_Status = 0
                GROUP BY Location
                ORDER BY COUNT(*) DESC
                LIMIT 1";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        // 执行查询
        $stmt->execute();
        
        // 获取结果，由于使用了LIMIT 1，这里直接返回第一条记录的Location
        $result = $stmt->fetch(PDO::FETCH_COLUMN);
        return $result !== false ? $result : ''; // 如果没有结果，返回空字符串
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询最常登录地点失败！" . $e->getMessage());
        return '';
    }
}

/**
 * 名称：getLongestLoginDurationAndLocationById
 * 时间：2024/05/09 创建
 * 作者：Guaxier
 * 功能：根据用户ID查询登录状态为0时登录时间最长的地点及总时长
 * 说明：考虑用户可能未登出的最近一次登录，将其登出时间设为当前时间。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * 
 * 返回:
 * @return array 包含最长登录地点和总时长的数组，格式为 ['location' => '地点名称', 'duration' => 总秒数]。
 *               如果没有符合条件的记录，则返回 ['location' => '', 'duration' => 0]
 * 
 * 示例:
 * 
 * $loginDurationInfo = getLongestLoginDurationAndLocationById($pdo, 123);
 * if ($loginDurationInfo['location']) {
 *     echo "最长登录地点: {$loginDurationInfo['location']}, 登录总时长: " 
 *          . gmdate('H:i:s', $loginDurationInfo['duration']);
 * } else {
 *     echo '该用户没有登录成功记录或相关信息不全';
 * }
 * 
 */
function getLongestLoginDurationAndLocationById(PDO $pdo, int $userId): array 
{
    try {
        // 获取当前时间，用于处理未登出的最近一次登录
        $currentDateTime = date('Y-m-d H:i:s');
        
        // SQL查询语句，考虑未登出情况，计算每个地点的总登录时长，按时长降序排列并限制结果为1条
        $sql = "SELECT Location, SUM(TIMESTAMPDIFF(SECOND, Login_Time, COALESCE(Logout_Time, :currentDateTime))) AS TotalDuration
                FROM UserLoginRecords 
                WHERE user_id = :userId AND Login_Status = 0
                GROUP BY Location
                ORDER BY TotalDuration DESC
                LIMIT 1";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':currentDateTime', $currentDateTime, PDO::PARAM_STR);
        
        // 执行查询
        $stmt->execute();
        
        // 获取结果，直接返回第一条记录的Location和TotalDuration，若无结果则默认返回空字符串和0
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: ['location' => '', 'duration' => 0];
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询最长登录时间和地点失败！" . $e->getMessage());
        return ['location' => '', 'duration' => 0];
    }
}