<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://www.team-ever.com
 */

namespace Everpsseo\Seo\Command;

use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class GenerateMetas extends Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;
    public const ABORTED = 3;

    private $allowedActions = [
        'idshop',
        'getrandomcomment'
    ];

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('everpsseo:seo:metas');
        $this->setDescription('Generate SEO title and meta description for each lang');
        $this->addArgument('action', InputArgument::OPTIONAL, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)));
        $this->addArgument('idshop id', InputArgument::OPTIONAL, 'Shop ID');
        $this->logFile = dirname(__FILE__) . '/../../output/logs/log-seo-meta-generation-'.date('Y-m-d').'.log';
        $this->module = \Module::getInstanceByName('everpsseo');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        $idShop = $input->getArgument('idshop id');
        if (!in_array($action, $this->allowedActions)) {
            $output->writeln('<comment>Unkown action</comment>');
            return self::ABORTED;
        }
        if ($action === 'getrandomcomment') {
            $output->writeln(
                $this->getRandomFunnyComment($output)
            );
            return self::SUCCESS;
        }
        $context = (new ContextAdapter())->getContext();
        $context->employee = new \Employee(1);
        if ($action === 'idshop') {
            $shop = new \Shop(
                (int)$idShop
            );
            if (!\Validate::isLoadedObject($shop)) {
                $output->writeln('<comment>Shop not found</comment>');
                return self::ABORTED;
            }
        } else {
            $shop = $context->shop;
            if (!\Validate::isLoadedObject($shop)) {
                $shop = new \Shop((int)\Configuration::get('PS_SHOP_DEFAULT'));
            }
        }
        //Important to setContext
        \Shop::setContext($shop::CONTEXT_SHOP, $shop->id);
        $context->shop = $shop;
        $context->cookie->id_shop = $shop->id;
        $rewriteLinks = (bool)\Configuration::get('EVERSEO_REWRITE_LINKS');
        $output->writeln(sprintf(
            '<info>Seo metas generation start : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));

        $output->writeln(sprintf(
            '<info>Start products metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_PRODUCT_LANGS'
        );
        $seoArray = \EverPsSeoProduct::getAllSeoProductsIds(
            (int)$shop->id,
            $allowedLangs
        );
        foreach ($seoArray as $seo) {
            if (in_array((int)$seo['id_seo_lang'], $allowedLangs)) {
                $this->autoSetTitle(
                    'id_seo_product',
                    (int)$seo['id_seo_product'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>SEO title for id product '.(int)$seo['id_seo_product'].' has been set</info>'
                ));
                $this->autoSetDescription(
                    'id_seo_product',
                    (int)$seo['id_seo_product'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                if ((bool)$rewriteLinks === true) {
                    $this->autoSetLinkRewrite(
                        'id_seo_product',
                        (int)$seo['id_seo_product'],
                        (int)$shop->id,
                        (int)$seo['id_seo_lang']
                    );
                }
                $output->writeln(sprintf(
                    '<info>SEO meta description for id product '.(int)$seo['id_seo_product'].' has been set</info>'
                ));
            }
        }
        $output->writeln(sprintf(
            '<info>End products metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));

        $output->writeln(sprintf(
            '<info>Start pages metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        $seoArray = \EverPsSeoPageMeta::getAllSeoPagemetasIds(
            (int)$shop->id
        );
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_PAGEMETA_LANGS'
        );
        foreach ($seoArray as $seo) {
            if (in_array((int)$seo['id_seo_lang'], $allowedLangs)) {
                $this->autoSetTitle(
                    'id_seo_pagemeta',
                    (int)$seo['id_seo_pagemeta'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>SEO title for id page '.(int)$seo['id_seo_pagemeta'].' has been set</info>'
                ));
                $this->autoSetDescription(
                    'id_seo_pagemeta',
                    (int)$seo['id_seo_pagemeta'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>SEO meta description for id page '.(int)$seo['id_seo_pagemeta'].' has been set</info>'
                ));
            }
        }
        $output->writeln(sprintf(
            '<info>End pages metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));

        $output->writeln(sprintf(
            '<info>Start CMS metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        $seoArray = \EverPsSeoCms::getAllSeoCmsIds(
            (int)$shop->id
        );
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_CMS_LANGS'
        );
        foreach ($seoArray as $seo) {
            if (in_array((int)$seo['id_seo_lang'], $allowedLangs)) {
                $this->autoSetTitle(
                    'id_seo_cms',
                    (int)$seo['id_seo_cms'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>SEO title for id CMS '.(int)$seo['id_seo_cms'].' has been set</info>'
                ));
                $this->autoSetDescription(
                    'id_seo_cms',
                    (int)$seo['id_seo_cms'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>SEO meta description for id CMS '.(int)$seo['id_seo_cms'].' has been set</info>'
                ));
            }
        }
        $output->writeln(sprintf(
            '<info>End CMS metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));

        $output->writeln(sprintf(
            '<info>Start suppliers metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        $seoArray = \EverPsSeoCms::getAllSeoCmsIds(
            (int)$shop->id
        );
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_SUPPLIER_LANGS'
        );
        foreach ($seoArray as $seo) {
            if (in_array((int)$seo['id_seo_lang'], $allowedLangs)) {
                $this->autoSetTitle(
                    'id_seo_supplier',
                    (int)$seo['id_seo_supplier'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>SEO title for id supplier '.(int)$seo['id_seo_supplier'].' has been set</info>'
                ));
                $this->autoSetDescription(
                    'id_seo_supplier',
                    (int)$seo['id_seo_supplier'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>SEO meta description for id supplier '.(int)$seo['id_seo_supplier'].' has been set</info>'
                ));
            }
        }
        $output->writeln(sprintf(
            '<info>End suppliers metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));

        $output->writeln(sprintf(
            '<info>Start manufacturers metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        $seoArray = \EverPsSeoCms::getAllSeoCmsIds(
            (int)$shop->id
        );
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_MANUFACTURER_LANGS'
        );
        foreach ($seoArray as $seo) {
            if (in_array((int)$seo['id_seo_lang'], $allowedLangs)) {
                $this->autoSetTitle(
                    'id_seo_manufacturer',
                    (int)$seo['id_seo_manufacturer'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>SEO title for id manufacturer '.(int)$seo['id_seo_manufacturer'].' has been set</info>'
                ));
                $this->autoSetDescription(
                    'id_seo_manufacturer',
                    (int)$seo['id_seo_manufacturer'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>SEO meta description for id manufacturer '.(int)$seo['id_seo_manufacturer'].' has been set</info>'
                ));
            }
        }
        $output->writeln(sprintf(
            '<info>End manufacturers metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));

        $output->writeln(sprintf(
            '<info>Start categories metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        $seoArray = \EverPsSeoCms::getAllSeoCmsIds(
            (int)$shop->id
        );
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_CATEGORY_LANGS'
        );
        foreach ($seoArray as $seo) {
            if (in_array((int)$seo['id_seo_lang'], $allowedLangs)) {
                $this->autoSetTitle(
                    'id_seo_category',
                    (int)$seo['id_seo_category'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>SEO title for id category '.(int)$seo['id_seo_category'].' has been set</info>'
                ));
                $this->autoSetDescription(
                    'id_seo_category',
                    (int)$seo['id_seo_category'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>SEO meta description for id category '.(int)$seo['id_seo_category'].' has been set</info>'
                ));
            }
        }
        $output->writeln(sprintf(
            '<info>End categories metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));

        $output->writeln(sprintf(
            '<info>Start images alt : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        $seoArray = \EverPsSeoCms::getAllSeoCmsIds(
            (int)$shop->id
        );
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_IMAGE_LANGS'
        );
        foreach ($seoArray as $seo) {
            if (in_array((int)$seo['id_seo_lang'], $allowedLangs)) {
                $this->autoSetAlt(
                    (int)$seo['id_seo_img'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $this->autoSetAltSeoImage(
                    (int)$seo['id_ever_seo_image'],
                    (int)$seo['id_seo_img'],
                    (int)$shop->id,
                    (int)$seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>Alt/legend for id image '.(int)$seo['id_seo_img'].' has been set</info>'
                ));
            }
        }
        $output->writeln(sprintf(
            '<info>End categories metas : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));


        \Tools::clearAllCache();
        $output->writeln(sprintf(
            '<info>Cache emptied</info>'
        ));
        $output->writeln(
            $this->getRandomFunnyComment($output)
        );
        return self::SUCCESS;
    }

    protected function autoSetTitle($object, $id_element, $id_shop, $id_lang)
    {
        switch ($object) {
            case 'id_seo_product':
                $meta_title = \EverPsSeoProduct::changeProductTitleShortcodes(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $meta_title = \Tools::substr($meta_title, 0, 128);

                $sql = 'UPDATE `'._DB_PREFIX_.'product_lang`
                SET meta_title = "'.\Db::getInstance()->escape($meta_title).'"
                WHERE id_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_product = '.(int)$id_element;

                $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_product`
                SET meta_title = "'.\Db::getInstance()->escape($meta_title).'"
                WHERE id_seo_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_seo_product = '.(int)$id_element;
                if (!\Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return \Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_category':
                $meta_title = \EverPsSeoCategory::changeCategoryTitleShortcodes(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $meta_title = \Tools::substr($meta_title, 0, 128);

                $sql = 'UPDATE `'._DB_PREFIX_.'category_lang`
                SET meta_title = "'.\Db::getInstance()->escape($meta_title).'"
                WHERE id_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_category = '.(int)$id_element;

                $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_category`
                SET meta_title = "'.\Db::getInstance()->escape($meta_title).'"
                WHERE id_seo_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_seo_category = '.(int)$id_element;
                if (!\Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return \Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_cms':
                $meta_title = \EverPsSeoCms::changeCmsTitleShortcodes(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $meta_title = \Tools::substr(\Db::getInstance()->escape($meta_title), 0, 128);

                $sql = 'UPDATE `'._DB_PREFIX_.'cms_lang`
                SET meta_title = "'.\Db::getInstance()->escape($meta_title).'"
                WHERE id_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_cms = '.(int)$id_element;

                $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_cms`
                SET meta_title = "'.\Db::getInstance()->escape($meta_title).'"
                WHERE id_seo_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_seo_cms = '.(int)$id_element;
                if (!\Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return \Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_manufacturer':
                $meta_title = \EverPsSeoManufacturer::changeManufacturerTitleShortcodes(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $meta_title = \Tools::substr(\Db::getInstance()->escape($meta_title), 0, 128);

                $sql = 'UPDATE `'._DB_PREFIX_.'manufacturer_lang`
                SET meta_title = "'.\Db::getInstance()->escape($meta_title).'"
                WHERE id_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_manufacturer = '.(int)$id_element;

                $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_manufacturer`
                SET meta_title = "'.\Db::getInstance()->escape($meta_title).'"
                WHERE id_seo_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_seo_manufacturer = '.(int)$id_element;
                if (!\Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return \Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_supplier':
                $meta_title = \EverPsSeoSupplier::changeSupplierTitleShortcodes(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $meta_title = \Tools::substr($meta_title, 0, 128);

                $sql = 'UPDATE `'._DB_PREFIX_.'supplier_lang`
                SET meta_title = "'.\Db::getInstance()->escape($meta_title).'"
                WHERE id_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_supplier = '.(int)$id_element;

                $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_supplier`
                SET meta_title = "'.\Db::getInstance()->escape($meta_title).'"
                WHERE id_seo_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_seo_supplier = '.(int)$id_element;
                if (!\Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return \Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_pagemeta':
                $meta_title = \EverPsSeoPageMeta::changePagemetaTitleShortcodes(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $pageMeta = new \Meta(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $pageMeta->title = \Tools::substr(\Db::getInstance()->escape($meta_title), 0, 128);
                // TODO : use SQL query
                // if ($pageMeta->save()) {
                //     return true;
                // }
                break;
        }
    }

    protected function autoSetDescription($object, $id_element, $id_shop, $id_lang)
    {
        switch ($object) {
            case 'id_seo_product':
                $meta_description = \EverPsSeoProduct::changeProductMetadescShortcodes(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $meta_description = \Tools::substr($meta_description, 0, 250);

                $sql = 'UPDATE `'._DB_PREFIX_.'product_lang`
                SET meta_description = "'.\Db::getInstance()->escape($meta_description).'"
                WHERE id_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_product = '.(int)$id_element;

                $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_product`
                SET meta_description = "'.\Db::getInstance()->escape($meta_description).'"
                WHERE id_seo_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_seo_product = '.(int)$id_element;
                if (!\Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return \Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_category':
                $meta_description = \EverPsSeoCategory::changeCategoryMetadescShortcodes(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $meta_description = \Tools::substr($meta_description, 0, 250);

                $sql = 'UPDATE `'._DB_PREFIX_.'category_lang`
                SET meta_description = "'.\Db::getInstance()->escape($meta_description).'"
                WHERE id_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_category = '.(int)$id_element;

                $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_category`
                SET meta_description = "'.\Db::getInstance()->escape($meta_description).'"
                WHERE id_seo_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_seo_category = '.(int)$id_element;
                if (!\Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return \Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_cms':
                $meta_description = \EverPsSeoCms::changeCmsMetadescShortcodes(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $meta_description = \Tools::substr($meta_description, 0, 250);

                $sql = 'UPDATE `'._DB_PREFIX_.'cms_lang`
                SET meta_description = "'.\Db::getInstance()->escape($meta_description).'"
                WHERE id_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_cms = '.(int)$id_element;

                $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_cms`
                SET meta_description = "'.\Db::getInstance()->escape($meta_description).'"
                WHERE id_seo_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_seo_cms = '.(int)$id_element;
                if (!\Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return \Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_manufacturer':
                $meta_description = \EverPsSeoManufacturer::changeManufacturerMetadescShortcodes(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $meta_description = \Tools::substr($meta_description, 0, 250);

                $sql = 'UPDATE `'._DB_PREFIX_.'manufacturer_lang`
                SET meta_description = "'.\Db::getInstance()->escape($meta_description).'"
                WHERE id_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_manufacturer = '.(int)$id_element;

                $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_manufacturer`
                SET meta_description = "'.\Db::getInstance()->escape($meta_description).'"
                WHERE id_seo_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_seo_manufacturer = '.(int)$id_element;
                if (!\Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return \Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_supplier':
                $meta_description = \EverPsSeoSupplier::changeSupplierMetadescShortcodes(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $meta_description = \Tools::substr(\Db::getInstance()->escape($meta_description), 0, 250);

                $sql = 'UPDATE `'._DB_PREFIX_.'supplier_lang`
                SET meta_description = "'.\Db::getInstance()->escape($meta_description).'"
                WHERE id_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_supplier = '.(int)$id_element;

                $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_supplier`
                SET meta_description = "'.\Db::getInstance()->escape($meta_description).'"
                WHERE id_seo_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_seo_supplier = '.(int)$id_element;
                if (!\Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return \Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_pagemeta':
                $meta_description = \EverPsSeoPageMeta::changePagemetaMetadescShortcodes(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $pageMeta = new Meta(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $pageMeta->meta_description = \Tools::substr(\Db::getInstance()->escape($meta_description), 0, 250);
                // TODO : use SQL query
                // if ($pageMeta->save()) {
                //     return true;
                // }
                break;
        }
    }

    protected function autoSetLinkRewrite($object, $id_element, $id_shop, $id_lang)
    {
        switch ($object) {
            case 'id_seo_product':
                $product = new \Product(
                    (int)$id_element,
                    false,
                    (int)$id_shop,
                    (int)$id_lang
                );
                $linkRewrite = \Tools::link_rewrite($product->name);

                $sql = 'UPDATE `'._DB_PREFIX_.'product_lang`
                SET link_rewrite = "'.\Db::getInstance()->escape($linkRewrite).'"
                WHERE id_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_product = '.(int)$id_element;

                $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_product`
                SET link_rewrite = "'.\Db::getInstance()->escape($linkRewrite).'"
                WHERE id_seo_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_seo_product = '.(int)$id_element;
                if (!\Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return \Db::getInstance()->execute($sql2);
                }
                break;

            case 'id_seo_category':
                $category = new \Category(
                    (int)$id_element,
                    (int)$id_lang,
                    (int)$id_shop
                );
                $linkRewrite = \Tools::link_rewrite($category->name);

                $sql = 'UPDATE `'._DB_PREFIX_.'category_lang`
                SET link_rewrite = "'.\Db::getInstance()->escape($linkRewrite).'"
                WHERE id_lang = '.(int)$id_lang.'
                AND id_category = '.(int)$id_element;

                $sql2 = 'UPDATE `'._DB_PREFIX_.'ever_seo_category`
                SET link_rewrite = "'.\Db::getInstance()->escape($linkRewrite).'"
                WHERE id_seo_lang = '.(int)$id_lang.'
                AND id_shop = '.(int)$id_shop.'
                AND id_seo_category = '.(int)$id_element;
                if (!\Db::getInstance()->execute($sql)) {
                    $this->errors[] = $this->l('An error has occurred: Can\'t update the current object');
                } else {
                    return \Db::getInstance()->execute($sql2);
                }
                break;
        }
    }

    private function autoSetAlt($id_element, $id_shop, $id_lang)
    {
        $alt = \EverPsSeoImage::changeImageAltShortcodes(
            $id_element,
            $id_lang,
            $id_shop
        );
        $image = new \Image(
            (int)$id_element,
            (int)$id_lang,
            (int)$id_shop
        );
        $legend = \Tools::substr(
            strip_tags($alt),
            0,
            128
        );
        if (\Validate::isGenericName($legend)) {
            $image->legend = $legend;
            // Hooked
            if ($image->id_product && $image->save()) {
                return true;
            }
        }
    }

    private function autoSetAltSeoImage($id_ever_seo_image, $id_element, $id_shop, $id_lang)
    {
        $alt = \EverPsSeoImage::changeImageAltShortcodes(
            $id_element,
            $id_lang,
            $id_shop
        );
        $everImg = new \EverPsSeoImage(
            (int)$id_ever_seo_image
        );
        $legend = \Tools::substr(
            strip_tags($alt),
            0,
            128
        );
        if (\Validate::isGenericName($legend)) {
            $everImg->legend = $legend;
            // Hooked
            if ($everImg->id_seo_product && $everImg->save()) {
                return true;
            }
        }
    }

    protected function getAllowedShortcodesLangs($getter)
    {
        $allowedLangs = json_decode(
            \Configuration::get(
                (string)$getter
            )
        );
        if (!$allowedLangs) {
            $allowedLangs = array((int)\Configuration::get('PS_LANG_DEFAULT'));
        }
        return $allowedLangs;
    }

    protected function logCommand($msg)
    {
        $log  = 'Msg: '.$msg.PHP_EOL.
                date('j.n.Y').PHP_EOL.
                "-------------------------".PHP_EOL;

        //Save string to log, use FILE_APPEND to append.
        file_put_contents(
            $this->logFile,
            $log,
            FILE_APPEND
        );
    }

    /**
     * Get funny random comment
     * Can be useful for setting comment style example
     * @see https://symfony.com/doc/current/console/coloring.html
    */
    protected function getRandomFunnyComment($output)
    {
        $outputStyle = new OutputFormatterStyle('green', 'white', ['bold', 'blink']);
        $output->getFormatter()->setStyle('styled', $outputStyle);
        $funnyComments = [];
        $funnyComments[] = "<styled>
            IMPORT ENDED, HAVE A BEER
                         .sssssssss.
                   .sssssssssssssssssss
                 sssssssssssssssssssssssss
                ssssssssssssssssssssssssssss
                 @@sssssssssssssssssssssss@ss
                 |s@@@@sssssssssssssss@@@@s|s
          _______|sssss@@@@@sssss@@@@@sssss|s
        /         sssssssss@sssss@sssssssss|s
       /  .------+.ssssssss@sssss@ssssssss.|
      /  /       |...sssssss@sss@sssssss...|
     |  |        |.......sss@sss@ssss......|
     |  |        |..........s@ss@sss.......|
     |  |        |...........@ss@..........|
      \  \       |............ss@..........|
       \  '------+...........ss@...........|
        \________ .........................|
                 |.........................|
                /...........................\
               |.............................|
                  |.......................|
                      |...............|
                </styled>";
        $funnyComments[] = "<styled>
            IMPORT ENDED, MEOW
              ^~^  ,
             ('Y') )
             /   \/
            (\|||/)
            </styled>";
        $funnyComments[] = "<styled>
            IMPORT ENDED, D'OH
            ...___.._____
            ....‘/,-Y”.............“~-.
            ..l.Y.......................^.
            ./\............................_\_
            i.................... ___/“....“\
            |.................../“....“\ .....o !
            l..................].......o !__../
            .\..._..._.........\..___./...... “~\
            ..X...\/...\.....................___./
            .(. \.___......_.....--~~“.......~`-.
            ....`.Z,--........./.................\
            .......\__....(......../..........______)
            ...........\.........l......../-----~~”/
            ............Y.......\................/
            ............|........“x______.^
            ............|.............\
            ............j...............Y
            </styled>";
        $funnyComments[] = '<styled>
            |￣￣￣￣￣￣￣￣￣ |
            |      IMPORT      |
            |      ENDED!      |
            |__________________|
            (\__/) ||
            (•ㅅ•) ||
            / 　 づ"
            </styled>';
        $funnyComments[] = "<styled>
            Import (•_•)
            has been ( •_•)>⌐■-■
            ended (⌐■_■)
            </styled>";
        $funnyComments[] = "<styled>
            ......_________________________
            ....../ `---___________--------    | ============= IMPORT-ENDED-BULLET !
            ...../_==o;;;;;;;;______________|
            .....), ---.(_(__) /
            .......// (..) ), /--
            ... //___//---
            .. //___//
            .//___//
            //___//
            </styled>";
        $funnyComments[] = "<styled>
               IMPORT ENDED
           ._________________.
           |.---------------.|
           ||               ||
           ||   -._ .-.     ||
           ||   -._| | |    ||
           ||   -._|'|'|    ||
           ||   -._|.-.|    ||
           ||_______________||
           /.-.-.-.-.-.-.-.-.\
          /.-.-.-.-.-.-.-.-.-.\
         /.-.-.-.-.-.-.-.-.-.-.\
        /______/__________\___o_\ 
        \_______________________/
         </styled>";
        $k = array_rand($funnyComments);
        return $funnyComments[$k];
    }
}
