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

$sql = [];

$sql[] =
    'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_lang` (
        id_seo_lang,
        id_shop,
        iso_code,
        language_code
    )

    SELECT l.id_lang,
        ls.id_shop,
        l.iso_code,
        l.language_code
    FROM `' . _DB_PREFIX_ . 'lang` l
    INNER JOIN `' . _DB_PREFIX_ . 'lang_shop` ls
        ON l.id_lang = ls.id_lang';

$sql[] =
    'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_product` (
        id_seo_product,
        id_shop,
        id_seo_lang,
        meta_title,
        meta_description,
        link_rewrite,
        canonical,
        indexable,
        follow,
        allowed_sitemap
    )
    SELECT
        pl.id_product,
        pl.id_shop,
        pl.id_lang,
        null,
        null,
        link_rewrite,
        link_rewrite,
        p.indexed,
        1,
        1
    FROM `' . _DB_PREFIX_ . 'product_lang` pl
    INNER JOIN `' . _DB_PREFIX_ . 'product` p
        ON (
            p.id_product = pl.id_product
        )';

// $sql[] = '
//     UPDATE ' . _DB_PREFIX_ . 'ever_seo_product
//         SET meta_description = (
//             SELECT meta_description
//             FROM ' . _DB_PREFIX_ . 'product_lang
//             WHERE ' . _DB_PREFIX_ . 'ever_seo_product.id_seo_product = ' . _DB_PREFIX_ . 'product_lang.id_product
//             AND ' . _DB_PREFIX_ . 'ever_seo_product.id_seo_lang = ' . _DB_PREFIX_ . 'product_lang.id_lang
//         );

//     UPDATE ' . _DB_PREFIX_ . 'ever_seo_product
//         SET meta_title = (
//             SELECT meta_title
//             FROM ' . _DB_PREFIX_ . 'product_lang
//             WHERE ' . _DB_PREFIX_ . 'ever_seo_product.id_seo_product = ' . _DB_PREFIX_ . 'product_lang.id_product
//             AND ' . _DB_PREFIX_ . 'ever_seo_product.id_seo_lang = ' . _DB_PREFIX_ . 'product_lang.id_lang
//         );
//     UPDATE ' . _DB_PREFIX_ . 'ever_seo_product
//         SET link_rewrite = (
//             SELECT link_rewrite
//             FROM ' . _DB_PREFIX_ . 'product_lang
//             WHERE ' . _DB_PREFIX_ . 'ever_seo_product.id_seo_product = ' . _DB_PREFIX_ . 'product_lang.id_product
//             AND ' . _DB_PREFIX_ . 'ever_seo_product.id_seo_lang = ' . _DB_PREFIX_ . 'product_lang.id_lang
//         );
//     UPDATE ' . _DB_PREFIX_ . 'ever_seo_product
//         SET canonical = (
//             SELECT link_rewrite
//             FROM ' . _DB_PREFIX_ . 'product_lang
//             WHERE ' . _DB_PREFIX_ . 'ever_seo_product.id_seo_product = ' . _DB_PREFIX_ . 'product_lang.id_product
//             AND ' . _DB_PREFIX_ . 'ever_seo_product.id_seo_lang = ' . _DB_PREFIX_ . 'product_lang.id_lang
//         );
//     UPDATE ' . _DB_PREFIX_ . 'ever_seo_product
//         SET follow = (
//             SELECT active
//             FROM ' . _DB_PREFIX_ . 'product_shop
//             WHERE ' . _DB_PREFIX_ . 'ever_seo_product.id_seo_product = ' . _DB_PREFIX_ . 'product_shop.id_product
//             AND ' . _DB_PREFIX_ . 'ever_seo_product.id_shop = ' . _DB_PREFIX_ . 'product_shop.id_shop
//         );
//     UPDATE ' . _DB_PREFIX_ . 'ever_seo_product
//         SET allowed_sitemap = (
//             SELECT active
//             FROM ' . _DB_PREFIX_ . 'product_shop
//             WHERE ' . _DB_PREFIX_ . 'ever_seo_product.id_seo_product = ' . _DB_PREFIX_ . 'product_shop.id_product
//             AND ' . _DB_PREFIX_ . 'ever_seo_product.id_shop = ' . _DB_PREFIX_ . 'product_shop.id_shop
//         );
//     ';

$sql[] =
    'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_image` (
        id_seo_img,
        id_seo_product,
        id_shop,
        id_seo_lang,
        alt,
        allowed_sitemap
    )

    SELECT
        il.id_image,
        i.id_product,
        i.id_shop,
        il.id_lang,
        il.legend,
        1
    FROM `' . _DB_PREFIX_ . 'image_lang` il
    INNER JOIN `' . _DB_PREFIX_ . 'image_shop` i
        ON i.id_image = il.id_image
    GROUP BY il.id_image, il.id_lang, i.id_shop';

$sql[] =
    'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_category` (
        id_seo_category,
        id_shop,
        id_seo_lang,
        meta_title,
        meta_description,
        link_rewrite,
        canonical,
        indexable,
        follow,
        allowed_sitemap
    )

    SELECT
        id_category,
        id_shop,
        id_lang,
        null,
        null,
        null,
        null,
        1,
        1,
        1
    FROM `' . _DB_PREFIX_ . 'category_lang`';

// $sql[] = '
//     UPDATE ' . _DB_PREFIX_ . 'ever_seo_category
//         SET meta_description = (
//             SELECT meta_description
//             FROM ' . _DB_PREFIX_ . 'category_lang
//             WHERE ' . _DB_PREFIX_ . 'ever_seo_category.id_seo_category = ' . _DB_PREFIX_ . 'category_lang.id_category
//             AND ' . _DB_PREFIX_ . 'ever_seo_category.id_seo_lang = ' . _DB_PREFIX_ . 'category_lang.id_lang
//         );

//     UPDATE ' . _DB_PREFIX_ . 'ever_seo_category
//         SET meta_title = (
//             SELECT meta_title
//             FROM ' . _DB_PREFIX_ . 'category_lang
//             WHERE ' . _DB_PREFIX_ . 'ever_seo_category.id_seo_category = ' . _DB_PREFIX_ . 'category_lang.id_category
//             AND ' . _DB_PREFIX_ . 'ever_seo_category.id_seo_lang = ' . _DB_PREFIX_ . 'category_lang.id_lang
//         );
//     UPDATE ' . _DB_PREFIX_ . 'ever_seo_category
//         SET link_rewrite = (
//             SELECT link_rewrite
//             FROM ' . _DB_PREFIX_ . 'category_lang
//             WHERE ' . _DB_PREFIX_ . 'ever_seo_category.id_seo_category = ' . _DB_PREFIX_ . 'category_lang.id_category
//             AND ' . _DB_PREFIX_ . 'ever_seo_category.id_seo_lang = ' . _DB_PREFIX_ . 'category_lang.id_lang
//         );
//     UPDATE ' . _DB_PREFIX_ . 'ever_seo_category
//         SET canonical = (
//             SELECT link_rewrite
//             FROM ' . _DB_PREFIX_ . 'category_lang
//             WHERE ' . _DB_PREFIX_ . 'ever_seo_category.id_seo_category = ' . _DB_PREFIX_ . 'category_lang.id_category
//             AND ' . _DB_PREFIX_ . 'ever_seo_category.id_seo_lang = ' . _DB_PREFIX_ . 'category_lang.id_lang
//         );
//     ';

$sql[] =
    'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_manufacturer` (
        id_seo_manufacturer,
        id_shop,
        id_seo_lang
    )
    SELECT
        ml.id_manufacturer,
        ms.id_shop,
        ml.id_lang
    FROM `' . _DB_PREFIX_ . 'manufacturer_lang` ml
    INNER JOIN `' . _DB_PREFIX_ . 'manufacturer_shop` ms
       ON (
            ms.id_manufacturer = ml.id_manufacturer
       )';

$sql[] =
    'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_supplier` (
        id_seo_supplier,
        id_shop,
        id_seo_lang
    )
    SELECT
        sl.id_supplier,
        ss.id_shop,
        sl.id_lang
    FROM `' . _DB_PREFIX_ . 'supplier_lang` sl
    INNER JOIN `' . _DB_PREFIX_ . 'supplier_shop` ss
        ON (
            ss.id_supplier = sl.id_supplier
        )';

$sql[] =
    'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_pagemeta` (
        id_seo_pagemeta,
        id_shop,
        id_seo_lang,
        meta_title,
        meta_description,
        indexable,
        follow,
        allowed_sitemap
    )
    SELECT
        id_meta,
        id_shop,
        id_lang,
        title,
        description,
        1,
        1,
        1
    FROM `' . _DB_PREFIX_ . 'meta_lang`';

$sql[] =
    'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_cms_category` (
        id_seo_cms_category,
        id_shop,
        id_seo_lang,
        meta_title,
        meta_description,
        indexable,
        follow,
        allowed_sitemap
    )

    SELECT
        id_cms_category,
        id_shop,
        id_lang,
        null,
        null,
        1,
        1,
        1
    FROM `' . _DB_PREFIX_ . 'cms_category_lang`';

$sql[] =
    'INSERT INTO `' . _DB_PREFIX_ . 'ever_seo_cms` (
        id_seo_cms,
        id_shop,
        id_seo_lang,
        meta_title,
        meta_description,
        indexable,
        follow,
        allowed_sitemap
    )
    SELECT
        cl.id_cms,
        cl.id_shop,
        cl.id_lang,
        null,
        null,
        c.indexation,
        1,
        1
    FROM `' . _DB_PREFIX_ . 'cms_lang` cl
    INNER JOIN `' . _DB_PREFIX_ . 'cms` c
        ON c.id_cms = cl.id_cms';

foreach ($sql as $s) {
    if (!Db::getInstance()->execute($s)) {
        return false;
    }
}
