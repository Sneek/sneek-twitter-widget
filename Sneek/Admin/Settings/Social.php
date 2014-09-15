<?php namespace Sneek\Admin\Settings;

class Social 
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'registerPluginSettings'));
        add_action('admin_menu', array($this, 'registerAdminMenu'));
    }

    public function registerPluginSettings()
    {
        register_setting( 'sneek-social-networks', 'sneek_twitter_widget_consumer_key' );
        register_setting( 'sneek-social-networks', 'sneek_twitter_widget_consumer_secret' );
        register_setting( 'sneek-social-networks', 'sneek_twitter_widget_oauth_key' );
        register_setting( 'sneek-social-networks', 'sneek_twitter_widget_oauth_secret' );
    }

    public function registerAdminMenu()
    {
        add_options_page(
            'Social Networks',
            'Social Networks',
            'manage_options',
            'sneek-social-networks',
            array($this, 'renderSettingsPage')
        );
    }

    public function renderSettingsPage()
    {

        $options = array(
            'sneek_twitter_widget_consumer_key' => 'Consumer Key',
            'sneek_twitter_widget_consumer_secret' => 'Consumer Secret',
            'sneek_twitter_widget_oauth_key' => 'OAuth Key',
            'sneek_twitter_widget_oauth_secret' => 'OAuth Secret',
        );
    ?>
        <div class="wrap">
            <h2>Social Networks</h2>
            <form method="POST" action="options.php">
                <?php settings_fields( 'sneek-social-networks' ); ?>
                <?php do_settings_sections( 'sneek-social-networks' ); ?>

                <h3 class="title">Twitter Settings</h3>
                <table class="form-table">
                <?php foreach ($options as $id => $label) : ?>
                    <tr valign="top">
                        <th scope="row">
                            <label for="<?php echo $id; ?>"><?php echo $label; ?></label>
                        </th>
                        <td>
                            <input type="text" class="regular-text" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php esc_attr_e( get_option( $id ) ) ?>"/>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </table>

                <?php submit_button(); ?>
            </form>
        </div><!-- /wrap -->
    <?php
    }
} 