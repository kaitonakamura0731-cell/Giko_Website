-- 下取り割引金額カラム追加
-- phpMyAdminで実行してください
ALTER TABLE products ADD COLUMN trade_in_discount INT DEFAULT 10000 AFTER vehicle_tags;
