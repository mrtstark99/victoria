<?php

declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';
use App\Core\Flash;
use App\Core\Security;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Security::verifyCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(400);
    exit('Yêu cầu không hợp lệ.');
}

$name = trim((string) ($_POST['name'] ?? ''));
$phone = trim((string) ($_POST['phone'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$program = (string) ($_POST['program'] ?? '');
$message = trim((string) ($_POST['message'] ?? ''));
$programs = ['tu-tuc', 'hoc-bong', 'tokutei', 'khoa-tieng'];
$lastContact = (int) ($_SESSION['last_contact_at'] ?? 0);

if (time() - $lastContact < 30) {
    Flash::set('error', 'Vui lòng đợi 30 giây trước khi gửi yêu cầu khác.');
} elseif (strlen($name) < 2 || strlen($name) > 160
    || !preg_match('/^[0-9+ .-]{8,20}$/', $phone)
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || !in_array($program, $programs, true)
    || strlen($message) > 2000) {
    Flash::set('error', 'Thông tin chưa hợp lệ. Vui lòng kiểm tra và gửi lại.');
} else {
    $statement = $database->prepare(
        'INSERT INTO contact_requests (user_id, name, phone, email, program, message, ip_address) '
        . 'VALUES (:user_id, :name, :phone, :email, :program, :message, :ip)'
    );
    $statement->execute([
        'user_id' => $currentUser['id'] ?? null,
        'name' => $name,
        'phone' => $phone,
        'email' => strtolower($email),
        'program' => $program,
        'message' => $message,
        'ip' => Security::clientIp(),
    ]);
    $_SESSION['last_contact_at'] = time();
    Flash::set('success', 'Victoria đã nhận yêu cầu và sẽ liên hệ với bạn trong 24 giờ.');
}
redirect('/#lien-he');
