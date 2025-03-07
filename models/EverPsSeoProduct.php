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

class EverPsSeoProduct extends ObjectModel
{
    public $id_seo_product;
    public $id_shop;
    public $id_seo_lang;
    public $meta_title;
    public $meta_description;
    public $social_title;
    public $social_description;
    public $social_img_url;
    public $bottom_content;
    public $link_rewrite;
    public $canonical;
    public $keywords;
    public $indexable;
    public $follow;
    public $allowed_sitemap;
    public $count;
    public $note;
    public $status_code;

    public static $definition = [
        'table' => 'ever_seo_product',
        'primary' => 'id_ever_seo_product',
        'multilang' => false,
        'fields' => [
            'id_seo_product' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
                'required' => true,
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
            'meta_title' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString',
            ],
            'meta_description' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString',
            ],
            'social_title' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString',
            ],
            'social_description' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isCleanHtml'
            ],
            'social_img_url' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isUrl',
            ],
            'bottom_content' => [
                'type' => self::TYPE_HTML,
                'lang' => false,
                'validate' => 'isCleanHtml',
            ],
            'link_rewrite' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isLinkRewrite',
            ],
            'canonical' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isUrl',
            ],
            'keywords' => [
                'type' => self::TYPE_STRING,
                'lang' => false,
                'validate' => 'isString',
            ],
            'indexable' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool',
            ],
            'follow' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool',
            ],
            'allowed_sitemap' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isBool',
            ],
            'count' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
            'note' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
            'status_code' => [
                'type' => self::TYPE_INT,
                'lang' => false,
                'validate' => 'isUnsignedInt',
            ],
        ],
    ];

    public static function getAllSeoProductsIds($id_shop, $allowedLangs = false)
    {
        $cache_id = 'EverPsSeoProduct::getAllSeoProductsIds_'
        . (int) $id_shop;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_product');
            if ($allowedLangs) {
                $sql->where('id_shop = ' . (int) $id_shop . ' AND id_seo_lang IN ('.implode(',', $allowedLangs).')');
            } else {
                $sql->where('id_shop = ' . (int) $id_shop);
            }
            $return = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getProductNameBySeoId($id_seo_product)
    {
        $cache_id = 'EverPsSeoProduct::getProductNameBySeoId_'
        . (int) $id_seo_product;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('name');
            $sql->from('product_lang');
            $sql->where('id_product = ' . (int) $id_seo_product);
            $return = Db::getInstance()->getValue($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    public static function getSeoProduct($id_seo_product, $id_shop, $id_seo_lang)
    {
        $cache_id = 'EverPsSeoProduct::getSeoProduct_'
        . (int) $id_seo_product
        . '_'
        . (int) $id_shop
        . '_'
        . (int) $id_seo_lang;
        if (!Cache::isStored($cache_id)) {
            $sql = new DbQuery();
            $sql->select('*');
            $sql->from('ever_seo_product');
            $sql->where(
                'id_seo_product = ' . (int) $id_seo_product
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

    public static function changeProductTitleShortcodes($id_seo_product, $id_seo_lang, $id_shop)
    {
        $product = new Product(
            (int) $id_seo_product,
            false,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $combination_names = '';
        if (self::hasEverCombinations((int) $product->id)) {
            $attr_resumes = $product->getAttributesResume(
                (int) $id_seo_lang
            );
            foreach ($attr_resumes as $key => $attr_resume) {
                if ($key == 0) {
                    $combination_names .= ' ' . $attr_resume['attribute_designation'];
                } else {
                    $combination_names .= ' ' . $attr_resume['attribute_designation'] . ', ';
                }
            }
        }
        $feature_names = '';
        // foreach ($product->getFeatures() as $key => $value) {
        //     $feature = Feature::getFeature(
        //         (int) $id_seo_lang,
        //         (int) $value['id_feature']
        //     );
        //     $feature_value = new FeatureValue(
        //         (int) $value['id_feature_value']
        //     );
        //     if ($key == 0) {
        //         $feature_names .= $feature['name'] . ': ' . $feature_value->value[(int) $id_seo_lang];
        //     } else {
        //         $feature_names .= $feature['name'] . ': ' . $feature_value->value[(int) $id_seo_lang].', ';
        //     }
        // }
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
            'EVERSEO_PRODUCT_TITLE_AUTO'
        );
        if (!$message) {
            foreach (Language::getLanguages(false) as $lang) {
                $message[(int) $lang['id_lang']] = '';
            }
        }
        $shortcodes = [
            '[product_title]' => $product->name ? $product->name : '',
            '[product_reference]' => $product->reference ? $product->reference : '',
            '[product_desc]' => $product->description ? $product->description : '',
            '[product_short_desc]' => $product->description_short ? $product->description_short : '',
            '[product_default_category]' => $category->name ? $category->name : '',
            '[product_manufacturer]' => $manufacturer->name ? $manufacturer->name : '',
            '[product_supplier]' => $supplier->name ? $supplier->name : '',
            '[product_combinations]' => $combination_names,
            '[product_features]' => $feature_names,
            '[product_tags]' => $product->meta_keywords,
            '[product_supplier]' => $supplier->name ? $supplier->name : '',
            '[category_desc]' => $category->description,
            '[category_meta_desc]' => $category->meta_description,
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

    public static function changeProductMetadescShortcodes($id_seo_product, $id_seo_lang, $id_shop)
    {
        $product = new Product(
            (int) $id_seo_product,
            false,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $combination_names = '';
        if (self::hasEverCombinations((int) $product->id)) {
            $attr_resumes = $product->getAttributesResume(
                (int) $id_seo_lang
            );
            foreach ($attr_resumes as $key => $attr_resume) {
                if ($key == 0) {
                    $combination_names .= $attr_resume['attribute_designation'];
                } else {
                    $combination_names .= $attr_resume['attribute_designation'] . ', ';
                }
            }
        }
        $feature_names = '';
        // foreach ($product->getFeatures() as $key => $value) {
        //     $feature = Feature::getFeature(
        //         (int) $id_seo_lang,
        //         (int) $value['id_feature']
        //     );
        //     $feature_value = new FeatureValue(
        //         (int) $value['id_feature_value']
        //     );
        //     if ($key == 0) {
        //         $feature_names .= $feature['name'] . ': ' . $feature_value->value[(int) $id_seo_lang];
        //     } else {
        //         $feature_names .= $feature['name'] . ': ' . $feature_value->value[(int) $id_seo_lang].', ';
        //     }
        // }
        $manufacturer = new Manufacturer(
            (int) $product->id_manufacturer,
            (int) $id_seo_lang
        );
        $supplier = new Supplier(
            (int) $product->id_supplier,
            (int) $id_seo_lang
        );
        $category = new Category(
            (int) $product->id_category_default,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $message = Configuration::getConfigInMultipleLangs(
            'EVERSEO_PRODUCT_METADESC_AUTO'
        );
        if (!$message) {
            foreach (Language::getLanguages(false) as $lang) {
                $message[(int) $lang['id_lang']] = '';
            }
        }
        $shortcodes = [
            '[product_title]' => $product->name ? $product->name : '',
            '[product_reference]' => $product->reference ? $product->reference : '',
            '[product_desc]' => $product->description ? $product->description : '',
            '[product_short_desc]' => $product->description_short ? $product->description_short : '',
            '[product_default_category]' => $category->name ? $category->name : '',
            '[category_desc]' => $category->description,
            '[category_meta_desc]' => $category->meta_description,
            '[product_manufacturer]' => $manufacturer->name ? $manufacturer->name : '',
            '[product_supplier]' => $supplier->name ? $supplier->name : '',
            '[product_combinations]' => $combination_names,
            '[product_features]' => $feature_names,
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

    public static function changeProductDescShortcodes($id_seo_product, $id_seo_lang, $id_shop)
    {
        $product = new Product(
            (int) $id_seo_product,
            false,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $combination_names = '';
        if (self::hasEverCombinations((int) $product->id)) {
            $attr_resumes = $product->getAttributesResume(
                (int) $id_seo_lang
            );
            foreach ($attr_resumes as $key => $attr_resume) {
                if ($key == 0) {
                    $combination_names .= $attr_resume['attribute_designation'];
                } else {
                    $combination_names .= $attr_resume['attribute_designation'] . ', ';
                }
            }
        }
        $feature_names = '';
        foreach ($product->getFeatures() as $key => $value) {
            $feature = Feature::getFeature(
                (int) $id_seo_lang,
                (int) $value['id_feature']
            );
            $feature_value = new FeatureValue(
                (int) $value['id_feature_value']
            );
            if ($key == 0) {
                $feature_names .= $feature['name'] . ': ' . $feature_value->value[(int) $id_seo_lang];
            } else {
                $feature_names .= $feature['name'] . ': ' . $feature_value->value[(int) $id_seo_lang].', ';
            }
        }
        $manufacturer = new Manufacturer(
            (int) $product->id_manufacturer,
            (int) $id_seo_lang
        );
        $supplier = new Supplier(
            (int) $product->id_supplier,
            (int) $id_seo_lang
        );
        $category = new Category(
            (int) $product->id_category_default,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $message = Configuration::getConfigInMultipleLangs(
            'PRODUCT_DESC_GENERATE'
        );

        $shortcodes = [
            '[product_title]' => $product->name ? $product->name : '',
            '[product_reference]' => $product->reference ? $product->reference : '',
            '[product_desc]' => $product->description ? $product->description : '',
            '[product_short_desc]' => $product->description_short ? $product->description_short : '',
            '[product_default_category]' => $category->name ? $category->name : '',
            '[product_manufacturer]' => $manufacturer->name ? $manufacturer->name : '',
            '[product_supplier]' => $supplier->name ? $supplier->name : '',
            '[product_combinations]' => $combination_names,
            '[product_features]' => $feature_names,
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

    public static function changeProductShortDescShortcodes($id_seo_product, $id_seo_lang, $id_shop)
    {
        $product = new Product(
            (int) $id_seo_product,
            false,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $combination_names = '';
        if (self::hasEverCombinations((int) $product->id)) {
            $attr_resumes = $product->getAttributesResume(
                (int) $id_seo_lang
            );
            foreach ($attr_resumes as $key => $attr_resume) {
                if ($key == 0) {
                    $combination_names .= $attr_resume['attribute_designation'];
                } else {
                    $combination_names .= $attr_resume['attribute_designation'] . ', ';
                }
            }
        }
        $feature_names = '';
        foreach ($product->getFeatures() as $key => $value) {
            $feature = Feature::getFeature(
                (int) $id_seo_lang,
                (int) $value['id_feature']
            );
            $feature_value = new FeatureValue(
                (int) $value['id_feature_value']
            );
            if ($key == 0) {
                $feature_names .= $feature['name'] . ': ' . $feature_value->value[(int) $id_seo_lang];
            } else {
                $feature_names .= $feature['name'] . ': ' . $feature_value->value[(int) $id_seo_lang].', ';
            }
        }
        $manufacturer = new Manufacturer(
            (int) $product->id_manufacturer,
            (int) $id_seo_lang
        );
        $supplier = new Supplier(
            (int) $product->id_supplier,
            (int) $id_seo_lang
        );
        $category = new Category(
            (int) $product->id_category_default,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $message = Configuration::getConfigInMultipleLangs(
            'PRODUCT_SHORT_DESC_GENERATE'
        );

        $shortcodes = [
            '[product_title]' => $product->name ? $product->name : '',
            '[product_reference]' => $product->reference ? $product->reference : '',
            '[product_desc]' => $product->description ? $product->description : '',
            '[product_short_desc]' => $product->description_short ? $product->description_short : '',
            '[product_default_category]' => $category->name ? $category->name : '',
            '[product_manufacturer]' => $manufacturer->name ? $manufacturer->name : '',
            '[product_supplier]' => $supplier->name ? $supplier->name : '',
            '[product_combinations]' => $combination_names,
            '[product_features]' => $feature_names,
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

    public static function changeProductBottomShortcodes($id_seo_product, $id_seo_lang, $id_shop)
    {
        $product = new Product(
            (int) $id_seo_product,
            false,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $combination_names = '';
        if (self::hasEverCombinations((int) $product->id)) {
            $attr_resumes = $product->getAttributesResume(
                (int) $id_seo_lang
            );
            foreach ($attr_resumes as $key => $attr_resume) {
                if ($key == 0) {
                    $combination_names .= $attr_resume['attribute_designation'];
                } else {
                    $combination_names .= $attr_resume['attribute_designation'] . ', ';
                }
            }
        }
        $feature_names = '';
        foreach ($product->getFeatures() as $key => $value) {
            $feature = Feature::getFeature(
                (int) $id_seo_lang,
                (int) $value['id_feature']
            );
            $feature_value = new FeatureValue(
                (int) $value['id_feature_value']
            );
            if ($key == 0) {
                $feature_names .= $feature['name'] . ': ' . $feature_value->value[(int) $id_seo_lang];
            } else {
                $feature_names .= $feature['name'] . ': ' . $feature_value->value[(int) $id_seo_lang].', ';
            }
        }
        $manufacturer = new Manufacturer(
            (int) $product->id_manufacturer,
            (int) $id_seo_lang
        );
        $supplier = new Supplier(
            (int) $product->id_supplier,
            (int) $id_seo_lang
        );
        $category = new Category(
            (int) $product->id_category_default,
            (int) $id_seo_lang,
            (int) $id_shop
        );
        $message = Configuration::getConfigInMultipleLangs(
            'PRODUCT_BOTTOM_GENERATE'
        );

        $shortcodes = [
            '[product_title]' => $product->name ? $product->name : '',
            '[product_reference]' => $product->reference ? $product->reference : '',
            '[product_desc]' => $product->description ? $product->description : '',
            '[product_short_desc]' => $product->description_short ? $product->description_short : '',
            '[product_default_category]' => $category->name ? $category->name : '',
            '[product_manufacturer]' => $manufacturer->name ? $manufacturer->name : '',
            '[product_supplier]' => $supplier->name ? $supplier->name : '',
            '[product_combinations]' => $combination_names,
            '[product_features]' => $feature_names,
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
                'ps_obj' => $product,
            ]);
        }
        if (!empty($message[(int) $id_seo_lang])) {
            return $message[(int) $id_seo_lang];
        }
    }

    public static function inactiveRedirect($id_seo_product, $id_shop)
    {
        $product = new Product(
            (int) $id_seo_product
        );
        $category = new Category(
            (int) $product->id_category_default
        );
        if ((bool) $category->active === true) {
            $id_type_redirected = (int) $product->id_category_default;
        } else {
            $top_category = Category::getTopCategory();
            $id_type_redirected = (int) $top_category->id;
        }
        $sql = [];
        $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'product`
        SET `redirect_type` = "301-category"
        WHERE `id_product` = ' . (int) $id_seo_product . ';';

        $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'product`
        SET `id_type_redirected` = ' . (int) $id_type_redirected . '
        WHERE `id_product` = ' . (int) $id_seo_product . ';';

        $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'product_shop`
        SET `redirect_type` = "301-category"
        WHERE `id_product` = ' . (int) $id_seo_product . '
        AND `id_shop` = ' . (int) $id_shop . ';';

        $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'product_shop`
        SET `id_type_redirected` = ' . (int) $id_type_redirected . '
        WHERE `id_product` = ' . (int) $id_seo_product . ';';

        foreach ($sql as $s) {
            Db::getInstance()->Execute($s);
        }
    }

    public static function getDisabledProduct()
    {
        $sql = '
            SELECT `id_product` 
            FROM `' . _DB_PREFIX_ . 'product` 
            WHERE `active` = 0 
            AND `redirect_type` = "404"
        ';
        
        return Db::getInstance()->ExecuteS($sql);
    }

    /**
     * @return bool
     */
    public static function hasEverCombinations($id_product)
    {
        if (null === $id_product || 0 >= $id_product) {
            return false;
        }
        $attributes = Product::getAttributesInformationsByProduct(
            (int) $id_product
        );

        return !empty($attributes);
    }
}
