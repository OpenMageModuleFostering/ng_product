<?php
/**
* Magento
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.'')
* that is bundled with this package in the file LICENSE_AFL.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.''.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magentocommerce.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade Magento to newer
* versions in the future. If you wish to customize Magento for your
* needs please refer to http://www.magentocommerce.com for more information.
*
* @category    NG
* @package     NG_Product
*/
class NG_Product_Model_System_Config_Source_required
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'name', 'label'=>Mage::helper('ng_product')->__('Name')),
            array('value' => 'sku', 'label'=>Mage::helper('ng_product')->__('SKU')),
            array('value' => 'description', 'label'=>Mage::helper('ng_product')->__('Description')),
            array('value' => 'short_description', 'label'=>Mage::helper('ng_product')->__('Short Description')),
            array('value' => 'weight', 'label'=>Mage::helper('ng_product')->__('Weight')),
            array('value' => 'status', 'label'=>Mage::helper('ng_product')->__('Status')),
            array('value' => 'url_key', 'label'=>Mage::helper('ng_product')->__('URL Key')),
            array('value' => 'visibility', 'label'=>Mage::helper('ng_product')->__('Visibility')),
            array('value' => 'price', 'label'=>Mage::helper('ng_product')->__('Price')),
            array('value' => 'tax_class', 'label'=>Mage::helper('ng_product')->__('Tax Class')),
            array('value' => 'meta_title', 'label'=>Mage::helper('ng_product')->__('Meta Title')),
            array('value' => 'meta_keyword', 'label'=>Mage::helper('ng_product')->__('Meta Keywords')),
            array('value' => 'meta_description', 'label'=>Mage::helper('ng_product')->__('Meta Description')),
            array('value' => 'qty', 'label'=>Mage::helper('ng_product')->__('Qty')),
            array('value' => 'manage_stock', 'label'=>Mage::helper('ng_product')->__('Manage Stock')),
            array('value' => 'is_in_stock', 'label'=>Mage::helper('ng_product')->__('Is In Stock')),
            array('value' => 'categories', 'label'=>Mage::helper('ng_product')->__('Categories')),
            array('value' => 'websites', 'label'=>Mage::helper('ng_product')->__('Websites')),
            array('value' => 'image', 'label'=>Mage::helper('ng_product')->__('Image')),
        );
    }
}