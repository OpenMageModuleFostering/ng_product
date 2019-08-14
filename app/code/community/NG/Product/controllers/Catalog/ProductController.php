<?php

require_once(Mage::getModuleDir('controllers', 'Mage_Catalog') . DS . 'ProductController.php');

class NG_Product_Catalog_ProductController extends Mage_Catalog_ProductController {

    /**
     * Current applied design settings
     *
     * @deprecated after 1.4.2.0-beta1
     * @var array
     */
    protected $_designProductSettingsApplied = array();

    /**
     * Initialize requested product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct() {
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId = (int) $this->getRequest()->getParam('id');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);

        return Mage::helper('catalog/product')->initProduct($productId, $this, $params);
    }

    /**
     * Initialize product view layout
     *
     * @param   Mage_Catalog_Model_Product $product
     * @return  Mage_Catalog_ProductController
     */
    protected function _initProductLayout($product) {
        Mage::helper('catalog/product_view')->initProductLayout($product, $this);
        return $this;
    }

    /**
     * Recursively apply custom design settings to product if it's container
     * category custom_use_for_products option is setted to 1.
     * If not or product shows not in category - applyes product's internal settings
     *
     * @deprecated after 1.4.2.0-beta1, functionality moved to Mage_Catalog_Model_Design
     * @param Mage_Catalog_Model_Category|Mage_Catalog_Model_Product $object
     * @param Mage_Core_Model_Layout_Update $update
     */
    protected function _applyCustomDesignSettings($object, $update) {
        if ($object instanceof Mage_Catalog_Model_Category) {
            // lookup the proper category recursively
            if ($object->getCustomUseParentSettings()) {
                $parentCategory = $object->getParentCategory();
                if ($parentCategory && $parentCategory->getId() && $parentCategory->getLevel() > 1) {
                    $this->_applyCustomDesignSettings($parentCategory, $update);
                }
                return;
            }

            // don't apply to the product
            if (!$object->getCustomApplyToProducts()) {
                return;
            }
        }

        if ($this->_designProductSettingsApplied) {
            return;
        }

        $date = $object->getCustomDesignDate();
        if (array_key_exists('from', $date) && array_key_exists('to', $date) && Mage::app()->getLocale()->isStoreDateInInterval(null, $date['from'], $date['to'])
        ) {
            if ($object->getPageLayout()) {
                $this->_designProductSettingsApplied['layout'] = $object->getPageLayout();
            }
            $this->_designProductSettingsApplied['update'] = $object->getCustomLayoutUpdate();
        }
    }

    /**
     * Product view action
     */
    public function viewAction() {
        // Get initial data from request
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        // Prepare helper and params
        $viewHelper = Mage::helper('catalog/product_view');

        $params = new Varien_Object();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        // Render page
        try {
            $viewHelper->prepareAndRender($productId, $this, $params);
        } catch (Exception $e) {
            if ($e->getCode() == $viewHelper->ERR_NO_PRODUCT_LOADED) {
                if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
                    $this->_redirect('');
                } elseif (!$this->getResponse()->isRedirect()) {
                    $this->_forward('noRoute');
                }
            } else {
                Mage::logException($e);
                $this->_forward('noRoute');
            }
        }
    }

    /**
     * View product gallery action
     */
    public function galleryAction() {
        if (!$this->_initProduct()) {
            if (isset($_GET['store']) && !$this->getResponse()->isRedirect()) {
                $this->_redirect('');
            } elseif (!$this->getResponse()->isRedirect()) {
                $this->_forward('noRoute');
            }
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Display product image action
     *
     * @deprecated
     */
    public function imageAction() {
        /*
         * All logic has been cut to avoid possible malicious usage of the method
         */
        $this->_forward('noRoute');
    }

    /**
     * upload product image action
     * @return string|int
     */
    public function uploadPhotoAction() {
        $io = new Varien_Io_File();
        $io->checkAndCreateFolder(Mage::getBaseDir('media').DS.'catalog'.DS.'product'.DS.'images');

        $uploaddir = Mage::getBaseDir() . '/media/catalog/product/images/';
        $file_name = time() . '-' . $_FILES['image']['name'];
        $file = $uploaddir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $file)) {
            return $file_name;
        } else {
            return 0;
        }
    }

    /**
     * product add action
     */
    public function addfrontAction() {
        ini_set("memory_limit", "5000M");
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $arr = array();
        $dat = array_filter(Mage::getStoreConfig('ng_product/product_settings'));       
        $required = explode(',', Mage::getStoreConfig('ng_product/product_required_settings/required'));
        foreach ($required as $key => $value) {
           if(array_key_exists($value, $dat)){
               $arr[] = $value;
           }
        }
        $key = array_keys($_POST, '');
        $result = array_intersect($arr, $key);
        
        if (is_array($result) && !empty($result)) {
            Mage::getSingleton('core/session')->addError('Product submition failed. Please fill all required fields.');
            header('Location: ' . Mage::getBaseUrl() . 'product');
            exit;
        } else {
            $name = !empty($_POST['name']) ? $_POST['name'] : '';
            $sku = !empty($_POST['sku']) ? $_POST['sku'] : '';
            $short_description = !empty($_POST['short_description']) ? $_POST['short_description'] : '';
            $description = !empty($_POST['description']) ? $_POST['description'] : '';
            $weight = !empty($_POST['weight']) ? $_POST['weight'] : '';
            $status = !empty($_POST['status']) ? $_POST['status'] : '';
            $url_key = !empty($_POST['url_key']) ? $_POST['url_key'] : '';
            $visibility = !empty($_POST['visibility']) ? $_POST['visibility'] : '';
            $price = !empty($_POST['price']) ? $_POST['price'] : '';
            $tax_class = !empty($_POST['tax_class']) ? $_POST['tax_class'] : '';
            $meta_title = !empty($_POST['meta_title']) ? $_POST['meta_title'] : '';
            $meta_keyword = !empty($_POST['meta_keyword']) ? $_POST['meta_keyword'] : '';
            $meta_description = !empty($_POST['meta_description']) ? $_POST['meta_description'] : '';
            $qty = !empty($_POST['qty']) ? $_POST['qty'] : '';
            $manage_stock = !empty($_POST['manage_stock']) ? $_POST['manage_stock'] : '';
            $is_in_stock = !empty($_POST['is_in_stock']) ? $_POST['is_in_stock'] : '';
            $categories = !empty($_POST['categories']) ? $_POST['categories'] : '';
            $websites = !empty($_POST['websites']) ? $_POST['websites'] : '';
            $terms = !empty($_POST['terms']) ? $_POST['terms'] : '';
            $customer_group = !empty($_POST['customer_group']) ? $_POST['customer_group'] : '';
            $customer_email = !empty($_POST['customer_email']) ? $_POST['customer_email'] : '';
            $image = '';
            if (isset($_FILES['image']) && !empty($_FILES['image'])) {
                if ($filename = $this->uploadPhotoAction())
                    $image = $filename;
            }

            $customer_group = new Mage_Customer_Model_Group();
            $allGroups = $customer_group->getCollection()->toOptionHash();
            foreach ($allGroups as $key => $allGroup) {
                $customerGroup[$key] = $allGroup;
            }
            $customer_group = array_search($customer_group, $customerGroup);

            $soapUrl = Mage::getBaseUrl() . 'api/soap/?wsdl';
            $client = new SoapClient($soapUrl);

            $username = Mage::getStoreConfig('ng_product/product_credentials_settings/username');
            $password = Mage::getStoreConfig('ng_product/product_credentials_settings/password');
            
            $session = $client->login($username, $password);
            
            $attributeSets = $client->call($session, 'product_attribute_set.list');
            $attributeSet = current($attributeSets);

            $product = new Mage_Catalog_Model_Product();

            $productId = Mage::getModel('catalog/product')->getIdBySku($sku);
            try {
                $result = $client->call($session, 'catalog_product.create', array('simple', $attributeSet['set_id'], $sku, array(
                        'categories' => array($categories),
                        'websites' => array($websites),
                        'name' => $name,
                        'description' => $description,
                        'short_description' => $short_description,
                        'weight' => $weight,
                        'status' => $status,
                        'url_key' => str_replace(' ', '_', $url_key),
                        'url_path' => str_replace(' ', '_', $url_key),
                        'visibility' => $visibility,
                        'price' => $price,
                        'tax_class_id' => $tax_class,
                        'meta_title' => $meta_title,
                        'meta_keyword' => $meta_keyword,
                        'meta_description' => $meta_description,
                        'front_image' => $image
                )));
            } catch (SoapFault $fault) {
                Mage::getSingleton('core/session')->addError($fault->getMessage());
                header('Location: ' . Mage::getBaseUrl() . 'product');
                exit;
            }

            $productId = Mage::getModel('catalog/product')->getIdBySku($sku);
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            $stockItemId = $stockItem->getId();
            $stockItem->setData('manage_stock', $manage_stock);
            $stockItem->setData('qty', $qty);
            $stockItem->setData('is_in_stock', $is_in_stock);
            $stockItem->save();

            $product = Mage::getModel('catalog/product')->load($productId);

            $imagePath = Mage::getBaseDir('media') . '/catalog/product/images/' . $image;
            $imageResized = Mage::getBaseDir('media') . '/catalog/product/compress/' . $image;


            if (!file_exists($imageResized) && file_exists($imagePath)) {
                $imageObj = new Varien_Image($imagePath);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(TRUE);
                $imageObj->keepFrame(FALSE);
                $imageObj->resize(600);
                $imageObj->save($imageResized);

                $product->setMediaGallery(array('images' => array(), 'values' => array()));
                $product->addImageToMediaGallery($imageResized, array('image', 'small_image', 'thumbnail'), false, false);
                $product->save();
            }
            Mage::getSingleton('core/session')->addSuccess('Product submition successful.');
            header('Location: ' . Mage::getBaseUrl() . 'product');
            exit;
        }
    }

}
