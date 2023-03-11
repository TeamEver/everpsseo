<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class EverPsSeoImage extends ObjectModel
{
    public $id_ever_seo_image;
    public $id_seo_img;
    public $id_seo_product;
    public $id_shop;
    public $id_seo_lang;
    public $alt;
    public $allowed_sitemap;
    public $status_code;

    public static $definition = [
        'table' => 'ever_seo_image',
        'primary' => 'id_ever_seo_image',
        'multilang' => false,
        'fields' => [
            'id_seo_img' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
                'required' => true,
            ],
            'id_seo_product' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
            'id_shop' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
            'id_seo_lang' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
            'alt' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString',
            ],
            'allowed_sitemap' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool',
            ],
            'status_code' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
        ],
    ];

    public static function getAllSeoImagesIds($id_shop)
    {
        $cache_id = 'EverPsSeoImage::getAllSeoImagesIds_'
        . (int) $id_shop;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_image');
            $sql->where('id_shop = ' . (int) $id_shop);
            $return = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getSeoImage($id_ever_seo_image, $id_shop, $id_seo_lang)
    {
        $cache_id = 'EverPsSeoImage::getSeoImage_'
        . (int) $id_ever_seo_image
        . '_'
        . (int) $id_shop
        . '_'
        . (int) $id_seo_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_image');
            $sql->where(
                'id_ever_seo_image = ' . (int) $id_ever_seo_image
            );
            $sql->where(
                'id_seo_lang = ' . (int) $id_seo_lang
            );
            $sql->where(
                'id_shop = ' . (int) $id_shop
            );
            $return = new self(Db::getInstance()->getValue($sql));
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function changeImageAltShortcodes($id_ever_seo_image, $id_seo_lang, $id_shop)
    {
        $image = new Image(
            (int) $id_ever_seo_image
        );
        $product = new Product(
            (int) $image->id_product,
            false,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $category = new Category(
            (int) $product->id_category_default,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $manufacturer = new Manufacturer(
            (int) $product->id_manufacturer,
            (int) $id_seo_lang
        );
        $supplier = new Supplier(
            (int) $product->id_supplier,
            (int) $id_seo_lang
        );
        $message = Configuration::getConfigInMultipleLangs(
            'EVERSEO_IMAGE_ALT_AUTO'
        );
        $shortcodes = [
            '[product_title]' => $product->name ? $product->name : '',
            '[product_reference]' => $product->reference ? $product->reference : '',
            '[product_desc]' => $product->description ? $product->description : '',
            '[product_short_desc]' => $product->description_short ? $product->description_short : '',
            '[product_default_category]' => $category->name ? $category->name : '',
            '[product_manufacturer]' => $manufacturer->name ? $manufacturer->name : '',
            '[product_supplier]' => $supplier->name ? $supplier->name : '',
            '[product_tags]' => $product->meta_keywords ? $product->meta_keywords : '',
            '[shop_name]' => (string) Configuration::get('PS_SHOP_NAME'),
            'NULL' => '', // Useful : remove empty strings in case of NULL
            'null' => '', // Useful : remove empty strings in case of null
        ];
        foreach ($shortcodes as $key => $value) {
            $message[(int) $id_seo_lang] = str_replace(
                (string) $key,
                (string) $value,
                (string) $message[(int) $id_seo_lang]
            );
            $message[(int) $id_seo_lang] = Hook::exec('actionChangeSeoShortcodes', [
                'content' => $message[(int) $id_seo_lang],
            ]);
        }
        if (!empty($message[(int) $id_seo_lang])) {
            return $message[(int) $id_seo_lang];
        }
    }

    public static function webpConvert2($file, $compressionQuality = 80)
    {
        // check if file exists
        if (!file_exists($file)) {
            return false;
        }
        $file_type = exif_imagetype($file);
        //https://www.php.net/manual/en/function.exif-imagetype.php
        //exif_imagetype($file);
        // 1    IMAGETYPE_GIF
        // 2    IMAGETYPE_JPEG
        // 3    IMAGETYPE_PNG
        // 6    IMAGETYPE_BMP
        // 15   IMAGETYPE_WBMP
        // 16   IMAGETYPE_XBM
        $output_file =  $file . '.webp';
        if (file_exists($output_file)) {
            return $output_file;
        }
        if (function_exists('imagewebp')) {
            switch ($file_type) {
                case '1': //IMAGETYPE_GIF
                    $image = imagecreatefromgif($file);
                    break;
                case '2': //IMAGETYPE_JPEG
                    $image = imagecreatefromjpeg($file);
                    break;
                case '3': //IMAGETYPE_PNG
                        $image = @imagecreatefrompng($file);
                        imagepalettetotruecolor($image);
                        imagealphablending($image, true);
                        imagesavealpha($image, true);
                        break;
                case '6': // IMAGETYPE_BMP
                    $image = imagecreatefrombmp($file);
                    break;
                case '15': //IMAGETYPE_Webp
                    $lock = false;
                    break;
                case '16': //IMAGETYPE_XBM
                    $image = imagecreatefromxbm($file);
                    break;
                default:
                    $lock = false;
                    break;
            }
            if (isset($lock) && (bool)$lock === false) {
                return false;
            }
            // Save the image
            $result = imagewebp($image, $output_file, $compressionQuality);
            if (false === $result) {
                return false;
            }
            // Free up memory
            imagedestroy($image);
            return $output_file;
        } elseif (class_exists('Imagick')) {
            $image = new Imagick();
            $image->readImage($file);
            if ($file_type === "3") {
                $image->setImageFormat('webp');
                $image->setImageCompressionQuality($compressionQuality);
                $image->setOption('webp:lossless', 'true');
            }
            $image->writeImage($output_file);
            return $output_file;
        }
        return false;
    }

    public static function setMedias2Webp()
    {
        if ((bool) Configuration::get('EVERSEO_WEBP') === false) {
            return false;
        }
        $allowedFormats = [
            'jpg',
            'jpeg',
            'png'
        ];
        // Logo
        $psLogo = Configuration::get(
            'PS_LOGO'
        );
        $psLogo = str_replace('.webp', '', $psLogo);
        self::webpConvert2(_PS_IMG_DIR_ . $psLogo);
        Configuration::updateValue(
            'PS_LOGO',
            $psLogo . '.webp'
        );
        // Products images
        $allImages = Image::getAllImages();
        foreach ($allImages as $img) {
            $image = new Image(
                (int) $img['id_image']
            );
            $productImages = glob(_PS_PRODUCT_IMG_DIR_ . $image->getImgFolder() . '*');
            foreach ($productImages as $img) {
                $info = new SplFileInfo(basename($img));
                if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                    self::webpConvert2($img);
                }
            }
        }
        // Default product images
        $defaultProductImages = glob(_PS_PRODUCT_IMG_DIR_ . '*');
        foreach ($defaultProductImages as $img) {
            $info = new SplFileInfo(basename($img));
            if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                self::webpConvert2($img);
            }
        }
        // Default product images 2
        $defaultProductImages = glob(_PS_PROD_IMG_DIR_ . '*');
        foreach ($defaultProductImages as $img) {
            $info = new SplFileInfo(basename($img));
            if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                self::webpConvert2($img);
            }
        }
        // Categories images
        $categoryImages = glob(_PS_CAT_IMG_DIR_ . '*');
        foreach ($categoryImages as $img) {
            $info = new SplFileInfo(basename($img));
            if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                self::webpConvert2($img);
            }
        }
        // Manufacturer images
        $manufacturerImages = glob(_PS_MANU_IMG_DIR_ . '*');
        foreach ($manufacturerImages as $img) {
            $info = new SplFileInfo(basename($img));
            if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                self::webpConvert2($img);
            }
        }
        // Supplier images
        $supplierImages = glob(_PS_SUPP_IMG_DIR_ . '*');
        foreach ($supplierImages as $img) {
            $info = new SplFileInfo(basename($img));
            if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                self::webpConvert2($img);
            }
        }
        // Default images
        $defaultImages = glob(_PS_IMG_DIR_ . '*');
        foreach ($defaultImages as $img) {
            $info = new SplFileInfo(basename($img));
            if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                self::webpConvert2($img);
            }
        }
        // Stores images
        $storeImages = glob(_PS_STORE_IMG_DIR_ . '*');
        foreach ($storeImages as $img) {
            $info = new SplFileInfo(basename($img));
            if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                self::webpConvert2($img);
            }
        }
        // Shipping images
        $shippingImages = glob(_PS_SHIP_IMG_DIR_ . '*');
        foreach ($shippingImages as $img) {
            $info = new SplFileInfo(basename($img));
            if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                self::webpConvert2($img);
            }
        }
        // CMS images
        $cmsImages = glob(_PS_IMG_DIR_ . 'cms/*');
        foreach ($cmsImages as $img) {
            $info = new SplFileInfo(basename($img));
            if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                self::webpConvert2($img);
            }
        }
        // Modules : jpg
        $modulesImages = EverPsSeoTools::rsearch(_PS_MODULE_DIR_, '/.*jpg/');
        foreach ($modulesImages as $img) {
            $info = new SplFileInfo(basename($img));
            self::webpConvert2($img);
        }
        // Modules : jpeg
        $modulesImages = EverPsSeoTools::rsearch(_PS_MODULE_DIR_, '/.*jpeg/');
        foreach ($modulesImages as $img) {
            self::webpConvert2($img);
        }
        // Modules : png
        $modulesImages = EverPsSeoTools::rsearch(_PS_MODULE_DIR_, '/.*png/');
        foreach ($modulesImages as $img) {
            self::webpConvert2($img);
        }
        // Ever Blog images
        if (Module::isInstalled('everpsblog')) {
            if (file_exists(_PS_IMG_DIR_ . 'post')) {
                $postImages = glob(_PS_IMG_DIR_ . 'post/*');
                foreach ($postImages as $img) {
                    $info = new SplFileInfo(basename($img));
                    if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                        self::webpConvert2($img);
                    }
                }
            }
            if (file_exists(_PS_IMG_DIR_ . 'category')) {
                $categoryImages = glob(_PS_IMG_DIR_ . 'category/*');
                foreach ($categoryImages as $img) {
                    $info = new SplFileInfo(basename($img));
                    if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                        self::webpConvert2($img);
                    }
                }
            }
            if (file_exists(_PS_IMG_DIR_ . 'tag')) {
                $tagImages = glob(_PS_IMG_DIR_ . 'tag/*');
                foreach ($tagImages as $img) {
                    $info = new SplFileInfo(basename($img));
                    if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                        self::webpConvert2($img);
                    }
                }
            }
            if (file_exists(_PS_IMG_DIR_ . 'author')) {
                $authorImages = glob(_PS_IMG_DIR_ . 'author/*');
                foreach ($authorImages as $img) {
                    $info = new SplFileInfo(basename($img));
                    if (is_file($img) && in_array($info->getExtension(), $allowedFormats)) {
                        self::webpConvert2($img);
                    }
                }
            }
        }
    }
}
