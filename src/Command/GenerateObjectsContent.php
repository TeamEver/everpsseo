<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
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

class GenerateObjectsContent extends Command
{
    const SUCCESS = 0;
    const FAILURE = 1;
    const INVALID = 2;
    const ABORTED = 3;

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
        $this->setName('everpsseo:seo:content');
        $this->setDescription('Generate objects content (description, etc) for each lang');
        $this->addArgument('action', InputArgument::OPTIONAL, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)));
        $this->addArgument('idshop id', InputArgument::OPTIONAL, 'Shop ID');
        $this->logFile = dirname(__FILE__) . '/../../output/logs/log-seo-content-generation-'.date('Y-m-d').'.log';
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
                (int) $idShop
            );
            if (!\Validate::isLoadedObject($shop)) {
                $output->writeln('<comment>Shop not found</comment>');
                return self::ABORTED;
            }
        } else {
            $shop = $context->shop;
            if (!\Validate::isLoadedObject($shop)) {
                $shop = new \Shop((int) \Configuration::get('PS_SHOP_DEFAULT'));
            }
        }
        //Important to setContext
        \Shop::setContext($shop::CONTEXT_SHOP, $shop->id);
        $context->shop = $shop;
        $context->cookie->id_shop = $shop->id;
        
        $output->writeln(sprintf(
            '<info>Start content spinning : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));

        $output->writeln(sprintf(
            '<info>Start products content spinning : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_PGENERATOR_LANGS'
        );
        $seoArray = \EverPsSeoProduct::getAllSeoProductsIds(
            (int) $shop->id,
            $allowedLangs
        );

        foreach ($seoArray as $seo) {
            $this->autoSetContentShortDesc(
                'id_seo_product',
                (int) $seo['id_seo_product'],
                (int) $shop->id,
                (int) $seo['id_seo_lang']
            );
            $output->writeln(sprintf(
                '<info>Short description for id product ' . (int) $seo['id_seo_product'].' has been set</info>'
            ));
            $this->autoSetContentDesc(
                'id_seo_product',
                (int) $seo['id_seo_product'],
                (int) $shop->id,
                (int) $seo['id_seo_lang']
            );
            $output->writeln(sprintf(
                '<info>Description for id product ' . (int) $seo['id_seo_product'].' has been set</info>'
            ));
        }
        $output->writeln(sprintf(
            '<info>End products content spinning : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));

        $output->writeln(sprintf(
            '<info>Start categories content spinning : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        $seoArray = \EverPsSeoCategory::getAllSeoCategoriesIds(
            (int) $shop->id
        );
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_CATEGORY_LANGS'
        );
        foreach ($seoArray as $seo) {
            if (in_array((int) $seo['id_seo_lang'], $allowedLangs)) {
                $this->autoSetContentDesc(
                    'id_seo_category',
                    (int) $seo['id_seo_category'],
                    (int) $shop->id,
                    (int) $seo['id_seo_lang']
                );
                $output->writeln(sprintf(
                    '<info>Description for id category ' . (int) $seo['id_seo_category'].' has been set</info>'
                ));
            }
        }
        $output->writeln(sprintf(
            '<info>End categories content spinning : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));

        $output->writeln(sprintf(
            '<info>Start manufacturers content spinning : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        $seoArray = \EverPsSeoManufacturer::getAllSeoManufacturersIds(
            (int) $shop->id
        );
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_MANUFACTURER_LANGS'
        );
        foreach ($seoArray as $seo) {
            if (in_array((int) $seo['id_seo_lang'], $allowedLangs)) {
                $this->autoSetContentDesc(
                    'id_seo_manufacturer',
                    (int) $seo['id_seo_manufacturer'],
                    (int) $shop->id,
                    (int) $seo['id_seo_lang']
                );
            }
        }
        $output->writeln(sprintf(
            '<info>End manufacturers content spinning : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));

        $output->writeln(sprintf(
            '<info>Start suppliers content spinning : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        $seoArray = \EverPsSeoSupplier::getAllSeoSuppliersIds(
            (int) $shop->id
        );
        $allowedLangs = $this->getAllowedShortcodesLangs(
            'EVERSEO_AUTO_SUPPLIER_LANGS'
        );
        foreach ($seoArray as $seo) {
            if (in_array((int) $seo['id_seo_lang'], $allowedLangs)) {
                $this->autoSetContentDesc(
                    'id_seo_supplier',
                    (int) $seo['id_seo_supplier'],
                    (int) $shop->id,
                    (int) $seo['id_seo_lang']
                );
            }
        }
        $output->writeln(sprintf(
            '<info>End suppliers content spinning : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        
        $output->writeln(sprintf(
            '<info>Content generation ended, start clearing cache</info>'
        ));
        \Tools::clearAllCache();
        $output->writeln(sprintf(
            '<info>Cache emptied</info>'
        ));
        $output->writeln(
            $this->getRandomFunnyComment($output)
        );
        $output->writeln(sprintf(
            '<comment>See logs at '.$this->logFile.'</comment>'
        ));
        return self::SUCCESS;
    }

    protected function getAllowedShortcodesLangs($getter)
    {
        $allowedLangs = json_decode(
            \Configuration::get(
                (string) $getter
            )
        );
        if (!$allowedLangs) {
            $allowedLangs = [(int) \Configuration::get('PS_LANG_DEFAULT')];
        }
        return $allowedLangs;
    }

    protected function autoSetContentShortDesc($object, $id_element, $id_shop, $id_lang)
    {
        switch ($object) {
            case 'id_seo_product':
                $description_short = \EverPsSeoProduct::changeProductShortDescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (empty($description_short)) {
                    return;
                }
                $product = new Product(
                    (int) $id_element,
                    false,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (!in_array($product->id_category_default, $this->getAllowedGeneratorCategories(true))) {
                    return;
                }
                if ((bool) Configuration::get('EVERSEO_DELETE_PRODUCT_CONTENT')) {
                    $product->description_short = $description_short;
                } else {
                    $product->description_short .= $description_short;
                }

                $sql_desc_short = 'UPDATE `' . _DB_PREFIX_ . 'product_lang`
                    SET description_short = "'.pSQL($product->description_short, true).'"
                    WHERE id_lang = ' . (int) $id_lang.'
                    AND id_shop = ' . (int) $id_shop.'
                    AND id_product = ' . (int) $id_element;

                if (\Db::getInstance()->execute($sql_desc_short)) {
                    return true;
                }
                break;
        }
    }

    protected function autoSetContentDesc($object, $id_element, $id_shop, $id_lang)
    {
        switch ($object) {
            case 'id_seo_product':
                if ((bool) \Configuration::get('EVERSEO_BOTTOM_PRODUCT_CONTENT') === false) {
                    $description = \EverPsSeoProduct::changeProductDescShortcodes(
                        (int) $id_element,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                } else {
                    $description = \EverPsSeoProduct::changeProductBottomShortcodes(
                        (int) $id_element,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                }
                if (empty($description)) {
                    return;
                }
                $product = new \Product(
                    (int) $id_element,
                    false,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (!in_array($product->id_category_default, $this->getAllowedGeneratorCategories(true))) {
                    return;
                }
                if ((bool) \Configuration::get('EVERSEO_BOTTOM_PRODUCT_CONTENT') === false) {
                    if ((bool) \Configuration::get('EVERSEO_DELETE_PRODUCT_CONTENT') === true) {
                        $product->description = $description;
                    } else {
                        $product->description .= $description;
                    }
                    $meta_title = \Tools::substr($meta_title, 0, 128);

                    $sql_desc = 'UPDATE `' . _DB_PREFIX_ . 'product_lang`
                        SET description = "'.pSQL($product->description, true).'"
                        WHERE id_lang = ' . (int) $id_lang.'
                        AND id_shop = ' . (int) $id_shop.'
                        AND id_product = ' . (int) $id_element;

                    if (\Db::getInstance()->execute($sql_desc)) {
                        return true;
                    }
                } else {
                    $obj = \EverPsSeoProduct::getSeoProduct(
                        (int) $id_element,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                    if ((bool) \Configuration::get('EVERSEO_DELETE_PRODUCT_CONTENT') === false) {
                        $obj->bottom_content = $obj->bottom_content.' '.$description;
                    } else {
                        $obj->bottom_content = $description;
                    }
                    $sql_ever_desc = 'UPDATE `' . _DB_PREFIX_ . 'ever_seo_product`
                        SET bottom_content = "'.pSQL($obj->bottom_content, true).'"
                        WHERE id_seo_lang = ' . (int) $id_lang.'
                        AND id_shop = ' . (int) $id_shop.'
                        AND id_seo_product = ' . (int) $id_element;

                    if (\Db::getInstance()->execute($sql_ever_desc)) {
                        return true;
                    }
                    // return $obj->save();
                }
                break;

            case 'id_seo_category':
                $description = \EverPsSeoCategory::changeCategoryDescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (empty($description)) {
                    return;
                }
                $category = new \Category(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (!in_array($category->id, $this->getAllowedGeneratorCategories())) {
                    return;
                }
                if ((bool) \Configuration::get('EVERSEO_BOTTOM_CATEGORY_CONTENT') === false) {
                    if ((bool) \Configuration::get('EVERSEO_DELETE_CATEGORY_CONTENT')) {
                        $category->description = $description;
                    } else {
                        $category->description = $category->description.' '.$description;
                    }
                    if (!$category->isParentCategoryAvailable()) {
                        $category->id_parent = 2;
                    }
                    if ($category->save()) {
                        return true;
                    }
                } else {
                    $obj = \EverPsSeoCategory::getSeoCategory(
                        (int) $id_element,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                    if ((bool) \Configuration::get('EVERSEO_DELETE_CATEGORY_CONTENT') === false) {
                        $obj->bottom_content = $obj->bottom_content.' '.$description;
                    } else {
                        $obj->bottom_content = $description;
                    }
                    return $obj->save();
                }
                break;

            case 'id_seo_manufacturer':
                $description = \EverPsSeoManufacturer::changeManufacturerDescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (empty($description)) {
                    return;
                }
                $manufacturer = new \Manufacturer(
                    (int) $id_element,
                    (int) $id_lang
                );
                if ((bool) \Configuration::get('EVERSEO_BOTTOM_MANUFACTURER_CONTENT') === false) {
                    $manufacturer->description = Tools::substr(pSQL($description), 0, 250);
                    if ($manufacturer->save()) {
                        return true;
                    }
                } else {
                    $obj = \EverPsSeoManufacturer::getSeoManufacturer(
                        (int) $id_element,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                    if ((bool) \Configuration::get('EVERSEO_DELETE_MANUFACTURER_CONTENT') === false) {
                        $obj->bottom_content = $obj->bottom_content.' '.$description;
                    } else {
                        $obj->bottom_content = $description;
                    }
                    return $obj->save();
                }
                break;

            case 'id_seo_supplier':
                $description = \EverPsSeoSupplier::changeSupplierDescShortcodes(
                    (int) $id_element,
                    (int) $id_lang,
                    (int) $id_shop
                );
                if (empty($description)) {
                    return;
                }
                $supplier = new \Supplier(
                    (int) $id_element,
                    (int) $id_lang
                );
                if ((bool) \Configuration::get('EVERSEO_BOTTOM_SUPPLIER_CONTENT') === false) {
                    $supplier->description = Tools::substr(pSQL($description), 0, 250);
                    if ($supplier->save()) {
                        return true;
                    }
                } else {
                    $obj = \EverPsSeoSupplier::getSeoSupplier(
                        (int) $id_element,
                        (int) $id_lang,
                        (int) $id_shop
                    );
                    if ((bool) \Configuration::get('EVERSEO_DELETE_SUPPLIER_CONTENT') === false) {
                        $obj->bottom_content = $obj->bottom_content.' '.$description;
                    } else {
                        $obj->bottom_content = $description;
                    }
                    return $obj->save();
                }
                break;
        }
    }

    protected function getAllowedGeneratorCategories($isProduct = false)
    {
        if ((bool) $isProduct === true) {
            $categories = json_decode(
                \Configuration::get(
                    'EVERSEO_PGENERATOR_CATEGORIES'
                )
            );
        } else {
            $categories = json_decode(
                \Configuration::get(
                    'EVERSEO_CGENERATOR_CATEGORIES'
                )
            );
        }
        if (!is_array($categories)) {
            $categories = [$categories];
        }
        return $categories;
    }

    protected function logCommand($msg)
    {
        $log  = 'Msg: ' . $msg . PHP_EOL .
                date('j.n.Y') . PHP_EOL .
                '-------------------------' . PHP_EOL;

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
