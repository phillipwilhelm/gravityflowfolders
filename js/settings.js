(function (gravityflowfolders, $) {
    var customFields, imgUrl;


    $(document).ready(function () {
        init();
    });

    function init() {
        var $custValsHidden = $("#custom_fields");
        customFields = $.parseJSON($custValsHidden.val());
        $custValsHidden.val('');
        imgUrl = gravityflowfolders_settings_js_strings.vars.imagesUrl;

        $("#gravityflowfolders-custom-fields").html(getCustomFieldsUI());

        setUpSortable();


        $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );

		$('.gravityflowfolders-multiselect').multiSelect();
    };

    function setUpSortable() {
        $('#gravityflowfolders-custom-fields').sortable({
            axis  : 'y',
            handle: '.gravityflowfolders-custom-field-handle'
        });
    }


    function getCustomFieldsUI() {
        var html = "";
        if (customFields == null || customFields.length == 0) {
            html = getNoCustomFields();
            return html;
        }

        $.each(customFields, function (key, field) {
            html += getCustomFieldRow(key, field);
        });

        return html;
    }

    function getNoCustomFields() {
        var html;
        html = '<div id="gcontact-no-custom-fields">';
        html += '<span onclick="gravityflowfolders.addNewCustomField(this);jQuery(this).parent().remove();">Add a custom field ';
        html += '<img src="{0}/add.png" class="gravityflowfolders-add" />'.format(imgUrl);
        html += "</span></div>"
        return html;
    }

    function CustomField(label) {
        this.label = typeof key == 'undefined' ? "" : label;
    }

    function getUniqueId() {
        return 'customxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : r & 0x3 | 0x8;
            return v.toString(16);
        });
    }

    gravityflowfolders.addNewCustomField = function (elem) {
        var m = getCustomFieldRow(getUniqueId(), new CustomField);
        $(elem).parent().after(m);
        var count = $(".gravityflowfolders-custom-field-row").length;
        if (count == 1)
            $(".gravityflowfolders-custom-field-handle").hide();
        else
            $(".gravityflowfolders-custom-field-handle").show();
    }

    gravityflowfolders.removeCustomField = function (elem) {
        $(elem).parent().remove();
        var count = $(".gravityflowfolders-custom-field-row").length;
        if (count == 0)
            displayNoCustomFields();
        else if (count == 1)
            $(".gravityflowfolders-custom-field-handle").hide();
    }

    function displayNoCustomFields() {
        $("#gravityflowfolders-custom-fields").html(getNoCustomFields());
    }

    function getCustomFieldRow(key, field) {
        var html;
        html = '<div class="gravityflowfolders-custom-field-row">';
        html += '<img src="{0}/arrow-handle.png" class="gravityflowfolders-custom-field-handle" /><input type="text" name="_gaddon_setting_custom_fields[{1}][label]" value="{2}"/>'.format(imgUrl, key, field.label);
		html += '<select size="8" multiple="multiple" name="_gaddon_setting_custom_fields[{1}][forms]" class="gravityflowfolders-multiselect" ><option value="">Select One</option><option value="hello">Hello</option></select>'.format(key);
        html += '<img class="gravityflowfolders-add" src="{0}/add.png" onclick="gravityflowfolders.addNewCustomField(this);" />'.format(imgUrl);
        html += '<img class="gravityflowfolders-remove" src="{0}/remove.png" onclick="gravityflowfolders.removeCustomField(this);" />'.format(imgUrl);
        html += "</div>";
        return html;
    }

    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined' ? args[number] : match;
        });
    };

}(window.gravityflowfolders = window.gravityflowfolders || {}, jQuery));



