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

class IndexNow extends Command
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
        $this->setName('everpsseo:seo:indexnow');
        $this->setDescription('IndexNow non indexed URL');
        $this->addArgument('action', InputArgument::OPTIONAL, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)));
        $this->addArgument('idshop id', InputArgument::OPTIONAL, 'Shop ID');
        $this->logFile = dirname(__FILE__) . '/../../output/logs/log-seo-index-now-'.date('Y-m-d').'.log';
        $this->module = \Module::getInstanceByName('everpsseo');;
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
        $output->writeln(sprintf(
            '<info>Start index now : datetime : '.date('Y-m-d H:i:s').'</info>'
        ));
        // Check limit per day
        $dayCounter = (int)\Configuration::get('EVERSEO_INDEXNOW_DAY');
        $dayOfWeek = date('N');
        if ($dayCounter <= 0) {
            \Configuration::updateValue('EVERSEO_INDEXNOW_DAY', (int)$dayOfWeek);
        }
        // Reset counter every day
        if ($dayCounter != $dayOfWeek) {
            \Configuration::updateValue('EVERSEO_INDEXNOW_DAY_COUNT', 0);
        }
        $dailyCount = (int)\Configuration::get('EVERSEO_INDEXNOW_DAY_COUNT');
        $maxLimit = (int)\Configuration::get('EVERSEO_INDEXNOW_LIMIT');
        if ($maxLimit <= 0) {
            $maxLimit = 200;
            \Configuration::updateValue('EVERSEO_INDEXNOW_LIMIT', $maxLimit);
        }
        $output->writeln(sprintf(
            '<info>Index now : daily count is : '.$dailyCount.'</info>'
        ));
        $output->writeln(sprintf(
            '<info>Index now : max limit is : '.$maxLimit.'</info>'
        ));
        $links = $this->getIndexNowUrls(
            (int)$shop->id
        );
        foreach ($links as $url) {
            if ($dailyCount >= $maxLimit) {
                $output->writeln(sprintf(
                    '<info>Daily limit has been reached, end index now</info>'
                ));
                $output->writeln(
                    $this->getRandomFunnyComment($output)
                );
                return self::SUCCESS;
            }
            \EverPsSeoTools::indexNow(
                $url
            );
        }
    }

    protected function getIndexNowUrls($idShop)
    {
        $return = [];
        $allProductIds = $this->getAllProducts(
            $idShop
        );
        foreach ($allProductIds as $arr) {
            $product = new \Product(
                $arr['id_product'],
                false,
                (int)$arr['id_lang'],
                (int)$idShop
            );
            if (!\Validate::isLoadedObject($product)) {
                continue;
            }
            $link = new \Link();
            $url = $link->getProductLink(
                $product,
                null,
                null,
                null,
                $arr['id_lang'],
                $idShop
            );
            $return[] = $url;
        }
        $allCategoriesIds = $this->getAllCategories(
            $idShop
        );
        foreach ($allCategoriesIds as $arr) {
            $category = new \Category(
                $arr['id_category'],
                (int)$arr['id_lang'],
                (int)$idShop
            );
            if (!\Validate::isLoadedObject($category)) {
                continue;
            }
            $link = new \Link();
            $url = $link->getCategoryLink(
                $category,
                null,
                null,
                null,
                $arr['id_lang'],
                $idShop
            );
            $return[] = $url;
        }
        $allManufacturersIds = $this->getAllManufacturers(
            $idShop
        );
        foreach ($allManufacturersIds as $arr) {
            $manufacturer = new \Manufacturer(
                $arr['id_manufacturer'],
                (int)$arr['id_lang']
            );
            if (!\Validate::isLoadedObject($manufacturer)) {
                continue;
            }
            $link = new \Link();
            $url = $link->getManufacturerLink(
                $manufacturer,
                null,
                null,
                null,
                $arr['id_lang'],
                $idShop
            );
            $return[] = $url;
        }
        return $return;
    }

    protected function getAllProducts($idShop, $idLang = 0, $idCategory = 0, $limit = 0)
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('product_lang', 'pl');
        $sql->leftJoin(
            'product',
            'ps',
            'ps.id_product = pl.id_product AND ps.id_shop_default = '.(int)$idShop
        );
        $sql->leftJoin(
            'ever_seo_product',
            'esp',
            'esp.id_seo_product = pl.id_product AND esp.id_shop = '.(int)$idShop
        );
        if ((int)$idLang > 0) {
            $sql->where('pl.id_lang = '.(int)$idLang);
        }
        if ((int)$idCategory > 0) {
            $sql->where('ps.id_category_default = '.(int)$idCategory);
        }
        if ((int)$limit > 0) {
            $sql->limit((int)$limit);
        }
        $sql->where('esp.status_code = 0');
        $allProductIds = \Db::getInstance()->executeS($sql);
        return $allProductIds;
    }

    protected function getAllCategories($idShop, $idLang = 0, $idCategory = 0, $limit = 0)
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('category_lang', 'pl');
        $sql->leftJoin(
            'category_shop',
            'ps',
            'ps.id_category = pl.id_category AND ps.id_shop = '.(int)$idShop
        );
        $sql->leftJoin(
            'ever_seo_category',
            'esc',
            'esc.id_seo_category = pl.id_category AND esc.id_shop = '.(int)$idShop
        );
        $sql->where('pl.id_shop = '.(int)$idShop);
        if ((int)$idLang > 0) {
            $sql->where('pl.id_lang = '.(int)$idLang);
        }
        if ((int)$idCategory > 0) {
            $sql->where('pl.id_category = '.(int)$idCategory);
        }
        $sql->where('esc.status_code = 0');
        $allCategoriesIds = \Db::getInstance()->executeS($sql);
        return $allCategoriesIds;
    }

    protected function getAllManufacturers($idShop, $idLang = 0, $idManufacturer = 0, $limit = 0)
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('manufacturer_lang', 'ml');
        $sql->leftJoin(
            'manufacturer_shop',
            'ms',
            'ms.id_manufacturer = ml.id_manufacturer AND ms.id_shop = '.(int)$idShop
        );
        $sql->leftJoin(
            'ever_seo_manufacturer',
            'esm',
            'esm.id_seo_manufacturer = ml.id_manufacturer AND esm.id_shop = '.(int)$idShop
        );
        $sql->where('ms.id_shop = '.(int)$idShop);
        if ((int)$idLang > 0) {
            $sql->where('ml.id_lang = '.(int)$idLang);
        }
        if ((int)$idManufacturer > 0) {
            $sql->where('pl.id_manufacturer = '.(int)$idManufacturer);
        }
        $sql->where('esm.status_code = 0');
        $allManufacturersIds = \Db::getInstance()->executeS($sql);
        return $allManufacturersIds;
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
