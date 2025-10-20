<?php
namespace Tourfic\App\Widgets\Elementor\Support;

// don't load directly
defined( 'ABSPATH' ) || exit;

trait Utils {

    /**
	 * Apply CSS property to the widget
     * @param $css_property
     * @return string
     */
	public function tf_apply_dim( $css_property, $important = false ) {
		return "{$css_property}: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} " . ($important ? '!important' : '') . ";";
	}

	/**
	 * Get the current post type being previewed in Elementor editor
	 */
	protected function get_current_post_type() {
		// Check if we're in Elementor editor and have a preview post ID
		if (isset($_GET['tf_preview_post_id']) && !empty($_GET['tf_preview_post_id'])) {
			$preview_post_id = intval($_GET['tf_preview_post_id']);
			$preview_post = get_post($preview_post_id);
			
			if ($preview_post && in_array($preview_post->post_type, ['tf_hotel', 'tf_tours', 'tf_apartment', 'tf_carrental'])) {
				return $preview_post->post_type;
			}
		}
		
		// Fallback to regular post type detection
		return get_post_type();
	}

    /**
     * Elementor conditions for single widgets (no "service" control).
     * - Service keys: tf_hotel, tf_tours, tf_apartment, tf_carrental (+ '!' for NOT)
     * - Control-key negation: e.g. 'highlights_style!' => ['style2']
     * - $fallback_show_on_miss:
     *      false (default) => if current service doesn't match any block, control is HIDDEN
     *      true            => if current service doesn't match any block, control is SHOWN
     */
    protected function tf_display_conditionally_single_old2(array $service_map, bool $fallback_show_on_miss = false): array {
        // --- Resolve current service (editor + frontend) ---
        $current = null;

        if (method_exists($this, 'get_current_post_type')) {
            $current = $this->get_current_post_type();
        }
        if (!$current && isset($_GET['tf_preview_post_id'])) {
            $preview_id = intval($_GET['tf_preview_post_id']);
            if ($preview_id) {
                $p = get_post($preview_id);
                if ($p && isset($p->post_type)) $current = $p->post_type;
            }
        }
        if (!$current && function_exists('get_post_type')) {
            $maybe = get_post_type();
            if ($maybe) $current = $maybe;
        }
        if (!$current && isset($GLOBALS['post']) && $GLOBALS['post'] instanceof \WP_Post) {
            $current = $GLOBALS['post']->post_type;
        }

        $groups = [];

        foreach ($service_map as $svc_key => $controls) {
            $svc_not = false;
            if (substr($svc_key, -1) === '!') {
                $svc_not = true;
                $svc_key = rtrim($svc_key, '!');
            }

            $matches = $current
                ? (($current === $svc_key && !$svc_not) || ($current !== $svc_key && $svc_not))
                : false;

            if (!$matches) continue;

            // AND across control keys for this matched service
            $and_terms = [];

            foreach ($controls as $control_key_raw => $values) {
                $ctrl_not    = false;
                $control_key = $control_key_raw;

                if (substr($control_key_raw, -1) === '!') {
                    $ctrl_not    = true;
                    $control_key = rtrim($control_key_raw, '!');
                }

                $vals = (array) $values;

                if ($ctrl_not) {
                    // Exclusion => AND of "!="
                    foreach ($vals as $val) {
                        $and_terms[] = [
                            'name'     => $control_key,
                            'operator' => '!=',
                            'value'    => $val,
                        ];
                    }
                } else {
                    // Inclusion => single "==" OR group (OR of "==")
                    if (count($vals) <= 1) {
                        $and_terms[] = [
                            'name'     => $control_key,
                            'operator' => '==',
                            'value'    => reset($vals),
                        ];
                    } else {
                        $or_group = ['relation' => 'or', 'terms' => []];
                        foreach ($vals as $val) {
                            $or_group['terms'][] = [
                                'name'     => $control_key,
                                'operator' => '==',
                                'value'    => $val,
                            ];
                        }
                        $and_terms[] = $or_group;
                    }
                }
            }

            if (!empty($and_terms)) {
                $groups[] = [
                    'relation' => 'and',
                    'terms'    => $and_terms,
                ];
            }
        }

        // --- Fallback when no service block matched or service unknown ---
        if (empty($groups)) {
            if ($fallback_show_on_miss) {
                // Always-true (Elementor-safe)
                return [
                    'relation' => 'or',
                    'terms'    => [
                        ['relation' => 'and', 'terms' => []],
                    ],
                ];
            }
            // Always-false (Elementor-safe)
            return [
                'relation' => 'or',
                'terms'    => [
                    [
                        'relation' => 'and',
                        'terms'    => [
                            ['name' => '__tf_dummy__', 'operator' => '==', 'value' => '__never__'],
                        ],
                    ],
                ],
            ];
        }

        return [
            'relation' => 'or',
            'terms'    => $groups,
        ];
    }


    /**
     * Return Elementor "conditions" for single widgets (no service control).
     * - Service keys: tf_hotel, tf_tours, tf_apartment, tf_carrental (+ '!').
     * - Control-key negation supported: e.g. 'booking_form_style!' => ['style3'].
     * - If service can't be resolved OR no blocks match, returns null (no conditions).
     */
    protected function tf_display_conditionally_single_old1(array $service_map): ?array {
        // Resolve current service/post type robustly in editor & frontend
        $current = null;

        if (method_exists($this, 'get_current_post_type')) {
            $current = $this->get_current_post_type();
        }

        if (!$current && isset($_GET['tf_preview_post_id'])) {
            $preview_id = intval($_GET['tf_preview_post_id']);
            if ($preview_id) {
                $p = get_post($preview_id);
                if ($p && isset($p->post_type)) {
                    $current = $p->post_type;
                }
            }
        }

        if (!$current && function_exists('get_post_type')) {
            $maybe = get_post_type();
            if ($maybe) {
                $current = $maybe;
            }
        }

        // Only proceed if we’re on one of the TF services; otherwise, no conditions
        $services = ['tf_hotel','tf_tours','tf_apartment','tf_carrental'];
        if (!in_array($current, $services, true)) {
            return null; // show control; avoids Elementor warnings
        }

        $groups = [];

        foreach ($service_map as $svc_key => $controls) {
            // Build AND across control keys for this matched service
            $and_terms = [];

            foreach ($controls as $control_key_raw => $values) {
                $ctrl_not   = false;
                $control_key = $control_key_raw;

                // Support 'key!' negation (NOT)
                if (substr($control_key_raw, -1) === '!') {
                    $ctrl_not   = true;
                    $control_key = rtrim($control_key_raw, '!');
                }
                $vals = (array) $values;

                if ($ctrl_not) {
                    // Exclusion => AND of "!="
                    foreach ($vals as $val) {
                        $and_terms[] = [
                            'name'     => $control_key,
                            'operator' => '!=',
                            'value'    => $val,
                        ];
                    }
                } else {
                    // Inclusion => single "==" OR group of "=="
                    if (count($vals) <= 1) {
                        $and_terms[] = [
                            'name'     => $control_key,
                            'operator' => '==',
                            'value'    => reset($vals),
                        ];
                    } else {
                        $or_group = ['relation' => 'or', 'terms' => []];
                        foreach ($vals as $val) {
                            $or_group['terms'][] = [
                                'name'     => $control_key,
                                'operator' => '==',
                                'value'    => $val,
                            ];
                        }
                        $and_terms[] = $or_group;
                    }
                }
            }

            if (!empty($and_terms)) {
                $groups[] = [
                    'relation' => 'and',
                    'terms'    => $and_terms,
                ];
            }
        }

        // If no service block matched (e.g., you only specified hotel/tour and we're on apartment), show control
        if (empty($groups)) {
            return null;
        }

        return [
            'relation' => 'or',
            'terms'    => $groups,
        ];
    }


    /**
     * Return Elementor "conditions" array for single widgets (no service control).
     * - Supports service keys: 'tf_hotel', 'tf_tours', 'tf_apartment', 'tf_carrental' (and '...!' negation on service)
     * - Supports control-key negation: e.g. 'booking_form_style!' => ['style3'] means "NOT style3"
     * - If no service block matches, returns null => Elementor shows the control with no conditions (no warnings).
     *
     * Example:
     * 'conditions' => $this->tf_display_conditionally_single([
     *   'tf_hotel' => [ 'booking_form_style!' => ['style3'] ],
     *   'tf_tours' => [ 'booking_form_style!' => ['style3'] ],
     * ]),
     */
    protected function tf_display_conditionally_single(array $service_map): ?array {
        // Detect current service from post type
        $current = method_exists($this, 'get_current_post_type') ? $this->get_current_post_type() : (function_exists('get_post_type') ? get_post_type() : null);
        if (!in_array($current, ['tf_hotel','tf_tours','tf_apartment','tf_carrental'], true)) {
            $current = 'tf_hotel'; // safe fallback
        }

        $groups = [];

        foreach ($service_map as $svc_key => $controls) {
            $svc_not = false;
            if (substr($svc_key, -1) === '!') {
                $svc_not = true;
                $svc_key = rtrim($svc_key, '!');
            }

            $matches = ($current === $svc_key && !$svc_not) || ($current !== $svc_key && $svc_not);
            if (!$matches) {
                continue; // skip this service block
            }

            // AND across different control keys for this service
            $and_terms = [];

            foreach ($controls as $control_key_raw => $values) {
                $ctrl_not = false;
                $control_key = $control_key_raw;

                // Support key-level negation like 'booking_form_style!'
                if (substr($control_key_raw, -1) === '!') {
                    $ctrl_not    = true;
                    $control_key = rtrim($control_key_raw, '!');
                }

                $vals = (array) $values;

                if ($ctrl_not) {
                    // Exclusion: all values must be != (AND of !=)
                    foreach ($vals as $val) {
                        $and_terms[] = [
                            'name'     => $control_key,
                            'operator' => '!=',
                            'value'    => $val,
                        ];
                    }
                } else {
                    // Inclusion: single value => == ; multiple => OR of ==
                    if (count($vals) <= 1) {
                        $and_terms[] = [
                            'name'     => $control_key,
                            'operator' => '==',
                            'value'    => reset($vals),
                        ];
                    } else {
                        $or_group = ['relation' => 'or', 'terms' => []];
                        foreach ($vals as $val) {
                            $or_group['terms'][] = [
                                'name'     => $control_key,
                                'operator' => '==',
                                'value'    => $val,
                            ];
                        }
                        $and_terms[] = $or_group;
                    }
                }
            }

            if (!empty($and_terms)) {
                $groups[] = [
                    'relation' => 'and',
                    'terms'    => $and_terms,
                ];
            }
        }

        // If no service block matched (e.g., apartment/car not listed), return null (no conditions) => show control.
        if (empty($groups)) {
            return null;
        }

        return [
            'relation' => 'or',
            'terms'    => $groups,
        ];
    }

    /**
     * Single-template conditional builder without a "service" control.
     *
     * Usage:
     * 'conditions' => $this->tf_display_conditionally_single([
     *     'tf_hotel' => [
     *         'booking_form_style' => ['style2','style3'],
     *         'another_control'    => 'on',
     *     ],
     *     'tf_apartment' => [
     *         'booking_form_style' => ['style1','default'],
     *     ],
     *     'tf_carrental!' => [
     *         'layout_style' => ['compact'],
     *     ],
     * ]),
     */
    protected function tf_display_conditionally_single_old(array $service_map): array {
        $current = $this->get_current_post_type();
        $groups  = [];

        foreach ($service_map as $svc_key => $controls) {
            $is_not = false;
            if (substr($svc_key, -1) === '!') {
                $is_not = true;
                $svc_key = rtrim($svc_key, '!');
            }

            $matches = ($current === $svc_key && !$is_not) || ($current !== $svc_key && $is_not);
            if (!$matches) {
                continue;
            }

            // AND across different control keys
            $and_terms = [];

            foreach ($controls as $control_key => $values) {
                $vals = (array) $values;

                if (count($vals) <= 1) {
                    // Single value => simple equality
                    $and_terms[] = [
                        'name'     => $control_key,
                        'operator' => '==',
                        'value'    => reset($vals),
                    ];
                } else {
                    // Multiple values => OR group for this control key
                    $or_group = ['relation' => 'or', 'terms' => []];
                    foreach ($vals as $val) {
                        $or_group['terms'][] = [
                            'name'     => $control_key,
                            'operator' => '==',
                            'value'    => $val,
                        ];
                    }
                    $and_terms[] = $or_group;
                }
            }

            // Only add if we produced conditions
            if (!empty($and_terms)) {
                $groups[] = [
                    'relation' => 'and',
                    'terms'    => $and_terms,
                ];
            }
        }

        // If no group matched, default to "show" (empty AND group)
        if (empty($groups)) {
            $groups[] = ['relation' => 'and', 'terms' => []];
        }

        return [
            'relation' => 'or',
            'terms'    => $groups,
        ];
    }

    /**
     * Generates conditional display rules for controls based on service and design
     * 
     * @param array $design Array of design conditions in format ['service' => 'design_value']
     * @return array Condition array for Elementor controls
     */
    protected function tf_display_conditionally($design, $extra_conditions = []) {
        $terms = [];
        
        foreach ($design as $service_key => $design_values) {
			// Detect if this is a "NOT" condition
            $is_not = false;
            if ( substr( $service_key, -1 ) === '!' ) {
                $is_not = true;
                $service = rtrim( $service_key, '!' );
            } else {
                $service = $service_key;
            }

            // Convert to array if it's not already
            $design_values = (array) $design_values;
            $design_control = 'design_' . str_replace('tf_', '', $service);

            foreach ($design_values as $design_value) {
                $service_terms = [
					[
						'name' => 'service',
						'operator' => $is_not ? '!=' : '==',
						'value' => $service,
					],
					[
						'name' => $design_control,
						'operator' => '==',
						'value' => $design_value,
					]
				];

				// Add extra conditions if provided
				if (!empty($extra_conditions)) {
					foreach ($extra_conditions as $key => $value) {
						$operator = '==';
						$actual_key = $key;
						
						// Handle negation operator
						if (substr($key, -1) === '!') {
							$operator = '!=';
							$actual_key = substr($key, 0, -1);
						}
						
						$service_terms[] = [
							'name' => $actual_key,
							'operator' => $operator,
							'value' => $value,
						];
					}
				}
				
				$terms[] = [
					'relation' => 'and',
					'terms' => $service_terms,
				];
            }
        }

        return [
            'relation' => 'or',
            'terms' => $terms,
        ];
    }
}
