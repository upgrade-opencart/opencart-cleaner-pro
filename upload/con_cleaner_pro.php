<?php
class ControllerExtensionModuleCleanerPro extends Controller {
	private $error = array();

	// Get total options
	public function getTotalOptions() {

        $result = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "option`");

        return $result->row['total'];
        
	}

	// Get total active options
	public function getTotalActiveOptions() {

		$result = $this->db->query("SELECT COUNT(DISTINCT option_id) AS total FROM " . DB_PREFIX . "product_option");

		return $result->row['total'];

	}

	// Delete unused options
	public function deleteUnusedOptions() {

		$results = $this->db->query("SELECT DISTINCT option_id FROM `" . DB_PREFIX . "product_option`");

		$options = array();

        foreach ($results->rows as $result) {

        	$options[]=$result['option_id'];

    	}

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "option` WHERE option_id NOT IN(".implode(",", $options).")");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "option_description` WHERE option_id NOT IN(".implode(",", $options).")");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "option_value` WHERE option_id NOT IN(".implode(",", $options).")");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "option_value_description` WHERE option_id NOT IN(".implode(",", $options).")");

    	$this->session->data['success'] = 'Unused options cleaned successfully.';

		$this->response->redirect($this->url->link('extension/module/cleaner_pro', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
	}

	// Get total attributes
	public function getTotalAttributes() {

        $result = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "attribute");

        return $result->row['total'];
        
	}

	// Get total active attributes
	public function getTotalActiveAttributes() {

		$result = $this->db->query("SELECT COUNT(DISTINCT attribute_id) AS total FROM " . DB_PREFIX . "product_attribute");

		return $result->row['total'];

	}

	// Get total attributes group
	public function getTotalAttributesGroup() {

        $result = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "attribute_group");

        return $result->row['total'];
        
	}

	// Get total active attributes group
	public function getTotalActiveAttributesGroup() {

        $results = $this->db->query("SELECT DISTINCT attribute_id FROM " . DB_PREFIX . "product_attribute");

        $attributes = array();

        foreach ($results->rows as $result) {

        	$attributes[]=$result['attribute_id'];

    	}

        $result = $this->db->query("SELECT DISTINCT attribute_group_id FROM " . DB_PREFIX . "attribute WHERE attribute_id IN(".implode(",", $attributes).")");

        return $result->num_rows;

	}

	// Delete unused attributes
	public function deleteUnusedAttributes() {

		$results = $this->db->query("SELECT DISTINCT attribute_id FROM " . DB_PREFIX . "product_attribute");

		$attributes = array();

        foreach ($results->rows as $result) {

        	$attributes[]=$result['attribute_id'];

    	}

     	$this->db->query("DELETE FROM " . DB_PREFIX . "attribute WHERE attribute_id NOT IN(".implode(",", $attributes).")");

     
     	$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_description WHERE attribute_id NOT IN(".implode(",", $attributes).")");

    	$this->session->data['success'] = 'Unused attributes cleaned successfully.';

		$this->response->redirect($this->url->link('extension/module/cleaner_pro', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
	}

	// Delete unused attributes group
	public function deleteUnusedAttributesGroup() {

		$results = $this->db->query("SELECT DISTINCT attribute_id FROM " . DB_PREFIX . "product_attribute");

        $attributes = array();

        foreach ($results->rows as $result) {

        	$attributes[]=$result['attribute_id'];

    	}

      	$results = $this->db->query("SELECT DISTINCT attribute_group_id FROM " . DB_PREFIX . "attribute WHERE attribute_id IN(".implode(",", $attributes).")");

  		$groups = array();

        foreach ($results->rows as $result) {

        	$groups[]=$result['attribute_group_id'];

    	}

      	$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_group WHERE attribute_group_id NOT IN(".implode(",", $groups).")");

      	$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_group_description WHERE attribute_group_id NOT IN(".implode(",", $groups).")");

      	$this->session->data['success'] = 'Unused attributes groups cleaned successfully.';

		$this->response->redirect($this->url->link('extension/module/cleaner_pro', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));

	}

	// Get directories content
	public function getDirContents($dir, $filter = '', &$results = array()) {

		$files = scandir($dir);
		
    	foreach($files as $key => $value){
	        $path = realpath($dir.DIRECTORY_SEPARATOR.$value); 

	        if(!is_dir($path)) {
	            if(empty($filter) || preg_match($filter, $path)) {						
	            	$results[] = $path;           
				}
	        } elseif($value != "." && $value != "..") {
	            $this->getDirContents($path, $filter, $results);
	        }
	    }

	    return $results;
	}
	
	// Get total images
	public function getTotalImages() {		

		return (count($this->getDirContents(DIR_IMAGE.'catalog'.DIRECTORY_SEPARATOR)));

	}

	// Get total cached images
	public function getTotalCacheImages() {		

		return (count($this->getDirContents(DIR_IMAGE.'cache'.DIRECTORY_SEPARATOR)));

	}

	// Get total product set images
	public function getTotalProductImgImages() {

		$result = $this->db->query("SELECT COUNT(image) AS total FROM `" . DB_PREFIX . "product_image`");

        return $result->row['total'];

	}
	
	// Get total product images
	public function getTotalProductImages() {

		$result = $this->db->query("SELECT COUNT(image) AS total FROM `" . DB_PREFIX . "product` WHERE image != '' " );

        return $result->row['total'];

	}
	
	// Get total category images
	public function getTotalCategoryImages() {

		$result = $this->db->query("SELECT COUNT(image) AS total FROM `" . DB_PREFIX . "category` WHERE image != '' " );

        return $result->row['total'];

	}

	// Delete cached images
	public function deleteCachedImages() {
		
		$files = $this->getDirContents(DIR_IMAGE.'cache'.DIRECTORY_SEPARATOR);
		foreach($files as $file){ // iterate files
		if(is_file($file))
		unlink($file); // delete file
		}
		
		$this->session->data['success'] = 'Cached images cleaned successfully.';
		$this->response->redirect($this->url->link('extension/module/cleaner_pro', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
    }

    // Get total orders
	public function getTotalOrders() {

        $result = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order`");

        return $result->row['total'];
        
	}

	// Get total junk orders
	public function getTotalJunkOrders() {

		$result = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id = '0' " );

		return $result->row['total'];

	}

	// Delete junk orders
	public function deleteJunkOrders() {

		$results = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE order_status_id = '0'");

		$orders = array();

        foreach ($results->rows as $result) {

        	$orders[]=$result['order_id'];

    	}

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_status_id = '0'");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE order_id IN(".implode(",", $orders).")");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "order_option` WHERE order_id IN(".implode(",", $orders).")");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "order_voucher` WHERE order_id IN(".implode(",", $orders).")");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "order_total` WHERE order_id IN(".implode(",", $orders).")");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "order_history` WHERE order_id IN(".implode(",", $orders).")");

     	$this->db->query("DELETE `or`, ort FROM `" . DB_PREFIX . "order_recurring` `or`, `" . DB_PREFIX . "order_recurring_transaction` `ort` WHERE order_id IN(".implode(",", $orders).") AND ort.order_recurring_id = `or`.order_recurring_id");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_transaction` WHERE order_id IN(".implode(",", $orders).")");

    	$this->session->data['success'] = 'Junk orders cleaned successfully.';

		$this->response->redirect($this->url->link('extension/module/cleaner_pro', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));

	}

	// Delete selected status orders
	public function deleteSelectedStatusOrders() {

		$order_status_id = $this->request->get['id'];

		$results = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int)$order_status_id . "'");

		$orders = array();

        foreach ($results->rows as $result) {

        	$orders[]=$result['order_id'];

    	}

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_status_id = '" . (int)$order_status_id . "'");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "order_product` WHERE order_id IN(".implode(",", $orders).")");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "order_option` WHERE order_id IN(".implode(",", $orders).")");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "order_voucher` WHERE order_id IN(".implode(",", $orders).")");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "order_total` WHERE order_id IN(".implode(",", $orders).")");

     	$this->db->query("DELETE FROM `" . DB_PREFIX . "order_history` WHERE order_id IN(".implode(",", $orders).")");

     	$this->db->query("DELETE `or`, ort FROM `" . DB_PREFIX . "order_recurring` `or`, `" . DB_PREFIX . "order_recurring_transaction` `ort` WHERE order_id IN(".implode(",", $orders).") AND ort.order_recurring_id = `or`.order_recurring_id");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_transaction` WHERE order_id IN(".implode(",", $orders).")");

    	$this->session->data['success'] = 'Selected status orders cleaned successfully.';

	}

	// Get total products
	public function getTotalProducts() {

        $result = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product`");

        return $result->row['total'];
        
	}

	// Get total zero quantity products
	public function getZeroQuantityProducts() {

		$result = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` WHERE quantity = '0' " );

		return $result->row['total'];

	}

	// Delete zero quantity products
	public function deleteZeroQuantityProducts() {

		$results = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE quantity = '0'");

		$products = array();

        foreach ($results->rows as $result) {

        	$products[]=$result['product_id'];

    	}

     	$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE quantity = '0'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_recurring WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=IN(".implode(",", $products).")'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE product_id IN(".implode(",", $products).")");

    	$this->session->data['success'] = 'Zero quantity products cleaned successfully.';

		$this->response->redirect($this->url->link('extension/module/cleaner_pro', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));

	}

	// Get disabled products
	public function getDisabledProducts() {

		$result = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` WHERE status = '0' " );

		return $result->row['total'];

	}

	// Delete disabled products
	public function deleteDisabledProducts() {

		$results = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE status = '0'");

		$products = array();

        foreach ($results->rows as $result) {

        	$products[]=$result['product_id'];

    	}

     	$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE status = '0'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_recurring WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id IN(".implode(",", $products).")");
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=IN(".implode(",", $products).")'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE product_id IN(".implode(",", $products).")");

    	$this->session->data['success'] = 'Disabled products cleaned successfully.';

		$this->response->redirect($this->url->link('extension/module/cleaner_pro', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));

	}

	public function index() {
		$this->load->language('extension/module/cleaner_pro');

		$this->document->setTitle($this->language->get('heading_title'));

		// Get order statuses
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		// Get options data
		$data['options_total'] = $this->getTotalOptions();

        $data['options_active_total'] = $this->getTotalActiveOptions();

        $data['options_in_active_total'] = (int)$this->getTotalOptions() - (int)$this->getTotalActiveOptions();

        // Get attributes data
        $data['attributes_total'] = $this->getTotalAttributes();

        $data['attributes_active_total'] = $this->getTotalActiveAttributes();

        $data['attributes_in_active_total'] = (int)$this->getTotalAttributes() - (int)$this->getTotalActiveAttributes();

        $data['attributes_group_total'] = $this->getTotalAttributesGroup();

        $data['active_attributes_group_total'] = $this->getTotalActiveAttributesGroup();

        $data['in_active_attributes_group_total'] = (int)$this->getTotalAttributesGroup() - (int)$this->getTotalActiveAttributesGroup();

        // Get images data
        $data['images_total'] = $this->getTotalImages();

		$data['images_cache_total'] = $this->getTotalCacheImages();

		$data['images_product_total'] = (int)$this->getTotalProductImgImages() + (int)$this->getTotalProductImages() + (int)$this->getTotalCategoryImages();

		// Get orders data
		$data['orders_total'] = $this->getTotalOrders();

        $data['orders_junk_total'] = $this->getTotalJunkOrders();

        // Get products data
		$data['products_total'] = $this->getTotalProducts();

		$data['zero_quantity_products'] = $this->getZeroQuantityProducts();

		$data['disabled_products'] = $this->getDisabledProducts();

        if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['user_token'] = $this->session->data['user_token'];

		// Delete unused options
		$data['delete_option'] = $this->url->link('extension/module/cleaner_pro/deleteUnusedOptions', 'user_token=' . $this->session->data['user_token'], true);

		// Delete unused attributes
		$data['delete_attribute'] = $this->url->link('extension/module/cleaner_pro/deleteUnusedAttributes', 'user_token=' . $this->session->data['user_token'], true);

		// Delete unused attributes group
		$data['delete_attribute_group'] = $this->url->link('extension/module/cleaner_pro/deleteUnusedAttributesGroup', 'user_token=' . $this->session->data['user_token'], true);

		// Delete cached images
		$data['delete_cached_image'] = $this->url->link('extension/module/cleaner_pro/deleteCachedImages', 'user_token=' . $this->session->data['user_token'], true);

		// Delete junk orders
		$data['delete_junk_orders'] = $this->url->link('extension/module/cleaner_pro/deleteJunkOrders', 'user_token=' . $this->session->data['user_token'], true);

		// Delete zero quantity products
		$data['delete_zero_quantity_products'] = $this->url->link('extension/module/cleaner_pro/deleteZeroQuantityProducts', 'user_token=' . $this->session->data['user_token'], true);

		// Delete disabled products
		$data['delete_disabled_products'] = $this->url->link('extension/module/cleaner_pro/deleteDisabledProducts', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/cleaner_pro', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/cleaner_pro', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/cleaner_pro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}