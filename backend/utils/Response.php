<?php
// backend/utils/Response.php
class Response {
    public static function send($data, $status = 200) {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}