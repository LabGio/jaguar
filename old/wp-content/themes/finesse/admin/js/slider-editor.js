jQuery(document).ready(function ($) {

    var pageTemplateSelector = $('#page_template');
    var sliderTypeElement = $('#slider-type');
    var pageTypeElement = $('#page-type');
    var sliderEditorButton = $('#slider-editor-button-id');
    var videoEditorButton = $('#video-editor-button-id');
    var audioEditorButton = $('#audio-editor-button-id');
    var linkEditorButton = $('#link-editor-button-id');

    if (pageTemplateSelector.length > 0) {
        showOrHideSliderEditorButtonInPage();
        pageTemplateSelector.on('change', function () {
            showOrHideSliderEditorButtonInPage();
        });
    } else if (sliderTypeElement.length > 0 && pageTypeElement.length > 0) {
        if (pageTypeElement.val() == 'portfolio') {
            showOrHideMediaEditorButtonInPortfolio();
            $('input[name=post_format]').on('click', function () {
                showOrHideMediaEditorButtonInPortfolio();
            });
        } else if (pageTypeElement.val() == 'post') {
            showOrHideMediaEditorButtonInPost();
            $('input[name=post_format]').on('click', function () {
                showOrHideMediaEditorButtonInPost();
            });
        }
    }

    function showOrHideSliderEditorButtonInPage() {
        var sliderType = sliderTypeElement.val();
        var sliderButtonHref = sliderEditorButton.attr('href');
        sliderButtonHref = sliderButtonHref.replace('slider-type=none', 'slider-type=' + sliderType);
        sliderEditorButton.attr('href', sliderButtonHref);

        var selectedOption = pageTemplateSelector.find('option:selected');
        if (selectedOption.val().indexOf('frontpage') > 0) {
            sliderEditorButton.show();
        } else {
            sliderEditorButton.hide();
        }
    }

    function showOrHideMediaEditorButtonInPortfolio() {
        $('.page-media-editor-button').hide();
        var postFormat = $('input[name=post_format]:checked').val();
        if (postFormat == 'video') {
            videoEditorButton.show();
        } else {
            var sliderType = sliderTypeElement.val();
            var sliderButtonHref = sliderEditorButton.attr('href');
            sliderButtonHref = sliderButtonHref.replace('slider-type=none', 'slider-type=' + sliderType);
            sliderEditorButton.attr('href', sliderButtonHref);
            sliderEditorButton.show();
        }
    }

    function showOrHideMediaEditorButtonInPost() {
        $('.page-media-editor-button').hide();
        var postFormat = $('input[name=post_format]:checked').val();
        if (postFormat == 'gallery') {
            var sliderType = sliderTypeElement.val();
            var sliderButtonHref = sliderEditorButton.attr('href');
            sliderButtonHref = sliderButtonHref.replace('slider-type=none', 'slider-type=' + sliderType);
            sliderEditorButton.attr('href', sliderButtonHref);
            sliderEditorButton.show();
        } else if (postFormat == 'video') {
            videoEditorButton.show();
        } else if (postFormat == 'audio') {
            audioEditorButton.show();
        } else if (postFormat == 'link') {
            linkEditorButton.show();
        }
    }

    $(document).on('click', '.page-media-editor-button', function () {
        var width = 840;
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
        var pageMediaType = $('#page-media-type');
        var windowTitleHeight = $("#TB_title").height();
        var windowHeight = $("#TB_window").height();
        var windowContentHeight = windowHeight - windowTitleHeight - 20;
        $('#TB_ajaxContent').height(windowContentHeight);
        $('#slider-manager-thumbs').height(windowContentHeight - 50);
        $('#slider-manager-slides').height(windowContentHeight - 110);

        if (pageMediaType.length > 0) {
            if (pageMediaType.val() == 'video') {
                $(document).on('click', '#page-media-add-embedded-video-form-submit', function () {
                    saveEmbeddedVideo('video');
                });
                $(document).on('click', '#page-media-add-embedded-video-form-cancel', function () {
                    tb_remove();
                });
                $(document).on('click', '#page-media-add-embedded-video-form-remove', function () {
                    removePageMedia('video');
                });

                $(document).on('click', '#page-media-add-sh-video-form-submit', function () {
                    saveSelfHostedVideo('video');
                });
                $(document).on('click', '#page-media-add-sh-video-form-cancel', function () {
                    tb_remove();
                });
                $(document).on('click', '#page-media-add-sh-video-form-remove', function () {
                    removePageMedia('video');
                });
            } else if (pageMediaType.val() == 'audio') {
                $(document).on('click', '#page-media-add-audio-form-submit', function () {
                    saveAudio();
                });
                $(document).on('click', '#page-media-add-audio-form-cancel', function () {
                    tb_remove();
                });
                $(document).on('click', '#page-media-add-audio-form-remove', function () {
                    removePageMedia('audio');
                });
            } else if (pageMediaType.val() == 'link') {
                $(document).on('click', '#page-media-add-link-form-submit', function () {
                    saveLink();
                });
                $(document).on('click', '#page-media-add-link-form-cancel', function () {
                    tb_remove();
                });
            }
        }

        $(".tabs").tabs();
        $(".color-thumbs").each(function () {
            $(this).colorRadioButtons();
        });
        $(".image-selector").each(function () {
            $(this).mediaLibraryImageSelector();
        });

        $("#slider-manager-thumbs li").draggable({
            appendTo:"body",
            helper:"clone"
        });
        $("#slider-manager-right").droppable({
            activeClass:"ui-state-default",
            hoverClass:"ui-state-hover",
            accept:":not(.ui-sortable-helper)",
            drop:function (event, ui) {
                addSlideToSlider($(ui.draggable));
            }
        });
        $("#slider-manager-slides").each(function () {
            $(this).sortable();
        });

        $('#slider-manager-search-image').on('keyup', function () {
            var filterInput = $.trim($(this).val().toLowerCase());
            $("#slider-manager-thumbs").find("li").each(function () {
                var $this = $(this);
                var filterData = $this.data('filter').toLowerCase();
                if (filterData.indexOf(filterInput) >= 0) {
                    $this.show();
                } else {
                    $this.hide();
                }
            });
        });

        $(document).on('change', '#slider-manager-slider-enabled', function () {
            if ($(this).is(':checked')) {
                $('.cycle-slider-settings').show();
            } else {
                $('.cycle-slider-settings').hide();
            }
        });

        initDialogs();
        jQuery("#TB_window").on('tb_unload', function () {
            destroyDialogs();
        });

    }

    function destroyDialogs(){
        $('#page-media-add-embedded-video-dialog').dialog('destroy');
        $('#page-media-add-sh-video-dialog').dialog('destroy');
        $('#page-media-add-slide-caption-dialog').dialog('destroy');
        $('#page-media-add-slide-meta-dialog').dialog('destroy');
    }

    function initDialogs(){

        var pageMediaAddEmbeddedVideoDialogSubmitListener;
        var pageMediaAddEmbeddedVideoDialogCancelListener;
        $("#page-media-add-embedded-video-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            draggable:true,
            dialogClass: 'wp-dialog',
            open: function () {
                pageMediaAddEmbeddedVideoDialogSubmitListener = function () {
                    saveEmbeddedVideo('slider');
                };
                pageMediaAddEmbeddedVideoDialogCancelListener = function () {
                    $("#page-media-add-embedded-video-dialog").dialog("close");
                };
                $(document).on('click', '#page-media-add-embedded-video-form-submit', pageMediaAddEmbeddedVideoDialogSubmitListener);
                $(document).on('click', '#page-media-add-embedded-video-form-cancel', pageMediaAddEmbeddedVideoDialogCancelListener);
            },
            close: function(){
                $(document).off('click', '#page-media-add-embedded-video-form-submit', pageMediaAddEmbeddedVideoDialogSubmitListener);
                $(document).off('click', '#page-media-add-embedded-video-form-cancel', pageMediaAddEmbeddedVideoDialogCancelListener);
            }
        });

        var pageMediaAddShVideoDialogSubmitListener;
        var pageMediaAddShVideoDialogCancelListener;
        $("#page-media-add-sh-video-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            draggable:true,
            resizable:false,
            dialogClass: 'wp-dialog',
            open: function () {
                pageMediaAddShVideoDialogSubmitListener = function () {
                    saveSelfHostedVideo('slider');
                };
                pageMediaAddShVideoDialogCancelListener = function () {
                    $("#page-media-add-sh-video-dialog").dialog("close");
                };
                $(document).on('click', '#page-media-add-sh-video-form-submit', pageMediaAddShVideoDialogSubmitListener);
                $(document).on('click', '#page-media-add-sh-video-form-cancel', pageMediaAddShVideoDialogCancelListener);
            },
            close: function(){
                $(document).off('click', '#page-media-add-sh-video-form-submit', pageMediaAddShVideoDialogSubmitListener);
                $(document).off('click', '#page-media-add-sh-video-form-cancel', pageMediaAddShVideoDialogCancelListener);
            }
        });

        var pageMediaAddSlideCaptionDialogSubmitListener;
        var pageMediaAddSlideCaptionDialogCancelListener;
        $("#page-media-add-slide-caption-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            draggable:true,
            resizable:false,
            dialogClass: 'wp-dialog',
            open: function () {
                pageMediaAddSlideCaptionDialogSubmitListener = function () {
                    setSlideCaption();
                    $("#page-media-add-slide-caption-dialog").dialog("close");
                };
                pageMediaAddSlideCaptionDialogCancelListener = function () {
                    $("#page-media-add-slide-caption-dialog").dialog("close");
                };
                $(document).on('click', '#page-media-add-slide-caption-form-submit', pageMediaAddSlideCaptionDialogSubmitListener);
                $(document).on('click', '#page-media-add-slide-caption-form-cancel', pageMediaAddSlideCaptionDialogCancelListener);
            },
            close: function(){
                $(document).off('click', '#page-media-add-slide-caption-form-submit', pageMediaAddSlideCaptionDialogSubmitListener);
                $(document).off('click', '#page-media-add-slide-caption-form-cancel', pageMediaAddSlideCaptionDialogCancelListener);
            }
        });

        var pageMediaAddSlideMetaDialogSubmitListener;
        var pageMediaAddSlideMetaDialogCancelListener;
        $("#page-media-add-slide-meta-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            draggable:true,
            resizable:false,
            dialogClass: 'wp-dialog',
            open: function () {
                pageMediaAddSlideMetaDialogSubmitListener = function () {
                    setSlideMeta();
                    $("#page-media-add-slide-meta-dialog").dialog("close");
                };
                pageMediaAddSlideMetaDialogCancelListener = function () {
                    $("#page-media-add-slide-meta-dialog").dialog("close");
                };
                $(document).on('click', '#page-media-add-slide-meta-form-submit', pageMediaAddSlideMetaDialogSubmitListener);
                $(document).on('click', '#page-media-add-slide-meta-form-cancel', pageMediaAddSlideMetaDialogCancelListener);
            },
            close: function(){
                $(document).off('click', '#page-media-add-slide-meta-form-submit', pageMediaAddSlideMetaDialogSubmitListener);
                $(document).off('click', '#page-media-add-slide-meta-form-cancel', pageMediaAddSlideMetaDialogCancelListener);
            }
        });

        $('#slider-manager-add-embedded-video').on('click', function () {
            $("#page-media-add-embedded-video-dialog").dialog("open");
        });

        $('#slider-manager-add-sh-video').on('click', function () {
            $("#page-media-add-sh-video-dialog").dialog("open");
        });
    }

    $(document).on('click', '.delete-slider-slide-button', function () {
        var ulSlider = $("#slider-manager-slides");
        $(this).parent().remove();
        if (ulSlider.find('li').length == 0) {
            $('<li class="slider-manager-slide-placeholder">Drag your images here</li>').appendTo(ulSlider);
        }
    });

    $(document).on('click', '.add-caption-slider-slide-button', function () {
        var sliderType = getSliderType();
        if (sliderType == 'cycleSlider') {
            alert('This slider (Cycle Slider) doesn\'t support caption feature.');
        } else {
            var dialogEl = $("#page-media-add-slide-caption-dialog");
            dialogEl.dialog("open");
            var liEl = $(this).parent();
            var imageLink = liEl.data('image-link');
            var captionTitle = liEl.data('caption-title');
            var captionContent = liEl.data('caption-content');
            if (captionTitle) {
                captionTitle = Base64.decode(captionTitle);
            } else {
                captionTitle = '';
            }
            if (captionContent) {
                captionContent = Base64.decode(captionContent);
            } else {
                captionContent = '';
            }
            if (imageLink) {
                imageLink = Base64.decode(imageLink);
            } else {
                imageLink = '';
            }
            $('#page-media-add-slide-caption-title').val(captionTitle);
            $('#page-media-add-slide-caption-content').val(captionContent);
            $('#page-media-add-slide-caption-link').val(imageLink);
            dialogEl.data('invoker', liEl);
        }
    });

    $(document).on('click', '.edit-meta-slider-slide-button', function () {
        var sliderType = getSliderType();
        if (sliderType == 'cycleSlider') {
            var dialogEl = $("#page-media-add-slide-meta-dialog");
            dialogEl.dialog("open");
            var liEl = $(this).parent();
            var imageTitle = liEl.data('image-title');

            if (imageTitle) {
                imageTitle = Base64.decode(imageTitle);
            } else {
                imageTitle = '';
            }

            $('#page-media-add-slide-meta-title').val(imageTitle);
            dialogEl.data('invoker', liEl);
        } else {
            alert('Feature not supported.');
        }
    });

    $(document).on('click', '#slider-save-button', function () {
        var saveButton = $(this);
        if (isSliderManagerFormValid()) {
            var sliderType = $('#slider-manager-slider-type').val();

            var sliderSlides = getSliderSlidesAsJSON();
            var sliderSettings = getSliderSettingsAsJSON();

            var sliderContent = '{"media_type": "slider", "slider_type": "' + sliderType + '", "slides": ' + sliderSlides + ', "settings": ' + sliderSettings + '}';

            var params = {
                'action':'finesse-page-media-save',
                'settings':sliderContent,
                'post-id':$('#page-media-post-id').val()
            };
            saveButton.attr("disabled", "true");
            AdminUtil.doPost(params, 'html', function (response) {
                saveButton.removeAttr("disabled");
                tb_remove();
                updatePageMediaSettingsMeta(sliderContent);
            }, function (errorThrown) {
                saveButton.removeAttr("disabled");
                alert(errorThrown);
            });
        } else {
            alert('The slider settings contains invalid data.');
        }
        return false;
    });

    $(document).on('click', '#slider-cancel-button', function () {
        tb_remove();
    });

    function isSliderManagerFormValid() {
        var valid = true;
        $('#slider-manager-form :input').each(function () {
            var field = $(this);
            if (field.hasClass('required')) {
                if ($.trim(field.val()).length > 0) {
                    field.css('border-color', '');
                    field.css('border', '');
                } else {
                    field.css('border-color', '#ff0000');
                    field.css('border', '1px solid #ff0000');
                    valid = false;
                }
            }
        });
        return valid;
    }

    function getSliderSlidesAsJSON() {
        var postContent = '[';
        var index = 0;
        $("#slider-manager-slides li").each(function () {
            var isPlaceholder = $(this).hasClass('slider-manager-slide-placeholder');
            if (!isPlaceholder) {
                var id = $(this).data('id');
                var type = $(this).data('type');
                var imageLink = $(this).data('image-link') ? $(this).data('image-link') : '';
                var imageTitle = $(this).data('image-title') ? $(this).data('image-title') : '';
                var captionTitle = $(this).data('caption-title') ? $(this).data('caption-title') : '';
                var captionContent = $(this).data('caption-content') ? $(this).data('caption-content') : '';
                if (index > 0) {
                    postContent += ',';
                }

                postContent += '{"type": "' + type + '", "id": "' + id + '", "caption_title": "' +
                    captionTitle + '", "caption_content": "' + captionContent + '", "image_link": "' +
                    imageLink + '", "image_title": "'+imageTitle+'" }';
                index++;
            }
        });
        postContent += ']';
        return postContent;
    }

    function getSliderSettingsAsJSON() {
        var sliderType = getSliderType();
        var settings = '{';
        if (sliderType == 'flexSlider') {
            var animationLoop = $('#slider-manager-slider-settings-animloop').is(':checked') ? 'true' : 'false';
            var slideShow = $('#slider-manager-slider-settings-slideshow').is(':checked') ? 'true' : 'false';
            var randomize = $('#slider-manager-slider-settings-random').is(':checked') ? 'true' : 'false';
            var pauseOnHover = $('#slider-manager-slider-settings-pausehover').is(':checked') ? 'true' : 'false';

            settings += ' "animation":"' + $('#slider-manager-slider-settings-animation').val() + '",';
            settings += ' "animation_speed":"' + $('#slider-manager-slider-settings-animspeed').val() + '",';
            settings += ' "easing":"' + $('#slider-manager-slider-settings-easing').val() + '",';
            settings += ' "animation_loop":"' + animationLoop + '",';
            settings += ' "slide_show":"' + slideShow + '",';
            settings += ' "slide_show_speed":"' + $('#slider-manager-slider-settings-slideshowspeed').val() + '",';
            settings += ' "randomize":"' + randomize + '",';
            settings += ' "pause_on_hover":"' + pauseOnHover + '",';
        } else if (sliderType == 'cycleSlider') {
            var enabled = $('#slider-manager-slider-enabled').is(':checked') ? 'true' : 'false';
            var nowrap = $('#slider-manager-slider-settings-circular').is(':checked') ? 'false' : 'true';
            var slideShow = $('#slider-manager-slider-settings-slideshow').is(':checked') ? 'true' : 'false';
            var pause_on_pager_hover = $('#slider-manager-slider-settings-pph').is(':checked') ? 'true' : 'false';
            var pauseOnHover = $('#slider-manager-slider-settings-pausehover').is(':checked') ? 'true' : 'false';

            settings += ' "enabled":"' + enabled + '",';
            settings += ' "fx":"' + $('#slider-manager-slider-settings-animation').val() + '",';
            settings += ' "speed":"' + $('#slider-manager-slider-settings-animspeed').val() + '",';
            settings += ' "nowrap":"' + nowrap + '",';
            settings += ' "pause_on_pager_hover":"' + pause_on_pager_hover + '",';
            settings += ' "slide_show":"' + slideShow + '",';
            settings += ' "timeout":"' + $('#slider-manager-slider-settings-slideshowspeed').val() + '",';
            settings += ' "pause":"' + pauseOnHover + '",';
        }
        settings += ' "dummy":""';
        settings += '}';
        return settings;
    }

    function setSlideCaption() {
        var invokerEl = $("#page-media-add-slide-caption-dialog").data('invoker');
        if ($('#page-media-add-slide-caption-form').valid()) {
            var captionTitle = $('#page-media-add-slide-caption-title').val();
            var captionContent = $('#page-media-add-slide-caption-content').val();
            var imageLink = $('#page-media-add-slide-caption-link').val();

            invokerEl.data('image-link', Base64.encode(imageLink));
            invokerEl.data('caption-title', Base64.encode(captionTitle));
            invokerEl.data('caption-content', Base64.encode(captionContent));
        }
        return false;
    }

    function setSlideMeta() {
        var invokerEl = $("#page-media-add-slide-meta-dialog").data('invoker');
        if ($('#page-media-add-slide-meta-form').valid()) {
            var imageTitle = $('#page-media-add-slide-meta-title').val();

            invokerEl.data('image-title', Base64.encode(imageTitle));
        }
        return false;
    }

    function addThumb(liElement) {
        if ($(liElement).is('li')) {
            $(liElement).appendTo($('#slider-manager-thumbs'));
        }
    }

    function addSlideToSlider(liElement) {
        if ($(liElement).is('li')) {
            var img = $(liElement).find('img');
            if (img.length > 0) {
                var sliderType = getSliderType();
                var imgId = img.data('id');
                var imgSrc = img.attr('src');
                var imgTitle = img.attr('title');
                var type = 'img';
                if (imgTitle.indexOf('video-') == 0) {
                    type = 'video';
                }
                $(".slider-manager-slide-placeholder").remove();

                var element = '<li data-type="' + type + '" data-id="' + imgId + '">';
                element += '<img src="' + imgSrc + '" title="' + imgTitle + '">';
                element += '<span class="type-' + type + '"></span>';
                if (sliderType == 'cycleSlider') {
                    element += '<a class="edit-meta-slider-slide-button" href="#" title="Edit Meta">Edit Meta</a>';
                } else {
                    element += '<a class="add-caption-slider-slide-button" href="#" title="Edit Caption">Edit Caption</a>';
                }
                element += '<a class="delete-slider-slide-button" href="#"></a>';
                element += '</li>';
                $(element).appendTo($('#slider-manager-slides'));
            }
        }
    }

    function saveEmbeddedVideo(mediaType) {
        if ($('#page-media-add-embedded-video-form').valid()) {
            var saveButton = $('#page-media-add-embedded-video-form-submit');
            var videoId = $('#page-media-add-embedded-video-id');
            var embedCode = $('#page-media-add-embedded-embed-code');
            var params = {
                'action':'finesse-page-media-add-embedded-video',
                'media-type':mediaType,
                'video-id':videoId.val(),
                'embed-code-base64':Base64.encode(embedCode.val()),
                'post-id':$('#page-media-post-id').val()
            };
            saveButton.attr("disabled", "true");
            AdminUtil.doPost(params, 'html', function (response) {
                saveButton.removeAttr("disabled");
                if (mediaType == 'slider') {
                    var liElement = $(response);
                    addSlideToSlider(liElement);
                    addThumb(liElement);
                    videoId.val('');
                    embedCode.val('');
                    $("#page-media-add-embedded-video-dialog").dialog("close");
                } else {
                    tb_remove();
                    updatePageMediaSettingsMeta(response);
                }
            }, function (errorThrown) {
                saveButton.removeAttr("disabled");
                alert(errorThrown);
            });
        }
        return false;
    }

    function saveSelfHostedVideo(mediaType) {
        if ($('#page-media-add-sh-video-form').valid()) {
            var saveButton = $('#page-media-add-sh-video-form-submit');
            var poster = $('#page-media-add-sh-video-poster');
            var m4v = $('#page-media-add-sh-video-m4v');
            var webm = $('#page-media-add-sh-video-webm');
            var ogg = $('#page-media-add-sh-video-ogg');
            var subtitle = $('#page-media-add-sh-video-subtitle');
            var chapter = $('#page-media-add-sh-video-chapter');
            if (m4v.val().length == 0 && webm.val().length == 0 && ogg.val().length == 0) {
                alert('Please specify at least one video source.');
            } else {
                var params = {
                    'action':'finesse-page-media-add-sh-video',
                    'media-type':mediaType,
                    'poster':poster.val(),
                    'm4v':m4v.val(),
                    'webm':webm.val(),
                    'ogg':ogg.val(),
                    'subtitle':subtitle.val(),
                    'chapter':chapter.val(),
                    'post-id':$('#page-media-post-id').val()
                };
                saveButton.attr("disabled", "true");
                AdminUtil.doPost(params, 'html', function (response) {
                    saveButton.removeAttr("disabled");
                    if (mediaType == 'slider') {
                        var liElement = $(response);
                        addSlideToSlider(liElement);
                        addThumb(liElement);
                        poster.val('');
                        m4v.val('');
                        webm.val('');
                        ogg.val('');
                        $("#page-media-add-sh-video-dialog").dialog("close");
                    } else {
                        tb_remove();
                        updatePageMediaSettingsMeta(response);
                    }
                }, function (errorThrown) {
                    saveButton.removeAttr("disabled");
                    alert(errorThrown);
                });
            }
        }
        return false;
    }

    function saveAudio() {
        if ($('#page-media-add-audio-form').valid()) {
            var saveButton = $('#page-media-add-audio-form-submit');
            var mp3 = $('#page-media-add-audio-mp3');
            var wav = $('#page-media-add-audio-wav');
            var ogg = $('#page-media-add-audio-ogg');
            if (mp3.val().length == 0 && wav.val().length == 0 && ogg.val().length == 0) {
                alert('Please specify at least one audio source.');
            } else {
                var params = {
                    'action':'finesse-page-media-add-audio',
                    'mp3':mp3.val(),
                    'wav':wav.val(),
                    'ogg':ogg.val(),
                    'post-id':$('#page-media-post-id').val()
                };
                saveButton.attr("disabled", "true");
                AdminUtil.doPost(params, 'html', function (response) {
                    saveButton.removeAttr("disabled");
                    tb_remove();
                    updatePageMediaSettingsMeta(response);
                }, function (errorThrown) {
                    saveButton.removeAttr("disabled");
                    alert(errorThrown);
                });
            }
        }
        return false;
    }

    function saveLink() {
        if ($('#page-media-add-link-form').valid()) {
            var saveButton = $('#page-media-add-link-form-submit');
            var link = $('#page-media-add-link-url');
            var params = {
                'action':'finesse-page-media-add-link',
                'link':link.val(),
                'post-id':$('#page-media-post-id').val()
            };
            saveButton.attr("disabled", "true");
            AdminUtil.doPost(params, 'html', function (response) {
                saveButton.removeAttr("disabled");
                tb_remove();
                updatePageMediaSettingsMeta(response);
            }, function (errorThrown) {
                saveButton.removeAttr("disabled");
                alert(errorThrown);
            });
        }
        return false;
    }

    function removePageMedia(mediaType) {
        var params = {
            'action':'finesse-page-media-remove',
            'media-type':mediaType,
            'post-id':$('#page-media-post-id').val()
        };
        AdminUtil.doPost(params, 'html', function (response) {
            tb_remove();
            updatePageMediaSettingsMeta(response);
        }, function (errorThrown) {
            alert(errorThrown);
        });
        return false;
    }

    function getSliderType() {
        return $('#slider-manager-slider-type').val();
    }

    function updatePageMediaSettingsMeta(value) {
        var name = '';
        $("input[type='text']").each(function () {
            var $this = $(this);
            if ($this.val() == 'finesse_page_media_settings') {
                name = $this.attr('name').replace('[key]', '[value]');
            }
        });
        if (name.length > 0) {
            $("textarea").each(function () {
                var $this = $(this);
                if ($this.attr('name') == name) {
                    $this.val(value);
                }
            });
        }
    }
});
