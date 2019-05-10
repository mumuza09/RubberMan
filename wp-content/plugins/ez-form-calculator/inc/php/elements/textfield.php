<?php

class Ezfc_Element_Textfield extends Ezfc_Element {
	public function get_output() {
		$el_text = "";

		$add_attr = "data-initvalue='" . esc_attr($this->data->value) . "'";

		// readonly
		if (!empty($this->data->read_only)) $add_attr .= " readonly";
		// max length
		if (isset($this->data->max_length) && $this->data->max_length != "") $add_attr .= " maxlength='{$this->data->max_length}'";

		$el_text .= "<textarea class='{$this->data->class} ezfc-element ezfc-element-textarea' id='{$this->output["element_child_id"]}' name='{$this->output["element_name"]}' placeholder='{$this->data->placeholder}' {$this->output["style"]} {$this->output["required"]} {$add_attr}>{$this->data->value}</textarea>";

		return $el_text;
	}
}