<?php
class Csrf
{
    // Sinh CSRF token mới và lưu vào session
    public static function generateToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Verify token trong request POST
    public static function verifyToken(string $token): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Kiểm tra session có tồn tại
        if (!isset($_SESSION['csrf_token'])) {
            return [
                'valid' => false,
                'message' => 'Phiên làm việc đã hết hạn hoặc không tồn tại. Vui lòng tải lại trang.'
            ];
        }

        // Kiểm tra token có rỗng không
        if (empty($token)) {
            return [
                'valid' => false,
                'message' => 'Token bảo mật bị thiếu. Vui lòng không chỉnh sửa form.'
            ];
        }

        $valid = hash_equals($_SESSION['csrf_token'], $token);
        
        if ($valid) {
            // Tạo token mới sau mỗi lần verify thành công
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return [
                'valid' => true,
                'message' => 'Token hợp lệ'
            ];
        }
        
        return [
            'valid' => false,
            'message' => 'Token không hợp lệ hoặc đã hết hạn. Có thể bạn đã submit form này rồi hoặc form đã bị can thiệp.'
        ];
    }

    // In ra input hidden trong form
    public static function inputField(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
