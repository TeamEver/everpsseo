<?php
/**
 * Project : everpsseo
 * @author Team Ever
 * @copyright Team Ever
 * @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
 * @see https://www.team-ever.com
 */

namespace Everpsseo\Seo\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PrestaShop\PrestaShop\Adapter\LegacyContext as ContextAdapter;

class ExportFileCommand extends Command
{
    const SUCCESS = 0;
    const FAILURE = 1;
    const INVALID = 2;
    const ABORTED = 3;
    
    protected $filename;

    private $allowedActions = [
        'getrandomcomment',
        'categories',
        'features',
        'products'
    ];

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('everpsseo:seo:export');
        $this->setDescription('Export SEO datas for categories & products');
        $this->addArgument('action', InputArgument::OPTIONAL, sprintf('Action to execute (Allowed actions: %s).', implode(' / ', $this->allowedActions)));
        $this->addArgument('idshop id', InputArgument::OPTIONAL, 'Shop ID');
        $this->addArgument('lang id', InputArgument::OPTIONAL, 'Language ID');
        $this->addArgument('category id', InputArgument::OPTIONAL, 'Category ID');
        $this->addArgument('limit l', InputArgument::OPTIONAL, 'Limit int');
        $this->filenameCategory = dirname(__FILE__) . '/../../output/categories.xlsx';
        $this->filenameProduct = dirname(__FILE__) . '/../../output/products.xlsx';
        $this->filenameFeatures = dirname(__FILE__) . '/../../output/features.xlsx';
        $this->logFile = dirname(__FILE__) . '/../../output/logs/log-seo-export-'.date('Y-m-d').'.log';
        $this->module = \Module::getInstanceByName('everpsseo');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $action = $input->getArgument('action');
        $idShop = $input->getArgument('idshop id');
        $idLang = $input->getArgument('lang id');
        $categoryId = $input->getArgument('category id');
        $limit = $input->getArgument('limit l');
        if (!in_array($action, $this->allowedActions)) {
            $output->writeln('<comment>Unkown action</comment>');
            return self::ABORTED;
        }
        $context = (new ContextAdapter())->getContext();
        $context->employee = new \Employee(1);
        if (\Validate::isInt($idShop)) {
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
                $idShop = $shop->id;
            } else {
                $output->writeln('<comment>Shop not found</comment>');
                return self::ABORTED;
            }
        }
        \Shop::setContext($shop::CONTEXT_SHOP, $idShop);

        if ($action === 'getrandomcomment') {
            $output->writeln(
                $this->getRandomFunnyComment($output)
            );
            return self::SUCCESS;
        }
        // Fine, let's output XLSX files
        $creator = \Configuration::get('PS_SHOP_NAME');
        $title = $action;
        $reportName = $action;
        if ($action === 'categories') {
            $dataSet = $this->getAllCategories(
                (int)$idShop,
                (int)$idLang,
                (int)$categoryId,
                (int)$limit
            );
            $spreadsheet = new Spreadsheet();
            // Set properties
            $spreadsheet->getProperties()->setCreator($creator)
                                         ->setLastModifiedBy($creator)
                                         ->setTitle($title)
                                         ->setSubject($title)
                                         ->setDescription($title)
                                         ->setCategory($title);
            $spreadsheet->setActiveSheetIndex(0);
            $r = 2;
            foreach ($dataSet as $category) {
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $r, $category['id_category']);
                $spreadsheet->getActiveSheet()->getStyle("A".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("A".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("A".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $r, $category['id_shop']);
                $spreadsheet->getActiveSheet()->getStyle("B".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("B".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("B".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $r, $category['id_lang']);
                $spreadsheet->getActiveSheet()->getStyle("C".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("C".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("C".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $r, $category['name']);
                $spreadsheet->getActiveSheet()->getStyle("D".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("D".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("D".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $r, $category['description']);
                $spreadsheet->getActiveSheet()->getStyle("E".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("E".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("E".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $r, $category['bottom_content']);
                $spreadsheet->getActiveSheet()->getStyle("F".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("F".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("F".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $r, $category['meta_description']);
                $spreadsheet->getActiveSheet()->getStyle("G".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("G".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("G".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $r, $category['meta_title']);
                $spreadsheet->getActiveSheet()->getStyle("H".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("H".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("H".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $r, $category['link_rewrite']);
                $spreadsheet->getActiveSheet()->getStyle("I".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("I".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("I".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);

                $r++;
            }
            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'id_category')
            ->setCellValue('B1', 'id_shop')
            ->setCellValue('C1', 'id_lang')
            ->setCellValue('D1', 'name')
            ->setCellValue('E1', 'description')
            ->setCellValue('F1', 'bottom_content')
            ->setCellValue('G1', 'meta_description')
            ->setCellValue('H1', 'meta_title')
            ->setCellValue('I1', 'link_rewrite');
            $spreadsheet->getActiveSheet()->setAutoFilter('A1:I1');
            // Rename sheet
            $spreadsheet->getActiveSheet()->setTitle(\Tools::substr($reportName, 0, 31));

            //Text bold in first row
            $spreadsheet->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true);

            //Freeze first row
            $spreadsheet->getActiveSheet()->freezePane('A2');
            $styleArray = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => [
                        'argb' => 'FFA0A0A0',
                    ],
                    'endColor' => [
                        'argb' => 'FFFFFFFF',
                    ],
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => ['argb' => 'FFFF0000'],
                    ],
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleArray);

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $spreadsheet->setActiveSheetIndex(0);

            $writer = new Xlsx($spreadsheet);
            $writer->save(
                $this->filenameCategory
            );
            $output->writeln(sprintf(
                '<comment>File generated, you can download it on SEO module from backoffice</comment>'
            ));
            $output->writeln(
                $this->getRandomFunnyComment($output)
            );
            return self::SUCCESS;

        }
        if ($action === 'products') {
            $dataSet = $this->getAllProducts(
                (int)$idShop,
                (int)$idLang,
                (int)$categoryId,
                (int)$limit
            );
            $spreadsheet = new Spreadsheet();
            // Set properties
            $spreadsheet->getProperties()->setCreator($creator)
                                         ->setLastModifiedBy($creator)
                                         ->setTitle($title)
                                         ->setSubject($title)
                                         ->setDescription($title)
                                         ->setCategory($title);
            $spreadsheet->setActiveSheetIndex(0);
            $r = 2;
            foreach ($dataSet as $product) {
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $r, $product['id_product']);
                $spreadsheet->getActiveSheet()->getStyle("A".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("A".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("A".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $r, $product['id_shop']);
                $spreadsheet->getActiveSheet()->getStyle("B".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("B".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("B".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $r, $product['id_lang']);
                $spreadsheet->getActiveSheet()->getStyle("C".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("C".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("C".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $r, $product['reference']);
                $spreadsheet->getActiveSheet()->getStyle("D".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("D".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("D".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $r, $product['name']);
                $spreadsheet->getActiveSheet()->getStyle("E".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("E".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("E".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $r, $product['description_short']);
                $spreadsheet->getActiveSheet()->getStyle("F".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("F".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("F".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $r, $product['description']);
                $spreadsheet->getActiveSheet()->getStyle("G".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("G".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("G".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $r, $product['meta_description']);
                $spreadsheet->getActiveSheet()->getStyle("H".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("H".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("H".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("H")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(9, $r, $product['meta_title']);
                $spreadsheet->getActiveSheet()->getStyle("I".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("I".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("I".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("I")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(10, $r, $product['link_rewrite']);
                $spreadsheet->getActiveSheet()->getStyle("J".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("J".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("J".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("J")->setAutoSize(true);

                $r++;
            }
            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'id_product')
            ->setCellValue('B1', 'id_shop')
            ->setCellValue('C1', 'id_lang')
            ->setCellValue('D1', 'reference')
            ->setCellValue('E1', 'name')
            ->setCellValue('F1', 'description_short')
            ->setCellValue('G1', 'description')
            ->setCellValue('H1', 'meta_description')
            ->setCellValue('I1', 'meta_title')
            ->setCellValue('J1', 'link_rewrite');
            $spreadsheet->getActiveSheet()->setAutoFilter('A1:J1');
            // Rename sheet
            $spreadsheet->getActiveSheet()->setTitle(\Tools::substr($reportName, 0, 31));

            //Text bold in first row
            $spreadsheet->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);

            //Freeze first row
            $spreadsheet->getActiveSheet()->freezePane('A2');
            $styleArray = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => [
                        'argb' => 'FFA0A0A0',
                    ],
                    'endColor' => [
                        'argb' => 'FFFFFFFF',
                    ],
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => ['argb' => 'FFFF0000'],
                    ],
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:J1')->applyFromArray($styleArray);

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $spreadsheet->setActiveSheetIndex(0);

            $writer = new Xlsx($spreadsheet);
            $writer->save(
                $this->filenameProduct
            );
            $output->writeln(sprintf(
                '<comment>File generated, you can download it on SEO module from backoffice</comment>'
            ));
            $output->writeln(
                $this->getRandomFunnyComment($output)
            );
            return self::SUCCESS;
        }
        if ($action === 'features') {
            $dataSet = $this->getAllFeatures(
                (int)$idShop,
                (int)$idLang,
                (int)$limit
            );
            $spreadsheet = new Spreadsheet();
            // Set properties
            $spreadsheet->getProperties()->setCreator($creator)
                                         ->setLastModifiedBy($creator)
                                         ->setTitle($title)
                                         ->setSubject($title)
                                         ->setDescription($title)
                                         ->setCategory($title);
            $spreadsheet->setActiveSheetIndex(0);
            $r = 2;
            foreach ($dataSet as $feature) {
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $r, $feature['id_feature']);
                $spreadsheet->getActiveSheet()->getStyle("A".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("A".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("A".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $r, $feature['id_shop']);
                $spreadsheet->getActiveSheet()->getStyle("B".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("B".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("B".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $r, $feature['id_lang']);
                $spreadsheet->getActiveSheet()->getStyle("C".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("C".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("C".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);

                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $r, $feature['name']);
                $spreadsheet->getActiveSheet()->getStyle("D".$r)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle("D".$r)->getFont()->setName('Arial');
                $spreadsheet->getActiveSheet()->getStyle("D".$r)->getFont()->setSize(9);
                $spreadsheet->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);

                $r++;
            }
            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'id_feature')
            ->setCellValue('B1', 'id_shop')
            ->setCellValue('C1', 'id_lang')
            ->setCellValue('D1', 'name');
            $spreadsheet->getActiveSheet()->setAutoFilter('A1:D1');
            // Rename sheet
            $spreadsheet->getActiveSheet()->setTitle(\Tools::substr($reportName, 0, 31));

            //Text bold in first row
            $spreadsheet->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);

            //Freeze first row
            $spreadsheet->getActiveSheet()->freezePane('A2');
            $styleArray = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => [
                        'argb' => 'FFA0A0A0',
                    ],
                    'endColor' => [
                        'argb' => 'FFFFFFFF',
                    ],
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                        'color' => ['argb' => 'FFFF0000'],
                    ],
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:D1')->applyFromArray($styleArray);

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $spreadsheet->setActiveSheetIndex(0);
            $writer = new Xlsx($spreadsheet);
            $writer->save(
                $this->filenameFeatures
            );
            $output->writeln(sprintf(
                '<comment>File generated, you can download it on SEO module from backoffice</comment>'
            ));
            $output->writeln(
                $this->getRandomFunnyComment($output)
            );
            return self::SUCCESS;
        }
    }

    protected function getAllFeatures($idShop, $idLang = 0, $limit = 0)
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('feature_lang', 'fl');
        $sql->leftJoin(
            'feature_shop',
            'fs',
            'fs.id_feature = fl.id_feature AND fs.id_shop = '.(int)$idShop
        );
        if ((int)$idLang > 0) {
            $sql->where('fl.id_lang = '.(int)$idLang);
        }
        if ((int)$limit > 0) {
            $sql->limit((int)$limit);
        }
        $allFeaturesIds = \Db::getInstance()->executeS($sql);
        return $allFeaturesIds;
    }

    protected function getAllFeatureValues($idLang = 0, $limit = 0)
    {
        $sql = new \DbQuery();
        $sql->select('*');
        $sql->from('feature_value_lang', 'fvl');
        if ((int)$idLang > 0) {
            $sql->where('fvl.id_lang = '.(int)$idLang);
        }
        if ((int)$limit > 0) {
            $sql->limit((int)$limit);
        }
        $allFeaturesIds = \Db::getInstance()->executeS($sql);
        return $allFeaturesIds;
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
        $allCategoriesIds = \Db::getInstance()->executeS($sql);
        return $allCategoriesIds;
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
            EXPORT ENDED, HAVE A BEER
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
            EXPORT ENDED, MEOW
              ^~^  ,
             ('Y') )
             /   \/
            (\|||/)
            </styled>";
        $funnyComments[] = "<styled>
            EXPORT ENDED, D'OH
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
            |      EXPORT      |
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
            ....../ `---___________--------    | ============= EXPORT-ENDED-BULLET !
            ...../_==o;;;;;;;;______________|
            .....), ---.(_(__) /
            .......// (..) ), /--
            ... //___//---
            .. //___//
            .//___//
            //___//
            </styled>";
        $funnyComments[] = "<styled>
               EXPORT ENDED
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
