<?php
require_once __DIR__ . '/../../includes/auth.php';

logoutUser();

header('Location: /index.php?success=logged_out');