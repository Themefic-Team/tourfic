<?php
namespace Tourfic\Admin\TF_Options;
// don't load directly
defined( 'ABSPATH' ) || exit;

class TF_Options {

    private static $instance = null;

    /**
     * Singleton instance
     * @since 1.0.0
     */
    public static function instance() {
        if ( self::$instance == null ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function __construct() {
        //load files
        $this->load_files();

        //load metaboxes
        $this->load_metaboxes();

        //load options
        $this->load_options();

        //load taxonomy
        $this->load_taxonomy();
    }

    public function tf_options_file_path( $file_path = '' ) {
        return plugin_dir_path( __FILE__ ) . $file_path;
    }

    public function tf_options_file_url( $file_url = '' ) {
        return plugin_dir_url( __FILE__ ) . $file_url;
    }

    /**
     * Load files
     * @author Foysal
     */
    public function load_files() {
        // Metaboxes Class
        require_once $this->tf_options_file_path( 'classes/TF_Metabox.php' );
        // Settings Class
        require_once $this->tf_options_file_path( 'classes/TF_Settings.php' );
        //Shortcodes Class
        require_once $this->tf_options_file_path( 'classes/TF_Shortcodes.php' );
        //Taxonomy Class
        require_once $this->tf_options_file_path( 'classes/TF_Taxonomy_Metabox.php' );

        require_once $this->tf_options_file_path( 'fields/icon/fontawesome-4.php');
        require_once $this->tf_options_file_path( 'fields/icon/fontawesome-5.php');
        require_once $this->tf_options_file_path( 'fields/icon/fontawesome-6.php');
        require_once $this->tf_options_file_path( 'fields/icon/remix-icon.php');
    }

    /**
     * Load metaboxes
     * @author Foysal
     */
    public function load_metaboxes() {
        if ( $this->is_tf_pro_active() ) {
            $metaboxes = glob( TF_PRO_ADMIN_PATH . 'tf-options/metaboxes/*.php' );
        } else {
            $metaboxes = glob( $this->tf_options_file_path( 'metaboxes/*.php' ) );
        }

        /*if( !empty( $pro_metaboxes ) ) {
            $metaboxes = array_merge( $metaboxes, $pro_metaboxes );
        }*/
        if ( ! empty( $metaboxes ) ) {
            foreach ( $metaboxes as $metabox ) {
                if ( file_exists( $metabox ) ) {
                    require_once $metabox;
                }
            }
        }
    }

    /**
     * Load Options
     * @author Foysal
     */
    public function load_options() {
        if ( $this->is_tf_pro_active() ) {
            $options = glob( TF_PRO_ADMIN_PATH . 'tf-options/options/*.php' );
        } else {
            $options = glob( $this->tf_options_file_path( 'options/*.php' ) );
        }

        if ( ! empty( $options ) ) {
            foreach ( $options as $option ) {
                if ( file_exists( $option ) ) {
                    require_once $option;
                }
            }
        }
    }

    /**
     * Load Taxonomy
     * @author Foysal
     */
    public function load_taxonomy() {
        if ( $this->is_tf_pro_active() ) {
            $taxonomies = glob( TF_PRO_ADMIN_PATH . 'tf-options/taxonomies/*.php' );
        } else {
            $taxonomies = glob( $this->tf_options_file_path( 'taxonomies/*.php' ) );
        }

        if ( ! empty( $taxonomies ) ) {
            foreach ( $taxonomies as $taxonomy ) {
                if ( file_exists( $taxonomy ) ) {
                    require_once $taxonomy;
                }
            }
        }
    }

    /*
     * Field Base
     * @author Foysal
     */
    public function field( $field, $value, $settings_id = '', $parent = '' ) {
        if ( $field['type'] == 'repeater' ) {
            $id = ( ! empty( $settings_id ) ) ? $settings_id . '[' . $field['id'] . '][0]' . '[' . $field['id'] . ']' : $field['id'] . '[0]' . '[' . $field['id'] . ']';
        } else {
            $id = $settings_id . '[' . $field['id'] . ']';
        }

        $class = isset( $field['class'] ) ? $field['class'] : '';

        $is_pro   = isset( $field['is_pro'] ) ? $field['is_pro'] : '';
        $badge_up = isset( $field['badge_up'] ) ? $field['badge_up'] : '';

        if ( function_exists( 'is_tf_pro' ) && is_tf_pro() ) {
            $is_pro = false;
        }
        if ( $is_pro == true ) {
            $class .= ' tf-field-disable tf-field-pro';
        }
        if ( $badge_up == true ) {
            $class .= ' tf-field-disable tf-field-upcoming';
        }
        $tf_meta_box_dep_value = get_post_meta( get_the_ID(), $settings_id, true );


        $depend = '';
        if ( ! empty( $field['dependency'] ) ) {

            $dependency      = $field['dependency'];
            $depend_visible  = '';
            $data_controller = '';
            $data_condition  = '';
            $data_value      = '';
            $data_global     = '';

            if ( is_array( $dependency[0] ) ) {
                $data_controller = implode( '|', array_column( $dependency, 0 ) );
                $data_condition  = implode( '|', array_column( $dependency, 1 ) );
                $data_value      = implode( '|', array_column( $dependency, 2 ) );
                $data_global     = implode( '|', array_column( $dependency, 3 ) );
                $depend_visible  = implode( '|', array_column( $dependency, 4 ) );
            } else {
                $data_controller = ( ! empty( $dependency[0] ) ) ? $dependency[0] : '';
                $data_condition  = ( ! empty( $dependency[1] ) ) ? $dependency[1] : '';
                $data_value      = ( ! empty( $dependency[2] ) ) ? $dependency[2] : '';
                $data_global     = ( ! empty( $dependency[3] ) ) ? $dependency[3] : '';
                $depend_visible  = ( ! empty( $dependency[4] ) ) ? $dependency[4] : '';
            }

            $depend .= ' data-controller="' . esc_attr( $data_controller ) . '' . $parent . '"';
            $depend .= ' data-condition="' . esc_attr( $data_condition ) . '"';
            $depend .= ' data-value="' . esc_attr( $data_value ) . '"';
            $depend .= ( ! empty( $data_global ) ) ? ' data-depend-global="true"' : '';

            $visible = ( ! empty( $depend_visible ) ) ? ' tf-depend-visible' : ' tf-depend-hidden';
        }

        //field width
        $field_width = isset( $field['field_width'] ) && ! empty( $field['field_width'] ) ? esc_attr( $field['field_width'] ) : '100';
        if ( $field_width == '100' ) {
            $field_style = 'width:100%;';
        } else {
            $field_style = 'width:calc(' . $field_width . '% - 10px);';
        }
        ?>

        <div class="tf-field tf-field-<?php echo esc_attr( $field['type'] ); ?> <?php echo esc_attr( $class ); ?> <?php echo ! empty( $visible ) ? wp_kses_post($visible) : ''; ?>" <?php echo ! empty( $depend ) ? wp_kses_post($depend) : ''; ?>
             style="<?php echo esc_attr( $field_style ); ?>">

            <?php if ( ! empty( $field['label'] ) ): ?>
                <label for="<?php echo esc_attr( $id ) ?>" class="tf-field-label">
                    <?php echo esc_html( $field['label'] ) ?>
                    <?php if ( $is_pro ): ?>
                        <div class="tf-csf-badge"><span class="tf-pro"><?php esc_html_e( "Pro", "tourfic" ); ?></span></div>
                    <?php endif; ?>
                    <?php if ( $badge_up ): ?>
                        <div class="tf-csf-badge"><span class="tf-upcoming"><?php esc_html_e( "Upcoming", "tourfic" ); ?></span></div>
                    <?php endif; ?>
                </label>
            <?php endif; ?>

            <?php if ( ! empty( $field['subtitle'] ) ) : ?>
                <span class="tf-field-sub-title"><?php echo wp_kses_post( $field['subtitle'] ) ?></span>
            <?php endif; ?>

            <div class="tf-fieldset">
                <?php
                $fieldClass = 'TF_' . $field['type'];
                if ( class_exists( $fieldClass ) ) {
                    $_field = new $fieldClass( $field, $value, $settings_id, $parent );
                    $_field->render();
                } else {
                    echo '<p>' . esc_html__( 'Field not found!', 'tourfic' ) . '</p>';
                }
                ?>
            </div>
            <?php if ( ! empty( $field['description'] ) ): ?>
                <p class="description"><?php echo wp_kses_post( $field['description'] ) ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    public function is_tf_pro_active() {
        if ( is_plugin_active( 'tourfic-pro/tourfic-pro.php' ) && defined( 'TF_PRO' ) ) {
            return true;
        }

        return false;
    }

}