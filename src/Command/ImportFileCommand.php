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
        $this->filenameCategory = dirname(__FILE__) . '/../../input/categories.xlsx';
        $this->filenameProduct = dirname(__FILE__) . '/../../input/products.xlsx';
        $this->logFile = dirname(__FILE__) . '/../../output/logs/log-seo-import-'.date('Y-m-d').'.log';
        $this->module = \Module::getInstanceByName('everpsseo');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (file_exists($this->filenameCategory)) {
            $file = new ImportFile($this->filenameCategory);
            $lines = $file->getLines();
            $headers = $file->getHeaders();
            $output->writeln(sprintf(
                '<info>Start SEO categories update : datetime : '.date('Y-m-d H:i:s').'. Lines total : '.count($lines).'</info>'
            ));
            foreach ($lines as $line) {
                $this->updateSeoCategories($line, $output);
            }
            $output->writeln(
                $this->getRandomFunnyComment($output)
            );
            $output->writeln(sprintf(
                '<comment>Seo categories files updated. Clearing cache</comment>'
            ));
            unlink($this->filenameCategory);
            \Tools::clearAllCache();
            $output->writeln(sprintf(
                '<comment>Cache cleared</comment>'
            ));
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
            foreach ($lines as $line) {
                $this->updateSeoProducts($line, $output);
            }
            $output->writeln(
                $this->getRandomFunnyComment($output)
            );
            $output->writeln(sprintf(
                '<comment>Seo products files updated. Clearing cache</comment>'
            ));
            unlink($this->filenameProduct);
            \Tools::clearAllCache();
            $output->writeln(sprintf(
                '<comment>Cache cleared</comment>'
            ));
        } else {
            $output->writeln(sprintf(
                '<error>Seo products file does not exists</error>'
            ));
        }
        return self::SUCCESS;
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
        $idCategory = preg_replace('/[^0-9]/', '', $line['id_category']);
        $seo_category = \EverPsSeoCategory::getSeoCategory(
            (int)$idCategory,
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
            if ((bool)\Configuration::get('EVERSEO_REWRITE_LINKS') === true) {
                $sql[] = 'UPDATE `'._DB_PREFIX_.'category_lang`
                SET link_rewrite = "'.\Db::getInstance()->escape(
                    \Tools::link_rewrite($line['name'])
                ).'"
                WHERE id_lang = '.(int)$idLang.'
                AND id_shop = '.(int)$idShop.'
                AND id_category = '.(int)$category->id;
            }
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
        if (isset($line['meta_title'])
            && !empty($line['meta_title'])
        ) {
            $sql[] = 'UPDATE `'._DB_PREFIX_.'category_lang`
            SET meta_title = "'.\Db::getInstance()->escape($line['meta_title']).'"
            WHERE id_lang = '.(int)$idLang.'
            AND id_shop = '.(int)$idShop.'
            AND id_category = '.(int)$category->id;
            $seo_category->meta_title = \Db::getInstance()->escape($line['meta_title']);
            $seo_category->save();
        }
        if (isset($line['meta_description'])
            && !empty($line['meta_description'])
        ) {
            $sql[] = 'UPDATE `'._DB_PREFIX_.'category_lang`
            SET meta_description = "'.\Db::getInstance()->escape($line['meta_description']).'"
            WHERE id_lang = '.(int)$idLang.'
            AND id_shop = '.(int)$idShop.'
            AND id_category = '.(int)$category->id;
            $seo_category->meta_description = \Db::getInstance()->escape($line['meta_description']);
            $seo_category->save();
        }
        if (isset($line['link_rewrite'])
            && !empty($line['link_rewrite'])
        ) {
            if (\Validate::isLinkRewrite($line['link_rewrite'])) {
                $sql[] = 'UPDATE `'._DB_PREFIX_.'category_lang`
                SET link_rewrite = "'.\Db::getInstance()->escape($line['link_rewrite']).'"
                WHERE id_lang = '.(int)$idLang.'
                AND id_shop = '.(int)$idShop.'
                AND id_product = '.(int)$category->id;
                $seo_category->link_rewrite = \Db::getInstance()->escape($line['link_rewrite']);
                $seo_category->save();
            } else {
                $output->writeln(
                   '<error>Invalid link rewrite on product '.$line['id_product'].' object</error>'
                );
            }
        }
        if (isset($line['bottom_description'])
            && !empty($line['bottom_content'])
        ) {
            $sql[] = 'UPDATE `'._DB_PREFIX_.'ever_seo_category`
            SET bottom_content = "'.\Db::getInstance()->escape($line['bottom_content']).'"
            WHERE id_lang = '.(int)$idLang.'
            AND id_shop = '.(int)$idShop.'
            AND id_category = '.(int)$category->id;
        }
        if (count($sql) > 0) {
            foreach ($sql as $q) {
                \Db::getInstance()->execute($q);
            }
        }
    }

    protected function updateSeoProducts($line, $output)
    {
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
        if (!isset($line['id_product'])
            || empty($line['id_product'])
        ) {
            $output->writeln(
               '<info>Missing id_product column, trying to see if column reference exists</info>'
            );
            if (!isset($line['reference'])
                || empty($line['reference'])
            ) {
                $output->writeln(
                   '<error>Missing reference column</error>'
                );
                return;
            } else {
                $idProduct = \Product::getIdByReference(
                    $line['reference']
                );
                $product = new \Product(
                    (int)$idProduct,
                    false,
                    (int)$idLang,
                    (int)$idShop
                );
            }
        } else {
            $idProduct = preg_replace('/[^0-9]/', '', $line['id_product']);
            $product = new \Product(
                (int)$idProduct,
                false,
                (int)$idLang,
                (int)$idShop
            );
        }
        if (!\Validate::isLoadedObject($product)) {
            $output->writeln(
               '<error>Invalid product '.$line['id_product'].' object</error>'
            );
            return;
        }
        $seo_product = \EverPsSeoProduct::getSeoProduct(
            (int)$product->id,
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
            if ((bool)\Configuration::get('EVERSEO_REWRITE_LINKS') === true) {
                $sql[] = 'UPDATE `'._DB_PREFIX_.'product_lang`
                SET link_rewrite = "'.\Db::getInstance()->escape(
                    \Tools::link_rewrite($line['name'])
                ).'"
                WHERE id_lang = '.(int)$idLang.'
                AND id_shop = '.(int)$idShop.'
                AND id_product = '.(int)$product->id;
            }
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
        if (isset($line['meta_description'])
            && !empty($line['meta_description'])
        ) {
            $sql[] = 'UPDATE `'._DB_PREFIX_.'product_lang`
            SET meta_description = "'.\Db::getInstance()->escape($line['meta_description']).'"
            WHERE id_lang = '.(int)$idLang.'
            AND id_shop = '.(int)$idShop.'
            AND id_product = '.(int)$product->id;
            $seo_product->meta_description = \Db::getInstance()->escape($line['meta_description']);
            $seo_product->save();
        }
        if (isset($line['meta_title'])
            && !empty($line['meta_title'])
        ) {
            $sql[] = 'UPDATE `'._DB_PREFIX_.'product_lang`
            SET meta_title = "'.\Db::getInstance()->escape($line['meta_title']).'"
            WHERE id_lang = '.(int)$idLang.'
            AND id_shop = '.(int)$idShop.'
            AND id_product = '.(int)$product->id;
            $seo_product->meta_title = \Db::getInstance()->escape($line['meta_title']);
            $seo_product->save();
        }
        if (isset($line['link_rewrite'])
            && !empty($line['link_rewrite'])
        ) {
            if (\Validate::isLinkRewrite($line['link_rewrite'])) {
                $sql[] = 'UPDATE `'._DB_PREFIX_.'product_lang`
                SET link_rewrite = "'.\Db::getInstance()->escape($line['link_rewrite']).'"
                WHERE id_lang = '.(int)$idLang.'
                AND id_shop = '.(int)$idShop.'
                AND id_product = '.(int)$product->id;
                $seo_product->link_rewrite = \Db::getInstance()->escape($line['link_rewrite']);
                $seo_product->save();
            } else {
                $output->writeln(
                   '<error>Invalid link rewrite on product '.$line['id_product'].' object</error>'
                );
            }
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
        $k = array_rand($funnyComments);
        return $funnyComments[$k];
    }
}
