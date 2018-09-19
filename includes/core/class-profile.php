<?php
namespace um\core;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'um\core\Profile' ) ) {


	/**
	 * Class Profile
	 * @package um\core
	 */
	class Profile {


		/**
		 * @var array
		 */
		public $arr_user_slugs = array();


		/**
		 * @var array
		 */
		public $arr_user_roles = array();


		/**
		 * @var
		 */
		var $active_tab;


		/**
		 * Profile constructor.
		 */
		function __construct() {
			add_action( 'template_redirect', array( &$this, 'active_tab' ), 10002 );
			add_action( 'template_redirect', array( &$this, 'active_subnav' ), 10002 );
		}
		/**
		 * Save Weight Entry
		 */
		function ajax_save_weight_entry() {
			global $wpdb;
			extract( $_REQUEST );
			
			$weight = $_REQUEST["weight"];
			$fat = $_REQUEST["fat"];
			$muscle = $_REQUEST["muscle"];
			$weightEntryDate = $_REQUEST["weightEntryDate"];
			$weight = $_REQUEST["weight"];
			$weightDate =  date('Y-m-d H:i:s');
			error_log("weightDate now" . $weightDate);
			if(isset($weightEntryDate)) {
				$weightDate = date('Y-m-d H:i:s', strtotime($weightEntryDate));
				error_log("weightDate entered " . $weightDate);
			}

			$wpdb->insert("wp_WEIGHT_TRACKER",array(
			"weight_user_id" => $user_id,
			"weight_date"=>$weightDate,
			"weight" => $weight,
			"fat"=>$fat,
			"muscle"=>$muscle	
			));
			$this->get_user_weight_list($user_id);	
		}
		/**
		 * Get list of weight information for a given user
		 *
		 *
		 * @param $user_id
		 *
		 * @return array
		 */
		function get_user_weight_list( $user_id ) {
			global $wpdb;

			$weightlist = $wpdb->get_results( $wpdb->prepare(
				"SELECT weight_date, weight, fat, muscle 
				FROM wp_WEIGHT_TRACKER
				WHERE weight_user_id = %d",
				$user_id
			), ARRAY_A );

			$filtered = array();
			foreach ( $weightlist as $data ) {
				error_log("weight data from db: " . $data['weight_date']." | ". $data['weight']." | ". $data['fat']. " | ".$data['muscle']);
			}

			return $weightlist;
		}
		/**
		 * Return data formats
		 *
		 * @param $data
		 * @return array
		 */
		function fc_field_format( $data ) {

		    $formats = [
		        'id' => '%d',
		        'weight' => '%d',
		        'fat' => '%d',
				'muscle' => '%d',
		        'weightEntryDate' => '%d'
		    ];

		    $return = [];

		    foreach ( $data as $key => $value) {
		        if ( false === empty( $formats[ $key ] ) ) {
		            $return[] = $formats[ $key ];
		        }
		    }

		    return $return;
		}

		/**
		 * Delete profile avatar AJAX handler
		 */
		function ajax_delete_profile_photo() {

			/**
			 * @var $user_id
			 */
			extract( $_REQUEST );

			if ( ! UM()->roles()->um_current_user_can( 'edit', $user_id ) )
				die( __( 'You can not edit this user' ) );

			UM()->files()->delete_core_user_photo( $user_id, 'profile_photo' );
		}


		/**
		 * Delete cover photo AJAX handler
		 */
		function ajax_delete_cover_photo() {
			/**
			 * @var $user_id
			 */
			extract( $_REQUEST );

			if ( ! UM()->roles()->um_current_user_can( 'edit', $user_id ) )
				die( __( 'You can not edit this user' ) );

			UM()->files()->delete_core_user_photo( $user_id, 'cover_photo' );
		}


		/**
		 * All tab data
		 *
		 * @return mixed|void
		 */
		function tabs() {

			/**
			 * UM hook
			 *
			 * @type filter
			 * @title um_profile_tabs
			 * @description Extend user profile tabs
			 * @input_vars
			 * [{"var":"$tabs","type":"array","desc":"Profile tabs"}]
			 * @change_log
			 * ["Since: 2.0"]
			 * @usage
			 * <?php add_filter( 'um_profile_tabs', 'function_name', 10, 1 ); ?>
			 * @example
			 * <?php
			 * add_filter( 'um_profile_tabs', 'my_profile_tabs', 10, 1 );
			 * function my_profile_tabs( $tabs ) {
			 *     // your code here
			 *     return $tabs;
			 * }
			 * ?>
			 */
			$tabs = apply_filters( 'um_profile_tabs', array(
				'main' => array(
					'name' => __( 'About', 'ultimate-member' ),
					'icon' => 'um-faicon-user'
				),
				'posts' => array(
					'name' => __( 'Posts', 'ultimate-member' ),
					'icon' => 'um-faicon-pencil'
				),
				'comments' => array(
					'name' => __( 'Comments', 'ultimate-member' ),
					'icon' => 'um-faicon-comment'
				)
			) );

			// disable private tabs
			if ( ! is_admin() ) {
				if ( is_user_logged_in() ) {
					$user_id = um_user('ID');
					um_fetch_user( get_current_user_id() );
				}

				foreach ( $tabs as $id => $tab ) {
					if ( ! $this->can_view_tab( $id ) ) {
						unset( $tabs[ $id ] );
					}
				}

				if ( is_user_logged_in() ) {
					um_fetch_user( $user_id );
				}
			}

			return $tabs;
		}


		/**
		 * Tabs that are active
		 *
		 * @return mixed|void
		 */
		function tabs_active() {
			$tabs = $this->tabs();

			foreach ( $tabs as $id => $info ) {
				if ( ! UM()->options()->get( 'profile_tab_' . $id ) && ! isset( $info['_builtin'] ) && ! isset( $info['custom'] ) ) {
					unset( $tabs[ $id ] );
				}
			}

			return $tabs;
		}


		/**
		 * Primary tabs only
		 *
		 * @return array
		 */
		function tabs_primary(){
			$tabs = $this->tabs();
			$primary = array();
			foreach ( $tabs as $id => $info ) {
				if ( isset( $info['name'] ) ) {
					$primary[$id] = $info['name'];
				}
			}
			return $primary;
		}


		/**
		 * Activated tabs in backend
		 *
		 * @return string
		 */
		function tabs_enabled(){
			$tabs = $this->tabs();
			foreach( $tabs as $id => $info ){
				if ( isset( $info['name'] ) ) {
					if ( UM()->options()->get('profile_tab_'.$id) || isset( $info['_builtin'] ) ) {
						$primary[$id] = $info['name'];
					}
				}
			}
			return ( isset( $primary ) ) ? $primary : '';
		}


		/**
		 * Privacy options
		 *
		 * @return array
		 */
		function tabs_privacy() {
			$privacy = array(
				0 => 'Anyone',
				1 => 'Guests only',
				2 => 'Members only',
				3 => 'Only the owner',
				4 => 'Specific roles'
			);

			return $privacy;
		}


		/**
		 * Check if the user can view the current tab
		 *
		 * @param $tab
		 *
		 * @return bool
		 */
		function can_view_tab( $tab ) {

			$target_id = UM()->user()->target_id;
			if ( empty( $target_id ) ) {
				return true;
			}

			$can_view = false;

			$privacy = intval( UM()->options()->get( 'profile_tab_' . $tab . '_privacy' ) );
			switch ( $privacy ) {
				case 0:
					$can_view = true;
					break;

				case 1:
					$can_view = ! is_user_logged_in();
					break;

				case 2:
					$can_view = is_user_logged_in();
					break;

				case 3:
					$can_view = is_user_logged_in() && get_current_user_id() === $target_id;
					break;

				case 4:
					if ( is_user_logged_in() ) {
						$roles = (array) UM()->options()->get( 'profile_tab_' . $tab . '_roles' );

						$current_user_roles = um_user( 'roles' );
						if ( ! empty( $current_user_roles ) && count( array_intersect( $current_user_roles, $roles ) ) > 0 ) {
							$can_view = true;
						}
					}
					break;

				default:
					$can_view = true;
					break;
			}

			return $can_view;
		}


		/**
		 * Get active_tab
		 *
		 * @return mixed|void
		 */
		function active_tab() {

			$this->active_tab = UM()->options()->get('profile_menu_default_tab');

			if ( get_query_var('profiletab') ) {
				$this->active_tab = get_query_var('profiletab');
			}

			/**
			 * UM hook
			 *
			 * @type filter
			 * @title um_profile_active_tab
			 * @description Change active profile tab
			 * @input_vars
			 * [{"var":"$tab","type":"string","desc":"Active Profile tab"}]
			 * @change_log
			 * ["Since: 2.0"]
			 * @usage
			 * <?php add_filter( 'um_profile_active_tab', 'function_name', 10, 1 ); ?>
			 * @example
			 * <?php
			 * add_filter( 'um_profile_active_tab', 'my_profile_active_tab', 10, 1 );
			 * function my_profile_active_tab( $tab ) {
			 *     // your code here
			 *     return $tab;
			 * }
			 * ?>
			 */
			$this->active_tab = apply_filters( 'um_profile_active_tab', $this->active_tab );

			return $this->active_tab;
		}


		/**
		 * Get active active_subnav
		 *
		 * @return mixed|null
		 */
		function active_subnav() {

			$this->active_subnav = null;

			if ( get_query_var('subnav') ) {
				$this->active_subnav = get_query_var('subnav');
			}

			return $this->active_subnav;
		}


		/**
		 * Show meta in profile
		 *
		 * @param array $array Meta Array
		 * @return string
		 */
		function show_meta( $array ) {
			$output = '';

			if ( ! empty( $array ) ) {
				foreach ( $array as $key ) {
					if ( $key ) {
						$data = array();
						if ( isset( UM()->builtin()->all_user_fields[ $key ] ) ){
							$data = UM()->builtin()->all_user_fields[ $key ];
						}

						$data['in_profile_meta'] = true;

						$value = um_filtered_value( $key, $data );
						if ( ! $value )
							continue;

						if ( ! UM()->options()->get( 'profile_show_metaicon' ) ) {
							$icon = '';
						} else {
							$icon = ! empty( $data['icon'] ) ? '<i class="' . $data['icon'] . '"></i>' : '';
						}

						$items[] = '<span>' . $icon . $value . '</span>';
						$items[] = '<span class="b">&bull;</span>';
					}
				}
			}

			if ( isset( $items ) ) {
				array_pop( $items );
				foreach ( $items as $item ) {
					$output .= $item;
				}
			}

			return $output;
		}


		/**
		 * New menu
		 *
		 * @param string $position
		 * @param string $element
		 * @param string $trigger
		 * @param array $items
		 */
		function new_ui( $position, $element, $trigger, $items ) {
			?>

			<div class="um-dropdown" data-element="<?php echo $element; ?>" data-position="<?php echo $position; ?>" data-trigger="<?php echo $trigger; ?>">
				<div class="um-dropdown-b">
					<div class="um-dropdown-arr"><i class=""></i></div>
					<ul>
						<?php foreach ( $items as $k => $v ) { ?>
							<li><?php echo $v; ?></li>
						<?php } ?>
					</ul>
				</div>
			</div>

			<?php
		} //end of new_ui

		/**
		 * converts the color from hex to RGB
		 *
		 * @param string $colour
		 * @param string $alpha
		 * @return string
		 */
		function um_hex_to_rgb( $colour = '', $alpha = null ) {

			if ( $colour[0] == '#' ) {
				$colour = substr( $colour, 1 );
			}
			if ( strlen( $colour ) == 6 ) {
				list( $r, $g, $b ) = array( $colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5] );
			} elseif ( strlen( $colour ) == 3 ) {
				list( $r, $g, $b ) = array( $colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2] );
			} else {
				return false;
			}
			$r = hexdec( $r );
			$g = hexdec( $g );
			$b = hexdec( $b );
			$value = $r . ',' . $g . ',' . $b;
			if ( empty($alpha) === false ) {
				return 'rgba(' . $value . ',' . $alpha . ')';
			} else {
				return 'rgb(' . $value . ')';
			}

		}
		 	/* Display Chart */
	 	function display_weight_tracker_chart($weight_data, $options = false) {
    	// Build the default arguments for a chart. This can then be overrided by what is being passed in (i.e. to support shortcode arguments)
	 		$user_id = get_current_user_id();
	 		$chart_config = array(
				'user-id' => $user_id, //get_current_user_id(),
				'type' => 'line',
				'height' => 250,
				'weight-line-color' => '#aeaeae',
				'weight-fill-color' => '#f9f9f9',
				'weight-target-color' => '#76bada',
				'show-gridlines' => true,
				'bezier' => true,
				'hide_login_message_if_needed' => true,
				'exclude-measurements' => false,
				'ignore-login-status' => false
			);

    	// If we are PRO and the developer has specified options then override the default
	 		if($options){
	 			$chart_config = wp_parse_args( $options, $chart_config );
	 		}

			//$measurements_enabled = (false == $chart_config['exclude-measurements'] && ws_ls_any_active_measurement_fields()) ? true : false;
	 		$measurements_enabled = false;
    		// Make sure they are logged in                        
	 		if (false == $chart_config['ignore-login-status'] && !is_user_logged_in())	{
	 			if (false == $chart_config['hide_login_message_if_needed']) {
	 				return ws_ls_display_blockquote(__('You need to be logged in to record your weight.', WE_LS_SLUG) , '', false, true);
	 			} else {
	 				return;
	 			}
	 		}

	 		$chart_id = 'ws_ls_chart_' . rand(10,1000) . '_' . rand(10,1000);

			// If Pro disabled or Measurements to be displayed then force to line
	 		if($measurements_enabled) {
	 			$chart_config['type'] = 'line';
	 		}

			//$y_axis_unit = (ws_ls_get_config('WE_LS_IMPERIAL_WEIGHTS')) ? __('kg', WE_LS_SLUG) : __('kg', WE_LS_SLUG) ;
	 		$y_axis_unit = 'lbs';
			//$y_axis_measurement_unit = ('inches' == ws_ls_get_config('WE_LS_MEASUREMENTS_UNIT')) ? __('Inches', WE_LS_SLUG) : __('CM', WE_LS_SLUG) ;
	 		$y_axis_measurement_unit = 'inches';
			//$point_size = (WE_LS_ALLOW_POINTS && WE_LS_CHART_POINT_SIZE > 0) ? WE_LS_CHART_POINT_SIZE : 0;
	 		$point_size = 1;
	 		$line_thickness = 2;

			// Build graph data
	 		$graph_data['labels'] = array();
	 		$graph_data['datasets'][0] = array( 'label' => 'Weight',
	 			'borderColor' => $chart_config['weight-line-color'],
	 		);

			// Determine fill based on chart type
	 		if ('line' == $chart_config['type']) {

			// Add a fill colour under weight line?
			//if ( true === WE_LS_WEIGHT_FILL_LINE_ENABLED ) {
	 			if (false ) {
	 				$graph_data['datasets'][0]['fill'] = true;
	 				$graph_data['datasets'][0]['backgroundColor'] =  um_hex_to_rgb( '#aeaeae', '0.5' );
	 			} else {
	 				$graph_data['datasets'][0]['fill'] = false;
	 			}

	 			$graph_data['datasets'][0]['lineTension'] = ($chart_config['bezier']) ? 0.4 : 0;
	 			$graph_data['datasets'][0]['pointRadius'] = $point_size;
	 			$graph_data['datasets'][0]['borderWidth'] = $line_thickness;
	 		} else {
	 			$graph_data['datasets'][0]['fill'] = true;
	 			$graph_data['datasets'][0]['backgroundColor'] = $chart_config['weight-fill-color'];
	 			$graph_data['datasets'][0]['borderWidth'] = 2;

	 		}

	 		$graph_data['datasets'][0]['data'] = array();
	 		$graph_data['datasets'][0]['yAxisID'] = 0;

	 		$target_weight = 150; //ws_ls_get_user_target($chart_config['user-id']);

	 		$chart_type_supports_target_data = ('bar' == $chart_config['type']) ? false : true;

	 		$dataset_index = 1;
	 		$number_of_measurement_datasets_with_data = 0;

			// If target weights are enabled, then include into javascript data object
	 		if ($target_weight != false && false && $chart_type_supports_target_data){

	 			$graph_data['datasets'][1] = array( 'label' =>  __('Target', WE_LS_SLUG),
	 				'borderColor' => $chart_config['weight-target-color'],
	 				'borderWidth' => $line_thickness,
	 				'pointRadius' => 0,
	 				'borderDash' => array(5,5),
	 				'fill' => false,
	 				'type' => 'line'
	 			);
	 			$graph_data['datasets'][1]['data'] = array();
	 			$dataset_index = 2;
	 		}

	// ----------------------------------------------------------------------------
	// Measurements - add measurement sets if enabled!
	// ----------------------------------------------------------------------------

	 		if($measurements_enabled) {
	 			$active_measurement_fields =  array(); //ws_ls_get_active_measurement_fields();
	 			$active_measurment_field_keys =  array(); //ws_ls_get_keys_for_active_measurement_fields('', true);
	 			$measurement_graph_indexes = array();


	 			foreach ($active_measurement_fields as $key => $data) {

	 				$graph_data['datasets'][$dataset_index] = array( 'label' => 'title',
	 					'borderColor' => $data['chart_colour'],
	 					'borderWidth' => $line_thickness,
	 					'pointRadius' => $point_size,
	 					'fill' => false,
	 					'spanGaps' => true,
	 					'yAxisID' => 'y-axis-measurements',
	 					'type' => 'line',
	 					'lineTension' => ($chart_config['bezier']) ? 0.4 : 0
	 				);
	 				$graph_data['datasets'][$dataset_index]['data'] = array();
	 				$graph_data['datasets'][$dataset_index]['data-count'] = 0;

	 				$measurement_graph_indexes[$key] = $dataset_index;

	 				$dataset_index++;
	 			}
	 		}

	 		if($weight_data) {
	 			foreach ($weight_data as $weight_object) {

	 				array_push($graph_data['labels'], $weight_object['weight_date']);
	 				array_push($graph_data['datasets'][0]['data'], $weight_object['weight']);

					// Set target weight if specified
	 				if ($target_weight != false && false && $chart_type_supports_target_data){
	 					array_push($graph_data['datasets'][1]['data'], $target_weight['graph_value']);
	 				}

			// ----------------------------------------------------------------------------
			// Add data for all measurements
			// ----------------------------------------------------------------------------
	 				if($measurements_enabled) {
	 					foreach ($active_measurment_field_keys as $key) {

					// If we have a genuine measurement value then add to graph data - otherwise NULL
	 						if(!is_null($weight_object['measurements'][$key]) && 0 != $weight_object['measurements'][$key]) {
	 							$graph_data['datasets'][$measurement_graph_indexes[$key]]['data'][] = $weight_object['measurements'][$key];
	 							$graph_data['datasets'][$measurement_graph_indexes[$key]]['data-count']++;
	 						} else {
	 							$graph_data['datasets'][$measurement_graph_indexes[$key]]['data'][] = NULL;
	 						}
	 					}
	 				}
	 			}

	 		}

			// Remove any empty measurements from graph
	 		if($measurements_enabled) {
	 			foreach ($active_measurment_field_keys as $key) {
	 				if(0 == $graph_data['datasets'][$measurement_graph_indexes[$key]]['data-count']) {
					//		unset($graph_data['datasets'][$measurement_graph_indexes[$key]]);
	 				} else {
	 					$number_of_measurement_datasets_with_data++;
	 				}
	 			}
	 		}

			// Embed JavaScript data object for this graph into page
	 		wp_localize_script( 'jquery-chart-ws-ls', $chart_id . '_data', $graph_data );

	 		$graph_line_options = array();

			// Set initial y axis for weight
	 		$graph_line_options = array(
	 			'scales' => array('yAxes' => array(array('scaleLabel' => array('display' => true, 'labelString' => 'Weight' . ' (' . $y_axis_unit . ')'), 'type' => "linear", 'ticks' => array('beginAtZero' => false), "display" => "true", "position" => "left", "id" => "y-axis-weight", '' , 'gridLines' => array('display' => $chart_config['show-gridlines']))))
	 		);

	 		if ('line' == $chart_config['type']) {

				// Add measurement Axis?
	 			if ($measurements_enabled ) {
	 				$graph_line_options['scales']['yAxes'] = array_merge($graph_line_options['scales']['yAxes'], array(array('scaleLabel' => array('display' => true, 'labelString' => __('Measurement', WE_LS_SLUG) . ' (' .$y_axis_measurement_unit. ')'), 'ticks' => array('beginAtZero' => false), 'type' => "linear", "display" => (($number_of_measurement_datasets_with_data != 0) ? true : false), "position" => "right", "id" => "y-axis-measurements", 'gridLines' => array('display' => $chart_config['show-gridlines']))));
	 			}
	 		}

			// If gridlines are disabled, hide x axes too
	 		if(!$chart_config['show-gridlines']) {
	 			$graph_line_options['scales']['xAxes'] = array(array('gridLines' => array('display' => false)));
	 		}

			// Legend
	 		$graph_line_options['legend']['position'] = 'bottom';
	 		$graph_line_options['legend']['labels']['boxWidth'] = 10;
	 		$graph_line_options['legend']['labels']['fontSize'] = 10;

			// Font settings
	 		$graph_line_options['fontColor'] = '#AEAEAE';
	 		$graph_line_options['fontFamily'] = '';

			// Embed JavaScript options object for this graph into page
	 		wp_localize_script( 'jquery-chart-ws-ls', $chart_id . '_options', $graph_line_options );

	 		$html = '<div><canvas id="' . $chart_id . '" class="ws-ls-chart" ' . (($chart_config['height']) ? 'height="'.  esc_attr($chart_config['height']) . '" ' : '') . ' data-chart-type="' . esc_attr($chart_config['type'])  . '" data-target-weight="' . esc_attr($target_weight['graph_value']) . '" data-target-colour="' . esc_attr($chart_config['weight-target-color']) . '"></canvas>';
	 		$html .= '<div class="ws-ls-notice-of-refresh ws-ls-reload-page-if-clicked ws-ls-hide"><a href="#">' . 'You have modified data. Please refresh page.' . '</a></div>';
	 		$html .= '</div>';
	 		return $html;
	 	}

	} //end Profile class
}