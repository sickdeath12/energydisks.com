<?php

class ppSupportPage {


	const HASH = '3292042e1867c7ca2258b0b56f04d416';
	public $blogUrl;
	public $blogUrlNoScheme;
	public $remoteAuthUrl;
	private $examineWidgets = false;
	private $examineImgs = false;
	private $showPhpInfo = false;
	private $p3Data;
	private $showChangeBlogAddressForm = false;
	private $showEditPermalinksForm = false;
	private $showChangeUploadPathForm = false;
	private $notice;
	private $warn;
	private $autoUpdateInfo;


	public function __construct() {
		$this->cachePluginFix();
		$this->remoteAuthUrl   = PROPHOTO_SITE_URL . '?support_page_auth=1';
		$this->blogUrl         = trailingslashit( isset( $_GET['override_url'] ) ? $_GET['override_url'] : pp::site()->url );
		$this->blogUrlNoScheme = ltrim( str_replace( array( 'http://', 'www' ), '', $this->blogUrl ), '.' );

		if ( NrUtil::GET( 'ppblogdata', 'show' ) ) {
			echo $this->dataDump();
			exit();
		}

		if ( !$this->userIsAuthed() ) {
			$this->renderHeader();
			ppUtil::renderView( 'support_login_form', $this->viewData() );
			$this->renderFooter();
			exit();
		}

		if ( !empty( $_POST ) ) {
			$this->processPOST();
		}
	}


	public function render() {
		if ( isset( $_GET['nslookup'] ) ) {
			echo ppUtil::lookupNameservers();
			exit();
		}

		if ( isset( $_GET['export_p3_design'] ) ) {
			$this->exportP3Design();
			exit();
		}

		$this->renderHeader();

		if ( $this->autoUpdateInfo ) {
			echo $this->autoUpdateInfo;
		}

		echo $this->blogData();

		if ( $this->notice ) {
			echo "<p id='notice'>$this->notice</p>";
		}

		if ( $this->warn ) {
			echo "<p id='warn'>$this->warn</p>";
		}

		echo $this->actionBtns();

		if ( $this->examineWidgets ) {
			echo $this->examineWidgets();
		}

		if ( $this->examineImgs ) {
			echo $this->examineImgs();
		}

		if ( $this->showChangeBlogAddressForm ) {
			echo $this->changeBlogAddressForm();
		}

		if ( $this->showEditPermalinksForm ) {
			echo $this->editPermalinksForm();
		}

		if ( $this->showChangeUploadPathForm ) {
			echo $this->changeUploadPathForm();
		}

		if ( $this->p3Data ) {
			echo $this->p3Data;
		}

		if ( $this->showPhpInfo ) {
			phpinfo();
		}

		echo $this->changeOptionForm();
		echo $this->optionsList();

		$this->renderFooter();

		do_action( 'shutdown' );
	}


	private function processPOST() {
		if ( !isset( $_POST['support_action'] ) ) {
			return;
		}

		switch ( $_POST['support_action'] ) {

			case 'force_immediate_update':
				set_transient( 'pp_force_immediate_auto_update', 'true', 60*60*24 * 3 );
				$this->notice = 'ProPhoto will <b>auto-update</b> the next time an admin logs in.';
				break;

			case 'regenerate_static_files':
				ppStorage::saveCustomizations( ppStorage::FORCE_FILE_REGEN );
				$this->notice = 'Static files regenerated.';
				break;

			case 'export_active_design':
				$exportMsg = ppDesignUtil::export( ppActiveDesign::id() );
				$this->notice = str_replace( array( '<h3>', '</h3>', '<p>', '</p>' ), array( '', ': ', '', '' ), $exportMsg );
				break;

			case 'examine_widgets':
				$this->examineWidgets = true;
				break;

			case 'examine_images':
				$this->examineImgs = true;
				break;

			case 'change_blog_address':
				$this->showChangeBlogAddressForm = true;
				break;

			case 'update_blog_address':
				$this->updateBlogAddress();
				break;

			case 'full_php_info':
				$this->showPhpInfo = true;
				break;

			case 'show_widget_edit_form':
				$this->editWidgetForm( $_POST['widget_type'], $_POST['widget_id'] );
				break;

			case 'delete_widget':
				$this->deleteWidget( $_POST['widget_type'], $_POST['widget_id'] );
				break;

			case 'process_edit_widget':
				$this->updateWidget();
				break;

			case 'show_permalink_form':
				$this->showEditPermalinksForm = true;
				break;

			case 'update_permalink_structure':
				update_option( 'permalink_structure', $_POST['update_permalink_structure'] );
				$this->notice = "Permalink structure updated to: <b>{$_POST['update_permalink_structure']}</b>.";
				break;

			case 'show_change_upload_path_form':
				$this->showChangeUploadPathForm = true;
				break;

			case 'update_upload_path':
				update_option( 'upload_path', $_POST['update_upload_path'] );
				ppStaticFile::generateAll();
				$this->notice = "Upload path set to <b>{$_POST['update_upload_path']}</b> and static files regenerated.";
				break;

			case 'change_option':
				ppOpt::update( $_POST['option_key'], $_POST['option_val'] );
				ppStaticFile::generateAll();
				$this->notice = "Option <em>{$_POST['option_key']}</em> updated to <b>{$_POST['option_val']}</b> and static files regenerated.";
				break;

			case 'P3_active_design':
				if ( $p3 = ppImportP3::p3Storage() ) {
					$activeDesignID = $p3['active_design'];
					$options = array();
					foreach ( $p3[$activeDesignID]['options'] as $key => $val ) {
						if ( !NrUtil::endsWith( $key, '_value=' ) ) {
							$options[$key] = $val;
						}
					}
					$activeP3DesignData = array(
						'active_design' => array(
							'id' => $activeDesignID,
							'name' => $p3['design_meta'][$activeDesignID]['name'],
							'desc' => $p3['design_meta'][$activeDesignID]['description'],
						),
						'non_design' => $p3['non_design'],
						'imgs' => $p3[$activeDesignID]['images'],
						'options' => $options,
					);
					$this->p3Data  = NrHtml::a( $this->blogUrl . '?pp_support=true&export_p3_design=' . $activeDesignID, 'Export current P3 design' );
					$this->p3Data .= NrDump::it( $activeP3DesignData, false );
				} else {
					$this->notice = 'No P3 data detected, likely not a P3 user.';
				}
				break;

			case 'auto_update_info':
				$m = '<div id="update-info"><h3>Auto Update Info:</h3><pre>';
				$m .= $this->valDisplay( 'Self Update', ppUtil::isAutoUpgradeCapable() ? 'capable' : $this->warn( 'incapable' ) );
				if ( ppUtil::isAutoUpgradeCapable() ) {
					$m .= $this->valDisplay( 'Auto Updates', ppOpt::test( 'auto_auto_upgrade', 'true' ) ? 'enabled' : $this->warn( 'disabled' ) );
					$m .= $this->valDisplay( 'Delay Auto Update', ( get_transient( 'pp_delay_next_auto_upgrade_attempt' ) ) ? 'yes, delaying' : 'no' );
				}
				$m .= $this->valDisplay( 'PP Site Reachable', ppUtil::prophotoSiteReachable() ? 'yes' : $this->warn( 'no' ) );
				$m .= $this->valDisplay( 'Recommended SVN', get_transient( 'pp_recommended_svn' ) );
				$m .= $this->valDisplay( 'Next SVN Check', intval( ( ppCron::scheduled( 'ppUpgrader::checkRecommendedSvn' ) - time() ) / 60 ) . ' minutes' );
				if ( ppUtil::isAutoUpgradeCapable() ) {
					$m .= $this->valDisplay( 'Force update', $this->inlineFormBtn( 'force_immediate_update', 'click here' ) );
				}
				$m .= '</pre><hr /></div>';
				$this->autoUpdateInfo = $m;
				break;

			default:
				$this->warn = "Unknown 'support_action' posted: '{$_POST['support_action']}'";
		}
	}


	private function changeOptionForm() {
		return NrHtml::group( array(
			'openForm' => array( '', 'id=change_settings' ),
				'hiddenInput' => array( 'support_action', 'change_option' ),
				'labledTextInput_1' => array( 'key:', 'option_key', '' ),
				'labledTextInput_2' => array( 'val:', 'option_val', '' ),
				'textarea' => array( '', '', 13 ),
				'submit' => 'change settings',
			'closeForm'
		) );
	}


	private function deleteWidget( $type, $id ) {
		if ( ppWidgetUtil::deleteWidget( $type, $id ) ) {
			ppStaticFile::generateAll();
			$this->notice = "Widget <b>$type-$id</b> deleted successfully and static files regenerated.";
		} else {
			$this->warn = "Error deleting widget: <b>$type-$id</b>";
		}
	}


	private function updateBlogAddress() {
		$blogUrl = $_POST['blog_address'];
		$wpUrl   = $_POST['wp_address'];
		if ( !NrUtil::validUrl( $blogUrl ) || !NrUtil::validUrl( $wpUrl ) ) {
			$this->warn = 'Error updating blog addresses, invalid URL encountered.';
			return;
		}
		update_option( 'home', $blogUrl );
		update_option( 'siteurl', $wpUrl );
		ppStaticFile::generateAll();
		$this->notice = "Blog/WP install URLs updated to <b>$blogUrl</b>, <b>$wpUrl</b> and static files regenerated.";
	}


	private function updateWidget() {
		$widgetType = $_POST['widget_type'];
		$widgetId   = $_POST['widget_id'];

		$newWidgetInstance = array();
		foreach ( $_POST as $key => $val ) {
			if ( !NrUtil::isIn( 'widget_data_', $key ) ) {
				continue;
			}
			$dataKey = str_replace( 'widget_data_', '', $key );
			$newWidgetInstance[$dataKey] = stripslashes( $val );
		}

		$updateSuccess = ppWidgetUtil::updateWidget( $widgetType, $widgetId, $newWidgetInstance );

		if ( $updateSuccess ) {
			$this->notice = "Widget <b>$widgetType-$widgetId</b> successfully updated.";
		} else {
			$this->warn = "Error updating widget <b>$widget_type-$widgetId</b>.";
		}
	}


	private function editWidgetForm( $type, $id ) {
		$instance = end( ppWidgetUtil::instanceData( "{$type}-{$id}" ) );

		$uniqueFormMarkup = '';
		foreach ( $instance as $key => $val ) {
			$uniqueFormMarkup .= "<label for='widget_data_$key'>$key:</label>";
			if ( strlen( $val ) < 50 && $key != 'text' ) {
				$uniqueFormMarkup .= NrHtml::textInput( "widget_data_$key", $val, 50 );
			} else {
				$uniqueFormMarkup .= NrHtml::textarea( "widget_data_$key", $val, 15 );
			}
		}

		$this->renderHeader();
		echo NrHtml::group( array(
			'h3' => "Edit widget: $type-$id",
			'openForm' => array( '', 'id=edit-widget' ),
				$uniqueFormMarkup,
				'hiddenInput_1' => array( 'widget_type', $type ),
				'hiddenInput_2' => array( 'widget_id', $id ),
				'hiddenInput_3' => array( 'support_action', 'process_edit_widget' ),
				'p' => NrHtml::submit( 'Edit widget' ),
			'closeForm'
		) );
		$this->renderFooter();
		exit();
	}


	private function changeUploadPathForm() {
		return NrHtml::group( array(
			'openForm' => array( '', 'class=compact' ),
				'h2' => 'Change upload path:',
				'hiddenInput' => array( 'support_action', 'update_upload_path' ),
				'textInput' => array( 'update_upload_path', get_option( 'upload_path' ), 55 ),
				'submit' => 'change',
			'closeForm'
		) );
	}


	private function editPermalinksForm() {
		return NrHtml::group( array(
			'openForm' => array( '', 'class=compact' ),
				'h2' => 'Update permalink structure:',
				'hiddenInput' => array( 'support_action', 'update_permalink_structure' ),
				'textInput' => array( 'update_permalink_structure', get_option( 'permalink_structure' ), 43 ),
				'submit' => 'update',
			'closeForm'
		) );
	}


	private function changeBlogAddressForm() {
		return NrHtml::group( array(
			'openForm' => array( '', 'class=compact' ),
				'h2' => 'Update Blog/WP URL:',
				'hiddenInput' => array( 'support_action', 'update_blog_address' ),
				'labledTextInput_1' => array( 'Blog address:', 'blog_address', get_option( 'home' ), 43 ),
				'labledTextInput_2' => array( 'WP install address:', 'wp_address', get_option( 'siteurl' ), 43 ),
				'submit' => 'update',
			'closeForm',
		) );
	}


	private function examineImgs() {
		$imgs = ppImg::getCustomImgs();
		$markup = '<div class="image-data"><h2>Image Data:</h2>';
		foreach ( $imgs as $imgId => $imgFilename ) {
			$img = ppImg::id( $imgId );
			$markup .= '<div class="image">';
			$markup .= $this->valDisplay( 'Image ID', $imgId ) . '<br />';
			$markup .= $this->valDisplay( 'Image filename', $imgFilename ) . '<br />';
			$markup .= $this->valDisplay( 'Image filesize', $img->fileSize, 'kb' ) . '<br />';
			$markup .= $this->valDisplay( 'Image height', $img->height, 'px' ) . '<br />';
			$markup .= $this->valDisplay( 'Image width', $img->width, 'px' ) . '<br />';
			$markup .= '<a class="img" target="_blank" href="' . $img->url . '"><img src="' . $img->url . '" /></a><br />';
			$markup .= '</div>';
		}
		$markup .= '</div>';
		return $markup;
	}


	private function examineWidgets() {
		$allWidgets = get_option( 'sidebars_widgets' );
		$inactiveWidgets = $allWidgets['wp_inactive_widgets'];
		unset( $allWidgets['wp_inactive_widgets'], $allWidgets['array_version'] );
		$allWidgets['wp_inactive_widgets'] = $inactiveWidgets;

		$markup = '<h2>Widgets Data:</h2><pre id="examine_widgets">';

		foreach ( $allWidgets as $widgetColumnName => $widgetColumn ) {

			if ( empty( $widgetColumn ) ) {
				continue;
			}

			$markup .= $this->valDisplay( 'Widget column', '<b>' . $widgetColumnName . '</b>' ) . '    ';
			for ( $i = 0; $i <= 142; $i++ ) $markup .= '-';
			$markup .= "\n";

			foreach ( (array) $widgetColumn as $key => $widgetHandle ) {

				$widgetData = ppWidgetUtil::instanceData( $widgetHandle );

				$editBtn = NrHtml::group( array(
					'openForm' => array( '', 'class=widget-edit' ),
						'hiddenInput_1' => array( 'support_action', 'show_widget_edit_form' ),
						'hiddenInput_2' => array( 'widget_type', $widgetData['type'] ),
						'hiddenInput_3' => array( 'widget_id', $widgetData['id'] ),
						'submit' => 'edit',
					'closeForm'
				) );

				$deleteBtn = NrHtml::group( array(
					'openForm' => array( '', 'class=widget-edit' ),
						'hiddenInput_1' => array( 'support_action', 'delete_widget' ),
						'hiddenInput_2' => array( 'widget_type', $widgetData['type'] ),
						'hiddenInput_3' => array( 'widget_id', $widgetData['id'] ),
						'submit' => 'delete',
					'closeForm'
				) );

				$markup .= "\n" . $this->valDisplay( 'Widget type', $widgetData['type'] . $editBtn . $deleteBtn );

				foreach ( (array) $widgetData['instance'] as $key => $val ) {
					$chunkedVal = '';
					$val = str_replace( "\n", '%NEWLINE%', $val );
					$valParts = explode( '%NEWLINE%', $val );
					foreach ( (array) $valParts as $part ) {
						$chunkedVal .= chunk_split( $part, 115, '%NEWLINE%' );
					}
					$finalVal = preg_replace( '/\%NEWLINE\%( )?/', "\n                   ", htmlspecialchars( $chunkedVal ) );
					$markup .= $this->valDisplay( $key, $finalVal );
				}
			}
			$markup .= "\n\n\n";
		}
		$markup .= '</pre>';
		return $markup;
	}


	private function optionsList() {
		$val = isset( $_POST['settings_key'] ) ? $_POST['settings_key'] : '';
		$list = '<div id="options-list"><p id="check_settings">View settings: ' . NrHtml::textInput( '', $val, 30 ) . '</p>';

		$options = array_merge( ppOpt::getOptions(), ppOpt::getNonDesignOptions() );
		foreach ( $options as $key => $val ) {
			$val = htmlspecialchars( $val );
			$list .= "<p class='options-data' id='$key' val='$val'>$key: <span>'$val'</span></p>\n";
		}

		$defaults = ppOpt::getDefaults();
		foreach ( $defaults as $key => $val ) {
			$val = htmlspecialchars( $val );
			if ( isset( $options[$key] ) ) {
				continue;
			}
			$list .= "<p class='options-data default' id='$key' val='$val'><em>default</em> $key: <span>'$val'</span></p>\n";
		}

		return $list . '</div>';
	}


	private function actionBtns() {
		return NrHtml::group( array(
			'openForm' => array( '', 'id=support-actions' ),
				'hiddenInput' => array( 'support_action', '' ),
				'submit_1' => 'regenerate static files',
				'submit_2' => 'export active design',
				'submit_3' => 'examine widgets',
				'submit_4' => 'examine images',
				'submit_5' => 'change blog address',
				'submit_6' => 'P3 active design',
				'submit_7' => 'auto update info',
				'submit_8' => 'full php info',
			'closeForm'
		) );
	}


	private function blogData() {
		$data = array(
			'Blog URL' => get_option( 'home' ),
			'WP URL' => get_option( 'siteurl' ),
			'Admin links' => $this->adminLinks(),
			'WP version' => $GLOBALS['wp_version'],
			'P4 svn#' => pp::site()->svn,
			'CSS' => ppStaticFile::url( 'style.css' ),
			'Javascript' => ppStaticFile::url( 'script.js' ),
			'Active plugins' => $this->pluginsList(),
			'Users' => $this->userList(),
			'Server info' => $_SERVER['SERVER_SOFTWARE'],
			'Permalinks' => get_option( 'permalink_structure' ) . $this->inlineFormBtn( 'show_permalink_form' ),
			'Payer email' => ppOpt::id( 'payer_email' ),
			'TXN ID' => ppOpt::id( 'txn_id' ),
			'Upload path' => '',
			'Safe mode' => ini_get( 'safe_mode' ) ? $this->warn( 'On' ) : 'Off',
			'Register globals' => ( ini_get( 'register_globals' ) == 1 || ini_get( 'register_globals' ) == 'On' ) ? $this->warn( 'On' ) : '',
			'Nameservers' => ppUtil::nameservers() ? '<span id="ns">' . ppUtil::nameservers() . '</span>' : '<span id="ns-loading">looking up...</span>',
		);

		if ( ppUtil::webHost() != 'Unknown' ) {
			$data['Web host'] = ppUtil::webHost();
		}

		$data['Upload path'] = get_option( 'upload_path' ) ? get_option( 'upload_path' ) : '<em style="opacity:0.5">empty</em> ';
		$data['Upload path'] .= ' ' . $this->inlineFormBtn( 'show_change_upload_path_form' );

		if ( ppOpt::test( 'design_slug' ) ) {
			$data['From paid design'] = NrHtml::a( PROPHOTO_SITE_URL . 'store/' . ppOpt::id( 'design_slug' ) . '/', ppOpt::id( 'design_slug' ) );
		}

		$markup = '<pre>';
		foreach ( $data as $label => $val ) {
			$markup .= $this->valDisplay( $label, $val );
		}
		return $markup . '</pre>';
	}


	private function valDisplay( $label, $val, $suffix = '' ) {
		if ( empty( $val ) ) {
			return;
		}
		if ( NrUtil::validUrl( $val ) ) {
			$val = "<a href='$val'>$val</a>";
		}
		$label = str_pad( $label . ':', 18, ' ', STR_PAD_LEFT );
		$markup = "$label <span>$val{$suffix}</span>\n";
		return preg_replace("/( |\t|\n)+\<\/span>/m", '</span>', $markup );
	}


	private function warn( $text ) {
		return '<span class="warn">' . $text . '</span>';
	}


	private function inlineFormBtn( $action, $label = 'edit' ) {
		$form = NrHtml::group( array(
			'openForm' => array( '', 'class=inline-form-btn' ),
				'hiddenInput' => array( 'support_action', $action ),
				'submit' => $label,
			'closeForm'
		) );
		return trim( str_replace( array( "\n", "\t" ), '', $form ) );
	}


	private function pluginsList() {
		$plugins = get_option( 'active_plugins' );
		$lineLength = 0;
		$pluginsList = '';
		foreach ( (array) $plugins as $plugin ) {
			$pluginName = preg_replace( '/\/.+/', '', $plugin );
			$pluginsList .= $pluginName . ' ';
			$lineLength += strlen( $pluginName );
			if ( $lineLength > 80 ) {
				$pluginsList .= '<br />                   ';
				$lineLength = 0;
			}
		}
		return $pluginsList;
	}


	private function userList() {
		$users = array();
		$userQuery = new WP_User_Query( array( 'fields' => 'ID' ) );
		$userIDs = $userQuery->get_results();
		foreach ( $userIDs as $ID ) {
			$user = get_userdata( $ID );
			$users[] = $user->data->user_login . '/' . $user->data->user_email . '/' . join( '+', $user->roles );
		}
		return join( ', ', $users );
	}


	private function adminLinks() {
		if ( !current_user_can( 'level_1' ) ) {
			 return '';
		}
		return NrHtml::group( array(
			'<span id="admin-links">Logged in. ',
				'a_1' => array( ppUtil::customizeURL( 'background' ), 'ProPhoto Customize' ),
				'a_2' => array( ppUtil::manageDesignsURL(), 'ProPhoto Manage Designs' ),
				'a_3' => array( admin_url( 'widgets.php' ), 'Widgets' ),
				'a_4' => array( admin_url( 'plugins.php' ), 'Plugins' ),
				'a_5' => array( admin_url( 'post-new.php' ), 'New Post' ),
			'</span>'
		) );
	}


	private function renderHeader() {
		ppUtil::renderView( 'support_head', $this->viewData() );
	}


	private function renderFooter() {
		echo "\n<body>\n</html>";
	}


	private function userIsAuthed() {
		if ( pp::site()->isDev ) {
			return true;
		}

		if ( isset( $_COOKIE['pp_support_auth'] ) && md5( $_COOKIE['pp_support_auth'] ) == self::HASH ) {
			return true;
		}

		if ( isset( $_POST['pp_auth'] ) ) {

			if ( md5( $_POST['pp_auth'] ) == self::HASH ) {

				$postArgs = array( 'body' => array( 'pp_support_auth_verify' => $this->blogUrl ) );
				$response = wp_remote_retrieve_body( wp_remote_post( $this->remoteAuthUrl, $postArgs ) );

				if ( $response && $response != 'fail' ) {
					setcookie( 'pp_support_auth', $response, ONE_YEAR_FROM_NOW, '/' );
					return true;
				}
			}
		}

		return false;
	}


	private function viewData() {
		return array( 'support' => $this );
	}


	private function dataDump() {
		$d = array();
		$d['wp_ver'] = $GLOBALS['wp_version'];
		$d['pp_svn'] = pp::site()->svn;
		$d['pp_ver'] = 'P4';

		// only dump full data if authenticity of request verified
		if ( wp_remote_retrieve_body( wp_remote_get( PROPHOTO_SITE_URL . '?requestHandler=Doctor::verifyCheckup&domain=' . urlencode( NrUtil::extractDomain( $this->blogUrl ) ) ) ) != 'pass' ) {
			$d['verify'] = 0;
			 return serialize( $d );
		}

		// server
		$d['php_ver'] = phpversion();
		$d['php_ver_med'] = floatval( phpversion() );
		$d['php_ver_maj'] = intval( phpversion() );
		$d['mysql_ver'] = mysql_get_server_info();
		$d['mysql_ver_san'] = preg_replace( '/[^0-9\.]/', '', mysql_get_server_info() );
		$d['server_raw'] = $_SERVER['SERVER_SOFTWARE'];
		$d['server'] = ppUtil::server();
		$d['is_1and1'] = ( ppUtil::webHost() == '1and1' ) ? 1 : 0;
		$d['nameservers'] = ppUtil::nameservers() ? ppUtil::nameservers() : 'Unknown';
		$d['web_host'] = ppUtil::webHost();
		$d['doc_root'] = $_SERVER['DOCUMENT_ROOT'];
		$d['safe_mode'] = ini_get( 'safe_mode' );
		$d['register_globals'] = ( ini_get( 'register_globals' ) == 1 || ini_get( 'register_globals' ) == 'On' ) ? 1 : 0;
		$d['mod_rewrite'] = ( apache_mod_loaded( 'mod_rewrite' ) ) ? 1 : 0;
		$d['mod_security'] = ( apache_mod_loaded( 'mod_security' ) ) ? 1 : 0;
		$d['curl'] = ( function_exists( 'curl_version' ) ) ? curl_version() : 0;

		// test one-click upgrades
		$d['one_click_upgrades_should_work'] = ppUtil::isAutoUpgradeCapable();

		// wp stuff
		$d['blog_url'] = get_option( 'home' );
		$d['wp_url'] = get_option( 'siteurl' );
		$d['wp_ver_san'] = ppUtil::wpVersion();
		$d['active_plugins'] = (array) get_option( 'active_plugins' );
		$d['users'] = $this->userList();

		// p4 stuff
		$d['payer_email'] = ppOpt::id( 'payer_email' );
		$d['txn_id'] = ppOpt::id( 'txn_id' );
		$d['purch_time'] = ppOpt::id( 'purch_time' );
		$d['has_ga_code'] = ( @file_exists( pp::fileInfo()->folderPath . '/' . md5( 'ga_analytics_code' ) . '.php' ) ) ? 1: 0;

		// wp settings/options
		$d['permalink_structure'] = get_option( 'permalink_structure' );
		$d['tag_base'] = get_option( 'tag_base' );
		$d['category_base'] = get_option( 'category_base' );
		$d['upload_path'] = get_option( 'upload_path' );
		$d['upload_url_path'] = get_option( 'upload_url_path' );
		$d['uploads_use_yearmonth_folders'] = get_option( 'uploads_use_yearmonth_folders' );
		$d['users_can_register'] = ( get_option( 'users_can_register' ) ) ? 1 : 0;
		$d['show_on_front'] = get_option( 'show_on_front' );
		$d['page_on_front'] = get_option( 'page_on_front' );
		$d['page_for_posts'] = get_option( 'page_for_posts' );
		$d['posts_per_page'] = get_option( 'posts_per_page' );
		$d['posts_per_rss'] = get_option( 'posts_per_rss' );
		$d['rss_use_excerpt'] = get_option( 'rss_use_excerpt' );
		$d['default_comment_status'] = get_option( 'default_comment_status' );
		$d['comment_registration'] = get_option( 'comment_registration' );
		$d['comments_notify'] = get_option( 'comments_notify' );
		$d['moderation_notify'] = get_option( 'moderation_notify' );
		$d['comment_moderation'] = get_option( 'comment_moderation' );
		$d['comment_whitelist'] = get_option( 'comment_whitelist' );
		$d['show_avatars'] = get_option( 'show_avatars' );
		$d['thumbnail_size_w'] = get_option( 'thumbnail_size_w' );
		$d['thumbnail_size_h'] = get_option( 'thumbnail_size_h' );
		$d['medium_size_w'] = get_option( 'medium_size_w' );
		$d['medium_size_h'] = get_option( 'medium_size_h' );
		$d['large_size_w'] = get_option( 'large_size_w' );
		$d['large_size_h'] = get_option( 'large_size_h' );
		$d['thumbnail_crop'] = get_option( 'thumbnail_crop' );
		$d['embed_autourls'] = get_option( 'embed_autourls' );
		$d['embed_size_w'] = get_option( 'embed_size_w' );
		$d['embed_size_h'] = get_option( 'embed_size_h' );
		$d['blog_public'] = get_option( 'blog_public' );

		// file perms
		$d['wp_content_perms']              = NrUtil::filePermissions( WP_CONTENT_DIR );
		$d['uploads_folder_perms']          = NrUtil::filePermissions( pp::fileInfo()->wpUploadPath );
		$d['p4_folder_perms']               = NrUtil::filePermissions( pp::fileInfo()->folderPath );
		$d['wp_folder_perms']               = NrUtil::filePermissions( ABSPATH );
		$d['theme_folder_perms']            = NrUtil::filePermissions( TEMPLATEPATH );
		$d['theme_adminpages_folder_perms'] = NrUtil::filePermissions( TEMPLATEPATH . '/adminpages' );
		$d['p4_backup_folder_perms']        = NrUtil::filePermissions( pp::fileInfo()->backupFolderPath );
		$d['p4_designs_folder_perms']       = NrUtil::filePermissions( pp::fileInfo()->designsFolderPath );
		$d['p4_gallery_folder_perms']       = NrUtil::filePermissions( pp::fileInfo()->galleryFolderPath );
		$d['p4_images_folder_perms']        = NrUtil::filePermissions( pp::fileInfo()->imagesFolderPath );
		$d['p4_music_folder_perms']         = NrUtil::filePermissions( pp::fileInfo()->musicFolderPath );
		$d['p4_static_folder_perms']        = NrUtil::filePermissions( pp::fileInfo()->staticFolderPath );

		return serialize( $d );
	}


	protected function cachePluginFix() {
		if ( class_exists( 'W3_Plugin_TotalCache' ) ) {
			/* echoing some HTML first prevents W3 Total Cache from suppressing page */
			echo '<br style="display:none;">';
		}
	}


	protected function exportP3Design() {
		define( 'P3_TEMPLATEPATH', str_replace( 'prophoto4', 'prophoto3', TEMPLATEPATH ) );
		define( 'P3_DB_STORAGE_NAME', 'p3theme_storage' );
		$_REQUEST['design'] = $_GET['export_p3_design'];
		global $p3_notices;
		@require_once( P3_TEMPLATEPATH . '/includes/constants.php' );
		@require_once( P3_TEMPLATEPATH . '/includes/settings/notices.php' );
		@require_once( P3_TEMPLATEPATH . '/functions/folders.php' );
		@require_once( P3_TEMPLATEPATH . '/functions/static.php' );
		@require_once( P3_TEMPLATEPATH . '/functions/designs.php' );
		@require_once( P3_TEMPLATEPATH . '/functions/utility.php' );
		@p3_export_design();
	}
}

