<?php
// Function to get a setting value
function get_setting($key, $default = '')
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['setting_value'] : $default;
    } catch (PDOException $e) {
        return $default;
    }
}

// Function to set a setting value
function update_setting($key, $value)
{
    global $pdo;
    try {
        // Upsert logic (Insert on duplicate update)
        $sql = "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$key, $value]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
