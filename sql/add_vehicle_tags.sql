-- 車種タグカラムを追加するSQL
-- productsテーブルにvehicle_tagsカラムを追加
-- サーバーのphpMyAdminまたはSSH経由で実行してください

ALTER TABLE products ADD COLUMN vehicle_tags TEXT DEFAULT NULL AFTER stock_status;
