<?php namespace Sneek\Widgets;

class Manager 
{
    public function __construct()
    {
        add_action('widgets_init', array($this, 'register_widgets'));
    }

    public function register_widgets()
    {
        register_widget('Sneek\Widgets\Twitter');
    }
} 