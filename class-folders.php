<?php
/**
 * Gravity Flow Folders
 *
 *
 * @package     GravityFlow
 * @subpackage  Classes/Extension
 * @copyright   Copyright (c) 2015, Steven Henty
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       1.0
 */

// Make sure Gravity Forms is active and already loaded.
if ( class_exists( 'GFForms' ) ) {

	class Gravity_Flow_Folders extends Gravity_Flow_Extension {

		private static $_instance = null;

		public $_version = GRAVITY_FLOW_FOLDERS_VERSION;

		public $edd_item_name = GRAVITY_FLOW_FOLDERS_EDD_ITEM_NAME;

		// The Framework will display an appropriate message on the plugins page if necessary
		protected $_min_gravityforms_version = '1.9.10';

		protected $_slug = 'gravityflowfolders';

		protected $_path = 'gravityflowfolders/folders.php';

		protected $_full_path = __FILE__;

		// Title of the plugin to be used on the settings page, form settings and plugins page.
		protected $_title = 'Folders Extension';

		// Short version of the plugin title to be used on menus and other places where a less verbose string is useful.
		protected $_short_title = 'Folders';

		protected $_capabilities = array(
			'gravityflowfolders_folders',
			'gravityflowfolders_uninstall',
			'gravityflowfolders_settings',
			'gravityflowfolders_user_admin',
		);

		protected $_capabilities_app_settings = 'gravityflowfolders_settings';
		protected $_capabilities_uninstall = 'gravityflowfolders_uninstall';

		public static function get_instance() {
			if ( self::$_instance == null ) {
				self::$_instance = new Gravity_Flow_Folders();
			}

			return self::$_instance;
		}

		private function __clone() {
		} /* do nothing */

		public function init() {
			parent::init();
			add_filter( 'gravityflow_permission_granted_entry_detail', array( $this, 'filter_gravityflow_permission_granted_entry_detail' ), 10, 4 );
		}

		public function init_frontend() {
			parent::init_frontend();
			add_filter( 'gravityflow_shortcode_folders', array( $this, 'shortcode' ), 10, 2 );
			add_filter( 'gravityflow_enqueue_frontend_scripts', array( $this, 'action_gravityflow_enqueue_frontend_scripts' ), 10 );
		}

		public function init_admin() {
			parent::init_admin();
			if ( $this->current_user_can_any( 'gravityflowfolders_user_admin' ) ) {
				add_filter( 'user_row_actions', array( $this, 'filter_user_row_actions' ), 10, 2 );
			}
		}

		public function scripts() {
			if ( $this->is_settings_page() ) {
				$forms = GFFormsModel::get_forms();

				$form_choices = array( array( 'value' => '', 'label' => __( 'Select a form', 'gravityflowfolders' ) ) );
				foreach ( $forms as $form ) {
					$form_choices[] = array(
						'value' => $form->id,
						'label' => $form->title,
					);
				}

				$user_choices = $this->get_users_as_choices();
				$scripts[] = array(
					'handle'  => 'gravityflowfolders_settings_js',
					'src'     => $this->get_base_url() . "/js/folder-settings-build{$min}.js",
					'version' => $this->_version,
					'deps'    => array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-tabs' ),
					'enqueue' => array(
						array( 'query' => 'page=gravityflow_settings&view=gravityflowfolders' ),
					),
					'strings' => array(
						'vars'              => array(
							'forms'       => $form_choices,
							'userChoices' => $user_choices,
						),
						'folderName'        => __( 'Name', 'gravityflowfolders' ),
						'customLabel'       => __( 'Custom Label', 'gravityflowfolders' ),
						'forms'             => __( 'Forms', 'gravityflowfolders' ),
						'entryList'         => __( 'Entry List', 'gravityflowfolders' ),
						'checklist'         => __( 'Personal Checklist', 'gravityflowfolders' ),
						'sequential'        => __( 'Sequential', 'gravityflowfolders' ),
						'noItems'           => __( "You don't have any folders.", 'graviytflowfolders' ),
						'addOne'            => __( "Let's add one", 'graviytflowfolders' ),
						'areYouSure'        => __( 'This item will be deleted. Are you sure?', 'graviytflowfolders' ),
						'defaultFolderName' => __( 'New Folder', 'graviytflowfolders' ),
						'allUsers'          => __( 'All Users', 'gravityflowfolders' ),
						'selectUsers'       => __( 'Select Users', 'gravityflowfolders' ),
					),
				);
			}

			$scripts[] = array(
				'handle'  => 'gravityflow_status_list',
				'src'     => gravity_flow()->get_base_url() . "/js/status-list{$min}.js",
				'deps'    => array( 'jquery', 'gform_field_filter' ),
				'version' => $this->_version,
				'enqueue' => array(
					array(
						'query' => 'page=gravityflow-folders&folder=_notempty_',
					),
				),
				'strings' => array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ),
			);

			return array_merge( parent::scripts(), $scripts );
		}

		public function styles() {
			$styles = array(
				array(
					'handle'  => 'gravityflowfolders_settings_css',
					'src'     => $this->get_base_url() . "/css/settings{$min}.css",
					'version' => $this->_version,
					'enqueue' => array(
						array( 'query' => 'page=gravityflow_settings&view=gravityflowfolders' ),
					),
				),
				array(
					'handle'  => 'gform_admin',
					'src'     => GFCommon::get_base_url() . "/css/admin{$min}.css",
					'version' => GFForms::$version,
					'enqueue' => array(
						array(
							'query' => 'page=gravityflow-folders',
						),
					),
				),
				array(
					'handle'  => 'gravityflowfolders_folders',
					'src'     => $this->get_base_url() . "/css/folders{$min}.css",
					'version' => GFForms::$version,
					'enqueue' => array(
						array(
							'query' => 'page=gravityflow-folders',
						),
					),
				),
				array(
					'handle'  => 'gravityflow_status',
					'src'     => gravity_flow()->get_base_url() . "/css/status{$min}.css",
					'version' => $this->_version,
					'enqueue' => array(
						array(
							'query' => 'page=gravityflow-folders&folder=_notempty_',
						),
					),
				),
			);

			return array_merge( parent::styles(), $styles );
		}


		public function app_settings_fields() {
			$settings   = parent::app_settings_fields();
			$settings[] = array(
				'title'  => esc_html__( 'Configuration', 'gravityflowfolders' ),
				'fields' => array(
					array(
						'name'  => 'folders',
						'label' => esc_html__( 'Folders', 'gravityflowfolders' ),
						'type'  => 'folders',
					),
				),
			);

			return $settings;
		}


		public function get_entry_meta( $entry_meta, $form_id ) {
			$folders = $this->get_folders();
			foreach ( $folders as $folder ) {
				$meta_key                = $folder->get_meta_key();
				$entry_meta[ $meta_key ] = array(
					'label'             => $folder->get_name(),
					'is_numeric'        => true,
					'is_default_column' => false,
					'filter'            => array(
						'operators' => array( '>', '<' ),
					),
				);
			}

			return $entry_meta;
		}

		public function get_folder_settings() {
			$settings        = $this->get_app_settings();
			$folder_settings = isset( $settings['folders'] ) ? $settings['folders'] : array();

			return $folder_settings;
		}

		public function settings_folders() {
			$hidden_field = array(
				'name'          => 'folders',
				'default_value' => '[]',
			);
			$this->settings_hidden( $hidden_field );
			?>
			<div id="gravityflowfolders-folders-settings-ui">
				<!-- placeholder for custom fields UI -->
			</div>
			<script>
				jQuery.ajax({
					url: '/wp-json/gf/v2/entries/652',
					method: 'GET',
					beforeSend: function (xhr) {
						xhr.setRequestHeader('X-WP-Nonce', <?php echo json_encode( wp_create_nonce( 'wp_rest' ) ); ?> )
					},
					dataType: 'json',
					success: function (data) {
						console.log(data);
					}
				});
			</script>
			<?php
		}

		public function menu_items( $menu_items ) {
			$folders_menu = array(
				'name'       => 'gravityflow-folders',
				'label'      => esc_html__( 'Folders', 'gravityflowfolders' ),
				'permission' => 'gravityflowfolders_folders',
				'callback'   => array( $this, 'folders' ),
			);

			$index = 3;

			$first_bit = array_slice( $menu_items, 0, $index, true );

			$last_bit = array_slice( $menu_items, $index, count( $menu_items ) - $index, true );

			$menu_items = array_merge( $first_bit, array( $folders_menu ), $last_bit );

			return $menu_items;
		}

		public function toolbar_menu_items( $menu_items ) {

			$active_class     = 'gf_toolbar_active';
			$not_active_class = '';

			$menu_items['folders'] = array(
				'label'        => esc_html__( 'Folders', 'gravityflowfolders' ),
				'icon'         => '<i class="fa fa fa-folder-o fa-lg"></i>',
				'title'        => __( 'Folders', 'gravityflow' ),
				'url'          => '?page=gravityflow-folders',
				'menu_class'   => 'gf_form_toolbar_settings',
				'link_class'   => ( rgget( 'page' ) == 'gravityflow-folders' ) ? $active_class : $not_active_class,
				'capabilities' => 'gravityflowfolders_folders',
				'priority'     => 850,
			);

			return $menu_items;
		}

		public function folders() {
			$args = array(
				'display_header' => true,
			);
			$this->folders_page( $args );
		}

		public function folders_page( $args ) {
			$defaults = array(
				'display_header' => true,
				'breadcrumbs' => true,
			);
			$args = array_merge( $defaults, $args );
			?>
			<div class="wrap gf_entry_wrap gravityflow_workflow_wrap gravityflow_workflow_submit">
				<?php if ( $args['display_header'] ) : ?>
					<h2 class="gf_admin_page_title">
						<img width="45" height="22"
						     src="<?php echo gravity_flow()->get_base_url(); ?>/images/gravityflow-icon-blue-grad.svg"
						     style="margin-right:5px;"/>

						<span><?php esc_html_e( 'Folders', 'gravityflow' ); ?></span>

					</h2>
					<?php
					$this->toolbar();
				endif;

				require_once( $this->get_base_path() . '/includes/class-folders-page.php' );
				Gravity_Flow_Folders_Page::render( $args );
				?>
			</div>
			<?php
		}

		public function toolbar() {
			gravity_flow()->toolbar();
		}

		/**
		 * @param WP_User|null $user
		 *
		 * @return Gravity_Flow_Folder[]
		 */
		public function get_folders( WP_User $user = null ) {


			$folder_configs = $this->get_folder_settings();

			$folder_configs = apply_filters( 'gravityflowfolders_folders', $folder_configs );

			$folders = array();

			$folder = null;

			foreach ( $folder_configs as $folder_config ) {
				switch ( $folder_config['type'] ) {
					case 'checklist' :
						$folder = new Gravity_Flow_Folder_Checklist( $folder_config, $user );
						break;
					case 'list' :
						$folder = new Gravity_Flow_Folder_List( $folder_config, $user );
				}
				$folders[] = $folder;
			}

			return $folders;
		}

		/**
		 * Get Folder by ID or Name.
		 *
		 * @param string $folder_id
		 * @param WP_User @user
		 *
		 * @return bool|Gravity_Flow_Folder
		 */
		public function get_folder( $folder_id, WP_User $user = null ) {
			$folders = $this->get_folders( $user );

			foreach ( $folders as $folder ) {
				if ( $folder->get_id() == $folder_id || strtolower( $folder->get_name() ) == strtolower( $folder_id ) ) {
					return $folder;
				}
			}

			return false;
		}

		public static function get_entry_count_per_form( $user_id, $form_ids ) {
			global $wpdb;
			$lead_table_name = GFFormsModel::get_lead_table_name();

			$cache_key = 'folders:entries_by_form_by_user_' . $user_id;

			$entry_count = GFCache::get( $cache_key );
			if ( empty( $entry_count ) ) {
				//Getting entry count per form
				$sql         = $wpdb->prepare( "SELECT form_id, count(id) as lead_count FROM $lead_table_name l WHERE status='active' AND created_by = %d GROUP BY form_id", $user_id );
				$entry_count = $wpdb->get_results( $sql );

				GFCache::set( $cache_key, $entry_count, true, 60 );
			}

			return $entry_count;
		}

		/**
		 * Adds the Folders action item to the User actions.
		 *
		 * @param array $actions An array of action links to be displayed.
		 *                             Default 'Edit', 'Delete' for single site, and
		 *                             'Edit', 'Remove' for Multisite.
		 * @param WP_User $user_object WP_User object for the currently-listed user.
		 *
		 * @return array $actions
		 */
		public function filter_user_row_actions( $actions, $user_object ) {

			$user_object->ID;
			$url                             = admin_url( 'admin.php?page=gravityflow-folders&user_id=' . $user_object->ID );
			$url                             = esc_url_raw( $url );
			$new_actions['workflow_folders'] = "<a href='" . $url . "'>" . __( 'Folders' ) . '</a>';

			return array_merge( $new_actions, $actions );
		}

		public function shortcode( $html, $atts ) {

			$default_shortcode_atts = gravity_flow()->get_shortcode_defaults();

			$default_shortcode_atts['folder'] = '';

			$a = shortcode_atts( $default_shortcode_atts, $atts );

			if ( rgget( 'view' ) ) {
				wp_enqueue_script( 'gravityflow_entry_detail' );
				$html .= $this->get_shortcode_folders_page_entry_detail( $a );
			} else {
				$html .= $this->get_shortcode_folders_page( $a );
			}

			return $html;
		}

		/**
		 * Returns the markup for the folders page.
		 *
		 * @param $a
		 *
		 * @return string
		 */
		public function get_shortcode_folders_page( $a ) {
			if ( ! class_exists( 'WP_Screen' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/screen.php' );
			}
			require_once( ABSPATH . 'wp-admin/includes/template.php' );

			$check_permissions = true;

			if ( $a['allow_anonymous'] || $a['display_all'] ) {
				$check_permissions = false;
			}

			$detail_base_url = add_query_arg( array( 'page' => 'gravityflow-inbox', 'view' => 'entry' ) );
			$args = array(
				'display_header'    => false,
				'detail_base_url'   => $detail_base_url,
				'check_permissions' => $check_permissions,
			);

			$folder = sanitize_text_field( rgget( 'folder' ) );

			if ( empty( $folder ) ) {
				$folder = rgar( $a, 'folder' );
			}

			$args['folder'] = $folder;

			if ( ! empty( $a['folder'] ) ) {
				$args['breadcrumbs'] = false;
			}

			wp_enqueue_script( 'gravityflow_status_list' );
			ob_start();
			$this->folders_page( $args );
			$html = ob_get_clean();

			return $html;
		}

		/**
		 * Returns the markup for the folders shortcode detail page.
		 *
		 * @param $a
		 *
		 * @return string
		 */
		public function get_shortcode_folders_page_entry_detail( $a ) {

			ob_start();
			$check_permissions = true;

			if ( $a['allow_anonymous'] || $a['display_all'] ) {
				$check_permissions = false;
			}

			$args = array(
				'show_header'       => false,
				'detail_base_url'   => add_query_arg( array( 'page' => 'gravityflow-inbox', 'view' => 'entry' ) ),
				'check_permissions' => $check_permissions,
				'timeline'          => $a['timeline'],
			);

			gravity_flow()->inbox_page( $args );
			$html = ob_get_clean();

			return $html;
		}

		public function filter_gravityflow_permission_granted_entry_detail( $permission_granted, $entry, $form, $current_step ) {
			if ( ! $permission_granted ) {
				if ( isset( $_GET['folder'] ) ) {
					$folder_id = sanitize_text_field( $_GET['folder'] );
					if ( ! empty( $entry[ 'workflow_folder_' . $folder_id ] ) ) {
						$folder = $this->get_folder( $folder_id );
						if ( $folder->user_is_assignee() ) {
							$permission_granted = true;
						}
					}
				} else {
					$folders = $this->get_folders();
					foreach ( $folders as $folder ) {
						if ( ! empty( $entry[ 'workflow_folder_' . $folder->get_id() ] ) ) {
							if ( $folder->user_is_assignee() ) {
								$permission_granted = true;
								break;
							}
						}
					}
				}
			}
			return $permission_granted;
		}

		public function get_users_as_choices() {
			$editable_roles = array_reverse( get_editable_roles() );

			$role_choices = array();
			foreach ( $editable_roles as $role => $details ) {
				$name           = translate_user_role( $details['name'] );
				$role_choices[] = array( 'value' => 'role|' . $role, 'label' => $name );
			}

			$args            = apply_filters( 'gravityflow_get_users_args', array( 'orderby' => 'display_name' ) );
			$accounts        = get_users( $args );
			$account_choices = array();
			foreach ( $accounts as $account ) {
				$account_choices[] = array( 'value' => 'user_id|' . $account->ID, 'label' => $account->display_name );
			}

			$choices = array(
				array(
					'label'   => __( 'Users', 'gravityflow' ),
					'choices' => $account_choices,
				),
				array(
					'label'   => __( 'Roles', 'gravityflow' ),
					'choices' => $role_choices,
				),
			);

			return $choices;
		}

		public function is_settings_page() {
			return is_admin() && rgget( 'page' ) == 'gravityflow_settings' && rgget( 'view' ) == 'gravityflowfolders';
		}

		public function action_gravityflow_enqueue_frontend_scripts() {
			wp_enqueue_style( 'gravityflowfolders_folders',  $this->get_base_url() . "/css/folders{$min}.css", null, $this->_version );
		}
	}
}
