<?php
/**
 * delete.php — Redirect ke hapus.php (legacy compatibility)
 * Elektromedis Management System
 */
header('Location: hapus.php' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;
