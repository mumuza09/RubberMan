<?php

class Ezfc_Element {
	// frontend class
	public $frontend;
	// form options
	public $options;
	// element object
	public $element;
	// element id
	public $e_id;
	// form object
	public $form;
	// form element id
	public $id;
	// form element data
	public $data;
	// output
	public $output = array();

	public function __construct($form, $element, $id = null, $type = "input") {
		$this->frontend = Ezfc_frontend::instance();

		// form
		$this->form = $form;
		// element
		$this->element = $element;
		// element id
		$this->e_id = $element->e_id;
		// form element id
		$this->id = $id;
		// element type
		$this->type = $type;

		// default vars
		$this->add_vars            = array();
		$this->element_css_classes = "";
		$this->element_js_vars     = array();
		$this->step                = false;
	}

	public function set_element_data($data) {
		$this->data = $data;
	}

	// frontend output
	public function prepare_output($options, $add_vars = array()) {
		$this->options = $options;

		// wrapper id
		$this->output["element_id"] = "ezfc_element-{$this->id}";
		// input id
		$this->output["element_child_id"] = $this->output["element_id"] . "-child";
		// input name
		$this->output["element_name"] = $options["hard_submit"] == 1 ? "ezfc_element[{$this->data->name}]" : "ezfc_element[{$this->id}]";

		// additional vars
		$this->add_vars = $add_vars;

		// check if required
		$this->prepare_required();

		// prepare label
		$this->prepare_label();

		// prepare factor value
		$this->prepare_factor();
		// prepare styles
		$this->prepare_styles();
	}

	public function prepare_factor() {
		$factor = Ezfc_Functions::get_object_value($this->data, "factor", 1);

		if ($factor == "") {
			$factor = 1;
		}

		$this->factor           = $factor;
		$this->output["factor"] = "data-factor='{$factor}'";
	}

	public function prepare_label() {
		// data label
		$el_data_label = "";

		// trim labels
		if (property_exists($this->data, "label")) {
			$tmp_label = trim(htmlspecialchars_decode($this->data->label));

			// todo: cache option
			if (get_option("ezfc_allow_label_shortcodes", 0)) {
				$tmp_label = do_shortcode($tmp_label);
			}

			// placeholders
			$tmp_label = $this->frontend->get_listen_placeholders($this->data, $tmp_label);

			$el_data_label .= $tmp_label;
		}

		// element description
		if (!empty($this->data->description)) {
			$element_description = "<span class='ezfc-element-description ezfc-element-description-{$this->options["description_label_position"]}' data-ezfctip='" . esc_attr($this->data->description) . "'></span>";

			$element_description = apply_filters("ezfc_element_description", $element_description, $this->data->description);

			if ($this->options["description_label_position"] == "before") {
				$el_data_label = $element_description . $el_data_label;
			}
			else {
				$el_data_label = $el_data_label . $element_description;
			}
		}

		// add whitespace for empty labels
		if ($el_data_label == "" && $this->options["add_space_to_empty_label"] == 1) {
			$el_data_label .= " &nbsp;";
		}

		// additional styles
		// todo: globalize / cache option
		$css_label_width = get_option("ezfc_css_form_label_width");
		$css_label_width = empty($css_label_width) ? "" : "style='width: {$css_label_width}'";

		// default label
		$this->default_label = "<label class='ezfc-label' for='{$this->output["element_child_id"]}' {$css_label_width}>" . $el_data_label . "{$this->output["required_char"]}</label>";
	}

	public function prepare_required() {
		$required_check = Ezfc_Functions::get_object_value($this->data, "required", 0);

		$required      = "";
		$required_char = "";

		if ($required_check) {
			$required = "required";

			if ($this->options["show_required_char"] != 0) {
				$required_char = " <span class='ezfc-required-char'>*</span>";
			}
		}

		// is this element required?
		$this->required = $required_check;
		// text to be added in the input element
		$this->output["required"] = $required;
		// required char
		$this->output["required_char"]  = $required_char;
	}

	public function prepare_styles() {
		// inline style
		$this->output["style"] = "";
		if (!empty($this->data->style)) {
			$this->output["style"] = "style='{$this->data->style}'";
		}

		// options container class
		$this->output["options_container_class"] = "";
		// flex
		if (Ezfc_Functions::get_object_value($this->data, "flexbox", 0) == 1) {
			$this->output["options_container_class"] .= " ezfc-flexbox";
		}
	}

	public function prepare_value() {
		global $post;
		global $product; // woocommerce product (perhaps empty)

		// modify value
		if (property_exists($this->data, "value")) {
			// WC attribute
			if (!empty($this->data->value_attribute) && !empty($product) && method_exists($product, "get_attribute")) {
				$this->data->value = $product->get_attribute($this->data->value_attribute);
			}

			// acf
			if (strpos($this->data->value, "acf:") !== false && function_exists("get_field")) {
				$tmp_array = explode(":", $this->data->value);
				$this->data->value = get_field($tmp_array[1]);
			}

			// postmeta
			else if (strpos($this->data->value, "postmeta:") !== false) {
				$tmp_array = explode(":", $this->data->value);
				$this->data->value = get_post_meta(get_the_ID(), $tmp_array[1], true);
			}

			// woocommerce product attribute via this->data->value
			else if (strpos($this->data->value, "wc:") !== false && !empty($product) && method_exists($product, "get_attribute")) {
				$tmp_array = explode(":", $this->data->value);
				$this->data->value = $product->get_attribute($tmp_array[1]);
			}

			// php function
			else if (strpos($this->data->value, "php:") !== false) {
				$tmp_array = explode(":", $this->data->value);
				if (!empty($tmp_array[1]) && function_exists($tmp_array[1])) {
					$this->data->value = htmlspecialchars($tmp_array[1]($element, $this->data, $this->options, $this->form->id), ENT_QUOTES, "UTF-8");
				}
			}

			// replace placeholder values
			$replace_values = $this->frontend->get_frontend_replace_values();
			foreach ($replace_values as $replace => $replace_value) {
				$this->data->value = str_ireplace("{{" . $replace . "}}", $replace_value, $this->data->value);
			}

			// random number
			if ($this->data->value == "__rand__" && property_exists($this->data, "min") && is_numeric($this->data->min) && property_exists($this->data, "max") && is_numeric($this->data->max)) {
				$this->data->value = function_exists("mt_rand") ? mt_rand($this->data->min, $this->data->max) : rand($this->data->min, $this->data->max);
			}

			// shortcode value
			if (get_option("ezfc_allow_value_shortcodes", 1)) {
				$this->data->value = do_shortcode($this->data->value);
			}
		}
	}

	/**
		get css classes for element wrapper
	**/
	public function get_element_css($css_classes) {
		return $css_classes . " " . $this->element_css_classes;
	}

	/**
		get element js vars
	**/
	public function get_element_js_vars($element_js_vars) {
		$element_js_vars = array_merge($element_js_vars, $this->element_js_vars);

		return $element_js_vars;
	}

	/**
		get icon
	**/
	public function get_icon() {
		$icon = "";

		if (!empty($this->data->icon)) {
			$icon = "<i class='fa {$this->data->icon}'></i>";
			// add icon class
			$this->data->class .= " ezfc-has-icon";
		}

		return $icon;
	}

	/**
		get label
	**/
	public function get_label() {
		return $this->default_label;
	}

	/**
		get default output (input field)
	**/
	public function get_output() {
		$el_text  = "";
		$add_attr = "";

		// readonly
		if (!empty($this->data->read_only)) $add_attr .= " readonly";
		// max length
		if (property_exists($this->data, "max_length") && $this->data->max_length != "") $add_attr .= " maxlength='{$this->data->max_length}'";
		// autocomplete
		if (property_exists($this->data, "autocomplete") && $this->data->autocomplete == 0) $add_attr .= " autocomplete='something-new'";

		// value
		$value = "";
		if (property_exists($this->data, "value")) {
			$add_attr .= " data-initvalue='" . esc_attr($this->data->value) . "'";
			$value     = $this->data->value;
		}

		// placeholder
		$placeholder = Ezfc_Functions::get_object_value($this->data, "placeholder", "");

		// tel
		$input_type = empty($this->data->is_telephone_nr) ? "text" : "tel";

		$css_classes = property_exists($this->data, "class") ? $this->data->class : "";

		$el_text .= "<input	class='{$css_classes} ezfc-element ezfc-element-input' {$this->output["factor"]} id='{$this->output["element_child_id"]}' name='{$this->output["element_name"]}' placeholder='{$placeholder}' type='{$input_type}' value='{$value}' {$add_attr} {$this->output["style"]} {$this->output["required"]} />";

		return $el_text;
	}
}