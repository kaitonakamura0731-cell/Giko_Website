-- MySQL dump 10.13  Distrib 5.7.39, for osx11.0 (x86_64)
--
-- Host: localhost    Database: giko_db
-- ------------------------------------------------------
-- Server version	5.7.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','$2y$10$CLMtULQa.CjXnppw/DN3MOFWEsJVYieZ08tZgMe8ovt0ASHgFFnr.','2026-01-29 06:02:05');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published_date` date DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '1:Published, 0:Draft',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` int(11) NOT NULL,
  `shipping_fee` int(11) DEFAULT '0',
  `short_description` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `compatible_models` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `images` json DEFAULT NULL,
  `options` json DEFAULT NULL,
  `stock_status` int(11) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'【40系アルファード/ヴェルファイア専用】ステアリング',66000,1000,'','<p>\r\n                            40系アルファード/ヴェルファイア専用のレザーパッケージです。<br>\r\n                            純正では物足りない方に向けた、ハイエンドクラスです。<br>\r\n                            生地は本革からウルトラスエードを選択可能。\r\n                        </p>\r\n\r\n                        <div class=\"space-y-8 mb-12 mt-8\">\r\n                            <div>\r\n                                <h3 class=\"font-bold text-white mb-2 border-l-2 border-primary pl-3\">商品概要</h3>\r\n                                <p class=\"mb-4\">\r\n                                    <span class=\"text-primary\">☆専用設計</span><br>\r\n                                    純正交換が可能なので、スムーズに取り付け可能<br>\r\n                                    純正ステアリングを施工するため、ハンドルヒーター/ステアリングスイッチはそのままお使い頂けます。\r\n                                </p>\r\n                                <p class=\"mb-4\">\r\n                                    <span class=\"text-primary\">☆デザイン</span><br>\r\n                                    シンプルかつ個性を表現できる洗練されたデザインに仕立てています。難燃性の素材にこだわり耐久性にも優れています！カラーバリエーションも豊富なので、お好みに合わせて選べる楽しさがあります。\r\n                                </p>\r\n                                <p>\r\n                                    <span class=\"text-primary\">☆付属部品</span><br>\r\n                                    ステアリングのスポーク部分のみの販売になります。<br>\r\n                                    ※ステアリング裏のカバーや木目パネルは付属しません。\r\n                                </p>\r\n                            </div>\r\n\r\n                            <div>\r\n                                <h3 class=\"font-bold text-white mb-2 border-l-2 border-primary pl-3\">本革使用に伴う注意事項</h3>\r\n                                <p class=\"text-xs text-gray-400 leading-relaxed\">\r\n                                    本製品はイタリア産の天然皮革（本革）を使用しております。<br>\r\n                                    天然皮革ならではの特徴として、<br>\r\n                                    ・シワや血筋<br>\r\n                                    ・小さな傷やニキビ跡<br>\r\n                                    ・色味やシボ感の個体差<br>\r\n                                    が見られる場合がございます。<br>\r\n                                    ※できる限り傷や表面状態の良い部分を選んで使用しておりますが、部品に沿って張り込む工程では革を伸ばす必要があり、その過程でニキビ跡など革本来の表情が現れる場合がございます。<br>\r\n                                    天然素材ならではの特性として、ご理解頂けますと幸いです。\r\n                                </p>\r\n                            </div>\r\n\r\n                            <div>\r\n                                <h3 class=\"font-bold text-white mb-2 border-l-2 border-primary pl-3\">メッキパーツについて</h3>\r\n                                <p class=\"text-xs text-gray-400\">\r\n                                    画像にあるメッキパーツはオプションの”塗装”を追加した状態になります。<br>\r\n                                    オプション追加がない場合は純正同様シルバーメッキを装着しての納品になります。\r\n                                </p>\r\n                            </div>\r\n\r\n                            <div>\r\n                                <h3 class=\"font-bold text-white mb-2 border-l-2 border-primary pl-3\">下取り交換に関する注意事項</h3>\r\n                                <p class=\"text-xs text-gray-400 leading-relaxed\">\r\n                                    下取り交換とは、本製品とお客様のお手元にある中古部品を交換するサービスになります。<br>\r\n                                    下取り交換を選択されたお客様は、本製品が届いてから１週間以内に中古部品を下記住所までお送り頂きますようご協力お願い致します。<br>\r\n                                    万が一ご返却が確認できない場合や、正当な理由なく返却に応じて頂けない場合には、法的措置を含む対応を取らせて頂く場合がございますので、予めご了承下さい。\r\n                                </p>\r\n                                <div class=\"mt-2 text-xs text-gray-400 bg-white/5 p-3 rounded-sm\">\r\n                                    <span class=\"block font-bold text-white mb-1\">【下取り品返却住所】</span>\r\n                                    〒483-8013<br>\r\n                                    愛知県江南市般若町南山307<br>\r\n                                    GIKO307合同会社<br>\r\n                                    0587-22-7344\r\n                                </div>\r\n                            </div>\r\n                        </div>\r\n\r\n                        <!-- Parts Explanation -->\r\n                        <div class=\"mb-8\">\r\n                            <p class=\"text-xs font-bold font-en tracking-widest text-gray-500 mb-2\">パーツ解説</p>\r\n                            <div class=\"bg-white/5 rounded-sm p-4 border border-white/10\">\r\n                                <img src=\"../assets/images/items/steering_explanation.png\"\r\n                                    alt=\"Steering Parts Explanation\" class=\"w-full h-auto rounded-sm\">\r\n                            </div>\r\n                        </div>','アルファード/ヴェルファイア','3BA/5BA/6AA','[\"../assets/images/uploads/1769837976_697d95988be98.jpg\", \"../assets/images/items/steering_explanation.png\", \"../assets/images/items/steering_main.png\", \"../assets/images/items/steering_sub1.png\"]','[{\"type\": \"select\", \"label\": \"カラーA 【40AL/VEL-S】\", \"choices\": {\"red\": \"レッド\", \"black_shibo\": \"ブラックシボ\", \"black_smooth\": \"ブラックスムース\", \"sunset_brown\": \"サンセットブラウン\"}}, {\"type\": \"select\", \"label\": \"カラーB 【40AL/VEL-S】\", \"choices\": {\"black_shibo\": \"ブラックシボ\", \"black_smooth\": \"ブラックスムース\", \"sunset_brown\": \"サンセットブラウン\"}}, {\"type\": \"select\", \"label\": \"ステッチカラー\", \"choices\": {\"指定なし\": \"指定なし\", \"純正カラー\": \"純正カラー\"}}, {\"type\": \"select\", \"label\": \"下取り交換\", \"choices\": {\"なし (要別途費用)\": \"なし (要別途費用)\", \"あり（注意事項を要確認）\": \"あり（注意事項を要確認）\"}}, {\"type\": \"select\", \"label\": \"脱着依頼\", \"choices\": {\"なし\": \"なし\", \"あり (要別途予約)\": \"あり (要別途予約)\"}}]',1,'2026-01-29 09:28:59','2026-01-31 05:46:45'),(2,'steering_explanation.png',38500,1000,'','40系アルファード/ヴェルファイア専用のレザーパッケージです。\r\n純正では物足りない方に向けた、ハイエンドクラスです。\r\n生地は本革からウルトラスエードを選択可能。\r\n\r\n【商品概要】\r\n☆専用設計\r\n純正交換が可能なので、スムーズに取り付け可能\r\n\r\n☆デザイン\r\nシンプルかつ個性を表現できる洗練されたデザインに仕立てています。難燃性の素材にこだわり耐久性にも優れています！カラーバリエーションも豊富なので、お好みに合わせて選べる楽しさがあります。\r\n\r\n☆取付方法\r\nナビカバーの脱着にはナビを外す必要があります。\r\n\r\n【本革使用に伴う注意事項】\r\n本製品はイタリア産の天然皮革（本革）を使用しております。\r\n天然皮革ならではの特徴として、\r\n・シワや血筋\r\n・小さな傷やニキビ跡\r\n・色味やシボ感の個体差\r\nが見られる場合がございます。\r\n※できる限り傷や表面状態の良い部分を選んで使用しておりますが、部品に沿って張り込む工程では革を伸ばす必要があり、その過程でニキビ跡など革本来の表情が現れる場合がございます。\r\n天然素材ならではの特性として、ご理解頂けますと幸いです。\r\n\r\n【下取り交換に関する注意事項】\r\n下取り交換とは、本製品とお客様のお手元にある中古部品を交換するサービスになります。\r\n下取り交換を選択されたお客様は、本製品が届いてから１週間以内に中古部品を下記住所までお送り頂きますようご協力お願い致します。\r\n万が一ご返却が確認できない場合や、正当な理由なく返却に応じて頂けない場合には、法的措置を含む対応を取らせて頂く場合がございますので、予めご了承下さい。\r\n\r\n【下取り品返却住所】\r\n〒483-8013\r\n愛知県江南市般若町南山307\r\nGIKO307合同会社\r\n0587-22-7344','アルファード/ヴェルファイア','3BA/5BA/6AA','[\"../assets/images/uploads/1769914702_697ec14ee078a.png\", \"../assets/images/items/navicover_main.png\"]','[{\"type\": \"select\", \"label\": \"カラー\", \"choices\": [{\"image\": \"../assets/images/uploads/opt_697ec1266f1f0.png\", \"label\": \"ブラックシボ\", \"value\": \"blacksibo\"}, {\"image\": \"../assets/images/uploads/opt_697ec1266f52e.png\", \"label\": \"サンセットブラウン\", \"value\": \"sunset_brown\"}]}, {\"type\": \"select\", \"label\": \"ステッチカラー\", \"choices\": [{\"image\": \"../assets/images/uploads/opt_697ec1717fe52.png\", \"label\": \"レッド\", \"value\": \"レッド\"}, {\"image\": \"\", \"label\": \"シルバー\", \"value\": \"シルバー\"}, {\"image\": \"\", \"label\": \"ブラック\", \"value\": \"ブラック\"}, {\"image\": \"\", \"label\": \"純正カラー\", \"value\": \"純正カラー\"}]}, {\"type\": \"select\", \"label\": \"下取り交換\", \"choices\": [{\"image\": \"\", \"label\": \"なし (要別途費用)\", \"value\": \"なし (要別途費用)\"}, {\"image\": \"\", \"label\": \"あり（注意事項を要確認）\", \"value\": \"あり（注意事項を要確認）\"}]}, {\"type\": \"select\", \"label\": \"脱着依頼\", \"choices\": [{\"image\": \"\", \"label\": \"なし\", \"value\": \"なし\"}, {\"image\": \"\", \"label\": \"あり (要別途予約)\", \"value\": \"あり (要別途予約)\"}]}]',1,'2026-01-29 09:28:59','2026-02-01 03:00:57');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `site_settings`
--

DROP TABLE IF EXISTS `site_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `site_settings`
--

LOCK TABLES `site_settings` WRITE;
/*!40000 ALTER TABLE `site_settings` DISABLE KEYS */;
INSERT INTO `site_settings` VALUES (1,'site_title','','2026-02-03 07:38:21'),(2,'site_description','','2026-02-03 07:38:21'),(3,'company_name','','2026-02-03 07:38:21'),(4,'company_address','','2026-02-03 07:38:21'),(5,'company_tel','','2026-02-03 07:38:21'),(6,'company_email','','2026-02-03 07:38:21'),(7,'company_hours','','2026-02-03 07:38:21'),(8,'social_instagram','','2026-02-03 07:38:21'),(9,'social_twitter','','2026-02-03 07:38:21'),(10,'social_youtube','','2026-02-03 07:38:21');
/*!40000 ALTER TABLE `site_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `works`
--

DROP TABLE IF EXISTS `works`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `works` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'custom',
  `main_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hero_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `concept_text` text COLLATE utf8mb4_unicode_ci,
  `specs` json DEFAULT NULL,
  `data_info` json DEFAULT NULL,
  `price_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gallery_images` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `works`
--

LOCK TABLES `works` WRITE;
/*!40000 ALTER TABLE `works` DISABLE KEYS */;
INSERT INTO `works` VALUES (1,'ALPHARD','TOYOTA / 30 Series','full-order','custom','../assets/images/alphard/Alphard_TOP.jpg','../assets/images/alphard/alphard_06.jpg','Luxury White Leather Interior Custom. Creating a private lounge space.','純正の高級感をさらに昇華させた、フルホワイトレザーのカスタムインテリアです。オーナー様のご要望により、清潔感と広がりを感じさせるピュアホワイトのナッパレザーを全面に使用。アクセントとして、ドアトリムやセンターコンソールにはダイヤモンドステッチを施し、立体感と高級感を演出しました。<br><br>ステッチにはシルバーグレーを採用し、主張しすぎない洗練されたコントラストを実現。長時間のドライブでも疲れにくいよう、クッション材の調整も行っています。まさに「動くリビング」と呼ぶにふさわしい、至高のプライベート空間が完成しました。','{\"seat\": \"Full Leather\", \"color\": \"White\", \"period\": \"3 Weeks\", \"material\": \"Nappa\"}','{\"menu\": \"Full Interior Custom\", \"model\": \"TOYOTA ALPHARD (30 Series)\", \"price\": \"¥1,200,000 ~\", \"content\": \"全席シート張り替え\\nドアトリム張り替え\\n天井ルーフライニング\\nフロアマット製作\\nステアリング巻き替え\", \"material\": \"European Nappa Leather (White)\"}',NULL,'[\"../assets/images/alphard/Alphard_Seat.jpg\", \"../assets/images/alphard/Alphard_Seat2.jpg\", \"../assets/images/alphard/Alphard_Seat_01.jpg\", \"../assets/images/alphard/Alphard_Seat_02.jpg\"]','2026-01-29 09:28:58','2026-01-29 09:28:58'),(2,'NISSAN / BNR32','NISSAN / BNR32','full-order','custom','../assets/images/uploads/1769837773_697d94cdbc11b.png','../assets/images/gtr32/GTR32_TOP.jpg','Legendary Sports Car Interior Restoration. Reviving the original quality.','伝説の名車、R32 GT-Rのインテリアを当時の質感そのままに復元。経年劣化したシートや内張りを、オリジナルに近い質感のレザーとファブリックで張り替えました。','{\"seat\": \"Restoration\", \"color\": \"Black / Grey\", \"period\": \"4 Weeks\", \"material\": \"Original Style Fabric\"}','{\"menu\": \"Full Restoration\", \"model\": \"NISSAN GT-R (BNR32)\", \"price\": \"¥1,500,000 ~\", \"content\": \"全席シート張り替え\\r\\nダッシュボード補修\\r\\nドアトリム張り替え\\r\\n天井張替え\", \"material\": \"Genuine Leather / Fabric\"}',NULL,'[\"../assets/images/gtr32/GTR32_Seat.jpg\"]','2026-01-29 09:28:59','2026-01-31 05:36:13'),(3,'MR-S','TOYOTA / ZZW30','full-order','custom','../assets/images/mrs/MRS_TOP.jpg','../assets/images/mrs/MRS_TOP.jpg','Red & Black Sports Interior. High contrast design for open-top driving.','オープンドライブをより愉しむための、鮮烈な赤と黒のコントラスト。スポーツ走行時のホールド性を重視しつつ、見た目のインパクトも追求したカスタムインテリアです。','{\"seat\": \"Sports Custom\", \"color\": \"Red / Black\", \"period\": \"2 Weeks\", \"material\": \"Synthetic Leather\"}','{\"menu\": \"Seat Custom\", \"model\": \"TOYOTA MR-S\", \"price\": \"¥400,000 ~\", \"content\": \"シート張り替え\\nドアインナーパネル張り替え\", \"material\": \"PVC Leather\"}',NULL,'[]','2026-01-29 09:28:59','2026-01-29 09:28:59'),(4,'SL55 AMG','MERCEDES / R230','full-order','custom','../assets/images/sl55/SL55_TOP.png','../assets/images/sl55/SL55_TOP.png','Premium Beige Nappa & High-End Audio. Elegant open cruiser.','エレガントなベージュナッパレザーで統一された車内空間。最高級オーディオシステムとの融合を目指し、見た目だけでなく音響効果も考慮した素材配置を行いました。','{\"seat\": \"Luxury Custom\", \"color\": \"Beige\", \"period\": \"3 Weeks\", \"material\": \"Nappa Leather\"}','{\"menu\": \"Interior & Audio\", \"model\": \"MERCEDES BENZ SL55 AMG\", \"price\": \"¥1,800,000 ~\", \"content\": \"シート張り替え\\nドアトリム造形\\nオーディオインストール\\nトランクカスタム\", \"material\": \"Nappa Leather\"}',NULL,'[]','2026-01-29 09:28:59','2026-01-29 09:28:59'),(5,'V-CLASS','MERCEDES / W447','full-order','custom','../assets/images/vclass/VClass_Interior_TOP.jpg','../assets/images/vclass/VClass_Interior_TOP.jpg','VIP Lounge Specification. Ultimate comfort for executive travel.','移動時間を極上のリラックスタイムへ。後席をファーストクラスのような独立シートに変更し、パーティションやモニターを設置したVIP仕様です。','{\"seat\": \"VIP Captain Seats\", \"color\": \"Black\", \"period\": \"6 Weeks\", \"material\": \"Nappa Leather\"}','{\"menu\": \"Limousine Custom\", \"model\": \"MERCEDES BENZ V-CLASS\", \"price\": \"¥3,500,000 ~\", \"content\": \"後席キャプテンシート換装\\nパーティション製作\\nエンターテインメントシステム構築\\nフルデッドニング\", \"material\": \"Nappa Leather\"}',NULL,'[]','2026-01-29 09:28:59','2026-01-29 09:28:59'),(6,'AVENSIS','TOYOTA / WAGON','repair','custom','../assets/images/avensis/avensis_TOP.PNG','../assets/images/avensis/avensis_TOP.PNG','Seat Repair & Refresh. Restoring the original comfort.','長年の使用でへたってしまったシートウレタンの補修と表皮の張り替え。愛着のある愛車を長く乗り続けるためのリフレッシュプランです。','{\"seat\": \"Repair\", \"color\": \"Grey\", \"period\": \"1 Week\", \"material\": \"Genuine Fabric\"}','{\"menu\": \"Seat Repair\", \"model\": \"TOYOTA AVENSIS\", \"price\": \"¥150,000 ~\", \"content\": \"運転席座面ウレタン補修\\n表皮張り替え\", \"material\": \"Original Equivalent\"}',NULL,'[]','2026-01-29 09:28:59','2026-01-29 09:28:59');
/*!40000 ALTER TABLE `works` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-03 17:52:52
