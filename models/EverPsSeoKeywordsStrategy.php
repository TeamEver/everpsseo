<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://www.team-ever.com
 */

require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoCategory.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoCmsCategory.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoCms.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoImage.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoManufacturer.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoPageMeta.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoProduct.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoRedirect.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoSupplier.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoSitemap.php';
require_once _PS_MODULE_DIR_.'everpsseo/models/EverPsSeoBacklink.php';

class EverPsSeoKeywordsStrategy extends ObjectModel
{
    public $module;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->context = Context::getContext();

        $this->module = Module::getInstanceByName('everpsseo');

        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getKeywordsCount($controller_name, $id_object, $id_shop, $id_lang)
    {
        switch ($controller_name) {
            case 'product':
                $product = new Product(
                    (int)$id_object,
                    false,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $seo_obj = EverPsSeoProduct::getSeoProduct(
                    (int)$id_object,
                    (int)$id_shop,
                    (int)$id_shop
                );
                $content = $product->name.' '.$product->description_short.' '.$product->description;
                break;

            case 'category':
                $category = new Category(
                    (int)$id_object,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $seo_obj = EverPsSeoCategory::getSeoCategory(
                    (int)$id_object,
                    (int)$id_shop,
                    (int)$id_shop
                );
                $content = $category->name.' '.$category->description;
                break;

            case 'cms':
                $cms = new Cms(
                    (int)$id_object,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $seo_obj = EverPsSeoCms::getSeoCms(
                    (int)$id_object,
                    (int)$id_shop,
                    (int)$id_shop
                );
                $content = $cms->meta_title.' '.$cms->content;
                break;

            case 'manufacturer':
                $manufacturer = new Manufacturer(
                    (int)$id_object,
                    (int)$id_lang
                );
                $seo_obj = EverPsSeoManufacturer::getSeoManufacturer(
                    (int)$id_object,
                    (int)$id_shop,
                    (int)$id_shop
                );
                $content = $manufacturer->name.' '.$manufacturer->short_description.' '.$manufacturer->description;
                break;

            case 'supplier':
                $supplier = new Supplier(
                    (int)$id_object,
                    (int)$id_lang
                );
                $seo_obj = EverPsSeoSupplier::getSeoSupplier(
                    (int)$id_object,
                    (int)$id_shop,
                    (int)$id_shop
                );
                $content = $supplier->name.' '.$supplier->description;
                break;

            case 'meta':
                $meta = new Meta(
                    (int)$id_object,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $seo_obj = EverPsSeoPageMeta::getSeoPageMeta(
                    (int)$id_object,
                    (int)$id_shop,
                    (int)$id_shop
                );
                $content = $meta->title.' '.$meta->description;
                break;
        }

        $onlyWords = mb_strtolower(strip_tags($content), 'UTF-8');
        $keywords = explode(',', $seo_obj->keywords);
        $count = 0;
        if ($keywords) {
            foreach ($keywords as $keyword) {
                $keyword = mb_strtolower($keyword, 'UTF-8');
                $count += substr_count((string)$onlyWords, (string)$keyword);
            }
            return $count;
        }
    }

    public static function getEverSeoQltyLvl()
    {
        switch ((int)Configuration::get('EVERSEO_QUALITY_LEVEL')) {
            case 0:
                $words = 20000;
                $anchors = 1000;
                $hn = 100;
                $strong = 1000;
                $keywords = 500;
                break;

            case 1:
                $words = 15000;
                $anchors = 500;
                $hn = 50;
                $strong = 500;
                $keywords = 200;
                break;

            case 2:
                $words = 10000;
                $anchors = 300;
                $hn = 50;
                $strong = 300;
                $keywords = 100;
                break;

            case 3:
                $words = 5000;
                $anchors = 50;
                $hn = 45;
                $strong = 20;
                $keywords = 50;
                break;

            case 4:
                $words = 2500;
                $anchors = 35;
                $hn = 10;
                $strong = 10;
                $keywords = 30;
                break;

            case 5:
                $words = 1500;
                $anchors = 20;
                $hn = 8;
                $strong = 8;
                $keywords = 20;
                break;

            case 6:
                $words = 1000;
                $anchors = 15;
                $hn = 6;
                $strong = 6;
                $keywords = 15;
                break;

            case 7:
                $words = 600;
                $anchors = 10;
                $hn = 4;
                $strong = 4;
                $keywords = 10;
                break;

            case 8:
                $words = 300;
                $anchors = 5;
                $hn = 2;
                $strong = 2;
                $keywords = 5;
                break;

            default:
                $words = 0;
                $anchors = 0;
                $hn = 0;
                $strong = 0;
                $keywords = 0;
                break;
        }
        $qualityLevel = array($words, $anchors, $hn, $strong, $keywords);
        return $qualityLevel;
    }

    public static function getSeoProductNote($seoProduct, $product)
    {
        $link = new Link();
        $module = Module::getInstanceByName('everpsseo');
        $errors = array();
        if (!Configuration::get('PS_SHOP_ENABLE')) {
            return false;
        }
        $product = new Product(
            (int)$seoProduct->id_seo_product,
            false,
            (int)$seoProduct->id_seo_lang,
            (int)$seoProduct->id_shop
        );
        libxml_use_internal_errors(true);
        $url = $link->getProductLink(
            $product,
            null,
            null,
            null,
            (int)$seoProduct->id_seo_lang,
            (int)$seoProduct->id_shop
        );
        $content = self::urlGetContents($url);
        $dom = new DOMDocument;
        $dom->loadHTML($content);
        $firstH = $dom->getElementsByTagName('h1')->length;
        $secondtH = $dom->getElementsByTagName('h2')->length;
        $thirdH = $dom->getElementsByTagName('h3')->length;
        $fourthH = $dom->getElementsByTagName('h4')->length;
        $fifthH = $dom->getElementsByTagName('h5')->length;
        $six = $dom->getElementsByTagName('h6')->length;
        $strong = $dom->getElementsByTagName('strong')->length;
        $anchor = $dom->getElementsByTagName('a')->length;
        $keywords = explode(
            ',',
            $seoProduct->keywords
        );
        $keywordsCount = count($keywords);
        $qualityLevel = self::getEverSeoQltyLvl();
        $onlyWords = strip_tags($content);
        $wordCount = str_word_count($onlyWords);
        $titleLength = Tools::strlen($seoProduct->meta_title);
        $metaDescLength = Tools::strlen($seoProduct->meta_description);
        $hn = 0;
        if ($firstH > 1) {
            $hn -= (int)$firstH;
            $errors[] = $module->l('You have too much h1 tag', 'everpsseo');
        }
        if ($firstH == 1 && $secondtH >= 2) {
            $hn += (int)$secondtH;
        } else {
            $errors[] = $module->l('Leaping or missing h2 tag', 'everpsseo');
        }
        if ($secondtH >= 2 && $thirdH >= 2) {
            $hn += (int)$thirdH;
        } else {
            $errors[] = $module->l('Leaping or missing h3 tag', 'everpsseo');
        }
        if ($thirdH >= 2 && $fourthH >= 2) {
            $hn += (int)$fourthH;
        } else {
            $errors[] = $module->l('Leaping or missing h4 tag', 'everpsseo');
        }
        if ($fourthH >= 2 && $fifthH >= 2) {
            $hn += (int)$fifthH;
        } else {
            $errors[] = $module->l('Leaping or missing h5 tag', 'everpsseo');
        }
        if ($fifthH >= 2 && $six >= 2) {
            $hn += (int)$six;
        } else {
            $errors[] = $module->l('Leaping or missing h6 tag', 'everpsseo');
        }
        $hnNote = $hn * 100 / (int)$qualityLevel[2];
        if (!$anchor) {
            $anchorNote = (int)$anchor * 100 / (int)$qualityLevel[1];
            $errors[] = $module->l('Missing anchor tags', 'everpsseo');
        } else {
            $anchorNote = (int)$anchor * 100 / (int)$qualityLevel[1];
        }
        if (!$strong) {
            $strongNote = (int)$strong * 100 / (int)$qualityLevel[3];
            $errors[] = $module->l('Missing strong tags', 'everpsseo');
        } else {
            $strongNote = (int)$strong * 100 / (int)$qualityLevel[3];
        }
        if (!$keywordsCount || (int)$keywordsCount <= 0) {
            $keywordsNote = (int)$keywordsCount * 100 / (int)$qualityLevel[4];
            $errors[] = $module->l('Missing keywords', 'everpsseo');
        } else {
            $keywordsNote = (int)$keywordsCount * 100 / (int)$qualityLevel[4];
        }
        $wordsNote = $wordCount * 100 / (int)$qualityLevel[0];
        if ($titleLength < 65 && $titleLength > 30) {
            $titleNote = $titleLength * 100 / 65;
        } else {
            $titleNote = 0;
            if ($titleLength < 30 || $titleLength > 65) {
                $errors[] = $module->l('Wrong title length', 'everpsseo');
            }
        }
        if ($product->name == $seoProduct->meta_title) {
            $titleNote = 0;
            $errors[] = $module->l('Meta title is same as product name', 'everpsseo');
        }
        if ($metaDescLength < 165 && $metaDescLength > 90) {
            $metadescNote = $metaDescLength * 100 / 165;
        } else {
            $metadescNote = 0;
            if ($metaDescLength < 90 || $metaDescLength > 165) {
                $errors[] = $module->l('Wrong meta description length', 'everpsseo');
            }
        }
        $note = ($hnNote + $anchorNote + $strongNote + $keywordsNote + $wordsNote + $titleNote + $metadescNote) / 8;
        if ((int)$note > 100) {
            $note = 100;
        }
        $return = array(
            'note' => (int)$note,
            'errors' => $errors
        );
        return $return;
    }

    public static function getSeoCategoryNote($seoCategory, $category)
    {
        $link = new Link();
        $module = Module::getInstanceByName('everpsseo');
        $errors = array();
        if (!Configuration::get('PS_SHOP_ENABLE')) {
            return false;
        }
        libxml_use_internal_errors(true);
        $url = $link->getCategoryLink(
            (int)$seoCategory->id_seo_category,
            null,
            (int)$seoCategory->id_seo_lang,
            null,
            (int)$seoCategory->id_shop
        );
        $content = self::urlGetContents($url);
        $url = $link->getCategoryLink(
            (int)$seoCategory->id_seo_category,
            null,
            (int)$seoCategory->id_seo_lang,
            null,
            (int)$seoCategory->id_shop
        );
        $content = self::urlGetContents($url);
        $url = $link->getCategoryLink(
            (int)$seoCategory->id_seo_category,
            null,
            (int)$seoCategory->id_seo_lang,
            null,
            (int)$seoCategory->id_shop
        );
        $content = self::urlGetContents($url);
        $url = $link->getCategoryLink(
            (int)$seoCategory->id_seo_category,
            null,
            (int)$seoCategory->id_seo_lang,
            null,
            (int)$seoCategory->id_shop
        );
        $content = self::urlGetContents($url);
        $url = $link->getCategoryLink(
            (int)$seoCategory->id_seo_category,
            null,
            (int)$seoCategory->id_seo_lang,
            null,
            (int)$seoCategory->id_shop
        );
        $content = self::urlGetContents($url);
        $url = $link->getCategoryLink(
            (int)$seoCategory->id_seo_category,
            null,
            (int)$seoCategory->id_seo_lang,
            null,
            (int)$seoCategory->id_shop
        );
        $content = self::urlGetContents($url);
        $url = $link->getCategoryLink(
            (int)$seoCategory->id_seo_category,
            null,
            (int)$seoCategory->id_seo_lang,
            null,
            (int)$seoCategory->id_shop
        );
        $content = self::urlGetContents($url);
        $url = $link->getCategoryLink(
            (int)$seoCategory->id_seo_category,
            null,
            (int)$seoCategory->id_seo_lang,
            null,
            (int)$seoCategory->id_shop
        );
        $content = self::urlGetContents($url);
        $url = $link->getCategoryLink(
            (int)$seoCategory->id_seo_category,
            null,
            (int)$seoCategory->id_seo_lang,
            null,
            (int)$seoCategory->id_shop
        );
        $content = self::urlGetContents($url);
        $url = $link->getCategoryLink(
            (int)$seoCategory->id_seo_category,
            null,
            (int)$seoCategory->id_seo_lang,
            null,
            (int)$seoCategory->id_shop
        );
        $content = self::urlGetContents($url);
        $url = $link->getCategoryLink(
            (int)$seoCategory->id_seo_category,
            null,
            (int)$seoCategory->id_seo_lang,
            null,
            (int)$seoCategory->id_shop
        );
        $content = self::urlGetContents($url);
        $url = $link->getCategoryLink(
            (int)$seoCategory->id_seo_category,
            null,
            (int)$seoCategory->id_seo_lang,
            null,
            (int)$seoCategory->id_shop
        );
        $content = self::urlGetContents($url);
        if (empty($content)) {
            return 0;
        }
        $dom = new DOMDocument;
        $dom->loadHTML($content);
        $firstH = $dom->getElementsByTagName('h1')->length;
        $secondtH = $dom->getElementsByTagName('h2')->length;
        $thirdH = $dom->getElementsByTagName('h3')->length;
        $fourthH = $dom->getElementsByTagName('h4')->length;
        $fifthH = $dom->getElementsByTagName('h5')->length;
        $six = $dom->getElementsByTagName('h6')->length;
        $strong = $dom->getElementsByTagName('strong')->length;
        $anchor = $dom->getElementsByTagName('a')->length;
        $keywords = explode(
            ',',
            $seoCategory->keywords
        );
        $keywordsCount = count($keywords);
        $qualityLevel = self::getEverSeoQltyLvl();
        $onlyWords = strip_tags($content);
        $wordCount = str_word_count($onlyWords);
        $titleLength = Tools::strlen($seoCategory->meta_title);
        $metaDescLength = Tools::strlen($seoCategory->meta_description);
        $hn = 0;
        if ($firstH > 1) {
            $hn -= (int)$firstH;
            $errors[] = $module->l('You have too much h1 tag', 'everpsseo');
        }
        if ($firstH == 1 && $secondtH >= 2) {
            $hn += (int)$secondtH;
        } else {
            $errors[] = $module->l('Leaping or missing h2 tag', 'everpsseo');
        }
        if ($secondtH >= 2 && $thirdH >= 2) {
            $hn += (int)$thirdH;
        } else {
            $errors[] = $module->l('Leaping or missing h3 tag', 'everpsseo');
        }
        if ($thirdH >= 2 && $fourthH >= 2) {
            $hn += (int)$fourthH;
        } else {
            $errors[] = $module->l('Leaping or missing h4 tag', 'everpsseo');
        }
        if ($fourthH >= 2 && $fifthH >= 2) {
            $hn += (int)$fifthH;
        } else {
            $errors[] = $module->l('Leaping or missing h5 tag', 'everpsseo');
        }
        if ($fifthH >= 2 && $six >= 2) {
            $hn += (int)$six;
        } else {
            $errors[] = $module->l('Leaping or missing h6 tag', 'everpsseo');
        }
        $hnNote = $hn * 100 / (int)$qualityLevel[2];
        $hnNote = $hn * 100 / (int)$qualityLevel[2];
        if (!$anchor) {
            $anchorNote = (int)$anchor * 100 / (int)$qualityLevel[1];
            $errors[] = $module->l('Missing anchor tags', 'everpsseo');
        } else {
            $anchorNote = (int)$anchor * 100 / (int)$qualityLevel[1];
        }
        if (!$strong) {
            $strongNote = (int)$strong * 100 / (int)$qualityLevel[3];
            $errors[] = $module->l('Missing strong tags', 'everpsseo');
        } else {
            $strongNote = (int)$strong * 100 / (int)$qualityLevel[3];
        }
        if (!$keywordsCount || (int)$keywordsCount <= 0) {
            $keywordsNote = (int)$keywordsCount * 100 / (int)$qualityLevel[4];
            $errors[] = $module->l('Missing keywords', 'everpsseo');
        } else {
            $keywordsNote = (int)$keywordsCount * 100 / (int)$qualityLevel[4];
        }
        $wordsNote = $wordCount * 100 / (int)$qualityLevel[0];
        if ($titleLength < 65 && $titleLength > 30) {
            $titleNote = $titleLength * 100 / 65;
        } else {
            $titleNote = 0;
            if ($titleLength < 30 || $titleLength > 65) {
                $errors[] = $module->l('Wrong title length', 'everpsseo');
            }
        }
        if ($category->name == $seoCategory->meta_title) {
            $titleNote = 0;
            $errors[] = $module->l('Meta title is same as category name', 'everpsseo');
        }
        if ($metaDescLength < 165 && $metaDescLength > 90) {
            $metadescNote = $metaDescLength * 100 / 165;
        } else {
            $metadescNote = 0;
            if ($metaDescLength < 90 || $metaDescLength > 165) {
                $errors[] = $module->l('Wrong meta description length', 'everpsseo');
            }
        }
        $note = ($hnNote + $anchorNote + $strongNote + $keywordsNote + $wordsNote + $titleNote + $metadescNote) / 8;
        if ((int)$note > 100) {
            $note = 100;
        }
        $return = array(
            'note' => (int)$note,
            'errors' => $errors
        );
        return $return;
    }

    public static function getSeoManufacturerNote($seoManufacturer, $manufacturer)
    {
        $link = new Link();
        $module = Module::getInstanceByName('everpsseo');
        $errors = array();
        if (!Configuration::get('PS_SHOP_ENABLE')) {
            return false;
        }
        libxml_use_internal_errors(true);
        $url = $link->getManufacturerLink(
            (int)$seoManufacturer->id_seo_manufacturer,
            null,
            (int)$seoManufacturer->id_seo_lang,
            (int)$seoManufacturer->id_shop
        );
        $content = self::urlGetContents($url);
        if (empty($content)) {
            return 0;
        }
        $dom = new DOMDocument;
        $dom->loadHTML($content);
        $firstH = $dom->getElementsByTagName('h1')->length;
        $secondtH = $dom->getElementsByTagName('h2')->length;
        $thirdH = $dom->getElementsByTagName('h3')->length;
        $fourthH = $dom->getElementsByTagName('h4')->length;
        $fifthH = $dom->getElementsByTagName('h5')->length;
        $six = $dom->getElementsByTagName('h6')->length;
        $strong = $dom->getElementsByTagName('strong')->length;
        $anchor = $dom->getElementsByTagName('a')->length;
        $keywords = explode(
            ',',
            $seoManufacturer->keywords
        );
        $keywordsCount = count($keywords);
        $qualityLevel = self::getEverSeoQltyLvl();
        $onlyWords = strip_tags($content);
        $wordCount = str_word_count($onlyWords);
        $titleLength = Tools::strlen($seoManufacturer->meta_title);
        $metaDescLength = Tools::strlen($seoManufacturer->meta_description);
        $hn = 0;
        if ($firstH > 1) {
            $hn -= (int)$firstH;
            $errors[] = $module->l('You have too much h1 tag', 'everpsseo');
        }
        if ($firstH == 1 && $secondtH >= 2) {
            $hn += (int)$secondtH;
        } else {
            $errors[] = $module->l('Leaping or missing h2 tag', 'everpsseo');
        }
        if ($secondtH >= 2 && $thirdH >= 2) {
            $hn += (int)$thirdH;
        } else {
            $errors[] = $module->l('Leaping or missing h3 tag', 'everpsseo');
        }
        if ($thirdH >= 2 && $fourthH >= 2) {
            $hn += (int)$fourthH;
        } else {
            $errors[] = $module->l('Leaping or missing h4 tag', 'everpsseo');
        }
        if ($fourthH >= 2 && $fifthH >= 2) {
            $hn += (int)$fifthH;
        } else {
            $errors[] = $module->l('Leaping or missing h5 tag', 'everpsseo');
        }
        if ($fifthH >= 2 && $six >= 2) {
            $hn += (int)$six;
        } else {
            $errors[] = $module->l('Leaping or missing h6 tag', 'everpsseo');
        }
        $hnNote = $hn * 100 / (int)$qualityLevel[2];
        $hnNote = $hn * 100 / (int)$qualityLevel[2];
        if (!$anchor) {
            $anchorNote = (int)$anchor * 100 / (int)$qualityLevel[1];
            $errors[] = $module->l('Missing anchor tags', 'everpsseo');
        } else {
            $anchorNote = (int)$anchor * 100 / (int)$qualityLevel[1];
        }
        if (!$strong) {
            $strongNote = (int)$strong * 100 / (int)$qualityLevel[3];
            $errors[] = $module->l('Missing strong tags', 'everpsseo');
        } else {
            $strongNote = (int)$strong * 100 / (int)$qualityLevel[3];
        }
        if (!$keywordsCount || (int)$keywordsCount <= 0) {
            $keywordsNote = (int)$keywordsCount * 100 / (int)$qualityLevel[4];
            $errors[] = $module->l('Missing keywords', 'everpsseo');
        } else {
            $keywordsNote = (int)$keywordsCount * 100 / (int)$qualityLevel[4];
        }
        $wordsNote = $wordCount * 100 / (int)$qualityLevel[0];
        if ($titleLength < 65 && $titleLength > 30) {
            $titleNote = $titleLength * 100 / 65;
        } else {
            $titleNote = 0;
            if ($titleLength < 30 || $titleLength > 65) {
                $errors[] = $module->l('Wrong title length', 'everpsseo');
            }
        }
        if ($manufacturer->name == $seoManufacturer->meta_title) {
            $titleNote = 0;
            $errors[] = $module->l('Meta title is same as manufacturer name', 'everpsseo');
        }
        if ($metaDescLength < 165 && $metaDescLength > 90) {
            $metadescNote = $metaDescLength * 100 / 165;
        } else {
            $metadescNote = 0;
            if ($metaDescLength < 90 || $metaDescLength > 165) {
                $errors[] = $module->l('Wrong meta description length', 'everpsseo');
            }
        }
        $note = ($hnNote + $anchorNote + $strongNote + $keywordsNote + $wordsNote + $titleNote + $metadescNote) / 8;
        if ((int)$note > 100) {
            $note = 100;
        }
        $return = array(
            'note' => (int)$note,
            'errors' => $errors
        );
        return $return;
    }

    public static function getSeoSupplierNote($seoSupplier, $supplier)
    {
        $link = new Link();
        $module = Module::getInstanceByName('everpsseo');
        $errors = array();
        if (!Configuration::get('PS_SHOP_ENABLE')) {
            return false;
        }
        libxml_use_internal_errors(true);
        $url = $link->getSupplierLink(
            (int)$seoSupplier->id_seo_supplier,
            null,
            (int)$seoSupplier->id_seo_lang,
            (int)$seoSupplier->id_shop
        );
        $content = self::urlGetContents($url);
        if (empty($content)) {
            return 0;
        }
        $dom = new DOMDocument;
        $dom->loadHTML($content);
        $firstH = $dom->getElementsByTagName('h1')->length;
        $secondtH = $dom->getElementsByTagName('h2')->length;
        $thirdH = $dom->getElementsByTagName('h3')->length;
        $fourthH = $dom->getElementsByTagName('h4')->length;
        $fifthH = $dom->getElementsByTagName('h5')->length;
        $six = $dom->getElementsByTagName('h6')->length;
        $strong = $dom->getElementsByTagName('strong')->length;
        $anchor = $dom->getElementsByTagName('a')->length;
        $keywords = explode(
            ',',
            $seoSupplier->keywords
        );
        $keywordsCount = count($keywords);
        $qualityLevel = self::getEverSeoQltyLvl();
        $onlyWords = strip_tags($content);
        $wordCount = str_word_count($onlyWords);
        $titleLength = Tools::strlen($seoSupplier->meta_title);
        $metaDescLength = Tools::strlen($seoSupplier->meta_description);
        $hn = 0;
        if ($firstH > 1) {
            $hn -= (int)$firstH;
            $errors[] = $module->l('You have too much h1 tag', 'everpsseo');
        }
        if ($firstH == 1 && $secondtH >= 2) {
            $hn += (int)$secondtH;
        } else {
            $errors[] = $module->l('Leaping or missing h2 tag', 'everpsseo');
        }
        if ($secondtH >= 2 && $thirdH >= 2) {
            $hn += (int)$thirdH;
        } else {
            $errors[] = $module->l('Leaping or missing h3 tag', 'everpsseo');
        }
        if ($thirdH >= 2 && $fourthH >= 2) {
            $hn += (int)$fourthH;
        } else {
            $errors[] = $module->l('Leaping or missing h4 tag', 'everpsseo');
        }
        if ($fourthH >= 2 && $fifthH >= 2) {
            $hn += (int)$fifthH;
        } else {
            $errors[] = $module->l('Leaping or missing h5 tag', 'everpsseo');
        }
        if ($fifthH >= 2 && $six >= 2) {
            $hn += (int)$six;
        } else {
            $errors[] = $module->l('Leaping or missing h6 tag', 'everpsseo');
        }
        $hnNote = $hn * 100 / (int)$qualityLevel[2];
        $hnNote = $hn * 100 / (int)$qualityLevel[2];
        if (!$anchor) {
            $anchorNote = (int)$anchor * 100 / (int)$qualityLevel[1];
            $errors[] = $module->l('Missing anchor tags', 'everpsseo');
        } else {
            $anchorNote = (int)$anchor * 100 / (int)$qualityLevel[1];
        }
        if (!$strong) {
            $strongNote = (int)$strong * 100 / (int)$qualityLevel[3];
            $errors[] = $module->l('Missing strong tags', 'everpsseo');
        } else {
            $strongNote = (int)$strong * 100 / (int)$qualityLevel[3];
        }
        if (!$keywordsCount || (int)$keywordsCount <= 0) {
            $keywordsNote = (int)$keywordsCount * 100 / (int)$qualityLevel[4];
            $errors[] = $module->l('Missing keywords', 'everpsseo');
        } else {
            $keywordsNote = (int)$keywordsCount * 100 / (int)$qualityLevel[4];
        }
        $wordsNote = $wordCount * 100 / (int)$qualityLevel[0];
        if ($titleLength < 65 && $titleLength > 30) {
            $titleNote = $titleLength * 100 / 65;
        } else {
            $titleNote = 0;
            if ($titleLength < 30 || $titleLength > 65) {
                $errors[] = $module->l('Wrong title length', 'everpsseo');
            }
        }
        if ($supplier->name == $seoSupplier->meta_title) {
            $titleNote = 0;
            $errors[] = $module->l('Meta title is same as supplier name', 'everpsseo');
        }
        if ($metaDescLength < 165 && $metaDescLength > 90) {
            $metadescNote = $metaDescLength * 100 / 165;
        } else {
            $metadescNote = 0;
            if ($metaDescLength < 90 || $metaDescLength > 165) {
                $errors[] = $module->l('Wrong meta description length', 'everpsseo');
            }
        }
        $note = ($hnNote + $anchorNote + $strongNote + $keywordsNote + $wordsNote + $titleNote + $metadescNote) / 8;
        if ((int)$note > 100) {
            $note = 100;
        }
        $return = array(
            'note' => (int)$note,
            'errors' => $errors
        );
        return $return;
    }

    public static function getSeoPageMetaNote($seoMeta, $meta)
    {
        $link = new Link();
        $module = Module::getInstanceByName('everpsseo');
        $errors = array();
        if (!Configuration::get('PS_SHOP_ENABLE')) {
            return false;
        }
        libxml_use_internal_errors(true);
        $link = new Link();
        $dom = new DOMDocument;
        $url = $link->getPageLink((string)$meta->page);
        $content = self::urlGetContents($url);
        if (empty($content)) {
            return 0;
        }
        $dom->loadHTMLFile($content);
        // $tags = $dom->getElementsByTagName('body');
        $firstH = $dom->getElementsByTagName('h1')->length;
        $secondtH = $dom->getElementsByTagName('h2')->length;
        $thirdH = $dom->getElementsByTagName('h3')->length;
        $fourthH = $dom->getElementsByTagName('h4')->length;
        $fifthH = $dom->getElementsByTagName('h5')->length;
        $six = $dom->getElementsByTagName('h6')->length;
        $strong = $dom->getElementsByTagName('strong')->length;
        $anchor = $dom->getElementsByTagName('a')->length;
        $keywords = explode(
            ',',
            $seoMeta->keywords
        );
        $keywordsCount = count($keywords);
        $qualityLevel = self::getEverSeoQltyLvl();
        // $onlyWords = strip_tags($tags->item(0)->textContent);
        // $wordCount = str_word_count($onlyWords);
        $titleLength = Tools::strlen($seoMeta->meta_title);
        $metaDescLength = Tools::strlen($seoMeta->meta_description);
        $note = 0;
        if ($firstH > 1) {
            $note -= (int)$firstH;
            $errors[] = $module->l('You have too much h1 tag', 'everpsseo');
        }
        if ($firstH = 1 && $secondtH >= 2) {
            $note += (int)$secondtH;
        } else {
            $errors[] = $module->l('Leaping or missing h2 tag', 'everpsseo');
        }
        if ($secondtH >= 2 && $thirdH >= 2) {
            $note += (int)$thirdH;
        } else {
            $errors[] = $module->l('Leaping or missing h3 tag', 'everpsseo');
        }
        if ($thirdH >= 2 && $fourthH >= 2) {
            $note += (int)$fourthH;
        } else {
            $errors[] = $module->l('Leaping or missing h4 tag', 'everpsseo');
        }
        if ($fourthH >= 2 && $fifthH >= 2) {
            $note += (int)$fifthH;
        } else {
            $errors[] = $module->l('Leaping or missing h5 tag', 'everpsseo');
        }
        if ($fifthH >= 2 && $six >= 2) {
            $note += (int)$six;
        } else {
            $errors[] = $module->l('Leaping or missing h6 tag', 'everpsseo');
        }
        if ($anchor >= 1) {
            $anchorNote = (int)$anchor * 100 / (int)$qualityLevel[1];
            $note += $anchorNote;
        } else {
            $anchorNote = (int)$anchor * 100 / (int)$qualityLevel[1];
            $note += $anchorNote;
            $errors[] = $module->l('Missing anchor tags', 'everpsseo');
        }
        if ($strong >= 1) {
            $note += (int)$strong;
        } else {
            $errors[] = $module->l('Missing anchor tags', 'everpsseo');
        }
        $note += (int)$keywordsCount;
        // if ($wordCount > (int)$qualityLevel[0]) {
        //     $note += $wordCount;
        // } else {
        //     $note -= $wordCount;
        //     $errors[] = $module->l('Not enough content on page');
        // }
        if ($titleLength > 65) {
            $note -= 65 - $titleLength;
            $errors[] = $module->l('Title tag too long', 'everpsseo');
        } elseif ($titleLength < 30) {
            $note -= 30 - $titleLength;
            $errors[] = $module->l('Title tag too short', 'everpsseo');
        } else {
            $note += 65 - $titleLength;
        }
        if ($metaDescLength > 165) {
            $note -= 165 + ($metaDescLength + ((int)$qualityLevel[0] / 10));
            $errors[] = $module->l('Meta description tag too long', 'everpsseo');
        } elseif ($metaDescLength < 90) {
            $note -= 90 + ($metaDescLength + ((int)$qualityLevel[0] / 10));
            $errors[] = $module->l('Meta description tag too short', 'everpsseo');
        } else {
            $note += 165 - ($metaDescLength + ((int)$qualityLevel[0] / 10));
        }
        if ((int)$note > 100) {
            $note = 100;
        }
        $return = array(
            'note' => (int)$note,
            'errors' => $errors
        );
        return $return;
    }

    public static function getSeoCmsNote($seoCms, $cms)
    {
        $link = new Link();
        $module = Module::getInstanceByName('everpsseo');
        $errors = array();
        if (!Configuration::get('PS_SHOP_ENABLE')) {
            return false;
        }
        libxml_use_internal_errors(true);
        $url = $link->getCMSLink(
            (int)$seoCms->id_seo_cms,
            null,
            null,
            (int)$seoCms->id_seo_lang,
            (int)$seoCms->id_shop
        );
        $content = self::urlGetContents($url);
        if (empty($content)) {
            return 0;
        }
        $dom = new DOMDocument;
        $dom->loadHTML($content);
        $firstH = $dom->getElementsByTagName('h1')->length;
        $secondtH = $dom->getElementsByTagName('h2')->length;
        $thirdH = $dom->getElementsByTagName('h3')->length;
        $fourthH = $dom->getElementsByTagName('h4')->length;
        $fifthH = $dom->getElementsByTagName('h5')->length;
        $six = $dom->getElementsByTagName('h6')->length;
        $strong = $dom->getElementsByTagName('strong')->length;
        $anchor = $dom->getElementsByTagName('a')->length;
        $keywords = explode(
            ',',
            $seoCms->keywords
        );
        $keywordsCount = count($keywords);
        $qualityLevel = self::getEverSeoQltyLvl();
        $onlyWords = strip_tags($content);
        $wordCount = str_word_count($onlyWords);
        $titleLength = Tools::strlen($seoCms->meta_title);
        $metaDescLength = Tools::strlen($seoCms->meta_description);
        $hn = 0;
        if ($firstH) {
            $hn -= (int)$firstH;
            $errors[] = $module->l('You have too much h1 tag', 'everpsseo');
        }
        if ($firstH == 1 && $secondtH >= 2) {
            $hn += (int)$secondtH;
        } else {
            $errors[] = $module->l('Leaping or missing h2 tag', 'everpsseo');
        }
        if ($secondtH >= 2 && $thirdH >= 2) {
            $hn += (int)$thirdH;
        } else {
            $errors[] = $module->l('Leaping or missing h3 tag', 'everpsseo');
        }
        if ($thirdH >= 2 && $fourthH >= 2) {
            $hn += (int)$fourthH;
        } else {
            $errors[] = $module->l('Leaping or missing h4 tag', 'everpsseo');
        }
        if ($fourthH >= 2 && $fifthH >= 2) {
            $hn += (int)$fifthH;
        } else {
            $errors[] = $module->l('Leaping or missing h5 tag', 'everpsseo');
        }
        if ($fifthH >= 2 && $six >= 2) {
            $hn += (int)$six;
        } else {
            $errors[] = $module->l('Leaping or missing h6 tag', 'everpsseo');
        }
        $hnNote = $hn * 100 / (int)$qualityLevel[2];
        $hnNote = $hn * 100 / (int)$qualityLevel[2];
        if (!$anchor) {
            $anchorNote = (int)$anchor * 100 / (int)$qualityLevel[1];
            $errors[] = $module->l('Missing anchor tags', 'everpsseo');
        } else {
            $anchorNote = (int)$anchor * 100 / (int)$qualityLevel[1];
        }
        if (!$strong) {
            $strongNote = (int)$strong * 100 / (int)$qualityLevel[3];
            $errors[] = $module->l('Missing strong tags', 'everpsseo');
        } else {
            $strongNote = (int)$strong * 100 / (int)$qualityLevel[3];
        }
        if (!$keywordsCount || (int)$keywordsCount <= 0) {
            $keywordsNote = (int)$keywordsCount * 100 / (int)$qualityLevel[4];
            $errors[] = $module->l('Missing keywords', 'everpsseo');
        } else {
            $keywordsNote = (int)$keywordsCount * 100 / (int)$qualityLevel[4];
        }
        $wordsNote = $wordCount * 100 / (int)$qualityLevel[0];
        if ($titleLength < 65 && $titleLength > 30) {
            $titleNote = $titleLength * 100 / 65;
        } else {
            $titleNote = 0;
            if ($titleLength < 30 || $titleLength > 65) {
                $errors[] = $module->l('Wrong title length', 'everpsseo');
            }
        }
        if ($cms->meta_title == $seoCms->meta_title) {
            $titleNote = 0;
            $errors[] = $module->l('Meta title is same as CMS name', 'everpsseo');
        }
        if ($metaDescLength < 165 && $metaDescLength > 90) {
            $metadescNote = $metaDescLength * 100 / 165;
        } else {
            $metadescNote = 0;
            if ($metaDescLength < 90 || $metaDescLength > 165) {
                $errors[] = $module->l('Wrong meta description length', 'everpsseo');
            }
        }
        $note = ($hnNote + $anchorNote + $strongNote + $keywordsNote + $wordsNote + $titleNote + $metadescNote) / 8;
        if ((int)$note > 100) {
            $note = 100;
        }
        $return = array(
            'note' => (int)$note,
            'errors' => $errors
        );
        return $return;
    }

    public static function getSeoCmsCategoryNote($seoCmsCategory, $cms_category)
    {
        $link = new Link();
        $module = Module::getInstanceByName('everpsseo');
        $errors = array();
        if (!Configuration::get('PS_SHOP_ENABLE')
            || $seoCmsCategory->id_seo_cms_category == 1
        ) {
            $note = 0;
            return $note;
        }
        libxml_use_internal_errors(true);
        $url = $link->getCMSCategoryLink(
            (int)$seoCmsCategory->id_seo_cms_category,
            null,
            (int)$seoCmsCategory->id_seo_lang,
            null,
            (int)$seoCmsCategory->id_shop
        );
        $content = self::urlGetContents($url);
        if ($cms_category->id_seo_cms_category == 1) {
            $errors[] = $module->l('Root category should be disabled on SEO', 'everpsseo');
        }
        if (empty($content)) {
            $note = 0;
            return $note;
        }
        $dom = new DOMDocument;
        $dom->loadHTML($content);
        $firstH = $dom->getElementsByTagName('h1')->length;
        $secondtH = $dom->getElementsByTagName('h2')->length;
        $thirdH = $dom->getElementsByTagName('h3')->length;
        $fourthH = $dom->getElementsByTagName('h3')->length;
        $fifthH = $dom->getElementsByTagName('h3')->length;
        $six = $dom->getElementsByTagName('h3')->length;
        $strong = $dom->getElementsByTagName('strong')->length;
        $anchor = $dom->getElementsByTagName('a')->length;
        $keywords = explode(
            ',',
            $seoCmsCategory->keywords
        );
        $keywordsCount = count($keywords);
        $qualityLevel = self::getEverSeoQltyLvl();
        $onlyWords = strip_tags($content);
        $wordCount = str_word_count($onlyWords);
        $titleLength = Tools::strlen($seoCmsCategory->meta_title);
        $metaDescLength = Tools::strlen($seoCmsCategory->meta_description);
        $note = 0;
        if ($firstH) {
            $note -= (int)$firstH;
            $errors[] = $module->l('You have too much h1 tag', 'everpsseo');
        }
        if ($firstH == 1 && $secondtH >= 2) {
            $note += (int)$secondtH;
        } else {
            $errors[] = $module->l('Leaping or missing h2 tag', 'everpsseo');
        }
        if ($secondtH >= 2 && $thirdH >= 2) {
            $note += (int)$thirdH;
        } else {
            $errors[] = $module->l('Leaping or missing h3 tag', 'everpsseo');
        }
        if ($thirdH >= 2 && $fourthH >= 2) {
            $note += (int)$fourthH;
        } else {
            $errors[] = $module->l('Leaping or missing h4 tag', 'everpsseo');
        }
        if ($fourthH >= 2 && $fifthH >= 2) {
            $note += (int)$fifthH;
        } else {
            $errors[] = $module->l('Leaping or missing h5 tag', 'everpsseo');
        }
        if ($fifthH >= 2 && $six >= 2) {
            $note += (int)$six;
        } else {
            $errors[] = $module->l('Leaping or missing h6 tag', 'everpsseo');
        }
        if ($anchor >= 1) {
            $note += (int)$anchor;
        } else {
            $errors[] = $module->l('Missing anchors tags', 'everpsseo');
        }
        if ($strong >= 1) {
            $note += (int)$strong;
        } else {
            $errors[] = $module->l('Missing strong tags', 'everpsseo');
        }
        $note += (int)$keywordsCount;
        if ($wordCount > (int)$qualityLevel[0]) {
            $note += $wordCount;
        } else {
            $note -= $wordCount;
            $errors[] = $module->l('Not enough content', 'everpsseo');
        }
        if ($titleLength > 65) {
            $note -= 65 - $titleLength;
            $errors[] = $module->l('Title tag too long', 'everpsseo');
        } elseif ($titleLength < 30) {
            $note -= 30 - $titleLength;
            $errors[] = $module->l('Title tag too short', 'everpsseo');
        } else {
            $note += 65 - $titleLength;
        }
        if ($metaDescLength > 165) {
            $note -= 165 + ($metaDescLength + ((int)$qualityLevel[0] / 10));
            $errors[] = $module->l('Meta description too long', 'everpsseo');
        } elseif ($metaDescLength < 90) {
            $note -= 90 + ($metaDescLength + ((int)$qualityLevel[0] / 10));
            $errors[] = $module->l('Meta description too short', 'everpsseo');
        } else {
            $note += 165 - ($metaDescLength + ((int)$qualityLevel[0] / 10));
        }
        if ((int)$note > 100) {
            $note = 100;
        }
        $return = array(
            'note' => (int)$note,
            'errors' => $errors
        );
        return $return;
    }

    public static function urlGetContents(
        $url,
        $useragent = 'cURL',
        $headers = false,
        $follow_redirects = true,
        $debug = false
    ) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($headers == true) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }
        if ($headers == 'headers only') {
            curl_setopt($ch, CURLOPT_NOBODY, 1);
        }
        if ($follow_redirects == true) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        }
        if ($debug == true) {
            $result = array();
            $result['contents'] = curl_exec($ch);
            $result['info'] = curl_getinfo($ch);
        } else {
            $result = curl_exec($ch);
        }
        curl_close($ch);
        return $result;
    }
}
