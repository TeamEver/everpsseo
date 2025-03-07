<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
 */

namespace Everpsseo\Seo\Command;

use Configuration;
use Employee;
use Db;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class GenerateSitemaps extends Command
{
    const SUCCESS = 0;
    const FAILURE = 1;
    const INVALID = 2;
    const ABORTED = 3;

    private $allowedActions = [
        'pages',
        'products',
        'categories',
        'manufacturers',
        'suppliers',
        'cms',
        'images',
        'getrandomcomment'
    ];

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('everpsseo:seo:sitemaps');
        $this->setDescription('Generate sitemaps for each lang');
        $this->addArgument('action', InputArgument::OPTIONAL, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)));
        $this->addArgument('idshop id', InputArgument::OPTIONAL, 'Shop ID');
        $this->addArgument('idlang id', InputArgument::OPTIONAL, 'Language ID');
        $this->logFile = dirname(__FILE__) . '/../../output/logs/log-seo-sitemaps-generation-' . date('Y-m-d') . '.log';
        $this->module = \Module::getInstanceByName('everpsseo');;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        $idShop = $input->getArgument('idshop id');
        $idLang = $input->getArgument('idlang id');
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
        $shop = new \Shop(
            (int) $idShop
        );
        if (!\Validate::isLoadedObject($shop)) {
            $output->writeln('<comment>Shop not found</comment>');
            return self::ABORTED;
        }
        //Important to setContext
        \Shop::setContext($shop::CONTEXT_SHOP, $shop->id);
        $context->shop = $shop;
        $context->cookie->id_shop = $shop->id;
        $idEmployee =  Db::getInstance()->getValue('
            SELECT `id_employee`
            FROM `' . _DB_PREFIX_ . 'employee`'
        );
        $context->employee = new \Employee($idEmployee);
        $context->currency = new \Currency(
            (int) Configuration::get('PS_CURRENCY_DEFAULT')
        );
        $output->writeln(sprintf(
            '<info>Start sitemap generation : datetime : ' . date('Y-m-d H:i:s') . '</info>'
        ));
        if ($action == 'products') {
            $output->writeln(sprintf(
                '<info>Products sitemaps asked for lang '. (int) $idLang . ' and shop ' . (int) $idShop . '</info>'
            ));
            $this->module->processSitemapProduct((int) $idShop, (int) $idLang);
        }
        if ($action == 'images') {
            $output->writeln(sprintf(
                '<info>Images sitemaps asked for lang '. (int) $idLang . ' and shop ' . (int) $idShop . '</info>'
            ));
            $this->module->processSitemapImage((int) $idShop, (int) $idLang);
        }
        if ($action == 'categories') {
            $output->writeln(sprintf(
                '<info>Categories sitemaps asked for lang '. (int) $idLang . ' and shop ' . (int) $idShop . '</info>'
            ));
            $this->module->processSitemapCategory((int) $idShop, (int) $idLang);
        }
        if ($action == 'manufacturers') {
            $output->writeln(sprintf(
                '<info>Manufacturers sitemaps asked for lang '. (int) $idLang . ' and shop ' . (int) $idShop . '</info>'
            ));
            $this->module->processSitemapManufacturer((int) $idShop, (int) $idLang);
        }
        if ($action == 'suppliers') {
            $output->writeln(sprintf(
                '<info>Suppliers sitemaps asked for lang '. (int) $idLang . ' and shop ' . (int) $idShop . '</info>'
            ));
            $this->module->processSitemapSupplier((int) $idShop, (int) $idLang);
        }
        if ($action == 'cms') {
            $output->writeln(sprintf(
                '<info>CMS sitemaps asked for lang '. (int) $idLang . ' and shop ' . (int) $idShop . '</info>'
            ));
            $this->module->processSitemapCms((int) $idShop, (int) $idLang);
        }
        if ($action == 'pages') {
            $output->writeln(sprintf(
                '<info>Pages sitemaps asked for lang '. (int) $idLang . ' and shop ' . (int) $idShop . '</info>'
            ));
            $this->module->processSitemapPageMeta((int) $idShop, (int) $idLang);
        }
        $output->writeln(sprintf(
            '<info>Generation ended</info>'
        ));
        $output->writeln(
            $this->getRandomFunnyComment($output)
        );
        return self::SUCCESS;
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
