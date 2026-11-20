jQuery(document).ready(function ($) {

    var pageTemplateSelector = $('#page_template');
    var openContactEditorButton = $('#contact-editor-button-id');

    if (pageTemplateSelector.length > 0) {
        showOrHideContactEditorButton();
        pageTemplateSelector.on('change', function () {
            showOrHideContactEditorButton();
        });
    }

    function showOrHideContactEditorButton() {
        var selectedOption = pageTemplateSelector.find('option:selected');
        if (selectedOption.val().indexOf('contact') > 0) {
            openContactEditorButton.show();
        } else {
            openContactEditorButton.hide();
        }
    }

    $(document).on('click', '#contact-editor-button-id', function () {
        var width = 540;
        var height = 400;
        var tbWindowWidth = width + 30;
        var tbWindowHeight = height + 30;
        var t = this.title;
        var a = this.href + '&width=' + width + '&height=' + height + '';
        var g = this.rel || false;
        var old_tb_init = window.tb_init;
        window.tb_init = function (domChunk) {
            old_tb_init(domChunk);
            $("#TB_window").css({marginLeft:'-' + parseInt((tbWindowWidth / 2), 10) + 'px', width:tbWindowWidth + 'px'});
            window.tb_init = old_tb_init;
            afterThickBoxContentLoad();
        };
        tb_show(t, a, g);
        this.blur();
        return false;
    });

    function afterThickBoxContentLoad() {
        var windowTitleHeight = $("#TB_title").height();
        var windowHeight = $("#TB_window").height();
        var windowContentHeight = windowHeight - windowTitleHeight - 20;
        $('#TB_ajaxContent').height(windowContentHeight);
        switchLocalizationType();
        installListeners();
    }

    function installListeners() {

        $(document).on('change', '#contact-editor-loc-type', function () {
            switchLocalizationType();
        });

        $(document).on('click', '#contact-editor-form-save', function () {
            var saveButton = $(this);
            if ($('#contact-editor-form').valid()) {
                var latitudeEl = $('#contact-editor-lat');
                var longitudeEl = $('#contact-editor-long');

                var latitude = '';
                var longitude = '';
                var localizationType = $('#contact-editor-loc-type').val();
                var zoom = $('#contact-editor-zoom').val();
                var height = $('#contact-editor-height').val();
                if(localizationType == 'address'){
                    latitude = latitudeEl.prop("defaultValue");
                    longitude = longitudeEl.prop("defaultValue");
                }else{
                    latitude = latitudeEl.val();
                    longitude = longitudeEl.val();
                }

                var contactMapContent = '{"localization_type": "' + localizationType + '", "latitude": "' + latitude + '", "longitude": "' + longitude + '", "zoom": "' + zoom + '", "height": "' + height + '"}';

                var params = {
                    'action':'finesse-contact-map-settings-save',
                    'settings':contactMapContent,
                    'post-id':$('#contact-editor-post-id').val()
                };
                saveButton.attr("disabled", "true");
                AdminUtil.doPost(params, 'html', function (response) {
                    saveButton.removeAttr("disabled");
                    tb_remove();
                }, function (errorThrown) {
                    saveButton.removeAttr("disabled");
                    alert(errorThrown);
                });
            }
            return false;
        });

        $(document).on('click', '#contact-editor-form-cancel', function () {
            tb_remove();
        });
    }

    function switchLocalizationType() {
        var localizationType = $('#contact-editor-loc-type').val();
        var addressDiv = $('#contact-editor-address').parent();
        var latDiv = $('#contact-editor-lat').parent();
        var longDiv = $('#contact-editor-long').parent();

        if (localizationType == 'coordinates') {
            addressDiv.hide();
            latDiv.show();
            longDiv.show();
        } else {
            latDiv.hide();
            longDiv.hide();
            addressDiv.show();
        }
    }

});
