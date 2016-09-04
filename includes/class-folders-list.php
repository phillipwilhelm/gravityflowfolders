<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class Gravity_Flow_Folders_List {

	/**
	 * @param Gravity_Flow_Folder[] $folders
	 * @param WP_User $user
	 */
	public static function display( $folders, $user ) {
		if ( $user->ID !== get_current_user_id() ) {
			?>
			<h2>
			<span class="dashicons dashicons-admin-users"></span> <a href="<?php echo admin_url( 'users.php' ); ?>"><?php esc_html_e( 'Users', 'gravityflowfolders' ); ?></a> <i class="fa fa-long-arrow-right" style="color:silver"></i>
			<?php
			echo  $user->display_name;
			?>
			</h2>
			<?php
		}
		?>

		<div class="gravityflowfolders-folder-wrapper">
			<ul>
				<?php
				foreach ( $folders as $folder ) {
					if ( ! $folder->user_is_assignee( $user->ID ) ) {
						continue;
					}
					echo '<li>';
					$detail_url = add_query_arg( 'folder', $folder->get_id() );
					$detail_url = esc_url( $detail_url );
					?>
					<a href="<?php echo $detail_url; ?>">

						<div class="gravityflowfolders-folder-container">
							<div>
								<i class="gravityflowfolders-folder fa fa-folder-o"></i>
							</div>
							<div>
								<?php
								echo $folder->get_name();
								?>
							</div>
						</div>

					</a>

					<?php
					echo '</li>';
				}
				?>
			</ul>
		</div>
		<?php
	}
}
