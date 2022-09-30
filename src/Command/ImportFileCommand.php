<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @link https://www.team-ever.com
 */

namespace Everpsseo\Seo\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Everpsseo\Seo\Service\ImportFile;

class ImportFileCommand extends ContainerAwareCommand
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
        $this->setName('everpsseo:seo:import');
        $this->setDescription('Update SEO datas for categories & products');
        $this->filenameCategory = dirname(__FILE__) . '/../../input/majstock0.txt';
        $this->filenameProduct = dirname(__FILE__) . '/../../input/majstock1.txt';
        $this->logFile = dirname(__FILE__) . '/../../output/logs/log-seo-'.date('Y-m-d').'.log';
        $this->module = \Module::getInstanceByName('everpsseo');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Parse txt file categories
        if (file_exists($this->filenameCategory)) {
            $file = new ImportFile($this->filenameCategory);
            $lines = $file->getLines();
            $headers = $file->getHeaders();
            $output->writeln(sprintf(
                '<info>Start SEO categories update : datetime : '.date('Y-m-d H:i:s').'. Lines total : '.count($lines).'</info>'
            ));
            foreach ($records as $line) {
                $this->updateSeoCategories($line, $output);
            }
            $output->writeln(
                $this->getRandomFunnyComment($output)
            );
            $output->writeln(sprintf(
                '<comment>Seo categories files updated. Clearing cache</comment>'
            ));
            \Tools::clearAllCache();
            $output->writeln(sprintf(
                '<comment>Cache cleared</comment>'
            ));
            return self::SUCCESS;
        } else {
            $output->writeln(sprintf(
                '<error>Seo categories file does not exists</error>'
            ));
        }
        // Parse txt file categories
        if (file_exists($this->filenameProduct)) {
            $file = new ImportFile($this->filenameProduct);
            $lines = $file->getLines();
            $headers = $file->getHeaders();
            $output->writeln(sprintf(
                '<info>Start SEO products update : datetime : '.date('Y-m-d H:i:s').'. Lines total : '.count($lines).'</info>'
            ));
            foreach ($records as $line) {
                $this->updateSeoProducts($line, $output);
            }
            $output->writeln(
                $this->getRandomFunnyComment($output)
            );
            $output->writeln(sprintf(
                '<comment>Seo products files updated. Clearing cache</comment>'
            ));
            \Tools::clearAllCache();
            $output->writeln(sprintf(
                '<comment>Cache cleared</comment>'
            ));
            return self::SUCCESS;
        } else {
            $output->writeln(sprintf(
                '<error>Seo products file does not exists</error>'
            ));
        }
    }

    protected function updateSeoCategories($line, $output)
    {
        if (!isset($line['id_category'])
            || empty($line['id_category'])
        ) {
            $output->writeln(
               '<error>Missing id_category column</error>'
            );
            return;
        }
        if (!isset($line['id_lang'])
            || empty($line['id_lang'])
        ) {
            $output->writeln(
               '<error>Missing id_lang column</error>'
            );
            return;
        }
        if (!isset($line['id_shop'])
            || empty($line['id_shop'])
        ) {
            $output->writeln(
               '<error>Missing id_shop column</error>'
            );
            return;
        }
        $idLang = (int)$line['id_lang'];
        $idShop = (int)$line['id_shop'];
        $category = new \Category(
            (int)$line['id_category'],
            (int)$idLang,
            (int)$idShop
        );
        if (!\Validate::isLoadedObject($category)) {
            return;
        }
        $seo_category = \EverPsSeoCategory::getSeoCategory(
            (int)$line['id_category'],
            (int)$idShop,
            (int)$idLang
        );
        $sql = [];
        if (isset($line['name'])
            && !empty($line['name'])
        ) {
            $sql[] = 'UPDATE `'._DB_PREFIX_.'category_lang`
            SET name = "'.\Db::getInstance()->escape($line['name']).'"
            WHERE id_lang = '.(int)$idLang.'
            AND id_shop = '.(int)$idShop.'
            AND id_category = '.(int)$category->id;
        }
        if (isset($line['description'])
            && !empty($line['description'])
        ) {
            $sql[] = 'UPDATE `'._DB_PREFIX_.'category_lang`
            SET description = "'.\Db::getInstance()->escape($line['description']).'"
            WHERE id_lang = '.(int)$idLang.'
            AND id_shop = '.(int)$idShop.'
            AND id_category = '.(int)$category->id;
        }
        if (isset($line['bottom_description'])
            && !empty($line['description'])
        ) {
            $seo_category->bottom_content = \Db::getInstance()->escape($line['bottom_description']);
            $seo_category->save();
        }
    }

    protected function updateSeoProducts($line, $output)
    {
        if (!isset($line['id_product'])
            || empty($line['id_product'])
        ) {
            $output->writeln(
               '<error>Missing id_product column</error>'
            );
            return;
        }
        if (!isset($line['id_lang'])
            || empty($line['id_lang'])
        ) {
            $output->writeln(
               '<error>Missing id_lang column</error>'
            );
            return;
        }
        if (!isset($line['id_shop'])
            || empty($line['id_shop'])
        ) {
            $output->writeln(
               '<error>Missing id_shop column</error>'
            );
            return;
        }
        $idLang = (int)$line['id_lang'];
        $idShop = (int)$line['id_shop'];
        $product = new \Product(
            (int)$line['id_product'],
            false,
            (int)$idLang,
            (int)$idShop
        );
        if (!\Validate::isLoadedObject($product)) {
            return;
        }
        $seo_product = \EverPsSeoProduct::getSeoProduct(
            (int)$line['id_product'],
            (int)$idShop,
            (int)$idLang
        );
        $sql = [];
        if (isset($line['name'])
            && !empty($line['name'])
        ) {
            $sql[] = 'UPDATE `'._DB_PREFIX_.'product_lang`
            SET name = "'.\Db::getInstance()->escape($line['name']).'"
            WHERE id_lang = '.(int)$idLang.'
            AND id_shop = '.(int)$idShop.'
            AND id_product = '.(int)$product->id;
        }
        if (isset($line['description'])
            && !empty($line['description'])
        ) {
            $sql[] = 'UPDATE `'._DB_PREFIX_.'product_lang`
            SET description = "'.\Db::getInstance()->escape($line['description'], true).'"
            WHERE id_lang = '.(int)$idLang.'
            AND id_shop = '.(int)$idShop.'
            AND id_product = '.(int)$product->id;
        }
        if (isset($line['description_short'])
            && !empty($line['description_short'])
        ) {
            $sql[] = 'UPDATE `'._DB_PREFIX_.'product_lang`
            SET description_short = "'.\Db::getInstance()->escape($line['description_short'], true).'"
            WHERE id_lang = '.(int)$idLang.'
            AND id_shop = '.(int)$idShop.'
            AND id_product = '.(int)$product->id;
        }
        if (isset($line['bottom_description'])
            && !empty($line['description'])
        ) {
            $seo_product->bottom_content = \Db::getInstance()->escape($line['bottom_description'], true);
            $seo_product->save();
        }
        if (count($sql) > 0) {
            foreach ($sql as $q) {
                \Db::getInstance()->execute($q);
            }
        }
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

    /**
     * Convert to float without using forbidden floatval
     * @param string to convert
     * @return float converted
    */
    public static function clFloatval($string)
    {
        $string = str_replace(',', '.', $string);
        return (float)$string;
    }

    /**
     * Generate percentage change between two numbers.
     * @param int|float $old
     * @param int|float $new
     * @return string
     */
    public static function pctChange($old, $new)
    {
        if ($old == 0) {
            $old++;
            $new++;
        }

        $percent_change = (($new - $old) / $old);
        $percent_change = str_replace('-', '', $percent_change);

        return $percent_change;
    }
}
