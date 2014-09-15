<?php namespace Sneek\Widgets;

use Exception;
use Sneek\Twitter\Api;
use Sneek\Twitter\Presenter;
use WP_Widget;

class Twitter extends WP_Widget
{
    /**
     * @var array
     */
    protected $instance;

    public function __construct()
    {
        $widget_ops = array('classname' => 'widget-tweets', 'description' => __("Displays a styled list of your latest tweets"));
        parent::__construct('latest-tweets', __('Latest Tweets'), $widget_ops);
    }

    public function widget($args, $instance)
    {
        extract($args);

        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';

        $this->instance = $instance;

        $tweets = $this->getTweets();

        if ( ! $tweets) return;


        // ------> Start Output <----- //
        echo $before_widget;
        if ($title)
            echo $before_title . $title . $after_title;


        ?>

            <ul class="list-unstyled list-tweets">
            <?php foreach ($tweets as $tweet) : ?>
                <li>
                    <?php echo new Presenter($tweet); ?>
                </li>
            <?php endforeach; ?>
            </ul>

        <?php
    }

    public function update($new, $old)
    {
        $instance = $old;
        $instance['title'] = strip_tags($new['title']);
        $instance['count'] = ! empty($new['count']) ? $new['count'] : 2;
        $instance['handle'] = ! empty($new['handle']) ? $new['handle'] : 'sneek_digital';

        delete_transient('latest-tweets-widget-tweets-'.$this->number);
        delete_transient('latest-tweets-widget-response-'.$this->number);

        return $instance;
    }

    public function form($instance)
    {
        $instance = wp_parse_args((array) $instance, array('title' => ''));
        $title = esc_attr($instance['title']);
        $count = isset($instance['count']) ? (int) $instance['count'] : 2;
        $handle = isset($instance['handle']) ? $instance['handle'] : 'sneek_digital';
    ?>
        <!-- Title -->
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <!-- Count -->
        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Count:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" />
        </p>

        <!-- Handle -->
        <p>
            <label for="<?php echo $this->get_field_id('handle'); ?>"><?php _e('Handle:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('handle'); ?>" name="<?php echo $this->get_field_name('handle'); ?>" type="text" value="<?php echo $handle; ?>" />
        </p>
    <?php
    }

    protected function getTweets()
    {
        $tweets = get_transient('latest-tweets-widget-tweets-'.$this->number);
        $lastResponse = get_transient('latest-tweets-widget-response-'.$this->number);

        if ($lastResponse === '200' and $tweets)
        {
            return unserialize($tweets);
        }

        // -----> Let's Speak to Twitter <----- //
        $api = new Api(
            get_option( 'sneek_twitter_widget_consumer_key' ),
            get_option( 'sneek_twitter_widget_consumer_secret' ),
            get_option( 'sneek_twitter_widget_oauth_key' ),
            get_option( 'sneek_twitter_widget_oauth_secret' )
        );

        try {
            $result = $api->get('statuses/user_timeline', array(
                'screen_name' => $this->instance['handle'],
                'count' => $this->instance['count'],
            ));

            if ((int) $api->lastStatusCode() === 200)
            {
                set_transient('latest-tweets-widget-response-'.$this->number, '200', 15 * 60);
                set_transient('latest-tweets-widget-tweets-'.$this->number, serialize($result), 15 * 60);

                return $result;
            }
        } catch (Exception $ex) {
            return array();
        }
    }
} 
