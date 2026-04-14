<?php
namespace App\Controllers\MonUkou;

require_once __DIR__ . '/../../Models/MonUkou/Account.php';
require_once __DIR__ . '/../../../Config/database.php';

use App\Models\MonUkou\Account; // 
class AccountController {

    public function showLogin() {
        include 'App/Views/login.php';
    }
    public function profile() {

    if (!isset($_SESSION['user_obj'])) {
        header("Location: index.php?controller=account&action=login");
        exit;
    }

    $GLOBALS['user'] = $_SESSION['user_obj'];

    include 'App/Views/member/profile.php';
}
    public function updateProfile() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $user = $_SESSION['user_obj'];

        $email = $_POST['email'];
        $tel = $_POST['tel'];

        $user->updateProfile($email, $tel);

        // Lưu DB
        $user->save(\Database::getInstance()->getConnection());

        $_SESSION['user_obj'] = $user;
        $_SESSION['success'] = "Cập nhật thành công!";

        header("Location: index.php?controller=account&action=profile");
        exit;
    }
}
    public function logout() {
    session_unset();     // xóa dữ liệu session
    session_destroy();   // hủy session

    header("Location: index.php?controller=account&action=login");
    exit;
}

    public function login($db) {

    $user_input = $_POST['username'] ?? '';
    $pass_input = $_POST['password'] ?? '';

    if (empty($user_input) || empty($pass_input)) {
        echo "Vui lòng nhập đầy đủ!";
        return;
    }

    $sql = "SELECT * FROM tbl_account WHERE Username = ? AND Password = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$user_input, $pass_input]);
    $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

    if ($userData) {

        $_SESSION['user_obj'] = new Account(
            $userData['Account_ID'],
            $userData['Username'],
            $userData['Password'],
            $userData['Mail'],
            $userData['Tel'] ?? '',
            $userData['Role'],
            $userData['Img'] ?? 'default.png'
        );

        if ($_SESSION['user_obj']->getRole() == 1) {
            header("Location: index.php?controller=admin&action=dashboard");
        } else {
            header("Location: index.php");
        }
        exit;

    } else {
        echo "<div class='text-danger text-center mt-3'>Sai tài khoản hoặc mật khẩu!</div>";
    }
}
    }
