<?php

namespace Tourfic\App\Shortcodes;

defined( 'ABSPATH' ) || exit;

class Component extends \Tourfic\Core\Shortcodes {

	use \Tourfic\Traits\Singleton;

	protected $shortcode = 'tf_component';

	function render( $atts, $content = null ) {
		$defaults = array(
			'type' => null,
		);
		$atts     = wp_parse_args( $atts, $defaults );
		?>
        <h2>Button</h2>
        <div class="tf_btn_group" style="margin-bottom: 30px">
            <a href="#" class="tf_btn">Button Primary</a>
            <a href="#" class="tf_btn tf_btn_secondary">Button Secondery</a>
            <a href="#" class="tf_btn tf_btn_white">Button Secondery</a>
            <a href="#" class="tf_btn tf_btn_small">Button Small</a>
            <a href="#" class="tf_btn tf_btn_large">Button Large</a>
            <a href="#" class="tf_btn disabled">Button Disabled</a>
            <button class="tf_btn tf_btn_secondary" disabled>Button Disabled</button>
            <a href="#" class="tf_btn tf_btn_full">Button Full</a>
        </div>
        
        <h4>Button Border</h4>
        <div class="tf_btn_group" style="margin-bottom: 30px">
            <a href="#" class="tf_btn tf_btn_border">Button Primary</a>
            <a href="#" class="tf_btn tf_btn_secondary tf_btn_border">Button Secondery</a>
            <a href="#" class="tf_btn tf_btn_white tf_btn_border">Button Secondery</a>
            <a href="#" class="tf_btn tf_btn_small tf_btn_border">Button Small</a>
            <a href="#" class="tf_btn tf_btn_large tf_btn_border">Button Large</a>
            <a href="#" class="tf_btn tf_btn_border disabled">Button Disabled</a>
            <button class="tf_btn tf_btn_secondary tf_btn_border" disabled>Button Disabled</button>
            <a href="#" class="tf_btn tf_btn_full tf_btn_border">Button Full</a>
        </div>

        <h2>Modal</h2>
        <div class="tf_btn_group" style="margin-bottom: 30px">
            <a class="tf_btn tf-modal-btn" data-target="#tf-modal">Modal</a>
            <a class="tf_btn tf-modal-btn" data-target="#tf-modal-small">Modal Small</a>
            <a class="tf_btn tf-modal-btn" data-target="#tf-modal-large">Modal Large</a>
            <a class="tf_btn tf-modal-btn" data-target="#tf-modal-fullscreen">Modal Fullscreen</a>
        </div>

        <div class="tf-modal" id="tf-modal">
            <div class="tf-modal-dialog">
                <div class="tf-modal-content">
                    <div class="tf-modal-header">
                        <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                    </div>
                    <div class="tf-modal-body">
                        Normal Modal
                    </div>
                </div>
            </div>
        </div>

        <div class="tf-modal tf-modal-small" id="tf-modal-small">
            <div class="tf-modal-dialog">
                <div class="tf-modal-content">
                    <div class="tf-modal-header">
                        <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                    </div>
                    <div class="tf-modal-body">
                        Small Modal
                    </div>
                </div>
            </div>
        </div>

        <div class="tf-modal tf-modal-large" id="tf-modal-large">
            <div class="tf-modal-dialog">
                <div class="tf-modal-content">
                    <div class="tf-modal-header">
                        <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                    </div>
                    <div class="tf-modal-body">
                        Large Modal
                    </div>
                </div>
            </div>
        </div>

        <div class="tf-modal tf-modal-fullscreen" id="tf-modal-fullscreen">
            <div class="tf-modal-dialog">
                <div class="tf-modal-content">
                    <div class="tf-modal-header">
                        <a data-dismiss="modal" class="tf-modal-close">&#10005;</a>
                    </div>
                    <div class="tf-modal-body">
                        Fullscreen Modal
                    </div>
                </div>
            </div>
        </div>

        <h2>Ajax Loader</h2>
        <div class="tf_btn_group" style="margin-bottom: 30px">
            <button class="tf_btn tf-btn-loading">Button</button>
        </div>
        <?php
	}
}