<?php

/**
 * Gravity Flow Add Entry Step
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Step
 * @copyright   Copyright (c) 2015, Steven Henty
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0
 */

if ( class_exists( 'Gravity_Flow_Step' ) ) {

	class Gravity_Flow_Step_Folders extends Gravity_Flow_Step {
		public $_step_type = 'folders';

		public function get_label() {
			return esc_html__( 'Add to Folder', 'gravityflowformconnector' );
		}

		public function get_icon_url() {
			return '<i class="fa fa fa-folder-o" style="color:darkgreen"></i>';
		}

		/**
		 * Returns the settings for this step.
		 *
		 * @return array
		 */
		public function get_settings() {
			$fields = array();

			$folders = gravity_flow_folders()->get_folders();

			$folder_choices = array();
			foreach ( $folders as $folder ) {
				if ( $folder->get_type() == 'list' ) {
					$label = $folder->get_name();

					$folder_choices[] = array(
						'label' => $label,
						'name'  => $folder->get_meta_key(),
					);
				}
			}

			if ( ! empty( $folder_choices ) ) {
				$fields[] = array(
					'name'     => 'folders',
					'required' => true,
					'label'    => esc_html__( 'Folders', 'gravityflowfolders' ),
					'type'     => 'checkbox',
					'choices'  => $folder_choices,
				);
			}

			if ( empty( $fields ) ) {
				$html     = esc_html__( "You don't have any folders set up.", 'gravityflow' );
				$fields[] = array(
					'name'  => 'no_folders',
					'label' => esc_html__( 'Folders', 'gravityflowfolders' ),
					'type'  => 'html',
					'html'  => $html,
				);
			}

			return array(
				'title'  => $this->get_label(),
				'fields' => $fields,
			);
		}

		public function get_folders() {
			return gravity_flow_folders()->get_folders();
		}

		function process() {
			// add to folders

			$folders = $this->get_folders();

			foreach ( $folders as $folder ) {
				$setting_key = $folder->get_meta_key();
				if ( $this->{$setting_key} ) {
					$entry_id = $this->get_entry_id();
					$folder->add_entry( $entry_id );
					$label = $folder->get_name();
					$note = sprintf( esc_html__( 'Added to folder: %s', 'gravityflow' ), $label );

					$this->add_note( $note, 0, $this->get_type() );
				}
			}

			return true;
		}
	}
}



