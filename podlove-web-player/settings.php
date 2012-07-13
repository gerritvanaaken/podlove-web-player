<?php

// setting id => default value
$podlove_pwp_settings = array(
	'pwp_video_skin' => '',
	'pwp_script_on_demand' => false,

	'pwp_default_cover' => '',
	'pwp_default_tools' => '',

	'pwp_default_tags' => 'podcast',

	'pwp_default_video_height' => 270,
	'pwp_default_video_width' => 'auto',
	'pwp_default_video_type' => '',

	'pwp_default_audio_height' => '',
	'pwp_default_audio_width' => 'auto',
	'pwp_default_audio_type' => '',

	'pwp_flattr_uid' => '',
	'pwp_flattr_category' => 'audio',
	'pwp_flattr_button' => 'compact',
	'pwp_flattr_popout' => false
);
define(PODLOVEWEBPLAYER_OPTIONS, implode(',', array_keys($podlove_pwp_settings)));
define(PODLOVEWEBPLAYER_OPTIONS_DEFAULTS, implode(',', $podlove_pwp_settings));

/* Runs when plugin is activated */

function podlove_pwp_install() {
	$settings = array_combine(
		explode(',', PODLOVEWEBPLAYER_OPTIONS),
		explode(',', PODLOVEWEBPLAYER_OPTIONS_DEFAULTS)
	);

	foreach ($settings as $key => $value) {
		add_option($key, $value);
	}
}

register_activation_hook(PODLOVEWEBPLAYER_FILE, 'podlove_pwp_install');

/* Runs on plugin deactivation */

function podlove_pwp_remove() {
	$settings = explode(',', PODLOVEWEBPLAYER_OPTIONS);

	foreach ($settings as $setting) delete_option($setting);
}

register_deactivation_hook(PODLOVEWEBPLAYER_FILE, 'podlove_pwp_remove');

function podlove_pwp_register_settings() {
	$settings = explode(',', PODLOVEWEBPLAYER_OPTIONS);

	foreach ($settings as $setting) delete_option('pwp_settings', $setting);
}

/* create custom plugin settings menu */

function podlove_pwp_create_menu() {
	// create new top-level menu
	add_options_page('Podlove Web Player Options', 'Podlove Web Player', 'administrator', PODLOVEWEBPLAYER_FILE, 'podlove_pwp_settings_page');

	// call register settings function
	add_action('admin_init', 'podlove_pwp_register_settings');
}

add_action('admin_menu', 'podlove_pwp_create_menu');


function podlove_pwp_settings_page() { ?>

<div class="wrap">
<h2>Podlove Web Player Options</h2>

<p>See <a href="http://mediaelementjs.com/">MediaElementjs.com</a> for more details on how the HTML5 player and Flash fallbacks work.</p>

<form method="post" action="options.php">

	<?php wp_nonce_field('update-options'); ?>
	<input type="hidden" name="action" value="update">
	<input type="hidden" name="page_options" value="<?php echo PODLOVEWEBPLAYER_OPTIONS; ?>">

	<h3 class="title"><span>General Settings</span></h3>

	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<label for="pwp_script_on_demand">Load Script on Demand</label>
			</th>
			<td>
				<input name="pwp_script_on_demand" type="checkbox" id="pwp_script_on_demand" <?php echo (get_option('pwp_script_on_demand') == true ? "checked" : ""); ?>>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pwp_default_cover">Default Cover (URL)</label>
			</th>
			<td>
				<input name="pwp_default_cover" id="pwp_default_cover" value="<?php echo get_option('pwp_default_cover'); ?>">
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pwp_default_tools">Default Tools (comma seperated list)</label>
			</th>
			<td>
				<input name="pwp_default_tools" id="pwp_default_tools" value="<?php echo get_option('pwp_default_tools'); ?>"> <span class="description">such as "flattr"</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pwp_default_tags">Default tags (comma seperated list)</label>
			</th>
			<td>
				<input name="pwp_default_tags" id="pwp_default_tags" value="<?php echo get_option('pwp_default_tags'); ?>">
			</td>
		</tr>
	</table>


	<h3 class="title"><span>Video Settings</span></h3>

	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<label for="pwp_default_video_width">Default Width (in px or "auto")</label>
			</th>
			<td>
				<input name="pwp_default_video_width" id="pwp_default_video_width" value="<?php echo get_option('pwp_default_video_width'); ?>">
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pwp_default_video_height">Default Height (in px or "auto")</label>
			</th>
			<td>
				<input name="pwp_default_video_height" id="pwp_default_video_height" value="<?php echo get_option('pwp_default_video_height'); ?>">
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pwp_default_video_type">Default Type</label>
			</th>
			<td>
				<input name="pwp_default_video_type" id="pwp_default_video_type" value="<?php echo get_option('pwp_default_video_type'); ?>"> <span class="description">such as "video/mp4"</span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pwp_video_skin">Video Skin</label>
			</th>
			<td>
				<select name="pwp_video_skin" id="pwp_video_skin">
					<option value="" <?php echo (get_option('pwp_video_skin') == '') ? ' selected' : ''; ?>>Default</option>
					<option value="wmp" <?php echo (get_option('pwp_video_skin') == 'wmp') ? ' selected' : ''; ?>>WMP</option>
					<option value="ted" <?php echo (get_option('pwp_video_skin') == 'ted') ? ' selected' : ''; ?>>TED</option>
				</select>
			</td>
		</tr>
	</table>


	<h3 class="title"><span>Audio Settings</span></h3>

	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<label for="pwp_default_audio_width">Default Width (in px or "auto")</label>
			</th>
			<td>
				<input name="pwp_default_audio_width" id="pwp_default_audio_width" value="<?php echo get_option('pwp_default_audio_width'); ?>">
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pwp_default_audio_height">Default Height (in px or "auto")</label>
			</th>
			<td>
				<input name="pwp_default_audio_height" id="pwp_default_audio_height" value="<?php echo get_option('pwp_default_audio_height'); ?>">
			</td>
		</tr>
			<th scope="row">
				<label for="pwp_default_audio_type">Default Type</label>
			</th>
			<td>
				<input name="pwp_default_audio_type" id="pwp_default_audio_type" value="<?php echo get_option('pwp_default_audio_type'); ?>"> <span class="description">such as "audio/mp3"</span>
			</td>
		</tr>
	</table>


	<h3 class="title"><span>Flattr Settings</span></h3>

	<table class="form-table">
		<tr valign="top">
			<th scope="row">
				<label for="pwp_flattr_uid">Flattr UID</label>
			</th>
			<td>
				<input name="pwp_flattr_uid" id="pwp_flattr_uid" value="<?php echo get_option('pwp_flattr_uid'); ?>">
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pwp_flattr_category">Flattr category</label>
			</th>
			<td>
				<input name="pwp_flattr_category" id="pwp_flattr_category" value="<?php echo get_option('pwp_flattr_category'); ?>">
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pwp_flattr_button">Flattr compact button</label>
			</th>
			<td>
				<input type="checkbox"
					name="pwp_flattr_button" id="pwp_flattr_button"
					value="compact"
					<?php echo (get_option('pwp_flattr_button') == "compact" ? "checked" : ""); ?>>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">
				<label for="pwp_flattr_popout">Flattr popout</label>
			</th>
			<td>
				<input type="checkbox" name="pwp_flattr_popout" id="pwp_flattr_popout" <?php echo (get_option('pwp_flattr_popout') == true ? "checked" : ""); ?>>
			</td>
		</tr>
	</table>

	<p>
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>">
	</p>

</div>

</form>
</div>

<?php } ?>