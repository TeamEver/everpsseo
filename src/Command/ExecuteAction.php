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
use Everpsseo\Seo\Service\ImportFile;

class ExecuteAction extends Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;
    public const ABORTED = 3;

    private $allowedActions = [
        'idshop',
        'redirectDisabledProduct',
        'updateStock'
    ];

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('everpsseo:seo:execute');
        $this->setDescription('Execute action');
        $this->addArgument('action', InputArgument::REQUIRED, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)));
        $this->addArgument('idshop id', InputArgument::OPTIONAL, 'Shop ID');
        $this->filenameStock = dirname(__FILE__) . '/../../input/stock.xlsx';
        $this->logFile = dirname(__FILE__) . '/../../output/logs/log-seo-execute-action-' . date('Y-m-d') . '.log';
        $this->module = \Module::getInstanceByName('everpsseo');;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        $idShop = $input->getArgument('idshop id');
        $context = (new ContextAdapter())->getContext();
        $context->employee = new \Employee(1);
        if ($idShop && $idShop >= 1) {
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
        if (!in_array($action, $this->allowedActions)) {
            $output->writeln('<comment>Unkown action</comment>');
            return self::ABORTED;
        }
        if ($action === 'updateStock') {
            // Parse txt file categories
            if (file_exists($this->filenameStock)) {
                $file = new ImportFile($this->filenameStock);
                $lines = $file->getLines();
                $headers = $file->getHeaders();
                $output->writeln(sprintf(
                    '<info>Start SEO stock update : datetime : ' . date('Y-m-d H:i:s') . '. Lines total : '.count($lines).'</info>'
                ));
                foreach ($lines as $line) {
                    if (!isset($line['reference']) || empty($line['reference'])) {
                        $output->writeln('<comment>Missing reference</comment>');
                        continue;
                    }
                    $reference = $line['reference'];
                    if (!isset($line['stock'])) {
                        $output->writeln('<comment>Missing stock</comment>');
                        continue;
                    }
                    $stock = $line['stock'];
                    $id_product = 0;
                    $id_product_attribute = 0;

                    $product = new \Product();
                    $id = $product->getIdByReference($reference);
                    if ($id) {
                        $id_product = $id;
                    } else {
                        $query = new \DbQuery();
                        $query->select('pa.`id_product`, pa.`id_product_attribute`');
                        $query->from('product_attribute', 'pa');
                        $query->where('pa.`reference` = \'' . pSQL($reference) . '\'');

                        $result = \Db::getInstance()->getRow($query);

                        $id_product = isset($result['id_product']) ? (int) $result['id_product'] : 0;
                        $id_product_attribute = isset($result['id_product_attribute']) ? (int) $result['id_product_attribute'] : 0;
                    }
                    if ((int) $id_product <= 0) {
                        $output->writeln('<comment>Reference ' . $reference . ' not found</comment>');
                        $this->logCommand(
                            '<comment>Reference ' . $reference . ' not found</comment>'
                        );
                        continue;
                    }
                    \StockAvailable::updateQuantity(
                        $id_product,
                        $id_product_attribute,
                        $stock
                    );
                    $output->writeln('<comment>Stock updated on ' . $reference . '. Qty : ' . $stock . ' - ID product : ' . $id_product . ' - ID product attribute : ' . $id_product_attribute . '</comment>');
                    $this->logCommand(
                        '<comment>Stock updated on ' . $reference . '. Qty : ' . $stock . '</comment>'
                    );
                }
                $output->writeln(
                    $this->getRandomFunnyComment($output)
                );
                $output->writeln(sprintf(
                    '<comment>Seo products files updated. Clearing cache</comment>'
                ));
                // unlink($this->filenameStock);
                // \Tools::clearAllCache();
                $output->writeln(sprintf(
                    '<comment>Cache cleared</comment>'
                ));
            } else {
                $output->writeln(sprintf(
                    '<info>Seo products file does not exists</info>'
                ));
            }
            die('so we are here');
        }
        if ($action === 'redirectDisabledProduct') {
            if (!(bool) \Configuration::get('EVERSEO_FORCE_PRODUCT_REDIRECT')) {
                $output->writeln('<comment>This action is disabled in module conf</comment>');
                return self::ABORTED;
            }

            $products = \EverPsSeoProduct::getDisabledProduct();

            if ($products && count($products) > 0) {
                $output->writeln(sprintf(
                    '<info>Start forcing redirection disabled product type 404 to parent category : datetime : ' . date('Y-m-d H:i:s') . '</info>'
                ));

                foreach ($products as $value) {
                    \EverPsSeoProduct::inactiveRedirect($value['id_product'], $shop->id);
                    $output->writeln(sprintf(
                        '<info>Forcing redirection for id product ' . (int) $value['id_product'] . ' has been set</info>'
                    ));
                }

                $output->writeln(sprintf(
                    '<info>End forcing redirection disabled product type 404 to parent category : datetime : ' . date('Y-m-d H:i:s') . '</info>'
                ));
            }

            $output->writeln(sprintf(
                '<info>Execute ended : datetime : ' . date('Y-m-d H:i:s') . '</info>'
            ));

            $output->writeln(
                $this->getRandomFunnyComment($output)
            );

            return self::SUCCESS;
        }
        
        return self::ABORTED;
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
