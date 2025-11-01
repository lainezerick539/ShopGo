<?php
session_save_path(__DIR__ . '/sessions');
session_start();
echo json_encode(['loggedIn' => isset($_SESSION['user_id'])]);
?>
