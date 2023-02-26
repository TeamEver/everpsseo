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

class EverPsSeoTools extends ObjectModel
{
    /**
     * Detect if given link is absolute
     * @param full link
     * @return true absolute, false if not
    */
    public static function isAbsolutePath($url)
    {
        $pattern = "/^(?:ftp|https?|feed)?:?\/\/(?:(?:(?:[\w\.\-\+!$&'\(\)*\+,;=]|%[0-9a-f]{2})+:)*
        (?:[\w\.\-\+%!$&'\(\)*\+,;=]|%[0-9a-f]{2})+@)?(?:
        (?:[a-z0-9\-\.]|%[0-9a-f]{2})+|(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\]))(?::[0-9]+)?(?:[\/|\?]
        (?:[\w#!:\.\?\+\|=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})*)?$/xi";

        return (bool)preg_match($pattern, $url);
    }

    /**
     * Mobile detection function
     * @return true if mobile device is detected
    */
    public static function isMobileDevice($include_tablet = false)
    {
        if ($include_tablet) {
            return (bool)preg_match('#\b(ip(hone|od|ad)|android|opera m(ob|in)i|windows (phone|ce)|blackberry|tablet'.
            '|s(ymbian|eries60|amsung)|p(laybook|alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.
            '|mobile|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT']);
        } else {
            return (bool)preg_match('#\b(ip(hone|od)|android\b.+\bmobile|opera m(ob|in)i|windows (phone|ce)|blackberry'.
            '|s(ymbian|eries60|amsung)|p(alm|rofile/midp|laystation portable)|nokia|fennec|htc[\-_]'.
            '|up\.browser|[1-4][0-9]{2}x[1-4][0-9]{2})\b#i', $_SERVER['HTTP_USER_AGENT']);
        }
    }

    /**
    * Remove empty lines from string
    * @param string
    * @return correct string
    */
    public static function removeEmptyLines($string)
    {
        $string = trim(preg_replace('/\r|\n/', ' ', $string));
        return $string;
    }

    /**
    * Change shortcodes
    * @param string message, int id_entity (admin or customer)
    */
    public static function changeFrontShortcodes($message, $id_entity = false)
    {
        $link = new Link();
        $contactLink = $link->getPageLink('contact');
        $defaultShortcodes = array(
            '[shop_url]' => Tools::getShopDomainSsl(true),
            '[shop_name]'=> (string)Configuration::get('PS_SHOP_NAME'),
            '[start_cart_link]' => '<a href="'
            .Tools::getShopDomainSsl(true)
            .'/index.php?controller=cart&action=show" rel="nofollow" target="_blank">',
            '[end_cart_link]' => '</a>',
            '[start_shop_link]' => '<a href="'
            .Tools::getShopDomainSsl(true)
            .'" target="_blank">',
            '[start_contact_link]' => '<a href="'.$contactLink.'" rel="nofollow" target="_blank">',
            '[end_shop_link]' => '</a>',
            '[end_contact_link]' => '</a>'
        );
        if ($id_entity) {
            $entity = new Customer((int)$id_entity);
            $gender = new Gender((int)$entity->id_gender, (int)$entity->id_lang);
            $entityShortcodes = array(
                '[entity_lastname]' => $entity->lastname,
                '[entity_firstname]' => $entity->firstname,
                '[entity_company]' => $entity->company,
                '[entity_siret]' => $entity->siret,
                '[entity_ape]' => $entity->ape,
                '[entity_birthday]' => $entity->birthday,
                '[entity_website]' => $entity->website,
                '[entity_gender]' => $gender->name
            );
        } else {
            $entityShortcodes = array(
                '[entity_lastname]' => '',
                '[entity_firstname]' => '',
                '[entity_company]' => '',
                '[entity_siret]' => '',
                '[entity_ape]' => '',
                '[entity_birthday]' => '',
                '[entity_website]' => '',
                '[entity_gender]' => ''
            );
        }
        $shortcodes = array_merge($entityShortcodes, $defaultShortcodes);
        foreach ($shortcodes as $key => $value) {
            if (strpos($message, $key) !== false) {
                $message = str_replace($key, $value, $message);
                $message = Hook::exec('actionChangeSeoShortcodes', array(
                    'content' => $message
                ));
            }
        }
        return $message;
    }

    /**
     * Returns an array of language IDs.
     * Missing on PrestaShop 1.6.1.7
     * @param bool $active  Select only active languages
     * @param int|bool $id_shop Shop ID
     *
     * @return array
     */
    public static function getLanguagesIds($active = true, $id_shop = false)
    {
        if (_PS_VERSION_ <= '1.6.1.7') {
            $id_langs = array();
            $langs = Language::getLanguages($active, $id_shop, true);
            foreach ($langs as $lang) {
                $id_langs[] = (int)$lang;
            }
            if ($id_langs) {
                return $id_langs;
            }
        }
    }

    /**
     * Get basic SEO infos for each element, and set counter +1
     * @param string controller name, int id shop, int id object, int id lang
     * @return array of SEO datas
    */
    public static function getSeoIndexFollow($controller, $id_shop, $id, $id_lang)
    {
        $cache_id = 'EverPsSeoTools::getSeoIndexFollow_'
        .(string)$controller
        .'_'
        .(int)$id
        .'_'
        .(int)$id_shop
        .'_'
        .(int)$id_lang;
        if (!Cache::isStored($cache_id)) {
            //Return index and follow data
            switch ($controller) {
                case 'product':
                    $table = _DB_PREFIX_.'ever_seo_product';
                    $element = 'id_seo_product';
                    break;

                case 'category':
                    $table = _DB_PREFIX_.'ever_seo_category';
                    $element = 'id_seo_category';
                    break;

                case 'cms_category':
                    $table = _DB_PREFIX_.'ever_seo_cms_category';
                    $element = 'id_seo_cms_category';
                    break;

                case 'cms':
                    $table = _DB_PREFIX_.'ever_seo_cms';
                    $element = 'id_seo_cms';
                    break;

                case 'manufacturer':
                    $table = _DB_PREFIX_.'ever_seo_manufacturer';
                    $element = 'id_seo_manufacturer';
                    break;

                case 'supplier':
                    $table = _DB_PREFIX_.'ever_seo_supplier';
                    $element = 'id_seo_supplier';
                    break;

                default:
                    $table = _DB_PREFIX_.'ever_seo_pagemeta';
                    $element = 'id_seo_pagemeta';
                    break;
            }

            $sql =
                'SELECT meta_title,
                meta_description,
                social_title,
                social_description,
                social_img_url,
                indexable,
                follow,
                allowed_sitemap
                FROM '.pSQL((string)$table).'
                WHERE '.pSQL((string)$element).' = '.(int)$id.'
                    AND id_shop = '.(int)$id_shop.'
                    AND id_seo_lang = '.(int)$id_lang;

            $updateCounter =
                'UPDATE '.pSQL($table).'
                SET count = count + 1
                WHERE '.pSQL((string)$element).' = '.(int)$id.'
                    AND id_shop = '.(int)$id_shop.'
                    AND id_seo_lang = '.(int)$id_lang;

            Db::getInstance()->execute($updateCounter);

            $return = Db::getInstance()->executeS($sql);
            Cache::store($cache_id, $return);
            return $return;
        }
        return Cache::retrieve($cache_id);
    }

    /**
    * Get IP address for current visitor
    * @return string IP address
    */
    public static function getIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
    * Get all maintenance IP address
    * @return array of IP
    */
    public static function getMaintenanceIpAddress()
    {
        $maintenance_ip = explode(
            ',',
            Configuration::get('PS_MAINTENANCE_IP')
        );
        return $maintenance_ip;
    }

    /**
    * If IP address is on maintenance
    * @return bool
    */
    public static function isMaintenanceIpAddress()
    {
        if (in_array(self::getIpAddress(), self::getMaintenanceIpAddress())) {
            return true;
        }
        return false;
    }

    /**
     * Get visitor referrer
     * @return string URL or false
    */
    public static function getReferrer()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referrer = pSQL($_SERVER['HTTP_REFERER']);
        } else {
            $referrer = false;
        }
        return $referrer;
    }

    /**
     * If page has banned args
     * @return true if args are banned, false if not
    */
    public static function pageHasBannedArgs()
    {
        $result = false;
        if ((bool)Configuration::get('EVERSEO_INDEX_ARGS') === true) {
            return false;
        }
        if (Tools::getValue('page')) {
            $result = true;
        }
        if (Tools::getValue('q')) {
            $result = true;
        }
        if (Tools::getValue('s')) {
            $result = true;
        }
        if (Tools::getValue('city')) {
            $result = true;
        }
        if (Tools::getValue('controller') == 'search') {
            $result = true;
        }
        return $result;
    }

    /**
     * Update product objects, set index and sitemap, depending on id_shop
    */
    public static function updateMultishopSitemapIndex()
    {
        $sql = array();

        $sql[] = '
            UPDATE '._DB_PREFIX_.'ever_seo_product
                SET follow = (
                    SELECT active
                    FROM '._DB_PREFIX_.'product_shop
                    WHERE '._DB_PREFIX_.'ever_seo_product.id_seo_product = '._DB_PREFIX_.'product_shop.id_product
                    AND '._DB_PREFIX_.'ever_seo_product.id_shop = '._DB_PREFIX_.'product_shop.id_shop
                );
            ';
        $sql[] = '
            UPDATE '._DB_PREFIX_.'ever_seo_product
                SET allowed_sitemap = (
                    SELECT active
                    FROM '._DB_PREFIX_.'product_shop
                    WHERE '._DB_PREFIX_.'ever_seo_product.id_seo_product = '._DB_PREFIX_.'product_shop.id_product
                    AND '._DB_PREFIX_.'ever_seo_product.id_shop = '._DB_PREFIX_.'product_shop.id_shop
                );
            ';
        foreach ($sql as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
    }

    public static function isAdminController()
    {
        if (php_sapi_name() == 'cli') {
            return true;
        }
        $controllerTypes = array('admin', 'moduleadmin');
        if (!in_array(Context::getContext()->controller->controller_type, $controllerTypes)) {
            return false;
        }
        return true;
    }

    public static function getHeaderHreflangTemplate($controller, $id_shop, $id_lang)
    {
        $cache_id = 'EverPsSeoTools::getHeaderHreflangTemplate_'
        .(string)$controller
        .'_'
        .(int)$id_shop
        .'_'
        .(int)$id_lang
        .'_'
        .date('m');
        if (!Cache::isStored($cache_id)) {
            if (Tools::getValue('fc') === 'module') {
                return false;
            }
            $template = _PS_MODULE_DIR_ . '/everpsseo/views/templates/hook/hreflangs/'.pSQL($controller).'.tpl';
            if (file_exists($template)) {
                Context::getContext()->smarty->assign([
                    'xdefault' => (int)Configuration::get('PS_LANG_DEFAULT'),
                    'everpshreflang' => Language::getLanguages(
                        true,
                        (int)$id_shop
                    ),
                ]);
                $return = Context::getContext()->smarty->fetch(
                    $template
                );
                Cache::store($cache_id, $return);
                return $return;
            }
            return false;
        }
        return Cache::retrieve($cache_id);
    }

    public static function indexLang($idLang)
    {
        $queries = [];
        // Products
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_product
        SET indexable = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Categories
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_category
        SET indexable = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Manufacturers
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_manufacturer
        SET indexable = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Manufacturers
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_supplier
        SET indexable = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Pages
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_pagemeta
        SET indexable = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // CMS categories
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_cms_category
        SET indexable = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // CMS
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_cms
        SET indexable = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Images
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_image
        SET indexable = 1
        WHERE id_seo_lang = '.(int)$idLang;
        foreach ($queries as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
        return true;
    }

    public static function followLang($idLang)
    {
        $queries = [];
        // Products
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_product
        SET follow = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Categories
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_category
        SET follow = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Manufacturers
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_manufacturer
        SET follow = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Manufacturers
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_supplier
        SET follow = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Pages
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_pagemeta
        SET follow = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // CMS categories
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_cms_category
        SET follow = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // CMS
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_cms
        SET follow = 1
        WHERE id_seo_lang = '.(int)$idLang;
        foreach ($queries as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
        return true;
    }

    public static function sitemapLang($idLang)
    {
        $queries = [];
        // Products
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_product
        SET allowed_sitemap = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Categories
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_category
        SET allowed_sitemap = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Manufacturers
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_manufacturer
        SET allowed_sitemap = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Manufacturers
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_supplier
        SET allowed_sitemap = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Pages
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_pagemeta
        SET allowed_sitemap = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // CMS categories
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_cms_category
        SET allowed_sitemap = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // CMS
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_cms
        SET allowed_sitemap = 1
        WHERE id_seo_lang = '.(int)$idLang;
        // Images
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_image
        SET allowed_sitemap = 0
        WHERE id_seo_lang = '.(int)$idLang;
        foreach ($queries as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
        return true;
    }

    public static function noIndexLang($idLang)
    {
        $queries = [];
        // Products
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_product
        SET indexable = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Categories
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_category
        SET indexable = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Manufacturers
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_manufacturer
        SET indexable = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Manufacturers
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_supplier
        SET indexable = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Pages
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_pagemeta
        SET indexable = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // CMS categories
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_cms_category
        SET indexable = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // CMS
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_cms
        SET indexable = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Images
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_image
        SET indexable = 0
        WHERE id_seo_lang = '.(int)$idLang;
        foreach ($queries as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
        return true;
    }

    public static function noFollowLang($idLang)
    {
        $queries = [];
        // Products
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_product
        SET follow = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Categories
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_category
        SET follow = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Manufacturers
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_manufacturer
        SET follow = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Manufacturers
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_supplier
        SET follow = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Pages
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_pagemeta
        SET follow = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // CMS categories
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_cms_category
        SET follow = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // CMS
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_cms
        SET follow = 0
        WHERE id_seo_lang = '.(int)$idLang;
        foreach ($queries as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
        return true;
    }

    public static function noSitemapLang($idLang)
    {
        $queries = [];
        // Products
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_product
        SET allowed_sitemap = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Categories
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_category
        SET allowed_sitemap = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Manufacturers
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_manufacturer
        SET allowed_sitemap = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Manufacturers
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_supplier
        SET allowed_sitemap = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Pages
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_pagemeta
        SET allowed_sitemap = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // CMS categories
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_cms_category
        SET allowed_sitemap = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // CMS
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_cms
        SET allowed_sitemap = 0
        WHERE id_seo_lang = '.(int)$idLang;
        // Images
        $queries[] = 'UPDATE '._DB_PREFIX_.'ever_seo_image
        SET allowed_sitemap = 0
        WHERE id_seo_lang = '.(int)$idLang;
        foreach ($queries as $s) {
            if (!Db::getInstance()->execute($s)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Index now given url
     * @param string url to send
     * @return index now page http code
    */
    public static function indexNow($url)
    {
        if (!Validate::isUrl($url)) {
            return false;
        }
        // Limit per day
        $dayCounter = (int)Configuration::get('EVERSEO_INDEXNOW_DAY');
        $dayOfWeek = date('N');
        if ($dayCounter <= 0) {
            Configuration::updateValue('EVERSEO_INDEXNOW_DAY', (int)$dayOfWeek);
        }
        // Reset counter every day
        if ($dayCounter != $dayOfWeek) {
            Configuration::updateValue('EVERSEO_INDEXNOW_DAY_COUNT', 0);
        }
        // Get counter & limit
        $dailyCount = (int)Configuration::get('EVERSEO_INDEXNOW_DAY_COUNT');
        $maxLimit = (int)Configuration::get('EVERSEO_INDEXNOW_LIMIT');
        if ($maxLimit <= 0) {
            $maxLimit = 200;
            Configuration::updateValue('EVERSEO_INDEXNOW_LIMIT', $maxLimit);
        }
        if ($dailyCount >= $maxLimit) {
            return false;
        }
        // Prepare index now
        $siteUrl = Tools::getHttpHost(true).__PS_BASE_URI__;
        $key = Configuration::get('EVERSEO_INDEXNOW_KEY');
        if (!$key) {
            $key = self::generateIndexNowKey();
        }
        $indexNowUrl = 'https://api.indexnow.org/indexnow?url='.$url.'&key='.$key.'&keyLocation='.$siteUrl.$key.'.txt';
        $ch = curl_init(
            $indexNowUrl
        );
        curl_setopt(
            $ch,
            CURLOPT_RETURNTRANSFER,
            true
        );
        curl_exec($ch);
        $response = curl_getinfo($ch);
        curl_close($ch);
        $httpCode = $response['http_code'];
        // Save counter limit
        Configuration::updateValue('EVERSEO_INDEXNOW_DAY_COUNT', (int)$dailyCount + 1);
        Configuration::updateValue('EVERSEO_INDEXNOW_DAY', (int)$dayOfWeek);
        return $httpCode;
    }

    public static function generateIndexNowKey()
    {
        $ext = '.txt';
        $key = self::generateRandomString();
        file_put_contents(
            _PS_ROOT_DIR_.'/'.$key.$ext,
            $key
        );
        Configuration::updateValue(
            'EVERSEO_INDEXNOW_KEY',
            $key
        );
        return $key;
    }

    public static function generateRandomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return Tools::strtolower($randomString);
    }

    public static function addElementInTable($table, $object, $id_element, $id_shop, $id_lang)
    {
        return Db::getInstance()->insert(
            $table,
            array(
                $object => (int)$id_element,
                'id_shop' => (int)$id_shop,
                'id_seo_lang' => (int)$id_lang
            )
        );
    }

    public static function registerEverConfiguration()
    {
        return Configuration::updateValue('EVERSEO_CONFIGURE', false)
            && Configuration::updateValue('EVERSEO_LANG', false)
            && Configuration::updateValue('EVERSEO_LINKED_NBR', '1')
            && Configuration::updateValue('EVERSEO_CMS_LINKED', false)
            && Configuration::updateValue('EVERSEO_LONG_DESC_LINKED', false)
            && Configuration::updateValue('EVERSEO_SHORT_DESC_LINKED', false)
            && Configuration::updateValue('EVERSEO_CATEG_LINKED', false)
            && Configuration::updateValue('SEARCHED', 'Searched text')
            && Configuration::updateValue('LINKEDTO', 'Replace by')
            && Configuration::updateValue('EVERSEO_MANUFACTURER_REASSURANCE', false)
            && Configuration::updateValue('EVERSEO_SUPPLIER_REASSURANCE', false)
            && Configuration::updateValue('EVERSEO_RSNIPPETS', true)
            && Configuration::updateValue('EVERSEO_PRODUCT', true)
            && Configuration::updateValue('EVERSEO_CATEGORY', true)
            && Configuration::updateValue('EVERSEO_TAGS', true)
            && Configuration::updateValue('EVERSEO_PRIORITY', '1')
            && Configuration::updateValue('EVERSEO_ORDER_BY', '1')
            && Configuration::updateValue('EVERSEO_CUSTOM_404', true)
            && Configuration::updateValue('EVERSEO_404_SEARCH', true)
            && Configuration::updateValue('EVERSEO_REDIRECT', '302')
            && Configuration::updateValue('EVERSEO_NOT_FOUND', false)
            && Configuration::updateValue('EVERSEO_FORCE_PRODUCT_REDIRECT', false)
            && Configuration::updateValue('EVERSEO_QUALITY_LEVEL', '7')
            && Configuration::updateValue('EVERSEO_KNOWLEDGE', 'Organization')
            && Configuration::updateValue('EVERSEO_CANONICAL', false)
            && Configuration::updateValue('EVERSEO_HREF_LANG', false)
            && Configuration::updateValue('EVERSEO_ANALYTICS', '')
            && Configuration::updateValue('EVERSEO_SEARCHCONSOLE', '')
            && Configuration::updateValue('EVERSEO_GTAG', '')
            && Configuration::updateValue('EVERSEO_FBPIXEL', '')
            && Configuration::updateValue('EVERSEO_ADWORDS', '')
            && Configuration::updateValue('EVERSEO_ADWORDS_SENDTO', '')
            && Configuration::updateValue('EVERSEO_ADWORDS_CONTACT', '')
            && Configuration::updateValue('EVERSEO_ADWORDS_OPART', '')
            && Configuration::updateValue('EVERSEO_USE_TWITTER', false)
            && Configuration::updateValue('EVERSEO_USE_OPENGRAPH', false)
            && Configuration::updateValue('EVERSEO_TWITTER_NAME', '@te4mever')
            && Configuration::updateValue('EVERSEO_INDEX_CATEGORY', true)
            && Configuration::updateValue('EVERSEO_INDEX_PRODUCT', true)
            && Configuration::updateValue('EVERSEO_INDEX_CMS', true)
            && Configuration::updateValue('EVERSEO_INDEX_PAGE_META', true)
            && Configuration::updateValue('EVERSEO_INDEX_MANUFACTURER', true)
            && Configuration::updateValue('EVERSEO_INDEX_SUPPLIER', true)
            && Configuration::updateValue('EVERSEO_INDEX_ARGS', false)
            && Configuration::updateValue('EVERSEO_FOLLOW_CATEGORY', true)
            && Configuration::updateValue('EVERSEO_FOLLOW_PRODUCT', true)
            && Configuration::updateValue('EVERSEO_FOLLOW_CMS', true)
            && Configuration::updateValue('EVERSEO_FOLLOW_PAGE_META', true)
            && Configuration::updateValue('EVERSEO_FOLLOW_MANUFACTURER', true)
            && Configuration::updateValue('EVERSEO_FOLLOW_SUPPLIER', true)
            && Configuration::updateValue('EVERSEO_FOLLOW_ARGS', true)
            && Configuration::updateValue('EVERSEO_SITEMAP_PRODUCT', true)
            && Configuration::updateValue('EVERSEO_SITEMAP_IMAGE', true)
            && Configuration::updateValue('EVERSEO_SITEMAP_CATEGORY', true)
            && Configuration::updateValue('EVERSEO_SITEMAP_CMS', true)
            && Configuration::updateValue('EVERSEO_SITEMAP_PAGE_META', true)
            && Configuration::updateValue('EVERSEO_SITEMAP_MANUFACTURER', false)
            && Configuration::updateValue('EVERSEO_SITEMAP_SUPPLIER', false)
            && Configuration::updateValue('EVERSEO_SITEMAP_PRODUCT_FREQUENCY', 'weekly')
            && Configuration::updateValue('EVERSEO_SITEMAP_IMAGE_FREQUENCY', 'weekly')
            && Configuration::updateValue('EVERSEO_SITEMAP_CATEGORY_FREQUENCY', 'monthly')
            && Configuration::updateValue('EVERSEO_SITEMAP_CMS_FREQUENCY', 'monthly')
            && Configuration::updateValue('EVERSEO_SITEMAP_MANUFACTURER_FREQUENCY', 'yearly')
            && Configuration::updateValue('EVERSEO_SITEMAP_SUPPLIER_FREQUENCY', 'yearly')
            && Configuration::updateValue('EVERSEO_SITEMAP_PAGE_META_FREQUENCY', 'monthly')
            && Configuration::updateValue('EVERSEO_SITEMAP_PRODUCT_PRIORITY', '1')
            && Configuration::updateValue('EVERSEO_SITEMAP_IMAGE_PRIORITY', '0.8')
            && Configuration::updateValue('EVERSEO_SITEMAP_CATEGORY_PRIORITY', '0.8')
            && Configuration::updateValue('EVERSEO_SITEMAP_CMS_PRIORITY', '0.5')
            && Configuration::updateValue('EVERSEO_SITEMAP_MANUFACTURER_PRIORITY', '0.3')
            && Configuration::updateValue('EVERSEO_SITEMAP_SUPPLIER_PRIORITY', '0.3')
            && Configuration::updateValue('EVERSEO_SITEMAP_PAGE_META_PRIORITY', '1')
            && Configuration::updateValue('EVERSEO_SITEMAP_QTY_ELEMENTS', 5000)
            && Configuration::updateValue('EVERSEO_HEADER_TAGS', '')
            && Configuration::updateValue('EVERSEO_REWRITE', true)
            && Configuration::updateValue('EVERSEO_DELETE_CATEGORY', false)
            && Configuration::updateValue('EVERSEO_DELETE_PRODUCT', false)
            && Configuration::updateValue('EVERSEO_DELETE_CMS', false)
            && Configuration::updateValue('EVERSEO_DELETE_PAGE_META', false)
            && Configuration::updateValue('EVERSEO_DELETE_MANUFACTURER', false)
            && Configuration::updateValue('EVERSEO_DELETE_SUPPLIER', false)
            && Configuration::updateValue('EVERSEO_DELETE_INFO', false)
            && Configuration::updateValue('EVERSEO_DELETE_GROUP', false)
            && Configuration::updateValue('EVERSEO_DELETE_GENDER', false)
            && Configuration::updateValue('EVERSEO_DELETE_FEATURE', false)
            && Configuration::updateValue('EVERSEO_DELETE_FEATURE_VALUE', false)
            && Configuration::updateValue('EVERSEO_DELETE_CUST_FIELD', false)
            && Configuration::updateValue('EVERSEO_DELETE_CONTACT', false)
            && Configuration::updateValue('EVERSEO_DELETE_COUNTRY', false)
            && Configuration::updateValue('EVERSEO_DELETE_CART_RULE', false)
            && Configuration::updateValue('EVERSEO_DELETE_CARRIER', false)
            && Configuration::updateValue('EVERSEO_DELETE_ATTACHMENT', false)
            && Configuration::updateValue('EVERSEO_DELETE_ATTRIBUTE', false)
            && Configuration::updateValue('EVERSEO_DELETE_ATTRIBUTE_GROUP', false)
            && Configuration::updateValue('EVERSEO_ROBOTS_TXT_REWRITE', false)
            && Configuration::updateValue('EVERSEO_SITEMAP_QTY_ELEMENTS', 5000)
            && Configuration::updateValue(
                'EVERSEO_SITEMAP_LANGS',
                '["'.Configuration::get('PS_LANG_DEFAULT').'"]'
            )
            && Configuration::updateValue('EVERSEO_CACHE', false)
            && Configuration::updateValue('EVERSEO_COMPRESS_HTML', false)
            && Configuration::updateValue('EVERSEO_REMOVE_COMMENTS', false)
            && Configuration::updateValue('EVERSEO_ADD_MISSING_LABELS', false)
            && Configuration::updateValue('EVERSEO_GOOGLE_FONT', false)
            && Configuration::updateValue('EVERSEO_BOTTOM_SCRIPTS', false)
            && Configuration::updateValue('EVERSEO_DEFER', false)
            && Configuration::updateValue('EVERSEO_CACHE_LIFE', 20)
            && Configuration::updateValue('EVERSEO_LAZY_LOAD', false)
            && Configuration::updateValue('EVERSEO_MINIFY_PRODUCT', false)
            && Configuration::updateValue('EVERSEO_MINIFY_CATEGORY', false)
            && Configuration::updateValue('EVERSEO_MINIFY_HOME', false)
            && Configuration::updateValue('EVERSEO_MINIFY_CMS', false)
            && Configuration::updateValue('EVERSEO_MINIFY_OTHERS', false)
            && Configuration::updateValue('EVERSEO_CACHE_PRODUCT', false)
            && Configuration::updateValue('EVERSEO_CACHE_CATEGORY', false)
            && Configuration::updateValue('EVERSEO_CACHE_CMS', false)
            && Configuration::updateValue('EVERSEO_CACHE_MANUFACTURER', false)
            && Configuration::updateValue('EVERSEO_CACHE_SUPPLIER', false)
            && Configuration::updateValue('EVERSEO_CACHE_HOME', false)
            && Configuration::updateValue('EVERSEO_CACHE_OTHERS', false)
            && Configuration::updateValue('EVERSEO_EXTERNAL_NOFOLLOW', false)
            && Configuration::updateValue('EVERSEO_REMOVE_INLINE', false)
            && Configuration::updateValue('EVERSEO_REMOVE_EMPTY', false)
            && Configuration::updateValue('EVERSEO_ADD_ALT', false)
            && Configuration::updateValue('EVERSEO_INDEXNOW_LIMIT', 150);
    }

    public static function truncateStatsData()
    {
        Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'guest');
        Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'connections');
        Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'connections_source');
        Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'connections_page');
        Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'pagenotfound');
        Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'page_viewed');
        Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'referrer_shop');
    }

    public static function truncateSeo404()
    {
        Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'ever_seo_redirect');
    }
}
