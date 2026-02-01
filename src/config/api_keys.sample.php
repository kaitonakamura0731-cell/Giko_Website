<?php
/**
 * API Keys Configuration - SAMPLE
 * 
 * このファイルを api_keys.php にリネームして、実際のAPIキーを設定してください。
 * api_keys.php はGitにコミットしないでください。
 */

// PAY.JP API Keys
// テスト環境: PAY.JPダッシュボードから取得した sk_test_... / pk_test_... を設定
// 本番環境: PAY.JPダッシュボードから取得した sk_live_... / pk_live_... を設定

define('PAYJP_SECRET_KEY', 'YOUR_PAYJP_SECRET_KEY_HERE');
define('PAYJP_PUBLIC_KEY', 'YOUR_PAYJP_PUBLIC_KEY_HERE');
