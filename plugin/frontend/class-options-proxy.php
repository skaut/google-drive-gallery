<?php
namespace Sgdg\Frontend;

class Options_Proxy {
	public $overriden;
	private $option_list;
	private $ordering_option_list;

	public function __construct( $overriden = [] ) {
		$this->option_list          = [
			'grid_height'                => \Sgdg\Options::$grid_height,
			'grid_spacing'               => \Sgdg\Options::$grid_spacing,
			'dir_title_size'             => \Sgdg\Options::$dir_title_size,
			'dir_counts'                 => \Sgdg\Options::$dir_counts,

			'preview_size'               => \Sgdg\Options::$preview_size,
			'preview_speed'              => \Sgdg\Options::$preview_speed,
			'preview_arrows'             => \Sgdg\Options::$preview_arrows,
			'preview_close_button'       => \Sgdg\Options::$preview_close_button,
			'preview_loop'               => \Sgdg\Options::$preview_loop,
			'preview_activity_indicator' => \Sgdg\Options::$preview_activity_indicator,
		];
		$this->ordering_option_list = [
			'image_ordering' => \Sgdg\Options::$image_ordering,
			'dir_ordering'   => \Sgdg\Options::$dir_ordering,
		];
		$this->overriden            = [];
		if ( is_array( $overriden ) ) {
			foreach ( $overriden as $key => $value ) {
				if ( array_key_exists( $key, $this->option_list ) ) {
					$this->overriden[ $key ] = $value;
				}
				if ( substr( $key, -6 ) === '_order' ) {
					if ( array_key_exists( substr( $key, 0, -6 ), $this->ordering_option_list ) ) {
						$this->overriden[ $key ] = $value;
					}
				}
				if ( substr( $key, -3 ) === '_by' ) {
					if ( array_key_exists( substr( $key, 0, -3 ), $this->ordering_option_list ) ) {
						$this->overriden[ $key ] = $value;
					}
				}
			}
		}
	}

	public function get( $name, $default_value = null ) {
		if ( array_key_exists( $name, $this->overriden ) ) {
			return $this->overriden[ $name ];
		}
		if ( array_key_exists( $name . '_order', $this->overriden ) && array_key_exists( $name . '_by', $this->overriden ) ) {
			return ( 'name' === $this->overriden[ $name . '_by' ] ? 'name_natural' : 'modifiedTime' ) . ( 'ascending' === $this->overriden[ $name . '_order' ] ? '' : ' desc' );
		}
		if ( array_key_exists( $name, $this->option_list ) ) {
			return $this->option_list[ $name ]->get( $default_value );
		}
		if ( array_key_exists( $name, $this->ordering_option_list ) ) {
			return $this->ordering_option_list[ $name ]->get( $default_value );
		}
		return $default_value;
	}

	public function get_title( $name ) {
		if ( array_key_exists( $name, $this->option_list ) ) {
			return $this->option_list[ $name ]->get_title();
		}
		if ( array_key_exists( $name, $this->ordering_option_list ) ) {
			return $this->ordering_option_list[ $name ]->get_title();
		}
		return null;
	}

	public function get_order( $name, $default_value = null ) {
		if ( array_key_exists( $name . '_order', $this->overriden ) ) {
			return $this->overriden[ $name . '_order' ];
		}
		if ( array_key_exists( $name, $this->ordering_option_list ) ) {
			return $this->ordering_option_list[ $name ]->get_order( $default_value );
		}
		return $default_value;
	}

	public function get_by( $name, $default_value = null ) {
		if ( array_key_exists( $name . '_by', $this->overriden ) ) {
			return $this->overriden[ $name . '_by' ];
		}
		if ( array_key_exists( $name, $this->ordering_option_list ) ) {
			return $this->ordering_option_list[ $name ]->get_by( $default_value );
		}
		return $default_value;
	}
}
