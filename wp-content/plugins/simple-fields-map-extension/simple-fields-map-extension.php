<?php
/*
Plugin Name: Simple Fields Map extension
Plugin URI: http://earthpeople.se/
Description: Adds a Google Maps-field to Simple Fields
Version: 1.3.1
Author: Earth People
Author URI: http://earthpeople.se/
License: GPL2
*/

/* Add example post type and simple fields to test plugin. 
// In case I forget to comment it out, it will only be called when using on my test domain (hopefully pretty unique name :)
if ( "playground-nightly.ep" === $_SERVER["HTTP_HOST"] ) {

	// Full example to add post type with maps and stuff
	add_action("init", function() {

		register_post_type( "sf_map_test", array(
			"label" => "SF Map Extension Test",
			"public" => true
		));

		simple_fields_register_field_group('sf_map_test_field_fg',
			array (
				'name' => 'Map test',
				'repeatable' => 1,
				'fields' => array(
					array(
						"slug" => "sf_map",
						"name" => "Test map",
						"type" => "googlemaps",
						"options" => array(
							"defaultZoomLevel" => 10,
							"defaultMapTypeId" => "HYBRID", // ROADMAP | SATELLITE | HYBRID | TERRAIN
							"defaultLocationLat" => 40.71435,
							"defaultLocationLng" => -74.00597,
							"defaultZoomLevel" => 10
						)
					)
				)
			)
		);

		simple_fields_register_post_connector('sf_map_test', array(
			"name" => "Map extension post connector",
			"field_groups" => array(
				array("slug" => "sf_map_test_field_fg")
			),
			"post_types" => "sf_map_test"
		));

		simple_fields_register_post_type_default('sf_map_test', 'sf_map_test');

	});

	add_filter( "the_content", function($str) {
		$str .= "<pre>" . print_r( simple_fields_values('sf_map'), true) . "</pre>";
		return $str;
	} );

}
//*


// Check if Simple Fields is installed and notify user if not
function simple_fields_field_googlemaps_check_simple_fields_installed() {

	$plugin_is_active = is_plugin_active("Simple-Fields-GIT/simple_fields.php") || is_plugin_active("Simple-Fields/simple_fields.php");
	if ( ! $plugin_is_active ) {
		?>
		<div class="error">
			<p><?php _e('To use the plugin <em>Simple Fields Map Extension</em> you must also have <a target="_blank" href="http://wordpress.org/extend/plugins/simple-fields/">Simple Fields</a> installed.', 'simple-fields-field-googlemaps'); ?></p>
		</div>
		<?php
	}

}
add_action("admin_notices", "simple_fields_field_googlemaps_check_simple_fields_installed");


/**
 * Function that is called from simple fields
 */ 
function simple_fields_field_googlemaps_register() {

	/**
	 * Main class for the google maps extension
	 */
	class simple_fields_field_googlemaps extends simple_fields_field {
	
		public 
			$key = "googlemaps", 
			$name = "Google Maps location",
			$version = "1.3.1";
		
		function __construct() {
			parent::__construct();
			
			// This is the funky way I do it so it works with my symlinks
			$plugin_url = plugins_url(basename(dirname(__FILE__))) . "/";
			$plugin_version = $this->version;

			load_plugin_textdomain('simple-fields-field-googlemaps', false, basename( dirname( __FILE__ ) ) . '/languages' );
	
			// Add admin scripts that the the plugin uses
			add_action("admin_enqueue_scripts", function() use ($plugin_url, $plugin_version) {
				wp_enqueue_script( "simple-fields-googlemaps", $plugin_url . "scripts.js", array(), $plugin_version );
				wp_enqueue_style( "simple-fields-googlemaps", $plugin_url . "style.css", array(), $plugin_version );
			});
		
		}
		
		/**
		 * Generate output for the fields options screen
		 */
		function options_output($existing_vals) {
	
			$output = "";
	
			// Map type
			$selectedDefaultMap = isset($existing_vals["defaultMapTypeId"]) ? $existing_vals["defaultMapTypeId"] : "HYBRID";
			$output .= sprintf('
				<div class="simple-fields-field-group-one-field-row">
					<div class="simple-fields-field-group-one-field-row-col-first">
						<p>
							<label>%6$s</label>
						</p>
					</div>
					<div class="simple-fields-field-group-one-field-row-col-second">
						<p>
							<select name="%1$s">
								<option %2$s value="ROADMAP">%7$s
								<option %3$s value="SATELLITE">%8$s
								<option %4$s value="HYBRID">%9$s
								<option %5$s value="TERRAIN">%10$s
							</select>
						</p>
					</div>
				</div>
				',
				$this->get_options_name("defaultMapTypeId"), 
				"ROADMAP" 	== $selectedDefaultMap ? " selected " : "",
				"SATELLITE"	== $selectedDefaultMap ? " selected " : "",
				"HYBRID"	== $selectedDefaultMap ? " selected " : "",
				"TERRAIN"	== $selectedDefaultMap ? " selected " : "",
				__("Map type", "simple-fields-field-googlemaps"), // 6
				__("ROADMAP - default 2D tiles of Google Maps", "simple-fields-field-googlemaps"),
				__("SATELLITE - photographic tiles", "simple-fields-field-googlemaps"),
				__("HYBRID - a mix of photographic tiles and a tile layer for prominent features (roads, city names)", "simple-fields-field-googlemaps"),
				__("TERRAIN - physical relief tiles for displaying elevation and water features (mountains, rivers, etc.)", "simple-fields-field-googlemaps")
			);
	
			// Map type
			$output .= sprintf('
				<div class="simple-fields-field-group-one-field-row">
					<div class="simple-fields-field-group-one-field-row-col-first">
						<p>
							<label>%3$s</label>
						</p>
					</div>
					<div class="simple-fields-field-group-one-field-row-col-second">
						<p>
							<input class="regular-text" type=text name="%1$s" value="%2$s" type=number pattern="\d+" required>
						</p>
					</div>
				</div>
				',
				$this->get_options_name("defaultZoomLevel"), 
				isset($existing_vals["defaultZoomLevel"]) ? $existing_vals["defaultZoomLevel"] : 10,
				__("Default zoom level", "simple-fields-field-googlemaps") // 3
			);
	
			// Default location
			$output .= sprintf('
				<div class="simple-fields-field-group-one-field-row">
					<div class="simple-fields-field-group-one-field-row-col-first">
						<p>
							<label>%5$s</label>
						</p>
					</div>
					<div class="simple-fields-field-group-one-field-row-col-second">
						<p>
							%6$s: <input type=text name="%1$s" value="%2$s" type=number pattern="-?\d+.\d+" required>
							%7$s: <input type=text name="%3$s" value="%4$s" type=number pattern="-?\d+.\d+" required>
						</p>
					</div>
				</div>
				',
				$this->get_options_name("defaultLocationLat"), 
				isset($existing_vals["defaultLocationLat"]) ? $existing_vals["defaultLocationLat"] : "59.3300",
				$this->get_options_name("defaultLocationLng"), 
				isset($existing_vals["defaultLocationLng"]) ? $existing_vals["defaultLocationLng"] : "18.0700",
				__("Default location", "simple-fields-field-googlemaps"), // 5
				__("Lat", "simple-fields-field-googlemaps"), // 6
				__("Lng", "simple-fields-field-googlemaps") // 7
			);
	
			return $output;
	
		}
		
		/**
		 * Generate output for post edit screen
		 */
		function edit_output($saved_values, $options) {
	
			$output = "";
			
			$lat_init_pos		= isset($saved_values["lat"]) ? $saved_values["lat"] : $options["defaultLocationLat"];
			$lng_init_pos		= isset($saved_values["lng"]) ? $saved_values["lng"] : $options["defaultLocationLng"];
			$lat_saved			= isset($saved_values["lat"]) ? $saved_values["lat"] : NULL;
			$lng_saved			= isset($saved_values["lng"]) ? $saved_values["lng"] : NULL;
			$formatted_address 	= isset($saved_values["formatted_address"]) ? $saved_values["formatted_address"] : "";
			$address_components	= isset($saved_values["address_components"]) ? $saved_values["address_components"] : "";
			$name				= isset($saved_values["name"]) ? $saved_values["name"] : "";
			$saved_preferred_zoom	= ( isset( $saved_values["preferred_zoom"] ) && is_numeric($saved_values["preferred_zoom"]) ) ? (int) $saved_values["preferred_zoom"] : "";

			$str_zoom_options = sprintf( '<select name="%1$s">', $this->get_options_name("preferred_zoom") );
			$str_zoom_options .= sprintf('<option value="%1$s">%2$s</option>', "", __("None preferred selected", "simple-fields-field-googlemaps"));
			$ranges = range(0,21);
			$ranges[0] = __("0 - whole earth visible", "simple-fields-field-googlemaps");
			$ranges[21] = __("21 - very close", "simple-fields-field-googlemaps");
			foreach ( $ranges as $one_zoom_range_key => $one_zoom_range_value ) {
				$str_zoom_options .= sprintf( '<option value="%1$s" %3$s>%2$s</option>', $one_zoom_range_key, $one_zoom_range_value, ($one_zoom_range_key === $saved_preferred_zoom) ? " selected " : "" );
			}
			$str_zoom_options .= "</select>";
			
			$str_zoom = $str_zoom_options;

			$output .= sprintf(
				'
					<div 
						class="simple-fields-fieldtype-googlemap-map-container"
						data-defaultmaptypeid="%7$s"
						data-defaultzoomlevel="%8$s"
						>
						Loading map ...
					</div>
					<input type=hidden value="%18$s" class="simple-fields-field-googlemap-lat-init-position" />
					<input type=hidden value="%19$s" class="simple-fields-field-googlemap-lng-init-position" />
					<input type=hidden name=%1$s value="%2$s" class="simple-fields-field-googlemap-lat" />
					<input type=hidden name=%3$s value="%4$s" class="simple-fields-field-googlemap-lng" />
					<input type=hidden name=%14$s value="%15$s" class="simple-fields-field-googlemap-name" />
					<input type=hidden name=%10$s value="%11$s" class="simple-fields-field-googlemap-formatted_address" />
					<input type=hidden name=%12$s value="%13$s" class="simple-fields-field-googlemap-address_components" />
					<p class="simple-fields-fieldtype-googlemap-selected-positions">
						
						<span class="simple-fields-fieldtype-googlemap-preferred-zoom">
							<strong>%21$s:</strong>
							<br>
							%20$s
						</span>
						
						<br>

						<strong>Position</strong>: 
						<a class="simple-fields-fieldtype-googlemap-marker-remove" href="#">%16$s</a>
						<span class="simple-fields-fieldtype-googlemap-selected-positions-inner">
							latitude <span class="simple-fields-field-googlemap-selected-lat">%5$s</span>
							<br>
							longitude <span class="simple-fields-field-googlemap-selected-lng">%6$s</span>
							<span class="simple-fields-field-googlemap-selected-name">%15$s</span>
							<span class="simple-fields-field-googlemap-selected-formatted_address">%11$s</span>
						</span>

					</p>
					<p><a class="simple-fields-fieldtype-googlemap-marker-add" href="#">%17$s</a></p>
					<p class="simple-fields-fieldtype-googlemap-address-search">
						<input type="text" name="" value="" placeholder="%9$s">
					</p>
				',
				$this->get_options_name("lat"),
				esc_attr($lat_saved),
				$this->get_options_name("lng"),
				esc_attr($lng_saved),
				round($lat_saved, 5),
				round($lng_saved, 5),
				$options["defaultMapTypeId"],
				empty($saved_preferred_zoom) ? $options["defaultZoomLevel"] : $saved_preferred_zoom, // use preferred over default
				__("Search company/address or lat,lng", "simple-fields-field-googlemaps"), // 9
				$this->get_options_name("formatted_address"), // 10
				esc_attr($formatted_address),
				$this->get_options_name("address_components"), // 12
				esc_attr($address_components),
				$this->get_options_name("name"), // 14
				esc_attr($name),
				__("(remove)", "simple-fields-field-googlemaps"), // 16
				__("Add marker/location", "simple-fields-field-googlemaps"), // 17
				$lat_init_pos, // 18
				$lng_init_pos,  // 19
				$str_zoom, // 20
				__("Preferred zoom", "simple-fields-field-googlemaps") // 21
			);
			
			return $output;
		}
		
		/**
		 * When returning values, add some useful info like static maps
		 */
		function return_values($values, $options = NULL) {
			
			// All these defaults can be overwritten by simple_fields_value
			$defaults = array(
				"static_maps_zoom"			=> 16,
				"static_maps_scale" 		=> 1,
				"static_maps_maptype" 		=> "roadmap", // roadmap | satellite | terrain | hybrid
				"static_maps_marker_show"	=> TRUE, // https://developers.google.com/maps/documentation/staticmaps/#Markers
				"static_maps_marker_size"	=> "mid",
				"static_maps_marker_color"	=> "red"
			);
			$options = wp_parse_args( $options, $defaults );
			
			// Add default sizes
			$arr_sizes = array(
				"thumbnail" => array(
					"width"  => get_option("thumbnail_size_w"),
					"height" => get_option("thumbnail_size_h")
				),
				"medium" => array(
					"width"  => get_option("medium_size_w"),
					"height" => get_option("medium_size_h")
				),
				"large" => array(
					"width"  => get_option("large_size_w"),
					"height" => get_option("large_size_h")
				)				
			);
			
			// Add custom sizes
			global $_wp_additional_image_sizes;
			if (isset($_wp_additional_image_sizes) && is_array($_wp_additional_image_sizes)) {
				foreach ($_wp_additional_image_sizes as $size_key => $size_vals) { 
					$arr_sizes[$size_key] = array(
						"width"		=> $size_vals["width"],
						"height"	=> $size_vals["height"]
					);
				}
			}
			
			// Generate the src for a static map for each map, for each image size
			// https://developers.google.com/maps/documentation/staticmaps/
			$static_map_base = "http://maps.googleapis.com/maps/api/staticmap?sensor=false";
			foreach ($values as $key => $val) {
				
				$arr_static_maps = array();
				foreach($arr_sizes as $size_key => $size_vals) {
	
					$markers = "";
					if ($options["static_maps_marker_show"]) {
						$markers = "";
						$markers .= "size:" . $options["static_maps_marker_size"];
						$markers .= "|";
						$markers .= "color:" . $options["static_maps_marker_color"];
						$markers .= "|";
						$markers .= $val["lat"] . "," . $val["lng"];
					}
	
					// zoom order works like this: 
					// 1. $options["static_maps_zoom"], if set. got from arg to simple_fields_value or default val
					// 2. $val['preferred_zoom'], if set. is set in gui. is empty if not set, but can be 0 (then it is set)

					$static_map = add_query_arg(array(
						"center" 	=> $val["lat"] . "," . $val["lng"],
						"zoom" 		=> isset( $val["preferred_zoom"] ) && is_numeric( $val["preferred_zoom"] ) ? $val["preferred_zoom"] : $options["static_maps_zoom"],
						"size" 		=> $size_vals["width"] . "x" . $size_vals["height"],
						"scale"		=> $options["static_maps_scale"],
						"maptype"	=> $options["static_maps_maptype"],
						"markers"	=> $markers
					), $static_map_base);
					
					$arr_static_maps[$size_key] = $static_map;
					// echo "<p>$size_key:<br><img src='$static_map'></p>";
	
				}
	
				$values[$key]["static_maps"] = $arr_static_maps;
	
				// If address_components exists, decode the json to array
				if (isset($values[$key]["address_components"]) && $values[$key]["address_components"]) {
					$values[$key]["address_components"] = json_decode($values[$key]["address_components"]);
				}
	
	
			}
						
			return $values;
			
		}
	
	} // class

	simple_fields::register_field_type("simple_fields_field_googlemaps");
}

// Tell Simple Fields to register this plugin
add_action("simple_fields_register_field_types", "simple_fields_field_googlemaps_register");

