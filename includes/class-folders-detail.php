<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Folders_Detail {
	public static function display( Gravity_Flow_Folder $folder, $args = array() ) {

		$defaults = array(
			'breadbrumbs' => true,
		);

		$args = array_merge( $defaults, $args );

		$is_user_admin = $folder->user->ID !== get_current_user_id();

		if ( ! $is_user_admin && ! $folder->user_is_assignee( $folder->user->ID ) && ! gravity_flow_folders()->current_user_can_any( 'gravityflowfolders_user_admin' ) ) {
			esc_html_e( "You don't have permission to view this folder", 'gravityflowfolders' );
			return;
		}


		$list_url = remove_query_arg( 'folder' );
		if ( $args['breadcrumbs'] ) {
		?>
		<h2>
			<?php
			if ( $is_user_admin ) {
				?>
				<span class="dashicons dashicons-admin-users"></span> <a href="<?php echo admin_url( 'users.php' ); ?>"><?php esc_html_e( 'Users', 'gravityflowfolders' ); ?></a> <i class="fa fa-long-arrow-right" style="color:silver"></i>
				<?php
				$folders_name = $folder->user->display_name;
			} else {
				$folders_name = esc_html__( 'Folders', 'gravityflowfolders' );
			}
			?>
			<i class="fa fa-folder-open-o"></i> <a href="<?php echo esc_url( $list_url ); ?>"><?php echo $folders_name; ?></a> <i class="fa fa-long-arrow-right" style="color:silver"></i> <i class="fa fa-folder-open-o"></i>

			<?php
			echo $folder->get_name();
			?>
		</h2>
			<?php } ?>
		<div class="gravityflowfolders-folder-detail-wrapper <?php echo $folder->get_type(); ?>">
			<?php

			$folder->render( $args );
			?>
		</div>
		<?php
	}
}
