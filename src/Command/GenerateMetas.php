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

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('everpsseo:seo:metas');
        $this->setDescription('Generate SEO title and meta description for each lang');
        $this->logFile = dirname(__FILE__) . '/../../output/logs/log-metas-'.date('j-n-Y').'.log';
        $this->module = \Module::getInstanceByName('everpsseo');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateStart = date('Y-m-d H:i:s');
        if (!file_exists($this->filename)) {
            $this->logCommand('Missing SeoVersWebClient.txt file on date : '.$dateStart);
            $output->writeln(sprintf(
                '<error>Missing SeoVersWebClient.txt file on date : '.$dateStart.'</error>'
            ));
            \Logger::addLog(
                $dateStart . ' - Missing SeoVersWebClient.txt file : ' . $this->filename
            );
            return self::ABORTED;
        }
        if (\Db::getInstance()->ExecuteS(
            'SHOW COLUMNS FROM `'._DB_PREFIX_.'customer` LIKE \'id_magento\''
        ) == false) {
            $this->logCommand('Missing column id_magento on date : '.$dateStart);
            $output->writeln(sprintf(
                '<error>Missing column id_magento on date : '.$dateStart.'</error>'
            ));
            \Logger::addLog(
                $dateStart . ' - Missing column id_magento'
            );
            return self::ABORTED;
        }
        $this->logCommand('SeoVersWebClient start import : datetime : '.$dateStart);
        $output->writeln(sprintf(
            '<info>SeoVersWebClient start import : datetime : '.$dateStart.'</info>'
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
            // Date de mise à jour
            $dateUpd = $record['DateMiseAJour'];
            // ID Magento
            $idMagento = $record['IdMagento'];
            // ID Seo
            $idSeo = $record['IdClientSeo'];
            // Customer name from Seo
            $customerName = $record['ClientNom'];
            // Customer en cours
            $outstanding = $record['CT_Encours'];
            // Account state from Seo
            $state = $record['Statut Compte'];
            // Account state label from Seo
            $stateLabel = $record['Statut Libelle'];
            // Get Customer ID
            $idCustomer = \Db::getInstance()->getValue(
                'SELECT `ic_customer` FROM `'._DB_PREFIX_.'customer`
                WHERE id_magento = "'.pSQL($idMagento).'";'
            );
            if (!$idCustomer || !\Validate::isInt($idCustomer)) {
                $this->logCommand('Customer not found on ID Seo : '.$idSeo);
                $output->writeln(sprintf(
                    '<error>Customer not found on ID Seo : '.$idSeo.'</error>'
                ));
                continue;
            }
            $customer = new \Customer(
                (int)$idCustomer
            );
            $customer->outstanding_allow_amount = (float)$outstanding;
            if ($customer->save()) {
                $this->logCommand('Customer ID '.$idCustomer.' updated.');
                $output->writeln(sprintf(
                    '<info>Customer ID '.$idCustomer.' updated. </info>'
                ));
            }
        }
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
