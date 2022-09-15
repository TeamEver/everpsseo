<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://www.team-ever.com
 */

namespace Everpsseo\Seo\Command;

use League\Csv\Reader;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class GenerateSitemaps extends Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;
    public const ABORTED = 3;
    protected string $filename;

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('everpsseo:seo:sitemaps');
        $this->setDescription('Import des stocks provenant du fichier de Seo');
        $this->filename = dirname(__FILE__) . '/../../input/Arrivage.txt';
        $this->logFile = dirname(__FILE__) . '/../../output/logs/log-sitemaps-'.date('j-n-Y').'.log';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateStart = date('Y-m-d H:i:s');
        if (!file_exists($this->filename)) {
        $output->writeln(sprintf(
                '<error>Missing Arrivage.txt file on date : '.$dateStart.'</error>'
            ));
            \Logger::addLog(
                $dateStart . ' - Missing Arrivage.txt file : ' . $this->filename
            );
            return self::ABORTED;
        }
        $this->logCommand('Arrivage start import : datetime : '.$dateStart);
        $output->writeln(sprintf(
            '<info>Arrivage start import : datetime : '.$dateStart.'</info>'
        ));
        $context = (new ContextAdapter())->getContext();
        $context->employee = new \Employee(1);
        $shop = $context->shop;
        if (!\Validate::isLoadedObject($shop)) {
            $shop = new \Shop((int)\Configuration::get('PS_SHOP_DEFAULT'));
        }
        //Important to setContext
        \Shop::setContext($shop::CONTEXT_SHOP, $shop->id);
        $context->shop = $shop;
        $context->cookie->id_shop = $shop->id;

        $csv = Reader::createFromPath($this->filename, 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter("\t");

        $records = $csv->getRecords();

        foreach ($records as $record) {
            // Ref product w attribute
            $ref = $record['Ref'];
            // Qty arrivage
            $stock = $record['QteArrivage'];
            // Date de réception
            $dateReception = str_replace('/', '-', $record['DateReception']);
            $dateReception = strtotime($dateReception);
            $dateReception = date('Y-m-d H:i:s', $dateReception);
            // Date de mise à jour
            $dateUpd = str_replace('/', '-', $record['DateMiseAJour']);
            $dateUpd = strtotime($dateUpd);
            $dateUpd = date('Y-m-d H:i:s', $dateUpd);
            // Get combinations
            $combinations = \Db::getInstance()->executeS('SELECT id_product, id_product_attribute FROM '._DB_PREFIX_.'product_attribute WHERE reference = "'.pSQL($ref).'"');
            if ($combinations) {
                foreach ($combinations as $combination) {
                    $exists = \Db::getInstance()->getValue(
                        'SELECT id_ub_sage_arrivage FROM `'._DB_PREFIX_.'ub_sage_arrivage`
                        WHERE id_product = '.(int)$combination['id_product'].'
                        AND id_product_attribute = '.(int)$combination['id_product_attribute'].'
                        AND date_reception = "'.\Db::getInstance()->escape($dateReception).'"'
                    );
                    if ($exists && \Validate::isInt($exists)) {
                        $output->writeln(sprintf(
                            '<info>Updating Arrivage. ID Product : '.(int)$combination['id_product'].' - ID Product Attribute : '.(int)$combination['id_product_attribute'].' - QteArrivage : '.(int)$stock.' - DateReception : '.$dateReception.' - DateMiseAJour : '.$dateUpd.'</info>'
                        ));
                        $this->logCommand('Updating Arrivage. ID Product : '.(int)$combination['id_product'].' - ID Product Attribute : '.(int)$combination['id_product_attribute'].' - QteArrivage : '.(int)$stock.' - DateReception : '.$dateReception.' - DateMiseAJour : '.$dateUpd);
                        \Db::getInstance()->update(
                            'ub_sage_arrivage',
                            array(
                                'stock' => (int)$stock,
                                'date_reception' => $dateReception,
                                'date_upd' => $dateUpd
                            ),
                            'id_ub_sage_arrivage = '.(int)$exists
                        );
                    } else {
                        $output->writeln(sprintf(
                            '<info>Creating Arrivage. ID Product : '.(int)$combination['id_product'].' - ID Product Attribute : '.(int)$combination['id_product_attribute'].' - QteArrivage : '.(int)$stock.' - DateReception : '.$dateReception.' - DateMiseAJour : '.$dateUpd.'</info>'
                        ));
                        $this->logCommand('Creating Arrivage. ID Product : '.(int)$combination['id_product'].' - ID Product Attribute : '.(int)$combination['id_product_attribute'].' - QteArrivage : '.(int)$stock.' - DateReception : '.$dateReception.' - DateMiseAJour : '.$dateUpd);
                        \Db::getInstance()->insert(
                            'ub_sage_arrivage',
                            array(
                                'id_product' => (int)$combination['id_product'],
                                'id_product_attribute' => (int)$combination['id_product_attribute'],
                                'stock' => (int)$stock,
                                'date_reception' => $dateReception,
                                'date_upd' => $dateUpd
                            ),
                            false,
                            true,
                            \Db::REPLACE
                        );
                    }
                }
            }
        }
        $output->writeln(sprintf(
            '<info>Import ended, start clearing cache</info>'
        ));
        \Tools::clearAllCache();
        $output->writeln(sprintf(
            '<info>Cache emptied</info>'
        ));
        $output->writeln(sprintf(
            '<comment>See logs at '.$this->logFile.'</comment>'
        ));
        $output->writeln(
            $this->getRandomFunnyComment($output)
        );
        return self::SUCCESS;
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
            Export (•_•)
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
        $k = array_rand($funnyComments);
        return $funnyComments[$k];
    }
}
