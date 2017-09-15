<?php

namespace Dibs\Flexwin\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $setup->startSetup();
            $orderTable = 'sales_order';
            $invoiceTable = 'sales_invoice';
            $creditmemoTable = 'sales_creditmemo';
            //Order tables
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($orderTable),
                    'dibs_fee',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '10,2',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' =>'Dibs Fee'
                    ]
             );
            $setup->getConnection()
                 ->addColumn(
                    $setup->getTable($orderTable),
                    'base_dibs_fee',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '10,2',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' =>'Base Dibs Fee'
                    ]
                );
            //Invoice tables
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($invoiceTable),
                    'dibs_fee',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '10,2',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' =>'Dibs Fee'
                    ]
                );
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($invoiceTable),
                    'base_dibs_fee',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '10,2',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' =>'Base Dibs Fee'
                    ]
                );
            //Credit memo tables
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($creditmemoTable),
                    'dibs_fee',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '10,2',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' =>'Dibs Fee'
                    ]
                );
            $setup->getConnection()
                ->addColumn(
                    $setup->getTable($creditmemoTable),
                    'base_dibs_fee',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '10,2',
                        'default' => 0.00,
                        'nullable' => true,
                        'comment' =>'Base Dibs Fee'
                    ]
                );
            $setup->endSetup();
        }
        
    }
}
