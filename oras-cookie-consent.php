<?php 

/**
 * Plugin Name
 *
 * @package           PluginPackage
 * @author            Online-results
 * @copyright         2021 https://www.online-results.dk/
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Online-Results Cookie Manager
 * Plugin URI:        https://www.online-results.dk/cookie-manager
 * Description:       Dette plugin vil give dig mulighed for at tilføje cookie consent til din hjemmeside. Det eneste, du skal bruge, er en API-nøgle. Hvis du ikke allerede har en, kan du få en nøgle her: https://www.online-results.dk/cookie-manager
 * Version:           1.1.0
 * Requires PHP:      7.4
 * Author:            Online-results
 * Text Domain:       oras-cookie-consent
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Update URI:        https://www.online-results.dk/cookie-manager
 */




function oras_register_options_page() {
    add_options_page('Cookie Manager', 'Cookie Manager', 'manage_options', 'oras', 'oras_api_page');
}
add_action('admin_menu', 'oras_register_options_page');

// Register settings
function oras_register_settings() {
    add_option( 'oras_option_name', 'Tilføj api nøglen her');
    register_setting( 'oras_option_group', 'oras_api', 'oras_callback' );
    register_setting( 'oras_option_group', 'oras_languague', 'oras_callback' );
    register_setting( 'oras_option_group', 'oras_privacy_link', 'oras_callback' );
}
add_action( 'admin_init', 'oras_register_settings' );

// HTML for the option page
function oras_scripts() {
    wp_register_style('consent-css', plugins_url('assets/cookie.css',__FILE__ ));
    wp_enqueue_style('consent-css');
}
add_action( 'admin_enqueue_scripts', 'oras_scripts' );



function oras_api_page() { ?>
    <div class="api_ctn">
        <h2>Tilføj din API-nøgle her</h2>
        <form method="post" action="options.php">
        <?php settings_fields( 'oras_option_group' ); ?>
       
            <label for="oras_api">API nøgle:</label>
            <input type="text" id="oras_api" placeholder="Tilføj API nøglen her." name="oras_api" value="<?php echo wp_kses(get_option('oras_api'), wp_kses_allowed_html( 'strip' )); ?>" /> <br>
            <div class="oras_input_container">
                <label class="oras_label" for="oras_lang">Vælg sprog:</label>
                <select id="oras_lang" name="oras_languague">
                    <option <?php if (get_option('oras_languague') == 'da') { echo 'selected';} ?>value="da">Dansk</option>
                    <option value="en"<?php if(get_option('oras_languague') == 'en') { echo 'selected'; } ?>>Engelsk</option>
                </select>
            </div>  
            <div class="oras_input_container">
                <label class="oras_label" for="oras_privacy_link">Indtast link til privatlivs politik </label>
                <input id="oras_privacy_link" name="oras_privacy_link" value="<?php echo wp_kses(get_option('oras_privacy_link'), wp_kses_allowed_html( 'strip' )); ?>"  type="text"/>
            </div>
            <p class="btn-wrapper">
                <input type="submit" class="button button-primary" value="Gem API nøglen"/>
            </p>
        </form>
        <a href="https://www.online-results.dk/cookie-manager" target="_blank">Har du ikke en API-nøgle, kan du få en her</a>
        <p class="madeby">
            <img src="<?php echo plugins_url('oras-logo.png', __FILE__);?> "/>  
            <br>
            <b>Et plugin udviklet af online results.</b>
        </p>
  </div>
<?php } 

add_action('wp_head', function() {
    if(get_option('oras_api', false )) {
        $now = new DateTime();
        $time = strtotime($now->format('Y-m-d'));
        $lang = get_option('oras_languague', false );
        $privacy = get_option('oras_privacy_link', false);

        if (empty($lang)) {
            $lang = 'da';
        }

        if (empty($privacy)) {
            $privacy = '/privatlivspolitik';
        }

        echo wp_get_script_tag(
            array(
                'src'       => esc_url( 'https://cookie-manager.online-results.dk/cm.js?v='. $time .'' ),
                'data-cm-key' => get_option('oras_api', false ),
                'data-lang' => $lang,   
                'data-policy-link' => $privacy,   
           

            )
        );
    }
}, -2000);
