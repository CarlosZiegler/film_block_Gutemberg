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
    
    
    function register_script(){
        
        // register Stylesheet of Plugin
        wp_register_script(
            'custom_admin_js',
            //where is my style file
            plugin_dir_url(__FILE__) . 'assets/js/bootstrap.js'
        
        );
        //enqueue Style in $WP_filter
        wp_enqueue_style('custom_admin_js');
    }
    
    function register_style(){
        
        wp_register_style(
            'custom_admin_css',
            //where is my style file
            plugin_dir_url(__FILE__) . 'assets/css/bootstrap.css'
        
        );
        //enqueue Style in $WP_filter
        wp_enqueue_style('custom_admin_css');
    }
    
    function create_object_block(){
        
        
        // Block is a Class from Carbon fields
        $new_Block = Block::make('Film')// Name of Block, method make return the instance of Object block
        
        ->add_fields(array( // type , slug , Titel of field
                         Field::make('text', 'name_film', 'Name of the Film'),
                         Field::make('image', 'poster', 'Poster'),
                         Field::make('text', 'director', 'Director'),
                         Field::make('textarea', 'description_film', __('Description'))
                             ->set_rows(4),
                         Field::make('text', 'wikipedia', 'Link Wikipedia'),
                         Field::make('text', 'trailer', 'Link trailer'),
            
                         // Complex fields from Carbon Fields with Class Fiel from Carbon Fields
                         Field::make('complex', 'cast', 'Movies cast')
                             ->add_fields(array(
                                              Field::make('text', 'actor', 'Actor'),
                                              Field::make('image', 'actor_foto', 'Foto Actor')
                                          )),
                     ));
        return $new_Block;
    }
    
    function set_Block($new_Block){
        
        // Set a Style
        $new_Block
            ->set_style('custom_admin_css')
            // Set description
            ->set_description(__('A simple block consisting of a heading, an image and a text content.'))
            // Set category in Gutemberg block
            ->set_category('layout')
            // Set icon
            ->set_icon('heart');
        // Function renderize Block, musst return a String HTML
        return $new_Block;
    }
    
    function render_template($new_Block){
        
        $new_Block->set_render_callback(function ($block) {
            
            // The HTML Template
            ?>
            <div class="container card">
                <?php ?>
                <div class="card-body">
                    <!--Block Title-->
                    <?php if (esc_html($block['name_film']) !== '') { ?>
                        <h1 class="text-center card-title">
                            <strong><?php echo esc_html($block['name_film']); ?></strong>
                        </h1>
                    <?php } ?>
                    <!--Block Image-->
                    <?php if (esc_html($block['poster']) !== '') { ?>
                        <div class="container"><?php echo wp_get_attachment_image($block['poster'], array('700', '600'), "", array("class" => "rounded mx-auto d-block card-img-top ")); ?></div>
                    <?php } ?>
                    <!-- Director Name Block-->
                    <h3 class="card-text">
                        <strong>Director: </strong>  <?php echo esc_html($block['director']); ?></strong></h3>
                    <!-- Description Block -->
                    <h3 class="card-text"><strong>Description:</strong></h3>
                    <p class="text-justify"> <?php echo esc_html($block['description_film']); ?></p>
                    <!--Cast Block-->
                    <h3><strong>Cast:</strong></h3>
                    <?php
                        // We need to use a foreach to read the complex field elements
                        foreach ($block['cast'] as $actor) {
                            ?>
                            <!--Image Actor-->
                            <figure class="figure p-1">
                                <?php echo wp_get_attachment_image(esc_html($actor['actor_foto']), array('150', ''), "", array("class" => "figure-img img-fluid rounded")); ?>
                                <!--Name Actor in caption of the Image-->
                                <figcaption
                                        
                                        class="figure-caption text-center">  <?php echo esc_html($actor['actor']); ?></figcaption>
                            </figure>
                        <?php }
                    ?>
                    
                    <!--Block Youtube Link Trailer -->
                    <h3><strong>Trailer:</strong></h3>
                    <div class="embed-responsive embed-responsive-16by9 mt-2">
                        <?php $link_video = esc_html($block['trailer']);
                            $changed = "watch?v=";
                            $link_video = str_replace($changed, "embed/", $link_video);
                        ?>
                        <iframe class="embed-responsive-item" src="<?php echo $link_video; ?>" width="640"
                                height="360"
                                frameborder="0"></iframe>
                    </div>
                    <!--Button Wikipedia-->
                    <div class="container margin">
                        <a href="<?php echo $block['wikipedia'] ?>" target="_blank" class="btn btn-info mt-2">zum Wikipedia</a>
                    </div>
                </div>
            </div>
            
            
            <?php
            
        }
        );
    }
    
    function films_attach_theme_options()
    {
        
        register_script();
        register_style();
        $created_block=create_object_block();
        render_template(set_Block($created_block));
        
        
    }
    
    
    add_action('carbon_fields_register_fields', 'films_attach_theme_options');

    