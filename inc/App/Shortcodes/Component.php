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
            <button class="tf_btn" disabled>Button Disabled</button>
            <a href="#" class="tf_btn tf_btn_full">Button Full</a>
            <a href="#" class="tf_btn tf_btn_sharp">Button Sharp</a>
        </div>
        
        <h4>Button Border</h4>
        <div class="tf_btn_group" style="margin-bottom: 30px">
            <a href="#" class="tf_btn tf_btn_outline">Button Primary</a>
            <a href="#" class="tf_btn tf_btn_secondary tf_btn_outline">Button Secondery</a>
            <a href="#" class="tf_btn tf_btn_white tf_btn_outline">Button Secondery</a>
            <a href="#" class="tf_btn tf_btn_small tf_btn_outline">Button Small</a>
            <a href="#" class="tf_btn tf_btn_large tf_btn_outline">Button Large</a>
            <a href="#" class="tf_btn tf_btn_outline disabled">Button Disabled</a>
            <button class="tf_btn tf_btn_secondary tf_btn_outline" disabled>Button Disabled</button>
            <a href="#" class="tf_btn tf_btn_full tf_btn_outline">Button Full</a>
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
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime mollitia,
molestiae quas vel sint commodi repudiandae consequuntur voluptatum laborum
numquam blanditiis harum quisquam eius sed odit fugiat iusto fuga praesentium
optio, eaque rerum! Provident similique accusantium nemo autem. Veritatis
obcaecati tenetur iure eius earum ut molestias architecto voluptate aliquam
nihil, eveniet aliquid culpa officia aut! Impedit sit sunt quaerat, odit,
tenetur error, harum nesciunt ipsum debitis quas aliquid. Reprehenderit,
quia. Quo neque error repudiandae fuga? Ipsa laudantium molestias eos 
sapiente officiis modi at sunt excepturi expedita sint? Sed quibusdam
recusandae alias error harum maxime adipisci amet laborum. Perspiciatis 
minima nesciunt dolorem! Officiis iure rerum voluptates a cumque velit 
quibusdam sed amet tempora. Sit laborum ab, eius fugit doloribus tenetur 
fugiat, temporibus enim commodi iusto libero magni deleniti quod quam 
consequuntur! Commodi minima excepturi repudiandae velit hic maxime
doloremque. Quaerat provident commodi consectetur veniam similique ad 
earum omnis ipsum saepe, voluptas, hic voluptates pariatur est explicabo 
fugiat, dolorum eligendi quam cupiditate excepturi mollitia maiores labore 
suscipit quas? Nulla, placeat. Voluptatem quaerat non architecto ab laudantium
modi minima sunt esse temporibus sint culpa, recusandae aliquam numquam 
totam ratione voluptas quod exercitationem fuga. Possimus quis earum veniam 
quasi aliquam eligendi, placeat qui corporis!
Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime mollitia,
molestiae quas vel sint commodi repudiandae consequuntur voluptatum laborum
numquam blanditiis harum quisquam eius sed odit fugiat iusto fuga praesentium
optio, eaque rerum! Provident similique accusantium nemo autem. Veritatis
obcaecati tenetur iure eius earum ut molestias architecto voluptate aliquam
nihil, eveniet aliquid culpa officia aut! Impedit sit sunt quaerat, odit,
tenetur error, harum nesciunt ipsum debitis quas aliquid. Reprehenderit,
quia. Quo neque error repudiandae fuga? Ipsa laudantium molestias eos 
sapiente officiis modi at sunt excepturi expedita sint? Sed quibusdam
recusandae alias error harum maxime adipisci amet laborum. Perspiciatis 
minima nesciunt dolorem! Officiis iure rerum voluptates a cumque velit 
quibusdam sed amet tempora. Sit laborum ab, eius fugit doloribus tenetur 
fugiat, temporibus enim commodi iusto libero magni deleniti quod quam 
consequuntur! Commodi minima excepturi repudiandae velit hic maxime
doloremque. Quaerat provident commodi consectetur veniam similique ad 
earum omnis ipsum saepe, voluptas, hic voluptates pariatur est explicabo 
fugiat, dolorum eligendi quam cupiditate excepturi mollitia maiores labore 
suscipit quas? Nulla, placeat. Voluptatem quaerat non architecto ab laudantium
modi minima sunt esse temporibus sint culpa, recusandae aliquam numquam 
totam ratione voluptas quod exercitationem fuga. Possimus quis earum veniam 
quasi aliquam eligendi, placeat qui corporis!
Lorem ipsum dolor sit amet consectetur adipisicing elit. Maxime mollitia,
molestiae quas vel sint commodi repudiandae consequuntur voluptatum laborum
numquam blanditiis harum quisquam eius sed odit fugiat iusto fuga praesentium
optio, eaque rerum! Provident similique accusantium nemo autem. Veritatis
obcaecati tenetur iure eius earum ut molestias architecto voluptate aliquam
nihil, eveniet aliquid culpa officia aut! Impedit sit sunt quaerat, odit,
tenetur error, harum nesciunt ipsum debitis quas aliquid. Reprehenderit,
quia. Quo neque error repudiandae fuga? Ipsa laudantium molestias eos 
sapiente officiis modi at sunt excepturi expedita sint? Sed quibusdam
recusandae alias error harum maxime adipisci amet laborum. Perspiciatis 
minima nesciunt dolorem! Officiis iure rerum voluptates a cumque velit 
quibusdam sed amet tempora. Sit laborum ab, eius fugit doloribus tenetur 
fugiat, temporibus enim commodi iusto libero magni deleniti quod quam 
consequuntur! Commodi minima excepturi repudiandae velit hic maxime
doloremque. Quaerat provident commodi consectetur veniam similique ad 
earum omnis ipsum saepe, voluptas, hic voluptates pariatur est explicabo 
fugiat, dolorum eligendi quam cupiditate excepturi mollitia maiores labore 
suscipit quas? Nulla, placeat. Voluptatem quaerat non architecto ab laudantium
modi minima sunt esse temporibus sint culpa, recusandae aliquam numquam 
totam ratione voluptas quod exercitationem fuga. Possimus quis earum veniam 
quasi aliquam eligendi, placeat qui corporis!
                    </div>
                </div>
            </div>
        </div>

        <h2>Ajax Loader</h2>
        <div class="tf_btn_group" style="margin-bottom: 30px">
            <button class="tf_btn tf-btn-loading">Button</button>
        </div>

        <h2>Notice</h2>
        <div class="" style="display: flex; flex-wrap: wrap; gap: 8px; width: 100%">
            <div class="tf-notice">
                <div class="tf-notice-icon">
                    <i class="ri-information-fill"></i>
                </div>
                <div class="tf-notice-content has-content">
                    <h6>Info Notice</h6>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                </div>
            </div>
            <div class="tf-notice tf-notice-success">
                <div class="tf-notice-icon">
                    <i class="ri-information-fill"></i>
                </div>
                <div class="tf-notice-content has-content">
                    <h6>Success Notice</h6>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                </div>
            </div>
            <div class="tf-notice tf-notice-warning">
                <div class="tf-notice-icon">
                    <i class="ri-information-fill"></i>
                </div>
                <div class="tf-notice-content has-content">
                    <h6>Info Notice</h6>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                </div>
            </div>
            <div class="tf-notice tf-notice-danger">
                <div class="tf-notice-icon">
                    <i class="ri-information-fill"></i>
                </div>
                <div class="tf-notice-content has-content">
                    <h6>Danger Notice</h6>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                </div>
            </div>
        </div>
        
        <?php
	}
}