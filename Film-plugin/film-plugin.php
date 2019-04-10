<?php
    /**
     * Plugin Name: Film PLugin
     * Text Domain: film
     */
    
    // Carbon Fields use Namespace
    use Carbon_Fields\Block; //use Class Block from Carbon Fields
    use Carbon_Fields\Field; //use Class Field from Carbon Fields
    
    defined('ABSPATH') || exit;
    // call Library of Carbon Fields
    function films_load()
    {
        require_once('vendor/autoload.php');
        \Carbon_Fields\Carbon_Fields::boot();
    }
    
    // wp hook
    add_action('after_setup_theme', 'films_load');
    
    
    function films_attach_theme_options()
    {
        // original do tutorial
         // register Stylesheet of Plugin
        wp_register_script(
            'custom_admin_js',
            //where is my style file
            plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap.js'
            
        );
        //enqueue Style in $WP_filter
        wp_enqueue_style( 'custom_admin_js' );
    
        wp_register_style(
            'custom_admin_css',
            //where is my style file
            plugin_dir_url(__FILE__) . 'assets/css/bootstrap.css'
        
        );
        //enqueue Style in $WP_filter
        wp_enqueue_style('custom_admin_css');
        
        
        // Block is a Class from Carbon fields
        Block::make('Film')// Name of Block, method make return the instance of Object block
        
        ->add_fields(array( // type , slug , Titel of field
                         Field::make('text', 'name_film', 'Name of the Film'),
                         Field::make('text', 'director', 'Director'),
                         Field::make('image', 'poster', 'Poster'),
                         Field::make('text', 'wikipedia', 'Link Wikipedia'),
                         Field::make('text', 'trailer', 'Link trailer'),
                         // Complex fields from Carbon Fields with Class Fiel from Carbon Fields
                         Field::make('complex', 'cast', 'Movies cast')
                             ->add_fields(array(
                                              Field::make('text', 'actor', 'Actor'),
                                          )),
                     ))
            // Set a Style
            ->set_style('custom_admin_css')
            // Set description
            ->set_description(__('A simple block consisting of a heading, an image and a text content.'))
            // Set category in Gutemberg block
            ->set_category('layout')
            // Set icon
            ->set_icon('heart')
            // Function renderize Block, musst return a String HTML
            ->set_render_callback(function ($block) {
                
                
                // The HTML Template
                ?>
                
                
                <div class="container card">
                    
                    
                    <div class="card-body">
                        <h1 class="text-center card-title"><strong><?php echo esc_html($block['name_film']); ?></strong></h1>
                        <?php echo wp_get_attachment_image($block['poster'], array('700', '600'), "", array("class" => "rounded mx-auto d-block card-img-top ")); ?>
                        
                        <h3 class="card-text">Director: <strong> <?php echo esc_html($block['director']); ?></strong></h3>
                        
                        
                        <ul class="card-text list-group"><h3> Cast:</h3>
                            <?php
                                // We need to use a foreach to read the complex field elements
                                foreach ($block['cast'] as $actor) {
                                    ?>
                                    <li class="list-group-item list-group-item-info">
                                        <?php echo esc_html($actor['actor']); ?>
                                    </li>
                                <?php }
                            ?>
                        </ul>
                        <div class="embed-responsive embed-responsive-16by9 mt-2">
                            <?php $link_video = $block['trailer'];
                                $changed = "watch?v=";
                                $link_video = str_replace($changed, "embed/", $link_video);
                            ?>
                            
                            <iframe class="embed-responsive-item" src="<?php echo $link_video; ?>" width="640" height="360" frameborder="0"></iframe>
                        </div>
                        <div class="container margin">
                        <a href="<?php echo $block['wikipedia'] ?>" target="_blank" class="btn btn-primary mt-2">zum Wikipedia</a>
                        </div>
                    </div>
                </div>
                
                
                <?php
                
            }
            );
    }
    
    
    add_action('carbon_fields_register_fields', 'films_attach_theme_options');

    