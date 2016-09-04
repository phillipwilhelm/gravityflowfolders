<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Folder_List extends Gravity_Flow_Folder {

	protected $entries = null;

	public function get_entries( $search_criteria, $sorting = array(), $paging = array() ) {

		if ( isset( $this->entries ) ) {
			return $this->entries;
		}

		$entries = GFAPI::get_entries( 0, $search_criteria, $sorting, $paging, $total_count );

		$this->entries = $entries;
		return $this->entries;
	}

	public function render( $args = array() ) {

		require_once( gravity_flow()->get_base_path() . '/includes/pages/class-status.php' );

		$defaults = array(
			'action_url'         => admin_url( 'admin.php?page=gravityflow-folders&folder=' . $this->get_id() ),
			'base_url' => admin_url( 'admin.php?page=gravityflow-folders&folder=' . $this->get_id() ),
			'detail_base_url'   => admin_url( 'admin.php?page=gravityflow-inbox&view=entry&folder=' . $this->get_id() ),
			'filter_hidden_fields' => array(),
			'constraint_filters' => array(),
			'display_all' => true,
		);

		$args = array_merge( $defaults, $args );

		if ( empty( $args['constraint_filters'] ) ) {
			$args['constraint_filters'] = array(
				'field_filters' => array(
					array(
						'key'      => $this->get_meta_key(),
						'value'    => 0,
						'operator' => '>',
					),
				),
			);
		}

		if ( empty( $args['filter_hidden_fields'] ) ) {
			$args['filter_hidden_fields'] = array(
				'page' => 'gravityflow-folders',
				'folder' => $this->get_id(),
			);
		}


		Gravity_Flow_Status::render( $args );
	}
}
