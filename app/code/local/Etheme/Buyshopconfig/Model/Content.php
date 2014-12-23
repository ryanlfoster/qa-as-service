<?php
/**
 * @version   1.0 14.08.2012
 * @author    TonyEcommerce http://www.TonyEcommerce.com <support@TonyEcommerce.com>
 * @copyright Copyright (c) 2012 TonyEcommerce
 */

class Etheme_Buyshopconfig_Model_Content extends Mage_Core_Model_Abstract
{
    protected $data_resources_xml;
    protected $data_static_blocks_xml;

    public function __construct()
    {
        parent::__construct();
        $this->data_resources_xml = new Varien_Simplexml_Config(Mage::getBaseDir().'/app/code/local/Etheme/Buyshopconfig/Model/data_resources.xml');
        $this->data_static_blocks_xml = new Varien_Simplexml_Config(Mage::getBaseDir().'/app/code/local/Etheme/Buyshopconfig/Model/data_static_blocks.xml');
    }

    public function installTemplateConfig($store)
    {
        $scope=($store ? 'stores' : 'default');
        Mage::getConfig()->saveConfig('design/package/name', 'buyshop', $scope, $store);
        Mage::getConfig()->saveConfig('design/theme/default', 'default', $scope, $store);
        Mage::getConfig()->saveConfig('web/default/cms_home_page', 'buyshop_home_advanced', $scope, $store);
        Mage::getConfig()->saveConfig('currency/options/allow', 'GBP,EUR,USD', $scope, $store);
        Mage::getConfig()->saveConfig('web/default/cms_no_route', 'buyshop_404', $scope, $store);
    }

    public function installTemplateResources($rewrite=false)
    {
        $store=0;
        foreach ($this->data_resources_xml->getNode('resources')->children() as $resource )
        {
            $data = array();
            foreach ($resource as $param) $data[$param->getName()]=(string)$param;
            $data['stores']=array();
            $data['stores'][] = $store;
            $exist_collection = Mage::getModel('cms/page')->getCollection();
            $exist = $exist_collection->addFieldToFilter('identifier', array( 'eq' => $data['identifier']))->load();
            if($rewrite)
            {
                if (count($exist)) foreach($exist as $block) $block->delete();
                Mage::getModel('cms/page')->setData($data)->save();
            }
            else
            {
                if (!count($exist)) Mage::getModel('cms/page')->setData($data)->save();
            }
        }
    }

    public function installTemplateBlocks($rewrite=false)
    {
        $store=0;
        foreach ($this->data_static_blocks_xml->getNode('blocks')->children() as $resource )
        {
            $data = array();
            foreach ($resource as $param) $data[$param->getName()]=(string)$param;
            $data['stores']=array();
            $data['stores'][] = $store;
            $exist_collection = Mage::getModel('cms/block')->getCollection();
            $exist = $exist_collection->addFieldToFilter('identifier', array( 'eq' => $data['identifier']))->load();
            if($rewrite)
            {
                if (count($exist)) foreach($exist as $block) $block->delete();
                Mage::getModel('cms/block')->setData($data)->save();
            }
            else
            {
                if (!count($exist)) Mage::getModel('cms/block')->setData($data)->save();
            }
        }
    }

}
