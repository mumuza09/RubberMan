/**
	form builder functions
	param $: jQuery object
	param _this: EZFC_Backend
**/
EZFC_Builder_Functions = function($, _this) {
	// batch edit options
	this.batch_edit_save = function() {
		var $active_element = _this.builder_functions.get_active_element();
		var id              = _this.builder_functions.get_active_element_id();

		if (!_this.vars.current_form_elements[id]) return;

		// save data first
		_this.builder_functions.element_data_serialize(id);

		// modify element data
		var batch_values_textarea = $("#ezfc-batch-edit-textarea").val();
		var batch_values_array    = batch_values_textarea.split("\n");
		var options_new           = [];
		for (var i in batch_values_array) {
			var batch_values = batch_values_array[i].split(_this.vars.batch_separator);

			var tmp_options = {};
			for (var b in _this.vars.current_batch_keys) {
				var tmp_batch_value = "";

				if (batch_values[b]) {
					tmp_batch_value = batch_values[b];
				}

				// replace index number
				tmp_batch_value = tmp_batch_value.replace("{{n}}", i);

				tmp_options[_this.vars.current_batch_keys[b]] = tmp_batch_value;
			}

			options_new.push(tmp_options);
		}

		_this.vars.current_form_elements[id].data_json[_this.vars.current_dialog_action] = options_new;
		// write json
		_this.vars.current_form_elements[id].data = JSON.stringify(_this.vars.current_form_elements[id].data_json);

		// re-add element with new values
		_this.maybe_add_data_element($active_element, true);

		$("#ezfc-dialog-batch-edit").dialog("close");
	};

	this.calculation_check_valid = function(expr) {
		var calculation_text_parsed;

		try {
			calculation_text_parsed = math.parse(expr);
		}
		catch (err) {
			calculation_text_parsed = false;
		}

		return calculation_text_parsed;
	};

	// open add element dialog
	this.add_form_element_dialog = function(btn, add_from_position) {
		if (!add_from_position) {
			_this.vars.element_add_from_position = null;
		}
		else {
			_this.vars.element_add_from_position = btn;
		}

		$("#ezfc-add-element-dialog").dialog("open");
		return false;
	};

	// open change element dialog
	this.change_element_dialog = function(btn, id) {
		_this.vars.selected_element = id;
		$("#ezfc-change-element-dialog").dialog("open");
		return false;
	};

	this.check_individual_names = function() {
		var duplicates = [];
		var skip_types = ["stepstart", "stepend", "hr", "spacer", "placeholder", "html", "image", "group"];
		var names = _this.builder_functions.get_element_names(skip_types);			

		$.each(names, function(i, obj) {
			// check for duplicates
			var duplicate = $.grep(names, function(n) {
				// do not check with itself
				if (n.id == obj.id) return false;

				return n.name != "" && n.name == obj.name;
			});

			if (duplicate.length) {
				// add this id to duplicates
				if ($.inArray(obj.id, duplicates) === -1) duplicates.push(obj.id);
			}
		});

		$(".ezfc-form-element-notification-duplicate-name").remove();
		$.each(duplicates, function(i, d) {
			$("#ezfc-form-element-" + d).find(".ezfc-form-element-notification").append("<span class='ezfc-form-element-notification-duplicate-name'><i class='fa fa-info-circle'></i> " + ezfc_vars.texts.duplicate_name + "</span>");
		});
	};

	// html output conditional chain item
	this.conditional_chain_add = function(btn, args_tmp) {
		var args          = args_tmp.split(",");
		var input_name    = args[0];
		var counter       = args[1];
		var input_counter = _this.builder_functions.conditional_chain_get_counter_id(btn);

		var input_name_operator       = input_name + "[" + counter + "][operator_chain][" + input_counter + "]";
		var input_name_value          = input_name + "[" + counter + "][value_chain][" + input_counter + "]";
		var input_name_compare_target = input_name + "[" + counter + "][compare_value][" + input_counter + "]";

		var $cond_wrapper = $(btn).closest(".ezfc-form-element-conditional-wrapper");

		var input = _this.builder_functions.conditional_chain_get_html(input_name_operator, input_name_value, "", "", input_name_compare_target, "");

		$cond_wrapper.append(input);

		_this.custom_trigger_change($(btn).closest(".ezfc-form-element-data"));
	};

	// html output conditional chain
	this.conditional_chain_get_html = function(input_name_operator, input_name_value, input_name_operator_value, input_name_value_value, input_name_compare_target, input_name_compare_value_selected) {
		var input = "<div class='ezfc-conditional-chain-wrapper'>";
		input += "<div class='ezfc-clear'></div>";

		// and/or
		input += "<div class='col-xs-2'><span class='ezfc-conditional-chain-and-or'></span></div>";

		// conditional compare value
		input += "	<div class='col-xs-3'>";
		input += "		<select name='" + input_name_compare_target + "' class='ezfc-conditional-compare-value ezfc-form-element-data-input-has-action fill-elements' data-selected='" + input_name_compare_value_selected + "'>";
		input += 			_this.builder_functions.get_element_names_output(_this.builder_functions.get_element_names(), _this.vars.elements_list_add.conditional);
		input += "		</select>";
		// select target
		input += "		<button class='button ezfc-form-element-conditonal-target-select' data-func='select_target_activate_button'>" + _this.vars.icons.select_target + "</button>";
		input += "	</div>";

		// spacer
		input += "	<div class='col-xs-1'>";
		input += "		&nbsp;";
		input += "	</div>";

		// conditional operator
		input += "	<div class='col-xs-2'>";
		input += _this.get_html_input("select", input_name_operator, {
			class: "ezfc-conditional-chain-operator",
			options: _this.vars.cond_operators,
			selected: input_name_operator_value
		});
		input += "	</div>";

		// conditional value
		input += "	<div class='col-xs-2'>";
		input += _this.get_html_input("input", input_name_value, {
			class: "ezfc-conditional-chain-value",
			value: input_name_value_value
		});
		input += "	</div>";

		// remove button
		input += "	<div class='col-xs-2'>";
		input += "		<button class='button button-delete ezfc-form-element-conditional-chain-remove' data-func='conditional_chain_remove'><i class='fa fa-arrow-left'></i> <i class='fa fa-times'></i></button>";
		input += "	</div>";
		
		input += "<div class='ezfc-clear'></div>";
		input += "</div>";

		return input;
	};

	// return conditional chain counter id
	this.conditional_chain_get_counter_id = function(btn) {
		var $last_wrapper = $(btn).closest(".ezfc-form-element-conditional-wrapper").find(".ezfc-conditional-chain-wrapper:last");

		if ($last_wrapper.length < 1) return 0;

		var input_name_tmp = $last_wrapper.find(".ezfc-conditional-chain-operator").attr("name").split("]");
		var counter = input_name_tmp[4].replace("[", "");
		var counter_new = parseInt(counter) + 1;

		return counter_new;
	};

	// remove conditional chain item
	this.conditional_chain_remove = function(btn) {
		var $wrapper = $(btn).closest(".ezfc-conditional-chain-wrapper");
		$wrapper.remove();
	};

	this.dialog_open = function(btn, tmp_args) {
		var args = tmp_args.split(",");
		var name = args[0];

		if (args.length > 1) {
			_this.vars.current_dialog_action = args[1];
		}

		if (!$(name).length) return false;

		$(name).dialog("open");
	};

	// build data to duplicate group
	this.duplicate_group_build_data = function($group) {
		var group_id = $group.data("id");

		// find elements to duplicate
		var elements_to_duplicate = [group_id];
		elements_to_duplicate = elements_to_duplicate.concat(_this.builder_functions.duplicate_group_get_duplicate_ids($group));
		// get element group IDs (may be dragged to a new group)
		var element_group_ids = ["duplicate_element_id[" + group_id + "]=" + $group.data("group_id")];
		element_group_ids = element_group_ids.concat(_this.builder_functions.duplicate_group_get_duplicate_group_ids(elements_to_duplicate));

		// build string
		var ret = "elements_to_duplicate=" + elements_to_duplicate.join(",") + "&" + element_group_ids.join("&");
		return ret;
	};

	this.duplicate_group_get_duplicate_ids = function($group) {
		var ids = [];
		$group.find(".ezfc-form-element").each(function() {
			ids.push($(this).data("id"));
		});

		return ids;
	};

	this.duplicate_group_get_duplicate_group_ids = function(elements) {
		var out = [];

		$.each(elements, function(i, element_id) {
			var new_group_id = _this.vars.current_form_elements[element_id].data_json.group_id;

			out.push("duplicate_element_id[" + element_id + "]=" + new_group_id);
		});

		return out;
	};

	// close element data
	this.element_data_close = function() {
		var $element       = $(".ezfc-form-element-active");
		var $element_data  = $element.find(".ezfc-form-element-data");
		var element_id     = $element.data("id");
		var element_object = _this.vars.current_form_elements[element_id];

		// temporarily remove disabled fields
		el_disabled_list = $element_data.find("[disabled='disabled']");
		el_disabled_list.removeAttr("disabled");

		// concatenate preselect checkboxes
		if ($element.hasClass("ezfc-form-element-checkbox")) {
			// only concatenate for checkbox preselect options
			var is_checkbox_option_container = $element_data.find("input[name*='preselect_container']").length > 0;
			if (is_checkbox_option_container) {
				var preselect = [];

				$element_data.find("input[name*='preselect_container']").each(function(i, checkbox) {
					if ($(checkbox).is(":checked")) {
						preselect.push($(checkbox).val());
					}
				});

				$element_data.find(".ezfc-form-option-preselect").val(preselect.join(","));
				$element_data.find("input[name*='preselect_container']").remove();
			}
		}

		var data_options = $element_data.serialize();

		// re-add disabled elements
		el_disabled_list.removeAttr("disabled");

		$(".ezfc-form-element-data").hide();
		$("#ezfc-element-data-modal").fadeOut();
		$(".ezfc-form-element-active").removeClass("ezfc-form-element-active");
		$("body").removeClass("overflow-y-hidden");

		// save to current form elements data
		_this.builder_functions.element_data_serialize(element_id);

		return false;
	};
	// open element data
	this.element_data_open = function(id, disable_add_data) {
		var $parent_el = $("#ezfc-form-element-" + id);
		var form_element_data = $parent_el.find("> .ezfc-form-element-data");

		// add active class to form element
		$(".ezfc-form-element-active").removeClass("ezfc-form-element-active");
		$parent_el.addClass("ezfc-form-element-active");

		// only add element data if element hasn't been opened before
		//_this.maybe_add_data_element($parent_el);
		if (!disable_add_data) {
			_this.maybe_add_data_element($parent_el, true);
		}

		// toggle element data and increase z-index
		form_element_data.show().css("z-index", ++_this.vars.ezfc_z_index);

		if (ezfc_vars.editor.use_large_data_editor == 1) {
			var $modal = $("#ezfc-element-data-modal");
			if (!$modal.is(":visible")) $modal.fadeIn();
		}

		_this.builder_functions.set_section(_this.vars.active_section);
		_this.builder_functions.set_section_badges($parent_el);
		_this.custom_trigger_change(form_element_data);
		_this.fill_calculate_fields(false, true);
	};

	this.element_data_serialize = function(element_id) {
		var serialized_data = _this.vars.$form_elements.find("#ezfc-form-element-" + element_id + " :input").serializeObject();

		if (typeof serialized_data["elements"] === "undefined") return;

		_this.vars.current_form_elements[element_id].data_json = serialized_data.elements[element_id];
	};

	this.element_get_data = function(id) {
		if (typeof _this.vars.current_form_elements[id] === "undefined") return false;

		return _this.vars.current_form_elements[id].data_json;
	};

	// element info has calculation
	this.element_has_calculation = function(data) {
		if (typeof data.calculate === "undefined" || typeof data.calculate[0] === "undefined") return false;
		if (typeof data.calculate[0].operator === "undefined" || data.calculate[0].operator == 0) return false;

		return true;
	};
	// element info has condition
	this.element_has_condition = function(data) {
		if (typeof data.conditional === "undefined" || typeof data.conditional[0] === "undefined") return false;
		if (typeof data.conditional[0].action === "undefined" || data.conditional[0].action == 0) return false;

		return true;
	};
	// element info has discount
	this.element_has_discount = function(data) {
		if (typeof data.discount === "undefined" || typeof data.discount[0] === "undefined") return false;
		if (typeof data.discount[0].operator === "undefined" || data.discount[0].operator == 0) return false;

		return true;
	};

	// returns the current list index of the active element
	this.element_index_active = function(id) {
		var $element = _this.builder_functions.get_form_element_dom(id);

		return _this.vars.$form_elements_list.find("li.ezfc-form-element").index($element);
	};

	this.element_index_get = function(index) {
		return _this.vars.$form_elements_list.find("li.ezfc-form-element").eq(index);
	};

	this.element_open_next = function($button, id) {
		var index = _this.builder_functions.element_index_active(id);
		var index_next = index + 1;

		var $next = _this.builder_functions.element_index_get(index_next);
		if (!$next.length) return;

		// serialize
		_this.builder_functions.element_data_serialize(id);

		_this.builder_functions.element_data_open($next.data("id"));
	};
	this.element_open_prev = function($button, id) {
		var index = _this.builder_functions.element_index_active(id);
		var index_prev = index - 1;

		if (index_prev < 0) return;

		var $prev = _this.builder_functions.element_index_get(index_prev);
		if (!$prev.length) return;

		// serialize
		_this.builder_functions.element_data_serialize(id);

		_this.builder_functions.element_data_open($prev.data("id"));
	};

	// copy export data
	this.export_data_copy = function() {
		$("#form-export-data").select();

		if (document.execCommand('copy')) {
			$("#ezfc-export-data-log").fadeIn();

			setTimeout(function() {
				$("#ezfc-export-data-log").fadeOut();
			}, 5000);
		}
	};

	this.form_element_add_from_view = function($btn) {
		// add from position
		if (_this.vars.element_add_from_position) {
			var index;
			var item_count = _this.vars.$form_elements_list.find("li").length;

			// closest but exclude self first
			var $parent_group = $(_this.vars.element_add_from_position).closest(".ezfc-form-element-group");
			var group_id      = $parent_group.length ? $parent_group.data("id") : 0;
			// find last element in group
			var $last_element_in_group = $parent_group.find("> li:last-child");

			// check if elements exists in group
			if (!$last_element_in_group.length) {
				var $last_element_in_group_index = $parent_group.find("li:last-child");

				$last_element_in_group = $parent_group;
				index = item_count - _this.vars.$form_elements_list.find("li").index($last_element_in_group_index);

				$last_element_in_group.find("> .ezfc-group").append(_this.vars.drag_placeholder_html);
			}
			else {
				index = item_count - _this.vars.$form_elements_list.find("li").index($last_element_in_group);

				$last_element_in_group.after(_this.vars.drag_placeholder_html);
			}

			_this.do_action($btn, { position: index, group_id: group_id }, "form_element_add");
		}
		else {
			_this.vars.$form_elements_list.append(_this.vars.drag_placeholder_html);

			_this.do_action($btn, null, "form_element_add");
		}

		// close all dialogs
		$(".ui-dialog-content").dialog("close");

		return false;
	};

	this.forms_sort = function($btn, sort_type) {
		var $forms = $("#ezfc-forms-list .ezfc-form");

		// sort by text
		if (sort_type == "text") {
			$forms.sort(function(a, b) {
				var text_a = $(a).find(".ezfc-form-name").text();
				var text_b = $(b).find(".ezfc-form-name").text();

				return text_a.toUpperCase().localeCompare(text_b.toUpperCase());
			});
		}
		// sort by ID
		else {
			$forms.sort(function(a, b) {
				var text_a = $(a).data("id");
				var text_b = $(b).data("id");

				return text_a - text_b;
			});
		}

		$("#ezfc-forms-list .ezfc-form").remove();
		$("#ezfc-forms-list .clone").after($forms);
	};
	
	this.get_active_element_id = function() {
		var $active_element = _this.builder_functions.get_active_element();
		var id = 0;

		if ($active_element.length) id = $active_element.data("id");

		return id;
	};

	this.get_active_element = function() {
		return $(".ezfc-form-element-active");
	};

	this.get_calculation_text = function(id) {
		// todo
		return;
		var $element = $("li.ezfc-form-element[data-id='" + id + "']");
		var out = ["price"];

		$element.find(".ezfc-row-calculate .ezfc-form-element-option-list-item").each(function(i, v) {
			var $op = $(this).find(".ezfc-form-element-calculate-operator");
			var op  = $op.val();
			var op_text = $op.find(":selected").text();

			if (op_text == "=" && i > 0) op_text = "";

			var $target = $(this).find(".ezfc-form-element-calculate-target");
			var target  = $target.val();
			var target_text;

			if (target == 0) {
				target      = $(this).find(".ezfc-form-element-calculate-value").val();
				target_text = target;
			}
			else if (target == "__open__") {
				target_text = "(";
			}
			else if (target == "__close__") {
				target_text = ")";
			}
			else {
				target_text = $("li.ezfc-form-element[data-id='" + target + "'] .element-label").text();
				target_text = "[" + target_text + "]";
			}

			var tmp_out = "" + op_text;
			if (op_text.length) tmp_out += " ";
			tmp_out += target_text;

			out.push(tmp_out);
		});

		return out.join(" ");
	};

	// html output corrupt element
	this.get_element_error = function(element, data_editor_class) {
		var ret_error = "";
		ret_error += "<li class='ezfc-form-element ezfc-form-element-error ezfc-col-6'>";
		ret_error += "    <div class='ezfc-form-element-name'>Corrupt element";
		ret_error += "        <button class='ezfc-form-element-delete button' data-action='form_element_delete' data-id='" + element.id + "'><i class='fa fa-times'></i></button>";
		ret_error += "    </div>";
		ret_error += "    <div class='container-fluid ezfc-form-element-data ezfc-form-element-input ezfc-hidden " + data_editor_class + "'>";

		if (typeof element === "object") {
			ret_error += "        <p>" + JSON.stringify(element) + "</p>";
		}
		
		ret_error += "    </div>";
		ret_error += "</li>";

		return ret_error;
	};

	this.get_element_names = function(skip_types, include_types) {
		skip_types    = skip_types || [];
		include_types = include_types || [];
		var names     = [];

		// get names first
		$.each(_this.vars.current_form_elements, function(i, element) {
			if (element === undefined) return;

			var e_id;
			// extension
			if (element.data_json.extension !== undefined && ezfc.elements[element.data_json.extension] !== undefined) {
				e_id = ezfc.elements[element.data_json.extension].type;
			}
			// inbuilt element
			else if (ezfc.elements[element.e_id] !== undefined) {
				e_id = element.e_id;
			}
			// undefined element
			else return;

			var type = ezfc.elements[e_id].type;

			// check for skip types
			if ($.inArray(type, skip_types) !== -1) return;
			// check for include types
			if (include_types.length > 0 && $.inArray(type, include_types) === -1) return;

			names.push({
				id:   element.id,
				name: element.data_json.name,
				type: type
			});
		});

		// override name if it was edited
		$.each(names, function(i, obj) {
			var $el = $("#elements-name-" + obj.id);
			if ($el.length) {
				obj.name = $el.val();
			}
		});

		return names;
	};

	this.get_element_names_output = function(element_list, add_text) {
		// get dropdown list output for calculation elements
		var output = "<option value='0'> </option>";

		// create calculation elements list
		$.each(element_list, function(i, el) {
			output += "<option value='" + el.id + "'>" + el.name + " (" + el.type + ")</option>";
		});

		output += add_text;

		return output;
	};

	this.get_element_option_output = function(data_el, name, value, element, input_id, input_raw, input_name, element_id) {
		// default element name shown in form elements list
		var columns              = null;
		_this.vars.current_element_data = _this.builder_functions.element_get_data(element_id);
		var data_class           = "";
		var element_name_header  = name;
		var input                = "";
		var skip_early           = false;
		var skip_early_exclude   = false;

		// check for skip early exclude options
		if ($.inArray(name, _this.vars.skip_early_options_exclude) !== -1) skip_early_exclude = true;

		// check for skip early options
		if ($.inArray(name, _this.vars.skip_early_options) !== -1) skip_early = true;

		switch (name) {
			case "columns":
				columns = value;
				input = "<input name='" + input_name + "' id='" + input_id + "' data-element-name='" + name + "' type='hidden' value='" + value + "' />";
			break;

			case "group_id":
				input = "<input class='ezfc-form-group-id' name='" + input_name + "' id='" + input_id + "' data-element-name='" + name + "' type='hidden' value='" + value + "' />";
			break;

			case "name":
				data_class = (data_element_wrapper.type=="group" || data_element_wrapper.type=="html" || data_element_wrapper.type=="placeholder" || data_element_wrapper.type=="spacer" || data_element_wrapper.type=="stepstart" || data_element_wrapper.type=="stepend") ? "element-label-listener" : "";

				input = "<input class='ezfc-form-element-data-input-has-action " + data_class + "' type='text' value='" + value + "' name='" + input_name + "' data-element-name='" + name + "' id='" + input_id + "' />";
				input += "<button class='button ezfc-form-element-data-input-action' data-func='name_to_label'>" + _this.get_tip("Copy to label", "fa-level-down") + "</button>";

				element_name_header = value;
			break;

			case "label":
				data_class = (data_element_wrapper.type != "group" && data_element_wrapper.type != "html" && data_element_wrapper.type != "heading") ? "element-label-listener" : "";

				input = "<input class='" + data_class + "' type='text' value='" + value + "' name='" + input_name + "' data-element-name='" + name + "' id='" + input_id + "' />";

				element_name_header = value;
			break;

			case "html":
				var html_class = "";

				if (ezfc_vars.editor.use_tinymce == 1) {
					html_class = "ezfc-html";

					// wp tinymce hack (next version)
					input = '<div class="wp-editor-tools hide-if-no-js"><div class="wp-media-buttons">';
					input += '<a href="#" id="insert-media-button" class="button insert-media add_media" data-editor="' + input_id + '" title="Add Media"><span class="wp-media-buttons-icon"></span> Add Media</a>';
					input += "<button class='button ezfc-html-tinymce-toggle' data-target='" + input_id + "'>Toggle view</button>";
					input += '</div></div>';

					input += "<textarea class='" + html_class + "' name='" + input_name + "' id='" + input_id + "'>" + _this.stripslashes(value) + "</textarea>";
				}
				else {
					input = "<textarea class='" + html_class + "' name='" + input_name + "' id='" + input_id + "'>" + _this.stripslashes(value) + "</textarea>";
				}
			break;

			case "required":
				req_char = value==1 ? "*" : "";

				input = "<select class='ezfc-form-element-required-toggle' name='" + input_name + "' id='" + input_id + "' data-element-name='" + name + "'>";
				input += "	<option value='0'>" + ezfc_vars.yes_no.no + "</option>";
				input += "	<option value='1'" + (value==1 ? "selected" : "") + ">" + ezfc_vars.yes_no.yes + "</option>";
				input += "</select>";
			break;

			case "add_line":
			case "add_linebreaks":
			case "allow_multiple":
			case "autocomplete":
			case "collapsible":
			case "custom_regex_check_first":
			case "do_shortcode":
			case "calculate_enabled":
			case "calculate_before":
			case "calculate_when_hidden":
			case "datepicker_change_month":
			case "datepicker_change_year":
			case "double_check":
			case "expanded":
			case "featured_image":
			case "inline":
			case "is_currency":
			case "is_number":
			case "is_telephone_nr":
			case "overwrite_price":
			case "pips":
			case "pips_float":
			case "read_only":
			case "replace_placeholders":
			case "set_allow_zero":
			case "set_use_factor":
			case "show_empty_values_in_email":
			case "show_in_live_summary":
			case "show_item_price":
			case "show_subtotal_column":
			case "showWeek":
			case "spinner":
			case "use_address":
			case "text_only":
			case "use_woocommerce_price":
			case "value_external_listen":
			case "workdays_only":
				input = _this.get_html_input("yesno", input_name, {
					selected: value
				});
			break;

			case "add_to_price":
				input = "<select class='ezfc-form-element-" + name + "' name='" + input_name + "' id='" + input_id + "' data-element-name='" + name + "'>";
				input += "	<option value='0'>" + ezfc_vars.yes_no.no + "</option>";
				input += "	<option value='1'" + (value==1 ? "selected" : "") + ">" + ezfc_vars.yes_no.yes + "</option>";
				input += "	<option value='2'" + (value==2 ? "selected" : "") + ">" + "Partially" + "</option>";
				input += "</select>";
			break;

			case "daterange_single":
				input = _this.get_html_input("select", input_name, {
					options: [
						{ value: 0, text: ezfc_vars.yes_no.no },
						{ value: 1, text: ezfc_vars.texts.hide_from },
						{ value: 2, text: ezfc_vars.texts.hide_to }
					],
					selected: value
				});
			break;

			case "hidden":
				input = "<select class='ezfc-form-element-" + name + "' name='" + input_name + "' id='" + input_id + "' data-element-name='" + name + "'>";
				input += "	<option value='0'>" + ezfc_vars.yes_no.no + "</option>";
				input += "	<option value='1'" + (value==1 ? "selected" : "") + ">" + ezfc_vars.yes_no.yes + "</option>";
				input += "	<option value='2'" + (value==2 ? "selected" : "") + ">" + ezfc_vars.texts.conditional_hidden + "</option>";
				input += "</select>";
			break;

			case "steps_slider":
			case "steps_spinner":
			case "steps_pips":
				input = "<input class='ezfc-spinner' type='text' value='" + value + "' name='" + input_name + "' data-element-name='" + name + "' id='" + input_id + "' />";
			break;

			case "multiple":
				input = "<select class='ezfc-form-element-multiple' name='" + input_name + "' id='" + input_id + "' data-element-name='" + name + "'>";
				input += "	<option value='0'>" + ezfc_vars.yes_no.no + "</option>";
				input += "	<option value='1'" + (value==1 ? "selected" : "") + ">" + ezfc_vars.yes_no.yes + "</option>";
				input += "</select>";
			break;

			// used for radio-buttons, checkboxes
			case "options":
				var n              = 0,
					preselect      = (data_el.preselect || data_el.preselect == 0) ? data_el.preselect : "",
					preselect_html = "",
					preselect_name = "",
					preselect_type = "",
					preselect_val  = "",
					variable_column_text = ezfc_vars.texts.image;

				switch (data_element_wrapper.type) {
					case "checkbox":
					case "table_order":
						preselect_name = input_raw + "[preselect_container]";
						preselect_type = "checkbox";
						preselect_val  = [];

						if (preselect.length > 0) {
							preselect_val = $.map(preselect.split(","), function(v) {
								return parseInt(v);
							});
						}

						if (data_element_wrapper.type == "table_order") {
							variable_column_text = "min/max";
						}
					break;

					default:
						preselect_name = input_raw + "[preselect]";
						preselect_type = "radio";
						preselect_val  = parseInt(preselect);
					break;
				}

				// add option
				input = "<button class='button ezfc-form-element-option-add' data-element_id='" + element.id + "'>" + _this.vars.icons.add + ezfc_vars.texts.add_option + "</button>";
				// batch
				input += "&nbsp;<button class='button ezfc-form-element-option-batch-edit' data-element_id='" + element.id + "' data-func='dialog_open' data-args='#ezfc-dialog-batch-edit,options'>" + _this.vars.icons.batch_edit + ezfc_vars.texts.batch_edit + "</button>";
				// create condition for all options
				input += "&nbsp;<button class='button ezfc-form-element-option-batch-edit' data-element_id='" + element.id + "' data-func='option_create_all_conditions'>" + _this.vars.icons.option_create_condition + ezfc_vars.texts.option_create_all_conditions + "</button>";
				// generate option IDs
				input += "&nbsp;<button class='button' data-element_id='" + element.id + "' data-func='option_create_ids'>" + _this.vars.icons.option_create_ids + ezfc_vars.texts.option_create_ids + "</button>";
				input += "</div>";

				// spacer
				input += "<div class='ezfc-clear ezfc-spacer'></div>";
				
				input += "<div class='col-xs-3'>ID | " + ezfc_vars.texts.value + "</div>";
				input += "<div class='col-xs-3'>" + ezfc_vars.texts.text + "</div>";
				input += "<div class='col-xs-3'>" + variable_column_text + "</div>";
				input += "<div class='col-xs-3'>";
				if (data_element_wrapper.type != "table_order") {
					input += "	<abbr title='" + ezfc_vars.texts.preselect_values + "'>Sel</abbr>&nbsp;";
				}
				input += "	<abbr title='" + ezfc_vars.texts.disabled + "'>Dis</abbr>&nbsp;";
				input += "</div>";

				input += "<div class='ezfc-form-element-option-container ezfc-option-container'>";
				input += "<ul class='ezfc-form-element-option-container-list' data-indexnum='2'>";

				$.each(value, function(opt_val, opt_text) {
					input += "<li class='ezfc-form-element-option-list-item'>";
					input += "<div class='ezfc-form-element-option' data-element_id='" + element.id + "'>";

					input += "	<div class='col-xs-3'>";
					// ID
					input += "		<input class='ezfc-form-element-option-id ezfc-width-30' name='" + input_name + "[" + n + "][id]' value='" + (opt_text.id === undefined ? "" : opt_text.id) + "' type='text' />";
					// value
					input += "		<input class='ezfc-form-element-option-value ezfc-width-60' name='" + input_name + "[" + n + "][value]' value='" + opt_text.value + "' type='text' />";
					input += "	</div>";

					// text
					input += "	<div class='col-xs-3'><input class='ezfc-form-element-option-text' name='" + input_name + "[" + n + "][text]' type='text' value='" + opt_text.text + "' /></div>";

					// image wrapper
					input += "	<div class='col-xs-3'>";
					// image
					if (data_element_wrapper.type == "radio" || data_element_wrapper.type == "checkbox") {
						var tmp_img_src = "";
						if (opt_text.image) tmp_img_src = opt_text.image;

						// icon
						var tmp_icon_src     = _this.check_undefined_return_value(opt_text, "icon", "");
						var tmp_icon_input_id = "elements-option-icon-" + element.id + "-" + n;
						var tmp_icon_placeholder = tmp_icon_input_id + "-icon";

						// option image input
						input += "	<input class='ezfc-form-element-option-image' name='" + input_name + "[" + n + "][image]' type='hidden' value='" + tmp_img_src + "' />";
						// option icon input
						input += "	<input class='ezfc-form-element-option-icon' name='" + input_name + "[" + n + "][icon]' id='" + tmp_icon_input_id + "' type='hidden' value='" + tmp_icon_src + "' />";

						// image placeholder
						input += "	<img class='ezfc-option-image-placeholder' src='" + tmp_img_src + "' />";
						// icon placeholder
						input += "	<i class='ezfc-option-icon-placeholder fa " + tmp_icon_src + "' id='" + tmp_icon_placeholder + "' data-previewicon></i>";

						// choose image
						input += "	<button class='button ezfc-option-image-button' data-ot='" + ezfc_vars.texts.choose_image + "'><i class='fa fa-image'></i></button>";
						// choose icon
						input += "	<button class='button ezfc-icon-button' data-ot='" + ezfc_vars.texts.choose_image + "'><i class='fa fa-font-awesome'></i></button>";

						// remove image/icon
						input += "	<button class='button ezfc-option-image-remove' data-ot='" + ezfc_vars.texts.remove_image + "'><i class='fa fa-times'></i></button>";
					}
					// unavailable image
					else if (data_element_wrapper.type == "dropdown") {
						input += ezfc_vars.unavailable_element;
					}
					// table order --> min/max
					else if (data_element_wrapper.type == "table_order") {
						var item_min = _this.check_undefined_return_value(opt_text, "min", 0);
						var item_max = _this.check_undefined_return_value(opt_text, "max", 0);

						input += "<input class='input-small' name='" + input_name + "[" + n + "][min]' type='text' value='" + item_min + "' />";
						input += "<input class='input-small' name='" + input_name + "[" + n + "][max]' type='text' value='" + item_max + "' />";
					}

					input += "	</div>";

					// preselect
					preselect_html = "";

					// checkbox element can have multiple preselect values
					if (data_element_wrapper.type == "checkbox" || data_element_wrapper.type == "table_order") {
						preselect_html = $.inArray(n, preselect_val)!=-1 ? "checked='checked'" : "";
					}
					else {
						preselect_html = preselect_val == n ? "checked='checked'" : "";
					}

					// disabled
					disabled_html = opt_text.disabled == 1 ? "checked='checked'" : "";

					input += "	<div class='col-xs-3'>";
					// preselect
					if (data_element_wrapper.type != "table_order") {
						input += "		<input class='ezfc-form-element-option-" + preselect_type + " ezfc-fill-index-value' name='" + preselect_name + "' type='" + preselect_type + "' data-element_id='" + element.id + "' value='" + n + "' " + preselect_html + " />&nbsp;";
					}

					// disabled
					input += "		<input class='ezfc-form-element-option-disabled ezfc-fill-index-value' name='" + input_name + "[" + n + "][disabled]' type='checkbox' data-element_id='" + element.id + "' value='1' " + disabled_html + " />&nbsp;";
					// create selected condition
					input += "		<button class='button ezfc-form-option-create-condition' data-func='option_create_condition' data-args='" + n + "' data-ot='" + ezfc_vars.element_tip_description.option_create_condition + "'>" + _this.vars.icons.option_create_condition + "</button>";
					
					// remove
					input += "		<button class='button button-delete ezfc-form-element-option-delete' data-target='.ezfc-form-element-option' data-element_id='" + element.id + "' data-target_row='" + n + "'><i class='fa fa-times'></i></button>";

					// index number
					input += "		&nbsp;<abbr title='" + ezfc_vars.texts.index + "'>#<span class='ezfc-form-element-option-index-number'>" + n + "</span></abbr>";

					input += "		</div>";

					input += "	<div class='ezfc-clear'></div>";
					input += "</div>";
					input += "</li>";
					
					n++;
				});

				input += "</ul>";
				input += "</div>"; // move container

				if (preselect_type == "checkbox") {
					input += "<input class='ezfc-form-option-preselect' type='hidden' name='" + input_raw + "[preselect]' data-element_id='" + element.id + "' value='" + preselect + "' />";
				}
				else if (preselect_type == "radio") {
					preselect_html = preselect==-1 ? "checked='checked'" : "";

					input += "<div class='col-xs-9 text-right'>" + ezfc_vars.texts.clear_preselected_value + "</div>";
					input += "<div class='col-xs-3'><input class='ezfc-form-element-option-radio' name='" + input_raw + "[preselect]' type='radio' data-element_id='" + element.id + "' value='-1' " + preselect_html + " /></div>";
				}

				input += "<div>";
			break;

			case "options_source":
				input = _this.get_html_input("select", input_name + "[source]", {
					options: [
						{ value: "options", text: ezfc_vars.texts.options_source_options },
						{ value: "php", text: ezfc_vars.texts.options_source_php },
						{ value: "php_merge", text: ezfc_vars.texts.options_source_options + " + " + ezfc_vars.texts.options_source_php },
						{ value: "json", text: "JSON URL" },
						{ value: "json_merge", text: ezfc_vars.texts.options_source_options + " + JSON URL" },
					],
					selected: value.source
				});

				input += _this.get_html_input("input", input_name + "[value]", {
					value: value.value
				});
			break;

			// calculate
			case "calculate":
				// add calculation row
				input = "<button class='button ezfc-form-element-option-add' data-element_id='" + element.id + "'>" + _this.vars.icons.add + ezfc_vars.texts.add_calculation_field + "</button>&nbsp;";
				// batch
				input += "<button class='button ezfc-form-element-option-batch-edit' data-element_id='" + element.id + "' data-func='dialog_open' data-args='#ezfc-dialog-batch-edit,calculate'>" + _this.vars.icons.batch_edit + ezfc_vars.texts.batch_edit + "</button>&nbsp;";
				// doc
				input += "<a class='pull-right' href='https://ez-form-calculator.ezplugins.de/documentation/calculation/' target='_blank'>" + ezfc_vars.texts.documentation + "</a>";
				input += "</div>";

				// spacer
				input += "<div class='ezfc-clear ezfc-spacer'></div>";

				// calculation text
				input += "<div class='col-xs-11 ezfc-calculation-text'></div>";
				// calculation icon
				input += "<div class='col-xs-1 ezfc-calculation-text-icon'></div>";

				input += "<div class='ezfc-clear ezfc-spacer'></div>";

				input += "<div class='col-xs-1'>" + ezfc_vars.texts.operator + "</div>";
				input += "<div class='col-xs-3'>" + _this.get_tip(ezfc_vars.element_tip_description.calc_target_element) + " " + ezfc_vars.texts.target_element + "</div>";
				input += "<div class='col-xs-3'>" + _this.get_tip(ezfc_vars.element_tip_description.use_calculated_target_value) + " " + ezfc_vars.texts.use_calculated_target_value + "</div>";
				input += "<div class='col-xs-3'>" + _this.get_tip(ezfc_vars.element_tip_description.calc_target_value) + " " + ezfc_vars.texts.value + "</div>";
				input += "<div class='col-xs-2'></div>";
				input += "<div class='ezfc-clear'></div>";

				input += "<div class='ezfc-form-element-option-container ezfc-option-container'>";
				input += "<ul class='ezfc-form-element-option-container-list' data-indexnum='2'>";

				// calculation fields
				var n = 0;
				$.each(value, function(calc_key, calc_values) {
					var select_target_id = input_id + "-calculate-target-" + calc_key;
					var calc_prio = 0;
					if (typeof calc_values.prio !== "undefined" && !isNaN(calc_values.prio)) calc_prio = calc_values.prio;

					input += "<li class='ezfc-form-element-option-list-item'>";
					input += "<div class='ezfc-form-element-option ezfc-form-element-calculate-wrapper ezfc-calculate-prio-" + calc_prio + "' data-element_id='" + element.id + "' data-row='" + n + "'>";
					// prio
					input += _this.get_html_input("hidden", input_name + "[" + n + "][prio]", {
						class: "ezfc-form-element-calculate-prio",
						value: calc_prio
					});

					// operator
					input += "	<div class='col-xs-1'>";
					input += "		<select class='ezfc-form-element-calculate-operator ezfc-badge-listener' name='" + input_name + "[" + n + "][operator]' data-element-name='" + name + "'>";

					// iterate through operators
					$.each(_this.vars.operators, function(nn, operator) {
						var selected = "";
						if (calc_values.operator == operator.value) selected = "selected='selected'";

						input += "<option value='" + operator.value + "' " + selected + ">" + operator.text + "</option>";
					});

					input += "		</select>";
					input += "	</div>";

					// other elements (will be filled in from function _this.fill_calculate_fields())
					input += "	<div class='col-xs-3'>";
					input += "		<select class='ezfc-form-element-calculate-target fill-elements fill-elements-calculate ezfc-form-element-data-input-has-action' name='" + input_name + "[" + n + "][target]' data-element-name='" + name + "' data-selected='" + calc_values.target + "'>";
					// dummy target so value is saved for previews / saves
					input += "			<option value='" + calc_values.target + "' selected='selected'></option>";
					input += "		</select>";
					// select target
					input += "		<button class='button ezfc-form-element-calculate-target-select' data-func='select_target_activate_button'>" + _this.vars.icons.select_target + "</button>";
					input += "	</div>";

					// use calculated target value
					input += "	<div class='col-xs-3'>";
					input += _this.get_html_input("select", input_name + "[" + n + "][use_calculated_target_value]", {
						class: "ezfc-form-element-calculate-ctv",
						options: [
							{ value: 0, text: ezfc_vars.element_tip_description.calc_ctv_raw },
							{ value: 3, text: ezfc_vars.element_tip_description.calc_ctv_raw_without_factor },
							{ value: 1, text: ezfc_vars.element_tip_description.calc_ctv_with_subtotal },
							{ value: 2, text: ezfc_vars.element_tip_description.calc_ctv_without_subtotal },
							{ value: 4, text: "5) Selected count" }
						],
						selected: calc_values.use_calculated_target_value
					});
					input += "	</div>";

					// value when no element was selected
					if (!calc_values.value) calc_values.value = "";
					input += "	<div class='col-xs-3'>";
					input += "		<input class='ezfc-form-element-calculate-value' name='" + input_name + "[" + n + "][value]' id='" + input_id + "-value' data-element-name='" + name + "' value='" + calc_values.value + "' type='text' />";
					input += "	</div>";

					// actions
					input += "	<div class='col-xs-2 text-right'>";

					// prio
					input += "		<button class='button ezfc-calculate-prio-dec' data-func='prio_dec' data-ot='" + ezfc_vars.element_tip_description.prio_dec + "'>" + _this.vars.icons.prio_dec + "</button>";
					input += "		<button class='button ezfc-calculate-prio-inc' data-func='prio_inc' data-ot='" + ezfc_vars.element_tip_description.prio_inc + "'>" + _this.vars.icons.prio_inc + "</button>";

					// remove
					input += "		<button class='button button-delete ezfc-form-element-option-delete' data-target='.ezfc-form-element-calculate-wrapper' data-element_id='" + element.id + "'><i class='fa fa-times'></i></button>";
					input += "	</div>";

					input += "	<div class='ezfc-clear'></div>";

					input += "</div>";
					input += "</li>";

					n++;
				});

				input += "</ul>";

				input += "</div>"; // move container
				input += "<div>";
			break;

			// conditional fields
			case "conditional":
				// add conditional field
				input = "<button class='button ezfc-form-element-option-add' data-element_id='" + element.id + "'>" + _this.vars.icons.add + ezfc_vars.texts.add_conditional_field + "</button>&nbsp;";
				// batch
				input += "<button class='button ezfc-form-element-option-batch-edit' data-element_id='" + element.id + "' data-func='dialog_open' data-args='#ezfc-dialog-batch-edit,conditional'>" + _this.vars.icons.batch_edit + ezfc_vars.texts.batch_edit + "</button>&nbsp;";

				input += "<a class='pull-right' href='https://ez-form-calculator.ezplugins.de/documentation/conditional-fields/' target='_blank'>" + ezfc_vars.texts.documentation + "</a>";
				input += "</div>";

				// spacer
				input += "<div class='ezfc-clear ezfc-spacer'></div>";

				input += "<div class='col-xs-2'>" + _this.get_tip(ezfc_vars.element_tip_description.action_perform) + " " + ezfc_vars.texts.action + "</div>";
				input += "<div class='col-xs-3'>" + _this.get_tip(ezfc_vars.element_tip_description.target_element) + " " + ezfc_vars.texts.target_element + "</div>";
				input += "<div class='col-xs-1'>" + _this.get_tip(ezfc_vars.element_tip_description.target_value) + " " + ezfc_vars.texts.target_value_short + "</div>";
				input += "<div class='col-xs-2'>" + _this.get_tip(ezfc_vars.element_tip_description.conditional_operator) + " " + ezfc_vars.texts.conditional_operator_short + "</div>";
				input += "<div class='col-xs-2'>" + _this.get_tip(ezfc_vars.element_tip_description.conditional_value) + " " + ezfc_vars.texts.compare_value + "</div>";
				input += "<div class='col-xs-2'>" + _this.get_tip(ezfc_vars.element_tip_description.conditional_chain, "fa-chain") + " &nbsp; &nbsp;" + _this.get_tip(ezfc_vars.element_tip_description.conditional_row_operator) + " &nbsp; " + _this.get_tip(ezfc_vars.element_tip_description.conditional_toggle) + " &nbsp; " + _this.get_tip(ezfc_vars.element_tip_description.conditional_factor) + "</div>";
				input += "<div class='ezfc-clear'></div>";

				input += "<div class='ezfc-form-element-option-container ezfc-option-container'>";
				input += "<ul class='ezfc-form-element-option-container-list' data-indexnum='2'>";

				var n = 0;
				$.each(value, function(calc_key, calc_text) {
					input += "<li class='ezfc-form-element-option-list-item'>";
					input += "<div class='ezfc-form-element-option ezfc-form-element-conditional-wrapper' data-element_id='" + element.id + "' data-row='" + n + "'>";

					// show or hide
					input += "	<div class='col-xs-2'>";
					input += "		<select class='ezfc-form-element-conditional-action ezfc-badge-listener' name='" + input_name + "[" + n + "][action]' id='" + input_id + "-action' data-element-name='" + name + "'>";

					// iterate through conditional operators
					$.each(_this.vars.cond_actions, function(nn, operator) {
						var selected = "";
						if (calc_text.action == operator.value) selected = "selected='selected'";

						input += "<option value='" + operator.value + "' " + selected + ">" + operator.text + "</option>";
					});

					input += "		</select>";
					input += "	</div>";

					// target element
					input += "	<div class='col-xs-3'>";
					input += "		<select class='ezfc-form-element-conditional-target ezfc-conditional-target ezfc-form-element-data-input-has-action fill-elements' name='" + input_name + "[" + n + "][target]' data-element-name='" + name + "' data-selected='" + calc_text.target + "' data-show_all='true'>";
					// dummy target so value is saved for previews / saves
					input += "			<option value='" + calc_text.target + "' selected='selected'></option>";
					input += "		</select>";

					// select target
					input += "		<button class='button ezfc-form-element-conditonal-target-select' data-func='select_target_activate_button'>" + _this.vars.icons.select_target + "</button>";
					input += "	</div>";

					// target value
					if (!calc_text.target_value) calc_text.target_value = "";
					input += "	<div class='col-xs-1'>";
					input += "		<input class='ezfc-form-element-conditional-target-value' name='" + input_name + "[" + n + "][target_value]' id='" + input_id + "-target-value' data-element-name='" + name + "' value='" + calc_text.target_value + "' type='text' />";
					input += "	</div>";

					// conditional operator
					input += "	<div class='col-xs-2'>";
					input += "		<select class='ezfc-form-element-conditional-operator' name='" + input_name + "[" + n + "][operator]' id='" + input_id + "-target' data-element-name='" + name + "'>";

					// iterate through conditional operators
					$.each(_this.vars.cond_operators, function(nn, operator) {
						var selected = "";
						if (calc_text.operator == operator.value) selected = "selected='selected'";

						input += "<option value='" + operator.value + "' " + selected + ">" + operator.text + "</option>";
					});

					input += "		</select>";
					input += "	</div>";

					// conditional value
					if (!calc_text.value) calc_text.value = "";
					input += "	<div class='col-xs-2'>";
					input += "		<input class='ezfc-form-element-conditional-value' name='" + input_name + "[" + n + "][value]' id='" + input_id + "-value' data-element-name='" + name + "' value='" + calc_text.value + "' type='text' />";
					input += "	</div>";

					// conditional toggle
					var cond_row_operator = (calc_text.row_operator && calc_text.row_operator == 1) ? "checked='checked'" : "";
					var cond_toggle       = (calc_text.notoggle && calc_text.notoggle == 1) ? "checked='checked'" : "";
					var cond_use_factor   = (calc_text.use_factor && calc_text.use_factor == 1) ? "checked='checked'" : "";
					var cond_chain_args   = [input_name, n].join(",");

					input += "	<div class='col-xs-2'>";
					// add conditional operator
					input += "		<button class='button button-primary ezfc-form-element-conditional-chain-add' data-func='conditional_chain_add' data-args='" + cond_chain_args + "' id='" + input_id + "-chain' data-element-name='" + name + "'>" + _this.get_tip(ezfc_vars.texts.add_another_condition, "fa-plus-square-o") + "</button>";
					// conditional row operator (or / and)
					input += "		<input class='ezfc-form-element-conditional-row-operator' name='" + input_name + "[" + n + "][row_operator]' id='" + input_id + "-row-operator' data-element-name='" + name + "' value='1' type='checkbox' " + cond_row_operator + " />";
					// notoggle
					input += "		<input class='ezfc-form-element-conditional-notoggle' name='" + input_name + "[" + n + "][notoggle]' id='" + input_id + "-notoggle' data-element-name='" + name + "' value='1' type='checkbox' " + cond_toggle + " />";
					// use factor
					input += "		<input class='ezfc-form-element-conditional-use_factor' name='" + input_name + "[" + n + "][use_factor]' id='" + input_id + "-use_factor' data-element-name='" + name + "' value='1' type='checkbox' " + cond_use_factor + " />";

					// remove
					input += "		<button class='button button-delete ezfc-form-element-option-delete' data-target='.ezfc-form-element-conditional-wrapper' data-element_id='" + element.id + "'><i class='fa fa-times'></i></button>";
					input += "  </div>";

					input += "	<div class='ezfc-clear'></div>";

					// redirect
					input += "	<div class='col-xs-4 ezfc-hidden ezfc-conditional-redirect-wrapper'>URL: ";
					input += "		<input class='ezfc-form-element-conditional-redirect' name='" + input_name + "[" + n + "][redirect]' id='" + input_id + "-redirect' data-element-name='" + name + "' value='" + (calc_text.redirect ? calc_text.redirect : "") + "' type='text' />";
					input += "  </div>";

					// option
					input += "	<div class='col-xs-4 ezfc-hidden ezfc-conditional-option-value-wrapper'>" + ezfc_vars.texts.option_id + ": ";
					input += "		<input class='ezfc-form-element-conditional-option-value' name='" + input_name + "[" + n + "][option_index_value]' id='" + input_id + "-option-value' data-element-name='" + name + "' value='" + (calc_text.option_index_value ? calc_text.option_index_value : "") + "' type='text' />";
					input += "  </div>";


					input += "	<div class='ezfc-clear'></div>";

					if (calc_text.operator_chain && Object.keys(calc_text.operator_chain).length > 0) {
						$.each(calc_text.operator_chain, function(i, chain_operator_value) {
							var chain_value          = typeof calc_text.value_chain !== "undefined" ? calc_text.value_chain[i] : "";
							var chain_compare_target = typeof calc_text.compare_value !== "undefined" ? calc_text.compare_value[i] : "";

							var input_name_operator       = input_name + "[" + n + "][operator_chain][" + i + "]";
							var input_name_value          = input_name + "[" + n + "][value_chain][" + i + "]";
							var input_name_compare_target = input_name + "[" + n + "][compare_value][" + i + "]";

							input += _this.builder_functions.conditional_chain_get_html(input_name_operator, input_name_value, chain_operator_value, chain_value, input_name_compare_target, chain_compare_target);
						});
					}

					input += "</div>";
					input += "</li>";

					n++;
				});

				input += "</ul>";
				// option wrapper
				input += "</div>";

				input += "<div>";
			break;

			// discount
			case "discount":
				input = "<button class='button ezfc-form-element-option-add' data-element_id='" + element.id + "'>" + _this.vars.icons.add + ezfc_vars.texts.add_discount_field + "</button>&nbsp;";
				// batch
				input += "<button class='button ezfc-form-element-option-batch-edit' data-element_id='" + element.id + "' data-func='dialog_open' data-args='#ezfc-dialog-batch-edit,discount'>" + _this.vars.icons.batch_edit + ezfc_vars.texts.batch_edit + "</button>&nbsp;";
				input += "</div>";

				// spacer
				input += "<div class='ezfc-clear ezfc-spacer'></div>";

				input += "<div class='col-xs-3'>" + _this.get_tip(ezfc_vars.element_tip_description.discount_value_min) + " " + ezfc_vars.texts.value_min + "</div>";
				input += "<div class='col-xs-3'>" + _this.get_tip(ezfc_vars.element_tip_description.discount_value_max) + " " + ezfc_vars.texts.value_max + "</div>";
				input += "<div class='col-xs-2'>" + _this.get_tip(ezfc_vars.element_tip_description.discount_operator) + " Op</div>";
				input += "<div class='col-xs-3'>" + _this.get_tip(ezfc_vars.texts.discount_value) + " " + ezfc_vars.texts.discount_value + "</div>";
				input += "<div class='col-xs-1'>" + ezfc_vars.texts.remove + "</div>";
				input += "<div class='ezfc-clear'></div>";

				input += "<div class='ezfc-form-element-option-container ezfc-option-container'>";
				input += "<ul class='ezfc-form-element-option-container-list' data-indexnum='2'>";

				// discount fields
				var n = 0;
				$.each(value, function(discount_key, discount_text) {
					input += "<li class='ezfc-form-element-option-list-item'>";
					input += "<div class='ezfc-form-element-option ezfc-form-element-discount-wrapper' data-element_id='" + element.id + "' data-row='" + n + "'>";

					// range_min
					input += "	<div class='col-xs-3'>";
					input += "		<input class='ezfc-form-element-discount-range_min' name='" + input_name + "[" + n + "][range_min]' id='" + input_id + "-value' data-element-name='" + name + "' value='" + discount_text.range_min + "' type='text' />";
					input += "	</div>";

					// range_max
					input += "	<div class='col-xs-3'>";
					input += "		<input class='ezfc-form-element-discount-range_max' name='" + input_name + "[" + n + "][range_max]' id='" + input_id + "-value' data-element-name='" + name + "' value='" + discount_text.range_max + "' type='text' />";
					input += "	</div>";

					// operator
					input += "	<div class='col-xs-2'>";
					input += "		<select class='ezfc-form-element-discount-operator ezfc-badge-listener' name='" + input_name + "[" + n + "][operator]' data-element-name='" + name + "'>";

					// iterate through operators
					$.each(_this.vars.operators_discount, function(nn, operator) {
						var selected = "";
						if (discount_text.operator == operator.value) selected = "selected='selected'";

						input += "<option value='" + operator.value + "' " + selected + ">" + operator.text + "</option>";
					});

					input += "		</select>";
					input += "	</div>";

					// other elements (will be filled in from function _this.fill_calculate_fields())
					input += "	<div class='col-xs-3'>";
					input += "		<input class='ezfc-form-element-discount-discount_value' name='" + input_name + "[" + n + "][discount_value]' id='" + input_id + "-value' data-element-name='" + name + "' value='" + discount_text.discount_value + "' type='text' />";
					input += "	</div>";

					// remove
					input += "	<div class='col-xs-1'>";
					input += "		<button class='button button-delete ezfc-form-element-option-delete' data-target='.ezfc-form-element-discount-wrapper' data-element_id='" + element.id + "'><i class='fa fa-times'></i></button>";
					input += "	</div>";

					input += "	<div class='ezfc-clear'></div>";
					input += "</div>";
					input += "</li>";
					n++;
				});

				input += "</ul>";
				// option wrapper
				input += "</div>";

				input += "<div>";
			break;

			case "discount_value_type":
				input = _this.get_html_input("select", input_name, {
					options: [
						{ value: "calculated", text: "Calculated value" },
						{ value: "raw", text: "Raw value" }
					],
					selected: value
				});
			break;

			case "set":
				input = "<button class='button ezfc-form-element-option-add' data-element_id='" + element.id + "'>Add element to set</button>&nbsp;";
				input += "</div>";

				input += "<div class='col-xs-12'>Element / Remove</div>";
				input += "<div class='ezfc-clear'></div>";

				input += "<div class='ezfc-form-element-option-container ezfc-option-container'>";
				input += "<ul class='ezfc-form-element-option-container-list' data-indexnum='2'>";

				// set fields
				var n = 0;
				$.each(value, function(set_key, set_text) {
					input += "<li class='ezfc-form-element-option-list-item'>";
					input += "<div class='ezfc-form-element-option ezfc-form-element-set-wrapper' data-element_id='" + element.id + "' data-row='" + n + "'>";

					// field to show
					input += "	<div class='ezfc-form-element-set-element'>";
					input += "		<select class='ezfc-form-element-set-target fill-elements' name='" + input_name + "[" + n + "][target]' data-element-name='" + name + "' data-selected='" + set_text.target + "'>";
					input += "			<option value='" + set_text.target + "' selected='selected'></option>";
					input += "		</select>";

					// remove
					input += "		<button class='button button-delete ezfc-form-element-option-delete' data-target='.ezfc-form-element-set-wrapper' data-element_id='" + element.id + "'><i class='fa fa-times'></i></button>";
					input += "	</div>";

					input += "	<div class='ezfc-clear'></div>";
					input += "</div>";
					input += "</li>";
					n++;
				});

				input += "</ul>";
				// option wrapper
				input += "</div>";

				input += "<div>";
			break;
			case "set_operator":
				input = "<select class='ezfc-form-element-" + name + "' name='" + input_name + "' id='" + input_id + "' data-element-name='" + name + "'>";

				// iterate through operators
				$.each(_this.vars.set_operators, function(nn, operator) {
					var selected = "";
					if (value == operator.value) selected = "selected='selected'";

					input += "<option value='" + operator.value + "' " + selected + ">" + operator.text + "</option>";
				});

				input += "</select>";
			break;

			// matrix
			case "matrix":
				input = "</div><div class='col-xs-12'>";

				// action row
				input += "<div class='ezfc-form-element-action-row'>";

				var create_from_matrix_select_id = input_id + "-matrix-create-from-element";
				input += "<button class='button' data-func='matrix_create_from_element' data-element='#" + create_from_matrix_select_id + "'>" + ezfc_vars.texts.matrix_create_from_element + "</button>";
				input += "<select class='ezfc-matrix-create-from-element fill-elements' id='" + create_from_matrix_select_id + "'></select>";

				input += "<button class='button pull-right' data-func='matrix_clear_table_btn'>" + ezfc_vars.texts.matrix_clear_table + "</button>";

				input += "</div>"; // action row

				//		a 	b 	c
				//  x1 	1	2	3
				//  x2 	4	5	6
				input += "<table class='ezfc-form-element-matrix-table' id='" + input_id + "-matrix-wrapper' cellspacing='0' cellpadding='0'>";

				// element columns
				input += 	"<tr class='ezfc-matrix-elements'>";
				// actions
				input += 		"<td>";
				input += 			"<button class='button button-primary ezfc-matrix-action-add-row' data-func='matrix_add_row'>" + _this.vars.icons.matrix.add_row + "</button> ";
				input += 			"<button class='button button-primary ezfc-matrix-action-add-column' data-func='matrix_add_column'>" + _this.vars.icons.matrix.add_column + "</button>";
				input += 		"</td>";

				$.each(value.target_elements, function(i, target_element_id) {
					input += "<td class='ezfc-matrix-target-element' data-mcol='" + i + "'>";

					input += "<button class='button button-delete ezfc-matrix-action-remove-column' data-func='matrix_remove_column'>" + _this.vars.icons.delete + "</button>"; // remove
					input += _this.get_html_input("select", input_name + "[target_elements][" + i + "]", {
						class: "fill-elements",
						options: [],
						selected: target_element_id
					});

					input += "</td>";
				});

				input += 	"</tr>";

				$.each(value.conditions, function(i, condition) {
					// matrix condition + element values
					input += "<tr class='ezfc-matrix-row' data-mrow='" + i + "'>";
					input += "	<td class='ezfc-matrix-condition'>";

					// remove row
					input += 		"<button class='button button-delete ezfc-matrix-action-remove-row' data-func='matrix_remove_row'>" + _this.vars.icons.delete + "</button>";

					$.each(condition.elements, function(ci, condition_element_id) {
						input += "<div>"; // condition wrapper

						input += _this.get_html_input("select", input_name + "[conditions][" + i + "][elements][" + ci + "]", {
							class: "ezfc-matrix-conditional-element fill-elements",
							options: [],
							selected: condition_element_id
						});

						input += "<br>";

						input += _this.get_html_input("select", input_name + "[conditions][" + i + "][operators][" + ci + "]", {
							class: "ezfc-matrix-conditional-operator",
							options: _this.vars.cond_operators,
							selected: condition.operators[ci]
						});

						input += "<br>";

						input += _this.get_html_input("input", input_name + "[conditions][" + i + "][values][" + ci + "]", {
							class: "ezfc-matrix-conditional-value",
							value: condition.values[ci]
						});

						input += "</div>"; // condition wrapper
					});

					input += "	</td>";

					// target values
					$.each(value.target_values[i], function(ti, target_value) {
						input += "<td class='ezfc-matrix-col-value' data-mrow='" + i + "' data-mcol='" + ti + "'>";

						/*// option id (conditionally hidden)
						input += _this.get_html_input("input", input_name + "[target_ids][" + i + "][" + ti + "]", {
							class: "ezfc-matrix-target-value",
							value: typeof value["target_ids"] === "object" ? value.target_ids[i][ti] : ""
						});*/

						// target value
						input += _this.get_html_input("input", input_name + "[target_values][" + i + "][" + ti + "]", {
							class: "ezfc-matrix-target-value",
							value: target_value
						});

						input += "</td>";
					});

					input += "</tr>";
				});

				input += "</table>";

				input += "</div><div>";

				//var compiled = _.template("template test <%= ezfc_vars.texts.choose_image %> ok? func test = <% _this.get_html_input(); %>");
				//var out = compiled({ ezfc_vars: ezfc_vars, _this.builder_functions: _this.builder_functions });
				//console.log(out);
			break;
			case "matrix_action":
				input = _this.get_html_input("select", input_name, {
					options: _this.vars.cond_actions,
					selected: value
				});
			break;

			// image
			case "image":
			case "fallback_image":
				input += "<button class='button ezfc-image-upload'>" + ezfc_vars.texts.choose_image + "</button> ";
				input += "<button class='button ezfc-option-image-remove'>" + ezfc_vars.texts.remove_image + "</button>";
				input += "<br><br><img src='" + value + "' class='ezfc-image-preview' />";
				input += "<input type='text' class='ezfc-image-upload-hidden' name='" + input_name + "' id='" + input_id + "' value='" + value + "' placeholder='URL' />";
			break;

			case "icon":
				input = "<input type='hidden' value='" + value + "' name='" + input_name + "' data-element-name='" + name + "' id='" + input_id + "' />";
				input += "<button class='button ezfc-icon-button' data-target='" + input_id + "'>" + ezfc_vars.texts.choose_icon + "</button>";
				input += "<i class='fa " + value + "' id='" + input_id + "-icon' data-previewicon></i>";
			break;

			// custom calculation
			case "custom_calculation":
				input = "<textarea class='ezfc-custom-calculation' name='" + input_name + "' id='" + input_id + "'>" + _this.stripslashes(value) + "</textarea>";
				input += "<button class='ezfc-open-function-dialog button'>" + ezfc_vars.texts.functions + "</button>";
			break;

			case "show_in_email":
				input = "<select class='ezfc-form-element-" + name + "' name='" + input_name + "' id='" + input_id + "' data-element-name='" + name + "'>";

				var wrapper_id = input_id + "-show-in-email-conditional-wrapper";

				var tmp_options = [
					{ value: 0, text: ezfc_vars.yes_no.no},
					{ value: 1, text: ezfc_vars.yes_no.yes},
					{ value: 2, text: ezfc_vars.texts.show_if_not_empty},
					{ value: 3, text: ezfc_vars.texts.show_if_not_empty_0},
					{ value: 4, text: ezfc_vars.texts.show_if, toggle: "#" + wrapper_id }
				];

				var show_in_email_values_tmp = _this.check_undefined_return_value(_this.vars.current_element_data, "show_in_email_cond", null);
				var show_in_email_values = [];

				// string to array
				if (typeof show_in_email_values_tmp === "string") {
					show_in_email_values = [{
						cond: _this.vars.current_element_data.show_in_email_cond,
						operator: _this.vars.current_element_data.show_in_email_operator,
						value: _this.vars.current_element_data.show_in_email_value
					}];
				}
				else {
					if (!show_in_email_values_tmp || show_in_email_values_tmp.length == 0) {
						show_in_email_values = [{
							cond: 0,
							operator: 0,
							value: ""
						}];
					}
					else {
						for (var i in _this.vars.current_element_data.show_in_email_cond) {
							show_in_email_values.push({
								cond: _this.vars.current_element_data.show_in_email_cond[i],
								operator: _this.vars.current_element_data.show_in_email_operator[i],
								value: _this.vars.current_element_data.show_in_email_value[i]
							});
						}
					}
				}

				// show in email value
				input = _this.get_html_input("select", input_name, {
					class: "input-small ezfc-form-element-" + name,
					options: tmp_options,
					selected: value
				});

				// wrapper
				input += "<div class='ezfc-form-element-show-in-email-condition-wrapper' id='" + wrapper_id + "'>";

				// add condition
				input += "<button class='button ezfc-show-in-email-add-condition' data-func='show_in_email_add'>+</button>";

				// conditional rows
				$.each(show_in_email_values, function(i, cond_row) {
					input += "<div class='ezfc-show-in-email-row'>";

					// condition
					input += _this.get_html_input("select", input_raw + "[show_in_email_cond][" + i + "]", {
						class: "input-small ezfc-show-in-email-condition fill-elements",
						options: [],
						selected: cond_row.cond
					});

					// operator
					input += _this.get_html_input("select", input_raw + "[show_in_email_operator][" + i + "]", {
						class: "input-small ezfc-show-in-email-conditional-operator",
						options: _this.vars.cond_operators,
						selected: cond_row.operator
					});

					// conditional value
					input += _this.get_html_input("input", input_raw + "[show_in_email_value][" + i + "]", {
						class: "input-small ezfc-show-in-email-conditional-value",
						options: _this.vars.cond_operators,
						value: cond_row.value
					});

					// remove row
					input += "<button class='button ezfc-show-in-email-add-condition' data-func='show_in_email_remove'>-</button>";

					input += "</div>";
				});

				input += "</div>"; // condition wrapper
			break;

			case "slider":
				input = _this.get_html_input("yesno", input_name, {
					selected: value
				});

				// vertical slider
				var vertical_input_name = "elements[" + element.id + "][slider_vertical]";
				input += "<span class='ezfc-form-element-option-addon'>" + ezfc_vars.texts.vertical + ":</span>";
				input += _this.get_html_input("yesno", vertical_input_name, {
					selected: typeof data_el.slider_vertical === "undefined" ? 0 : data_el.slider_vertical
				});
			break;

			case "tag":
				input = _this.get_html_input("select", input_name, {
					options: [
						{ value: "h1", text: "h1"},
						{ value: "h2", text: "h2"},
						{ value: "h3", text: "h3"},
						{ value: "h4", text: "h4"},
						{ value: "h5", text: "h5"},
						{ value: "h6", text: "h6"}
					],
					selected: value
				});
			break;

			case "title":
				input = "<input type='text' class='element-label-listener' value='" + value + "' name='" + input_name + "' data-element-name='" + name + "' id='" + input_id + "' />";
			break;

			default:
				// default
				var tmp_type = skip_early ? "hidden" : "text";

				if (typeof value === "object") {
					input = "";

					$.each(value, function(key, val) {
						input += key + " <input type='" + tmp_type + "' value='" + val + "' name='" + input_name + "[" + key + "]' data-element-name='" + name + "' id='" + input_id + "-" + key + "' />";
					});
				}
				else {
					input = "<input type='" + tmp_type + "' value='" + value + "' name='" + input_name + "' data-element-name='" + name + "' id='" + input_id + "' />";
				}
			break;
		}

		// don't include this in element data
		if (skip_early_exclude) {
			input = "";
			skip_early = true;
		}

		return {
			columns:             columns,
			element_name_header: element_name_header,
			input:               input,
			skip_early:          skip_early
		};
	};

	this.get_element_option_section = function(option_name) {
		// set to basic by default
		var section = "basic";

		for (var key in _this.vars.element_option_sections) {
			if ($.inArray(option_name, _this.vars.element_option_sections[key]) >= 0) section = key;
		}

		return section;
	};

	this.get_element_type = function(id) {
		var e_id = _this.vars.current_form_elements[id].e_id;

		// extension
		if (e_id == 0 && typeof _this.vars.current_form_elements[id].data_json.extension !== "undefined") {
			e_id = _this.vars.current_form_elements[id].data_json.extension;
		}

		if (typeof ezfc.elements[e_id] === "undefined") return "";

		var type = ezfc.elements[e_id].type;

		return type;
	};

	this.get_form_element_dom = function(id) {
		return $("#ezfc-form-element-" + id);
	};

	this.is_image = function(filename) {
		var regex_extension  = /(?:\.([^.]+))?$/;
		var extension        = regex_extension.exec(filename)[1];
		var image_extensions = ["jpg", "jpeg", "png", "gif"];

		return $.inArray(extension, image_extensions) >= 0;
	};

	/**
		matrix
	**/
	// add matrix column
	this.matrix_add_column = function($button, $table, return_clone) {
		$table      = $table || $button.closest(".ezfc-form-element-matrix-table");
		var $target = $table.find(".ezfc-matrix-target-element").last();

		var mcol   = $target.data("mcol");
		var $clone = null;

		$table.find("[data-mcol='" + mcol + "']").each(function() {
			$clone = $(this).clone();
			$clone.data("mcol", ++mcol);
			$clone.insertAfter($(this));
		});

		_this.builder_functions.matrix_update($table);

		if (return_clone) return $clone;

		return false;
	};
	// add matrix row
	this.matrix_add_row = function($button, $table, return_clone) {
		$table      = $table || $button.closest(".ezfc-form-element-matrix-table");
		var $target = $table.find(".ezfc-matrix-row").last();
		var $clone  = $target.clone();
		$clone.insertAfter($target);

		// select values
		var $selects = $clone.find("select");
		$selects.each(function(i) {
			var select = this;
			$clone.find("select").eq(i).val($(select).val());
		});

		_this.builder_functions.matrix_update($table);

		if (return_clone) return $clone;

		return false;
	};
	// clear matrix table
	this.matrix_clear = function($table, clear_rows, clear_cols) {
		//var $cols = $table.find("[data-mcol]").slice(2);
		var $cols = $table.find(".ezfc-matrix-row").first().find("[data-mcol]").slice(1);
		var $rows = $table.find(".ezfc-matrix-row").slice(1);

		if (clear_rows) {
			$rows.remove();
		}

		if (clear_cols) {
			$cols.each(function(i, col) {
				$table.find("[data-mcol='" + $(col).attr("data-mcol") + "']").remove();
			});
		}
	};
	// clear all (from button)
	this.matrix_clear_table_btn = function($button) {
		var $table = $button.closest(".ezfc-row-matrix").find(".ezfc-form-element-matrix-table");
		_this.builder_functions.matrix_clear($table, true, true);

		$table.find(":input").val("");

		return false;
	};
	// remove matrix column
	this.matrix_remove_column = function($button) {
		var $table     = $button.closest(".ezfc-form-element-matrix-table");
		var mcol       = $button.closest("[data-mcol]").attr("data-mcol");
		var $cols      = $table.find("[data-mcol='" + mcol + "']");
		var cols_count = $table.find(".ezfc-matrix-row").first().find("[data-mcol]").length;

		// do not remove last column
		if (cols_count <= 2) return;

		$cols.remove();

		_this.builder_functions.matrix_update($table);
	};
	// remove matrix row
	this.matrix_remove_row = function($button) {
		var $table = $button.closest(".ezfc-form-element-matrix-table");
		var $rows  = $table.find(".ezfc-matrix-row");

		// do not remove last row
		if ($rows.length <= 1) return;

		$button.closest(".ezfc-matrix-row").remove();
		_this.builder_functions.matrix_update($table);
	};
	// update matrix table
	this.matrix_update = function($table) {
		// target element (first td row)
		_this.builder_functions.matrix_update_table($table, ".ezfc-matrix-target-element", 3);
		// conditions
		_this.builder_functions.matrix_update_table($table, ".ezfc-matrix-condition", 3);
		// values 
		$table.find(".ezfc-matrix-row").each(function(i, mrow) {
			_this.builder_functions.matrix_update_table($(this), ".ezfc-matrix-col-value", 3, i);
			_this.builder_functions.matrix_update_table($(this), ".ezfc-matrix-col-value", 4);
		});
		// update columns
		_this.builder_functions.matrix_update_columns($table);

		// update row index
		$table.find(".ezfc-matrix-row").each(function(i, tr) {
			$(tr).attr("data-mrow", i);
		});
	};
	// re-assign input names in matrix table
	this.matrix_update_table = function($table, $selector, index_num, custom_value) {
		$table.find($selector).each(function(option_row_index, option_row) {
			// update name indexes
			$(option_row).find("input, select, textarea").each(function() {
				var option_array_tmp = $(this).attr("name").replace(/\]/g, "").split("[");
				
				var option_name  = option_array_tmp[0];
				var option_array = option_array_tmp.slice(1);

				var option_out = option_name;

				for (var i in option_array) {
					var option_index_value = option_array[i];

					// index value
					if (i == index_num) {
						if (typeof custom_value !== "undefined") {
							option_index_value = custom_value;
						}
						else {
							option_index_value = option_row_index;
						}
					}

					option_out += "[" + option_index_value + "]";
				}

				$(this).attr("name", option_out);
			});
		});
	};
	// update columns
	this.matrix_update_columns = function($table) {
		$table.find("tr").each(function(row, tr) {
			$(tr).find("td").each(function(col, td) {
				$(this).attr("data-mcol", col - 1);
			});
		});
	};
	// generate matrix table from element options
	this.matrix_create_from_element = function($button) {
		var element_id = $($button.data("element")).val();

		// invalid element
		if (!element_id || typeof _this.vars.current_form_elements[element_id] === "undefined") return;

		var element = _this.vars.current_form_elements[element_id];
		// no options
		if (typeof element.data_json.options === "undefined") return;

		var $table = $button.closest(".ezfc-row-matrix").find(".ezfc-form-element-matrix-table");
		_this.builder_functions.matrix_clear($table, true, false);

		$.each(element.data_json.options, function(i, option) {
			if (option.id == "") option.id = "id-" + (i + 1);

			var $new_option;
			// use first row (persistent)
			if (i == 0) $new_option = $table.find(".ezfc-matrix-row").first();
			// add row
			else $new_option = _this.builder_functions.matrix_add_row(null, $table, true);

			$new_option.find(".ezfc-matrix-conditional-element").val(element.id);
			$new_option.find(".ezfc-matrix-conditional-operator").val("selected_id");
			$new_option.find(".ezfc-matrix-conditional-value").val(option.id);
		});
	};

	this.name_to_label = function($button) {
		var id   = $button.closest(".ezfc-form-element").data("id");
		var name = $button.siblings("input").val();

		$("#elements-label-" + id + ", #elements-title-" + id).val(name).trigger("change");
		return false;
	};

	this.option_add = function($element) {
		_this.vars.form_changed = true;

		var $list   = $element.closest(".row").find(".ezfc-form-element-option-container-list");
		var $target = $list.find("li:last");

		// clone last item
		var $clone = $target.clone();
		$clone.insertAfter($target);

		// select values
		var $selects = $clone.find("select");
		$selects.each(function(i) {
			var select = this;
			$clone.find("select").eq(i).val($(select).val());
		});

		_this.custom_trigger_change($(this).closest(".ezfc-form-element-data"));
		_this.builder_functions.reorder_options($list);
		_this.builder_functions.set_section_badges($element.closest(".ezfc-form-element"));
	};

	this.option_remove = function($element) {
		var $list             = $element.closest(".ezfc-form-element-option-container-list");
		var $self             = $element.closest(".ezfc-form-element-option-list-item");
		var target_children   = $list.find("> .ezfc-form-element-option-list-item").length;

		if (target_children <= 1) {
			// clear values
			$self.find(":input").removeAttr("disabled").val("");
			$self.find("select").val(0).data("selected", 0);
			$self.find(":checked").prop("checked", false);

			// remove chain rows
			$self.find(".ezfc-form-element-conditional-chain-remove").click();
		}
		else {
			$self.remove();
		}

		_this.builder_functions.reorder_options($list);
		_this.builder_functions.set_section_badges($element.closest(".ezfc-form-element"));
	};

	this.option_create_condition = function($btn, index) {
		var $element_data   = $btn.closest(".ezfc-form-element-data");
		var $option_wrapper = $btn.closest(".ezfc-form-element-option");
		
		// get option ID
		var option_id = _this.builder_functions.option_create_id($btn.closest(".ezfc-form-element-option-list-item"));
		$option_wrapper.find(".ezfc-form-element-option-id").val(option_id);

		// add condition row
		$element_data.find(".ezfc-row-conditional .ezfc-form-element-option-add").click();
		// fill values
		var $cond_row = $element_data.find(".ezfc-row-conditional .ezfc-form-element-conditional-wrapper").last();

		$cond_row.find(".ezfc-form-element-conditional-operator").val("selected_id");
		$cond_row.find(".ezfc-form-element-conditional-value").val(option_id);

		_this.builder_functions.set_section_badges($element_data.closest(".ezfc-form-element"));
		_this.builder_functions.set_section("conditional");
	};

	// create conditions for all options
	this.option_create_all_conditions = function(btn) {
		var $option_wrapper = $(btn).closest(".ezfc-row-options");

		// create option IDs
		_this.builder_functions.option_create_ids($option_wrapper);

		// create conditions for each element
		$option_wrapper.find(".ezfc-form-option-create-condition").click();
	};

	// create option IDs
	this.option_create_ids = function($element) {
		var $option_wrapper;

		// get wrapper
		if ($element.is(".ezfc-row-options")) $option_wrapper = $element.find(".ezfc-form-element-option-container-list");
		else $option_wrapper = $element.closest(".ezfc-row-options").find(".ezfc-form-element-option-container-list");

		$option_wrapper.find("li.ezfc-form-element-option-list-item").each(function(i, list_item) {
			var option_id_new = _this.builder_functions.option_create_id($(this), $option_wrapper);
			$(this).find(".ezfc-form-element-option-id").val(option_id_new);
		});
	};
	// create option ID
	this.option_create_id = function($list_item, $option_wrapper, add_id) {
		if (!$option_wrapper) $option_wrapper = $list_item.closest(".ezfc-form-element-option-container-list");
		add_id = add_id || "";

		// get option ID
		var value = $list_item.find(".ezfc-form-element-option-id").val();
		value = $.trim(value);

		// check if empty
		if (value == "") {
			// generate ID
			value = "id-" + ($list_item.index() + 1) + add_id;

			// check if ID exists
			var check_value_exists = $option_wrapper.find(".ezfc-form-element-option-id").filter(function() { return this.value == value; }).length;
			// add some text part recursively if ID exists
			if (check_value_exists) return _this.builder_functions.option_create_id($list_item, $option_wrapper, add_id + "-1");
		}

		return value;
	};

	// select option toggle
	this.option_toggle_select = function($element) {
		var $toggle_options = $element.find("[data-optiontoggle]");
		$toggle_options.each(function() {
			var $toggle_elem = $($(this).data("optiontoggle"));

			if (!$toggle_elem.length) return 1;

			var toggle_show = $(this).is(":selected") ? "show" : "hide";
			$toggle_elem[toggle_show]();
		});
	};

	this.prio_dec = function(btn) {
		var $parent_el = $(btn).closest(".ezfc-form-element-calculate-wrapper");
		var $prio_el   = $parent_el.find(".ezfc-form-element-calculate-prio");
		var prio       = parseInt($prio_el.val());
		if (isNaN(prio)) prio = 0;
		
		$parent_el.removeClass("ezfc-calculate-prio-" + prio);

		prio = Math.min(Math.max(prio - 1, 0), 4);
		$prio_el.val(prio);
		$parent_el.addClass("ezfc-calculate-prio-" + prio);
	};
	this.prio_inc = function(btn) {
		var $parent_el = $(btn).closest(".ezfc-form-element-calculate-wrapper");
		var $prio_el   = $parent_el.find(".ezfc-form-element-calculate-prio");
		var prio       = parseInt($prio_el.val());
		if (isNaN(prio)) prio = 0;
		
		$parent_el.removeClass("ezfc-calculate-prio-" + prio);

		prio = Math.min(Math.max(prio + 1, 0), 4);
		$prio_el.val(prio);
		$parent_el.addClass("ezfc-calculate-prio-" + prio);
	};

	// quick add elements via textarea
	this.quick_add = function(input_value) {
		_this.do_action(false, false, "quick_add", false, "text=" + encodeURIComponent(input_value));
	};

	// reorder options (calculate, conditional, discount)
	this.reorder_options = function($list, list_selector, indexnum) {
		indexnum      = typeof indexnum !== "undefined" ? indexnum : $list.data("indexnum");
		list_selector = typeof list_selector !== "undefined" ? list_selector : ".ezfc-form-element-option";

		$list.find(list_selector).each(function(option_row_index, option_row) {
			$(option_row).find("input, select, textarea").each(function() {
				var option_array_tmp = $(this).attr("name").replace(/\]/g, "").split("[");
				
				var option_name  = option_array_tmp[0];
				var option_array = option_array_tmp.slice(1);

				var option_out = option_name;

				for (var i in option_array) {
					var option_index_value = option_array[i];

					// index value
					if (i == indexnum) {
						option_index_value = option_row_index;
					}

					option_out += "[" + option_index_value + "]";
				}

				$(this).attr("name", option_out);

				if ($(this).hasClass("ezfc-fill-index-value")) $(this).val(option_row_index);
			});

			// index number
			$(option_row).find(".ezfc-form-element-option-index-number").text(option_row_index);
		});

		_this.fill_calculate_fields();
	};

	// search email in submissions
	this.search_email_in_submission = function($button) {
		var email_value = $("#ezfc-form-search-email-value").val();
		if (!email_value.length) return false;

		var add_data = "email_value=" + email_value;

		_this.do_action(null, null, "search_email_in_submission", add_data);
	};

	/**
		visually select a target element
	**/
	this.select_target_activate_button = function($button, target_id) {
		var $target = $button.siblings(".fill-elements");
		_this.builder_functions.select_target_activate($target);
	};
	this.select_target_activate = function($dropdown) {
		// store active element id
		_this.vars.active_element_id = _this.builder_functions.get_active_element_id();
		_this.vars.$active_element_dropdown = $dropdown;
		var target_id = $dropdown.val();

		// add pointer class
		$("body").addClass("ezfc-select-target");

		// add active class to target element
		if (target_id) {
			$("#ezfc-form-element-" + target_id).addClass("ezfc-form-element-tmp-active");
		}

		// close element
		_this.builder_functions.element_data_close();
	};
	this.select_target_reset = function() {
		// reset state
		_this.vars.active_element_id = 0;
		_this.vars.$active_element_dropdown = 0;
		$("body").removeClass("ezfc-select-target");
		$(".ezfc-form-element-tmp-active").removeClass("ezfc-form-element-tmp-active");
	};
	this.select_target_selected = function(id) {
		// check if ID exists or can be selected
		if (!_this.vars.$active_element_dropdown.find("option[value='" + id + "']").length) {
			alert("Only calculation elements can be selected.");
			return;
		}

		_this.builder_functions.element_data_open(_this.vars.active_element_id, true);
		_this.vars.$active_element_dropdown.val(id);
		_this.vars.$active_element_dropdown.data("selected", id);

		_this.builder_functions.select_target_reset();
	};

	/**
		set active section
	**/
	this.set_section = function(section) {
		if (section == "") section = "basic";

		_this.vars.active_section = section;
		var $active    = _this.builder_functions.get_active_element();
		var $el_data   = $active.find("> .ezfc-form-element-data");

		// remove active classes
		$el_data.find(".ezfc-element-option-section-heading.active, .ezfc-element-option-section-data.active").removeClass("active");

		// add active classes
		var $section_to_activate = $el_data.find(".ezfc-element-option-section-heading[data-section='" + section + "']");
		// check if section is available in the current element
		if (!$section_to_activate.length) {
			_this.vars.active_section = "basic";
			section        = _this.vars.active_section;

			$section_to_activate = $el_data.find(".ezfc-element-option-section-heading[data-section='" + section + "']");
		}

		$section_to_activate.addClass("active");
		
		// section data element
		var $section_data = $el_data.find(".ezfc-element-option-section-" + section);
		// set active
		$section_data.addClass("active");
		// focus first input
		$section_data.find("input:visible, select:visible").first().focus().select();
	};

	this.set_section_badges = function($element) {
		var $form_element_data = $element.find("> .ezfc-form-element-data");
		var sections = [
			{ name: "calculate", check_selector: ".ezfc-form-element-calculate-operator" },
			{ name: "conditional", check_selector: ".ezfc-form-element-conditional-action" },
			{ name: "discount", check_selector: ".ezfc-form-element-discount-operator" }
		];

		for (var i in sections) {
			var badge_count = 0;
			var $badge = $element.find(".ezfc-element-option-section-heading[data-section='" + sections[i].name + "'] .ezfc-badge");
			var $items = $element.find(".ezfc-element-option-section-" + sections[i].name + " .ezfc-form-element-option-list-item");

			$.each($items, function(ii, item) {
				var operator = $(item).find(sections[i].check_selector).val();

				if (operator != null && operator != 0) badge_count++;
			});

			$badge.text(badge_count);
		}
	};

	this.show_in_email_add = function($button) {
		var $wrapper = $button.closest(".ezfc-form-element-show-in-email-condition-wrapper");
		var $row     = $wrapper.find(".ezfc-show-in-email-row").last();

		var $clone = $row.clone();
		$clone.insertAfter($row);

		_this.builder_functions.reorder_options($wrapper, ".ezfc-show-in-email-row", 2);
	};
	this.show_in_email_remove = function($button) {
		var $wrapper   = $button.closest(".ezfc-form-element-show-in-email-condition-wrapper");
		var row_length = $wrapper.find(".ezfc-show-in-email-row").length;

		if (row_length <= 1) return;

		$button.closest(".ezfc-show-in-email-row").remove();

		_this.builder_functions.reorder_options($wrapper, ".ezfc-show-in-email-row", 2);
	};

	this.toggle_dialog = function(button) {
		var target_dialog = $(button).data("target");
		$(target_dialog).dialog("open");
	};

	this.update_element_title = function(fe_id) {
		var $element = $("#ezfc-form-element-" + fe_id);
		// get data wrapper
		var $element_data = $element.find("> .ezfc-form-element-data");
		// get listener element
		var $listener = $element_data.find(".element-label-listener");

		// listener text
		var text = $listener.val();

		// empty text, get fallback name
		if (text == "") {
			text = $element_data.find("input[data-element-name='name']").val();
		}

		// update text
		$element.find("> .ezfc-form-element-name .element-label").text(text);
	}
};