<?php namespace Sneek\Twitter;

class Presenter 
{
    /**
     * @var \stdClass
     */
    protected $resource;

    /**
     * @var String
     */
    protected $tweet;

    public function __construct($tweet)
    {
        $this->resource = $tweet;
    }

    public function format()
    {
        $this->tweet = links_add_target(make_clickable(esc_html($this->resource->text)));
        $this->tweet = preg_replace_callback('/(^|[^0-9A-Z&\/]+)(#|\xef\xbc\x83)([0-9A-Z_]*[A-Z_]+[a-z0-9_\xc0-\xd6\xd8-\xf6\xf8\xff]*)/iu',
            'Sneek\Twitter\Presenter::hashtag',
            $this->tweet
        );

        $this->tweet = preg_replace_callback('/([^a-zA-Z0-9_]|^)([@\xef\xbc\xa0]+)([a-zA-Z0-9_]{1,20})(\/[a-zA-Z][a-zA-Z0-9\x80-\xff-]{0,79})?/u',
            'Sneek\Twitter\Presenter::username',
            $this->tweet
        );
    }

    /**
     * Link a Twitter user mentioned in the tweet text to the user's page on Twitter.
     *
     * @param array $matches regex match
     * @return string Tweet text with inserted @user link
     */
    public static function username($matches) {
        return "$matches[1]@<a href='" . esc_url( 'http://twitter.com/' . urlencode( $matches[3] ) ) . "' target='_blank'>$matches[3]</a>";
    }

    /**
     * Link a Twitter hashtag with a search results page on Twitter.com
     *
     * @param array $matches regex match
     * @return string Tweet text with inserted #hashtag link
     */
    public static function hashtag($matches) {
        return "$matches[1]<a href='" . esc_url( 'http://twitter.com/search?q=%23' . urlencode( $matches[3] ) ) . "' target='_blank'>#$matches[3]</a>";
    }


    /**
     * Returns a human readable time since tweet .
     *
     * @access public
     * @static
     * @param mixed $original
     * @param int $do_more (default: 0)
     * @return void
     */
    public static function time_since($original, $do_more = 0)
    {
        // array of time period chunks
        $chunks = array(
            array(60 * 60 * 24 * 365 , 'year'),
            array(60 * 60 * 24 * 30 , 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24 , 'day'),
            array(60 * 60 , 'hour'),
            array(60 , 'minute'),
        );

        $today = time();
        $since = $today - $original;

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];

            if (($count = floor($since / $seconds)) != 0)
                break;
        }

        $print = ($count == 1) ? '1 '.$name : "$count {$name}s";

        if ($i + 1 < $j) {
            $seconds2 = $chunks[$i + 1][0];
            $name2 = $chunks[$i + 1][1];

            // add second item if it's greater than 0
            if ( (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) && $do_more )
                $print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
        }
        return $print;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $this->format();

        return $this->tweet;
    }
} 
