<div class="um <?php echo $this->get_class( $mode ); ?> um-<?php echo esc_attr( $form_id ); ?> um-role-<?php echo um_user( 'role' ); ?> ">

	<div class="um-form">
	
		<?php
		/**
		 * UM hook
		 *
		 * @type action
		 * @title um_profile_before_header
		 * @description Some actions before profile form header
		 * @input_vars
		 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]
		 * @change_log
		 * ["Since: 2.0"]
		 * @usage add_action( 'um_profile_before_header', 'function_name', 10, 1 );
		 * @example
		 * <?php
		 * add_action( 'um_profile_before_header', 'my_profile_before_header', 10, 1 );
		 * function my_profile_before_header( $args ) {
		 *     // your code here
		 * }
		 * ?>
		 */
		do_action( 'um_profile_before_header', $args );

		if ( um_is_on_edit_profile() ) { ?>
			<form method="post" action="">
		<?php }

		/**
		 * UM hook
		 *
		 * @type action
		 * @title um_profile_header_cover_area
		 * @description Profile header cover area
		 * @input_vars
		 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]
		 * @change_log
		 * ["Since: 2.0"]
		 * @usage add_action( 'um_profile_header_cover_area', 'function_name', 10, 1 );
		 * @example
		 * <?php
		 * add_action( 'um_profile_header_cover_area', 'my_profile_header_cover_area', 10, 1 );
		 * function my_profile_header_cover_area( $args ) {
		 *     // your code here
		 * }
		 * ?>
		 */
		do_action( 'um_profile_header_cover_area', $args );

		/**
		 * UM hook
		 *
		 * @type action
		 * @title um_profile_header
		 * @description Profile header area
		 * @input_vars
		 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]
		 * @change_log
		 * ["Since: 2.0"]
		 * @usage add_action( 'um_profile_header', 'function_name', 10, 1 );
		 * @example
		 * <?php
		 * add_action( 'um_profile_header', 'my_profile_header', 10, 1 );
		 * function my_profile_header( $args ) {
		 *     // your code here
		 * }
		 * ?>
		 */
		do_action( 'um_profile_header', $args );

		/**
		 * UM hook
		 *
		 * @type filter
		 * @title um_profile_navbar_classes
		 * @description Additional classes for profile navbar
		 * @input_vars
		 * [{"var":"$classes","type":"string","desc":"UM Posts Tab query"}]
		 * @change_log
		 * ["Since: 2.0"]
		 * @usage
		 * <?php add_filter( 'um_profile_navbar_classes', 'function_name', 10, 1 ); ?>
		 * @example
		 * <?php
		 * add_filter( 'um_profile_navbar_classes', 'my_profile_navbar_classes', 10, 1 );
		 * function my_profile_navbar_classes( $classes ) {
		 *     // your code here
		 *     return $classes;
		 * }
		 * ?>
		 */
		$classes = apply_filters( 'um_profile_navbar_classes', '' ); ?>

		<div class="um-profile-navbar <?php echo $classes ?>">
			<?php
			/**
			 * UM hook
			 *
			 * @type action
			 * @title um_profile_navbar
			 * @description Profile navigation bar
			 * @input_vars
			 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]
			 * @change_log
			 * ["Since: 2.0"]
			 * @usage add_action( 'um_profile_navbar', 'function_name', 10, 1 );
			 * @example
			 * <?php
			 * add_action( 'um_profile_navbar', 'my_profile_navbar', 10, 1 );
			 * function my_profile_navbar( $args ) {
			 *     // your code here
			 * }
			 * ?>
			 */
			do_action( 'um_profile_navbar', $args ); ?>
			<div class="um-clear"></div>
		</div>

		<?php
		/**
		 * UM hook
		 *
		 * @type action
		 * @title um_profile_menu
		 * @description Profile menu
		 * @input_vars
		 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]
		 * @change_log
		 * ["Since: 2.0"]
		 * @usage add_action( 'um_profile_menu', 'function_name', 10, 1 );
		 * @example
		 * <?php
		 * add_action( 'um_profile_menu', 'my_profile_navbar', 10, 1 );
		 * function my_profile_navbar( $args ) {
		 *     // your code here
		 * }
		 * ?>
		 */
		do_action( 'um_profile_menu', $args );

		$nav = UM()->profile()->active_tab;
		$subnav = ( get_query_var('subnav') ) ? get_query_var('subnav') : 'default';

		print "<div class='um-profile-body $nav $nav-$subnav'>";

			// Custom hook to display tabbed content
		/**
		 * UM hook
		 *
		 * @type action
		 * @title um_profile_content_{$nav}
		 * @description Custom hook to display tabbed content
		 * @input_vars
		 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]
		 * @change_log
		 * ["Since: 2.0"]
		 * @usage add_action( 'um_profile_content_{$nav}', 'function_name', 10, 1 );
		 * @example
		 * <?php
		 * add_action( 'um_profile_content_{$nav}', 'my_profile_content', 10, 1 );
		 * function my_profile_content( $args ) {
		 *     // your code here
		 * }
		 * ?>
		 */
		do_action("um_profile_content_{$nav}", $args);

		/**
		 * UM hook
		 *
		 * @type action
		 * @title um_profile_content_{$nav}_{$subnav}
		 * @description Custom hook to display tabbed content
		 * @input_vars
		 * [{"var":"$args","type":"array","desc":"Profile form shortcode arguments"}]
		 * @change_log
		 * ["Since: 2.0"]
		 * @usage add_action( 'um_profile_content_{$nav}_{$subnav}', 'function_name', 10, 1 );
		 * @example
		 * <?php
		 * add_action( 'um_profile_content_{$nav}_{$subnav}', 'my_profile_content', 10, 1 );
		 * function my_profile_content( $args ) {
		 *     // your code here
		 * }
		 * ?>
		 */
		do_action( "um_profile_content_{$nav}_{$subnav}", $args );
		do_action( "save_weight_record", $args );

		print "</div>";

		if ( um_is_on_edit_profile() ) { ?>
			</form>
		<?php } 
		$weightList = UM()->profile()->get_user_weight_list(um_user('ID'));
		?>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
		<script type="text/javascript">
			google.charts.load('current', {'packages':['corechart']});
			google.charts.setOnLoadCallback(drawChart);

			function drawChart() {
				var data=[];
				 var Header= ['Date', 'Muscle', 'Fat'];
				 data.push(Header);
				 var temp = [];
				 <?php 
				    $index = 0;
					foreach ( $weightList as $wdata ) { 
                     $index = $index + 1;
						?>
				      temp=[];
				      temp.push("<?php echo date("m/d/y", strtotime($wdata['weight_date'])) ?>");
				      temp.push(<?php echo $wdata['muscle'] ?>);
					  temp.push(<?php echo $wdata['fat'] ?>);
				      data.push(temp);
				  <?php } ?>
				var options = {
					title: 'Weight Management',
					curveType: 'function',
					legend: { position: 'bottom' }
				};
                var dataTable = google.visualization.arrayToDataTable(data);
				var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

				chart.draw(dataTable, options);
			}
		</script>
		<div id="curve_chart" style="width: 700px; height: 500px;left: -200px;"></div>
		<div>
			<table width="100%" class="ws-ls-data-table">
				<thead>
					<tr>
						<th width="25%">Date</th>
						<th width="25%">Weight</th>
						<th width="25%">Fat</th>
						<th width="25%">Muscle</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					//$weightList = UM()->profile()->get_user_weight_list(um_user('ID'));
					foreach ( $weightList as $data ) { ?>
					<tr>
						<td><?php echo date("m/d/Y", strtotime($data['weight_date'])) ?></td>
						<td><?php echo $data['weight'] ?></td>
						<td><?php echo $data['fat'] ?></td>
						<td><?php echo $data['muscle'] ?></td>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div>
			<form action="http://localhost:8888/fitness/user/" method="post" class="we-ls-weight-form we-ls-weight-form-validate ws_ls_display_form%s" id="fitness-club-weight-entry-form" data-measurements-enabled="false"  data-user_id="<?php echo um_profile_id(); ?>" data-measurements-all-required="false" data-is-target-form="false">
				<input type="hidden" value="false" id="ws_ls_is_target" name="ws_ls_is_target">
				<input type="hidden" value="true" id="ws_ls_is_weight_form" name="ws_ls_is_weight_form">
				<input type="hidden" value="1" id="ws_ls_user_id" name="ws_ls_user_id">
				<input type="hidden" value="d0646c9e7683d63e9e9cafce849c2acb" id="ws_ls_security" name="ws_ls_security"><div class="ws-ls-inner-form comment-input">
					<div class="ws-ls-error-summary" style="display: none;">
						<ul style="display: none;"></ul>
					</div>
					<input type="text" name="weight-entry-date" tabindex="4" id="weight-entry-date" value="<?php echo date("m/d/Y") ?>" placeholder="09/09/2018" size="22" class="we-ls-datepicker hasDatepicker ws-ls-valid" aria-required="true" aria-invalid="false"><input type="number" tabindex="5" step="any" min="1" name="fc-weight" id="fc-weight" value="" placeholder="Weight" size="22" aria-required="true">
					<input type="number" tabindex="5" step="any" min="1" name="fc-fat" id="fc-fat" value="" placeholder="Fat" size="22" aria-required="true">
					<input type="number" tabindex="5" step="any" min="1" name="fc-muscle" id="fc-muscle" value="" placeholder="Muscle" size="22" aria-required="true">
				</div><div class="ws-ls-form-buttons">
					<div>
						<input name="fc-weight-submit" type="button" id="fc-weight-submit" tabindex="7" value="Save Entry" class="comment-submit button" data-user_id="<?php echo um_profile_id(); ?>" >	</div>
					</div>
				</form>
			</div>
	</div>

</div>