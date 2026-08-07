<?php


/**
 * Class Cctor__Coupon__Plugin_Register
 */
class Cctor__Coupon__Plugin_Register extends Pngx__Abstract_Plugin_Register {

	protected $main_class   = 'Cctor__Coupon__Main';
	protected $dependencies = array(
		'addon-dependencies' => array(
			'Cctor__Coupon__Pro__Main'    => Cctor__Coupon__Main::MIN_PRO_VERSION,
			'Cctor__Coupon__Addons__Main' => Cctor__Coupon__Main::MIN_ADDONS_VERSION,
		),
	);

	public function __construct() {
		$this->base_dir = COUPON_CREATOR_MAIN_PLUGIN_FILE;
		$this->version  = Cctor__Coupon__Main::VERSION_NUM;

		$this->register_plugin();
	}
}