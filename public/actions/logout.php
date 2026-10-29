<?php
require_once __DIR__ . '/../config/auth.php';

logoutUser();

header('Location: /PrimerProyecto_ISW_613/index.php?success=logged_out');