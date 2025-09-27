<?php
class XSSProtection {

    /**
     * Lọc dữ liệu đầu vào để tránh XSS
     * - Xóa ký tự control (low ASCII)
     * - Giới hạn độ dài nếu có
     */
    public static function clean($data, $maxLength = null) {
        if (is_string($data)) {
            // Loại bỏ ký tự ASCII control
            $data = filter_var($data, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);

            // Giới hạn độ dài
            if ($maxLength !== null && strlen($data) > $maxLength) {
                $data = substr($data, 0, $maxLength);
            }

            return $data;
        } elseif (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::clean($value, $maxLength);
            }
            return $data;
        }
        return $data;
    }

    /**
     * Escape dữ liệu khi output ra HTML
     */
    public static function escape($data) {
        if (is_string($data)) {
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        return $data;
    }

    /**
     * Escape dành riêng cho output trong JavaScript (ví dụ in ra JSON, script inline)
     */
    public static function escapeJs($data) {
        if (!is_string($data)) return $data;
        return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    /**
     * Escape cho HTML attribute (ví dụ value="", title="")
     */
    public static function escapeAttr($data) {
        if (!is_string($data)) return $data;
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Thêm các HTTP security headers
     */
    public static function addSecurityHeaders() {
        // Content Security Policy cơ bản
        header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'");

        // Ngăn Clickjacking
        header("X-Frame-Options: DENY");

        // Ngăn MIME sniffing
        header("X-Content-Type-Options: nosniff");

        // Bật XSS filter trên browser cũ
        header("X-XSS-Protection: 1; mode=block");

        // Strict Transport Security (chỉ bật khi site dùng HTTPS)
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
        }
    }
}  
