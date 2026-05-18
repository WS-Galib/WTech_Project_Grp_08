<?php
if (!function_exists('log_activity')) {
    function log_activity($connection, $project_id, $user_id, $action_text) {
        $sql = "INSERT INTO activity_logs (project_id, user_id, action_text, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $connection->prepare($sql);        
        if ($stmt) {
            $stmt->bind_param("iis", $project_id, $user_id, $action_text);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
        return false;
    }
}



function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function requireAuth(): void
{
    if (!isLoggedIn()) {
        redirect(BASE_URL . '/index.php?page=login');
    }
}

function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function generateInviteCode(int $length = 6): string
{
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code  = '';
    for ($i = 0; $i < $length; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}


function initials(string $name): string
{
    $parts = explode(' ', trim($name));
    $init  = strtoupper(substr($parts[0], 0, 1));
    if (count($parts) > 1) {
        $init .= strtoupper(substr(end($parts), 0, 1));
    }
    return $init;
}

function avatarColor(string $name): string
{
    $colors = ['#6366f1','#8b5cf6','#ec4899','#f59e0b','#10b981','#3b82f6','#ef4444'];
    return $colors[crc32($name) % count($colors)];
}
?>
