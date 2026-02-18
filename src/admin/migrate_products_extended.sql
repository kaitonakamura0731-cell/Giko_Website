-- Migration: Add extended fields to products table
-- Date: 2026-02-17
-- Purpose: Add lead_text, product_summary_json, vehicle_type, detail_image_path, option_detail_image, vehicle_tags

USE 1lq8c_detabase;

-- Add columns if they don't exist
ALTER TABLE products
ADD COLUMN IF NOT EXISTS lead_text TEXT COMMENT 'リード文' AFTER description,
ADD COLUMN IF NOT EXISTS product_summary_json JSON COMMENT '商品概要（リスト形式）' AFTER lead_text,
ADD COLUMN IF NOT EXISTS vehicle_type VARCHAR(255) COMMENT '車両型式（追加）' AFTER model_code,
ADD COLUMN IF NOT EXISTS detail_image_path VARCHAR(255) COMMENT '詳細画像パス' AFTER vehicle_type,
ADD COLUMN IF NOT EXISTS option_detail_image VARCHAR(255) COMMENT 'オプション詳細画像' AFTER detail_image_path,
ADD COLUMN IF NOT EXISTS vehicle_tags VARCHAR(500) COMMENT '車種タグ（カンマ区切り）' AFTER option_detail_image;

-- Verify the changes
SHOW COLUMNS FROM products;
