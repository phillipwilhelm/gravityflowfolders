<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Folders_Submit {
	/**
	 * @param $form_id
	 * @param Gravity_Flow_Folder $folder
	 * @param array $args
	 */
	public static function render_form( $form_id, $folder, $args ) {
		$list_url = remove_query_arg( 'folder' );
		$folder_url = remove_query_arg( 'id' );
		$defaults = array(
			'breadcrumbs' => true,
		);

		$args = array_merge( $defaults, $args );

		if ( $args['breadcrumbs'] ) {
			?>
			<h2>
				<i class="fa fa-folder-open-o"></i>
				<a href="<?php echo esc_url( $list_url ); ?>">Folders</a>
				<i class="fa fa-long-arrow-right" style="color:silver"></i>
				<i class="fa fa-folder-open-o"></i>
				<a href="<?php echo esc_url( $folder_url ); ?>"><?php echo $folder->get_name(); ?></a>

			</h2>
			<?php
		}
		gravity_form_enqueue_scripts( $form_id );
		gravity_form( $form_id );

	}
}
