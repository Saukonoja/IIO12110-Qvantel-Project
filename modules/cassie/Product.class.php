<?php
	class Product{
		public $category_id = '';
		public $product_id;
		public $price;
		public $image_link;
		public $description;
		public $amount;
		//public $priority;
		//public $times_viewed;
		//public $times_purchased;
		//public $os_segment;
		//public $country_segment;
		//public $age_segment;
		//public $wealth_segment;

		public function __construct($category_id, $product_id, $price, $image_link, $description, $amount){
			$this->category_id = $category_id;
			$this->product_id = $product_id;
			$this->price = $price;
			$this->image_link = $image_link;
			$this->description = $description;
			$this->amount = $amount;
		}

		/*public function getCategory_id(){
			//return 'foo';
			return $this->category_id;
		}

		public function getProduct_id(){
			return $this->product_id;
		}

		public function getPrice(){
			return $this->price;
		}

		public function getImage_link(){
			return $this->image_link;
		}

		public function getDescription(){
			return $this->description;
		}

		public function getAmount(){
			return $this->amount;
		}*/
	}
