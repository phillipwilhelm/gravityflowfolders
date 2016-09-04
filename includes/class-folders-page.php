<?php

class Gravity_Flow_Folders_Page {
	public static function render( $args ) {

		$defaults = array(
			'user_id' => absint( rgget( 'user_id' ) ),
			'folder'  => sanitize_text_field( rgget( 'folder' ) ),
			'form_id' => absint( rgget( 'id' ) ),
		);

		$args = array_merge( $defaults, $args );

		if ( ! empty( $args['user_id'] ) ) {
			$user_id = $args['user_id'];
			$user    = get_user_by( 'ID', $user_id );

		} else {
			$user = wp_get_current_user();
		}

		if ( ! empty( $args['folder'] ) ) {
			$folder_id = $args['folder'];

			$folder = gravity_flow_folders()->get_folder( $folder_id, $user );
			if ( $folder ) {
				$form_id = $args['form_id'];
				if ( empty( $form_id ) ) {
					require_once( gravity_flow_folders()->get_base_path() . '/includes/class-folders-detail.php' );
					Gravity_Flow_Folders_Detail::display( $folder, $args );
				} else {
					require_once( gravity_flow_folders()->get_base_path() . '/includes/class-folders-submit.php' );
					Gravity_Flow_Folders_Submit::render_form( $form_id, $folder, $args );
				}
			} else {
				esc_html_e( 'Folder not found.', 'gravityflow' );
			}
		} else {
			require_once( gravity_flow_folders()->get_base_path() . '/includes/class-folders-list.php' );
			$folders = gravity_flow_folders()->get_folders( $user );
			if ( $folders ) {
				Gravity_Flow_Folders_List::display( $folders, $user );
			} else {
				esc_html_e( "You don't have any folders configured.", 'gravityflow' );
			}
		}
	}
}
