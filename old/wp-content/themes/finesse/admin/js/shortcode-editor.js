var ShortCodeUtil = {

    addToEditor:function (sc) {
        var ed;
        if (typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden()) {
            ed.focus();
            if (tinymce.isIE) {
                ed.selection.moveToBookmark(tinymce.EditorManager.activeEditor.windowManager.bookmark);
            }
            ed.execCommand('mceInsertContent', false, sc);
        } else if (typeof edInsertContent == 'function') {
            edInsertContent(edCanvas, sc);
        } else {
            jQuery(edCanvas).val(jQuery(edCanvas).val() + sc);
        }
        tb_remove();
    },

    replaceParagraphsWithShortCode:function (value) {

        while (value.indexOf('<p>') >= 0) {
            value = value.replace('<p>', '[p]');
        }
        while (value.indexOf('<P>') >= 0) {
            value = value.replace('<P>', '[p]');
        }

        while (value.indexOf('</p>') >= 0) {
            value = value.replace('</p>', '[/p]');
        }
        while (value.indexOf('</P>') >= 0) {
            value = value.replace('</P>', '[/p]');
        }

        return value;
    },

    getShortCode:function (formId) {
        var scName = jQuery('#' + formId).data('sc');
        var content = '';
        var attributes = '';
        jQuery('#' + formId + " :input").each(function () {
            var field = jQuery(this);
            var isHiddenField = field.attr('type') == 'hidden';
            if (isHiddenField || field.is(":visible")) {
                var attrType = field.data('attr-type');
                if (attrType) {
                    if (attrType == 'content') {
                        content = field.val().replace('\n', '');
                    } else {
                        var attrName = field.data('attr-name');
                        var val = field.val();
                        if (field.is(':checkbox')) {
                            val = field.is(':checked') ? 'true' : 'false';
                        }
                        if (val.length > 0) {
                            attributes += ' ' + attrName + '="' + val + '"';
                        }
                    }
                }
            }
        });
        return '[' + scName + attributes + ']' + content + '[/' + scName + ']';
    },

    resetForm:function (formId) {
        jQuery('#' + formId + " :input").each(function () {
            var field = jQuery(this);
            var tagName = field.prop("nodeName").toLowerCase();
            if (tagName == 'select') {
                field.prop('selectedIndex', 0);
            } else {
                if (field.is(':checkbox')) {
                    field.attr("checked", field.prop("defaultChecked"));
                } else {
                    var defaultValue = field.prop("defaultValue");
                    if (defaultValue) {
                        field.val(defaultValue);
                    } else {
                        field.val('');
                    }
                }
            }
        });
    },

    replaceAll:function(search, to, value){
        while (value.indexOf(search) >= 0) {
            value = value.replace(search, to);
        }
        return value;
    }

};

(function ($) {

    $.fn.mediaLibraryImageSelector = function () {
        var selectUploadedImgSrc = $(this);
        var baseId = selectUploadedImgSrc.data('base-id');
        var radioUploadedTypeSourceId = baseId + 'uploaded-type-source';
        var selectUploadedImgSrcId = baseId + 'uploaded-img-src';
        var checkUploadedSizeSrcId = baseId + 'uploaded-size-src';
        var radioNewTypeSourceId = baseId + 'new-type-source';
        var textNewImgSrcId = baseId + 'new-img-src';
        var textNewThumbSrcId = baseId + 'new-thumb-src';
        var imgUploadedImagePreviewId = baseId + 'preview';

        var radioUploadedTypeSource = $('#' + radioUploadedTypeSourceId);
        var selectUploadedSizeSrc = $('#' + checkUploadedSizeSrcId);
        var radioNewTypeSource = $('#' + radioNewTypeSourceId);
        var textNewImgSrc = $('#' + textNewImgSrcId);
        var textNewThumbSrc = $('#' + textNewThumbSrcId);
        var imgUploadedImagePreview = $('#' + imgUploadedImagePreviewId);

        previewSelectedImage();

        radioUploadedTypeSource.on('click', function () {
            toggleElements();
            previewSelectedImage();
        });
        radioNewTypeSource.on('click', function () {
            toggleElements();
            clearImagePreview();
        });
        selectUploadedImgSrc.on('change', function () {
            previewSelectedImage();
        });

        function toggleElements() {
            selectUploadedImgSrc.parent().toggle();
            if (selectUploadedSizeSrc) {
                selectUploadedSizeSrc.parent().toggle();
            }
            textNewImgSrc.parent().toggle();
            textNewThumbSrc.parent().toggle();
        }

        function clearImagePreview() {
            imgUploadedImagePreview.attr('src', '');
        }

        function previewSelectedImage() {
            $('#' + selectUploadedImgSrcId + ' option:selected').each(function () {
                var imgSrc = $(this).data('src');
                if (imgSrc) {
                    imgUploadedImagePreview.attr('src', imgSrc);
                } else {
                    imgUploadedImagePreview.attr('src', '');
                }
            });
        }

    };

})(jQuery);

jQuery(document).ready(function ($) {

    $(document).on('click', '#shortcode-editor-button-id', function () {
        var width = 900;
        var height = 500;
        var tbWindowWidth = width + 30;
        var tbWindowHeight = height + 30;
        var t = this.title;
        var a = this.href + '&width=' + width + '&height=' + height + '';
        var g = this.rel || false;
        var old_tb_init = window.tb_init;
        window.tb_init = function (domChunk) {
            old_tb_init(domChunk);
            $("#TB_window").css({marginLeft:'-' + parseInt((tbWindowWidth / 2), 10) + 'px', width:tbWindowWidth + 'px', height: tbWindowHeight + 'px'});
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
        $(".tabs").tabs();
        $(".accordion").accordion({
            header:'.accordion-title',
            active:false,
            animate:false,
            collapsible:true,
            autoHeight:false,
            heightStyle: 'content'
        });
        $(".color-thumbs").each(function () {
            $(this).colorRadioButtons();
        });
        $(".image-selector").each(function () {
            $(this).mediaLibraryImageSelector();
        });
        $(".sortable-slides").each(function () {
            $(this).sortable();
        });
        initDialogs();
        jQuery("#TB_window").on('tb_unload', function () {
            destroyDialogs();
        });
    }

    function destroyDialogs(){
        $('#sc-imgg-slide-dialog').dialog('destroy');
        $('#sc-video-source-dialog').dialog('destroy');
        $('#sc-video-track-dialog').dialog('destroy');
        $('#sc-audio-source-dialog').dialog('destroy');
        $('#sc-form-input-dialog').dialog('destroy');
        $('#sc-add-ib-carousel-item-dialog').dialog('destroy');
        $('#sc-add-member-dialog').dialog('destroy');
        $('#sc-add-client-dialog').dialog('destroy');
    }

    function initDialogs() {

        var scImggSlideDialogSubmitListener;
        var scImggSlideDialogCancelListener;
        $("#sc-imgg-slide-dialog").dialog({
            autoOpen:false,
            width:660,
            modal:true,
            resizable:false,
            dialogClass: 'wp-dialog',
            open: function () {
                scImggSlideDialogSubmitListener = function () {
                    if ($('#sc-imgg-slide-form').valid()) {
                        var imgSrc = '';
                        var imgName = '';
                        var thumb = '';
                        var newImgSrcEl = $('#sc-imgg-slide-src-new-img-src');
                        var newThumbSrcEl = $('#sc-imgg-slide-src-new-thumb-src');
                        var titleEl = $('#sc-imgg-slide-title');
                        var title = titleEl.val();

                        if ($('#sc-imgg-slide-src-uploaded-type-source').is(':checked')) {
                            $('#sc-imgg-slide-src-uploaded-img-src').find('option:selected').each(function () {
                                imgSrc = $(this).data('src');
                                imgName = $(this).val();
                            });
                        } else {
                            imgSrc = imgName = newImgSrcEl.val();
                            thumb = newThumbSrcEl.val();
                        }

                        imgName = ' data-img="' + imgName + '"';
                        thumb = thumb.length > 0 ? ' data-thumb="' + thumb + '"' : '';
                        title = title.length > 0 ? ' data-title="' + title + '"' : '';

                        var content = '<li' + imgName + thumb + title + '>';
                        content += '<div><img alt="" src="' + imgSrc + '"></div>';
                        content += '<a href="#" class="delete-gallery-item">Delete</a>';
                        content += '</li>';

                        $('#sc-imgg-slides').append(content);
                        $("#sc-imgg-slide-dialog").dialog("close");
                    }
                    return false;
                };
                scImggSlideDialogCancelListener = function () {
                    $("#sc-imgg-slide-dialog").dialog("close");
                    return false;
                };

                $(document).on('click', '#sc-imgg-slide-form-submit', scImggSlideDialogSubmitListener);
                $(document).on('click', '#sc-imgg-slide-form-cancel', scImggSlideDialogCancelListener);
            },
            close: function(){
                var newImgSrcEl = $('#sc-imgg-slide-src-new-img-src');
                var newThumbSrcEl = $('#sc-imgg-slide-src-new-thumb-src');
                var titleEl = $('#sc-imgg-slide-title');
                var altEl = $('#sc-imgg-slide-alt');
                titleEl.val('');
                altEl.val('');
                newImgSrcEl.val('');
                newThumbSrcEl.val('');
                $(document).off('click', '#sc-imgg-slide-form-submit', scImggSlideDialogSubmitListener);
                $(document).off('click', '#sc-imgg-slide-form-cancel', scImggSlideDialogCancelListener);
            }
        });

        var scVideoSourceDialogSubmitListener;
        var scVideoSourceDialogCancelListener;
        $("#sc-video-source-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            dialogClass: 'wp-dialog',
            open: function () {
                scVideoSourceDialogSubmitListener = function () {
                    if ($('#sc-video-source-form').valid()) {
                        var formatEl = $('#sc-video-source-format');
                        var srcEl = $('#sc-video-source-src');
                        var mainContentEl = $('#sc-video-content');
                        var format = formatEl.val();
                        var src = srcEl.val();
                        var globalContent = mainContentEl.val();

                        format = ' format="' + format + '"';
                        src = ' src="' + src + '"';
                        if (globalContent.indexOf(format) < 0) {
                            var sc = '[video_source' + format + src + '][/video_source]';
                            if ($.trim(globalContent).length > 0) {
                                globalContent = globalContent + '\n' + sc;
                            } else {
                                globalContent = sc;
                            }
                            srcEl.val('');
                            mainContentEl.val(globalContent);
                            $("#sc-video-source-dialog").dialog("close");
                        } else {
                            alert('A video source with this format already exists.');
                        }
                    }
                    return false;
                };
                scVideoSourceDialogCancelListener = function () {
                    $("#sc-video-source-dialog").dialog("close");
                    return false;
                };

                $(document).on('click', '#sc-video-source-form-submit', scVideoSourceDialogSubmitListener);
                $(document).on('click', '#sc-video-source-form-cancel', scVideoSourceDialogCancelListener);
            },
            close: function(){
                ShortCodeUtil.resetForm('sc-video-source-form');
                $(document).off('click', '#sc-video-source-form-submit', scVideoSourceDialogSubmitListener);
                $(document).off('click', '#sc-video-source-form-cancel', scVideoSourceDialogCancelListener);
            }
        });

        var scVideoTrackDialogSubmitListener;
        var scVideoTrackDialogCancelListener;
        $("#sc-video-track-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            dialogClass: 'wp-dialog',
            open: function () {
                scVideoTrackDialogSubmitListener = function () {
                    if ($('#sc-video-track-form').valid()) {
                        var typeEl = $('#sc-video-track-type');
                        var srcEl = $('#sc-video-track-src');
                        var langEl = $('#sc-video-track-lang');
                        var mainContentEl = $('#sc-video-content');
                        var type = typeEl.val();
                        var src = srcEl.val();
                        var lang = langEl.val();
                        var globalContent = mainContentEl.val();

                        type = ' type="' + type + '"';
                        src = ' src="' + src + '"';
                        lang = ' lang="' + lang + '"';
                        var sc = '[video_track' + type + src + lang + '][/video_track]';
                        if ($.trim(globalContent).length > 0) {
                            globalContent = globalContent + '\n' + sc;
                        } else {
                            globalContent = sc;
                        }
                        srcEl.val('');
                        langEl.val('en');
                        mainContentEl.val(globalContent);
                        $("#sc-video-track-dialog").dialog("close");
                    }
                    return false;
                };
                scVideoTrackDialogCancelListener = function () {
                    $("#sc-video-track-dialog").dialog("close");
                    return false;
                };
                $(document).on('click', '#sc-video-track-form-submit', scVideoTrackDialogSubmitListener);
                $(document).on('click', '#sc-video-track-form-cancel', scVideoTrackDialogCancelListener);
            },
            close: function(){
                ShortCodeUtil.resetForm('sc-video-track-form');
                $(document).off('click', '#sc-video-track-form-submit', scVideoTrackDialogSubmitListener);
                $(document).off('click', '#sc-video-track-form-cancel', scVideoTrackDialogCancelListener);
            }
        });

        var scAudioSourceDialogSubmitListener;
        var scAudioSourceDialogCancelListener;
        $("#sc-audio-source-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            dialogClass: 'wp-dialog',
            open: function () {
                scAudioSourceDialogSubmitListener = function () {
                    if ($('#sc-audio-source-form').valid()) {
                        var formatEl = $('#sc-audio-source-format');
                        var srcEl = $('#sc-audio-source-src');
                        var mainContentEl = $('#sc-audio-content');
                        var format = formatEl.val();
                        var src = srcEl.val();
                        var globalContent = mainContentEl.val();

                        format = ' format="' + format + '"';
                        src = ' src="' + src + '"';
                        if (globalContent.indexOf(format) < 0) {
                            var sc = '[audio_source' + format + src + '][/audio_source]';
                            if ($.trim(globalContent).length > 0) {
                                globalContent = globalContent + '\n' + sc;
                            } else {
                                globalContent = sc;
                            }
                            srcEl.val('');
                            mainContentEl.val(globalContent);
                            $("#sc-audio-source-dialog").dialog("close");
                        } else {
                            alert('An audio source with this format already exists.');
                        }
                    }
                    return false;
                };
                scAudioSourceDialogCancelListener = function () {
                    $("#sc-audio-source-dialog").dialog("close");
                    return false;
                };
                $(document).on('click', '#sc-audio-source-form-submit', scAudioSourceDialogSubmitListener);
                $(document).on('click', '#sc-audio-source-form-cancel', scAudioSourceDialogCancelListener);
            },
            close: function(){
                ShortCodeUtil.resetForm('sc-audio-source-form');
                $(document).off('click', '#sc-audio-source-form-submit', scAudioSourceDialogSubmitListener);
                $(document).off('click', '#sc-audio-source-form-cancel', scAudioSourceDialogCancelListener);
            }
        });

        var scFormInputDialogSubmitListener;
        var scFormInputDialogCancelListener;
        $("#sc-form-input-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            dialogClass: 'wp-dialog',
            open: function () {
                scFormInputDialogSubmitListener = function () {
                    if ($('#sc-form-input-form').valid()) {
                        var globalContentEl = $('#sc-form-content');
                        var globalContent = globalContentEl.val();

                        var sc = ShortCodeUtil.getShortCode('sc-form-input-form');
                        if ($.trim(globalContent).length > 0) {
                            globalContent = globalContent + '\n' + sc;
                        } else {
                            globalContent = sc;
                        }
                        globalContentEl.val(globalContent);
                        $("#sc-form-input-dialog").dialog("close");
                    }
                    return false;
                };
                scFormInputDialogCancelListener = function () {
                    $("#sc-form-input-dialog").dialog("close");
                    return false;
                };
                $(document).on('click', '#sc-form-input-form-submit', scFormInputDialogSubmitListener);
                $(document).on('click', '#sc-form-input-form-cancel', scFormInputDialogCancelListener);
            },
            close: function(){
                ShortCodeUtil.resetForm('sc-form-input-form');
                $(document).off('click', '#sc-form-input-form-submit', scFormInputDialogSubmitListener);
                $(document).off('click', '#sc-form-input-form-cancel', scFormInputDialogCancelListener);
            }
        });

        var scAddIbCarouselItemDialogSubmitListener;
        var scAddIbCarouselItemDialogCancelListener;
        $("#sc-add-ib-carousel-item-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            dialogClass: 'wp-dialog',
            open: function () {
                scAddIbCarouselItemDialogSubmitListener = function () {
                    if ($('#sc-add-ib-carousel-item-form').valid()) {
                        var globalContentEl = $('#sc-ib-carousel-content');
                        var globalContent = globalContentEl.val();

                        var sc = ShortCodeUtil.getShortCode('sc-add-ib-carousel-item-form');
                        if ($.trim(globalContent).length > 0) {
                            globalContent = globalContent + '\n' + sc;
                        } else {
                            globalContent = sc;
                        }
                        globalContentEl.val(globalContent);
                        $("#sc-add-ib-carousel-item-dialog").dialog("close");
                    }
                    return false;
                };
                scAddIbCarouselItemDialogCancelListener = function () {
                    $("#sc-add-ib-carousel-item-dialog").dialog("close");
                    return false;
                };
                $(document).on('click', '#sc-add-ib-carousel-item-form-submit', scAddIbCarouselItemDialogSubmitListener);
                $(document).on('click', '#sc-add-ib-carousel-item-form-cancel', scAddIbCarouselItemDialogCancelListener);
            },
            close: function(){
                ShortCodeUtil.resetForm('sc-add-ib-carousel-item-form');
                $(document).off('click', '#sc-add-ib-carousel-item-form-submit', scAddIbCarouselItemDialogSubmitListener);
                $(document).off('click', '#sc-add-ib-carousel-item-form-cancel', scAddIbCarouselItemDialogCancelListener);
            }
        });

        var scAddMemberDialogSubmitListener;
        var scAddMemberDialogCancelListener;
        $("#sc-add-member-dialog").dialog({
            autoOpen:false,
            width:660,
            modal:true,
            resizable:false,
            dialogClass: 'wp-dialog',
            open: function () {
                scAddMemberDialogSubmitListener = function () {
                    if ($('#sc-add-member-form').valid()) {
                        var globalContentEl = $('#sc-team-content');
                        var globalContent = globalContentEl.val();

                        var sc = ShortCodeUtil.getShortCode('sc-add-member-form');
                        if ($.trim(globalContent).length > 0) {
                            globalContent = globalContent + '\n' + sc;
                        } else {
                            globalContent = sc;
                        }
                        globalContentEl.val(globalContent);
                        $("#sc-add-member-dialog").dialog("close");
                    }
                    return false;
                };
                scAddMemberDialogCancelListener = function () {
                    $("#sc-add-member-dialog").dialog("close");
                    return false;
                };
                $(document).on('click', '#sc-add-member-form-submit', scAddMemberDialogSubmitListener);
                $(document).on('click', '#sc-add-member-form-cancel', scAddMemberDialogCancelListener);
            },
            close: function(){
                ShortCodeUtil.resetForm('sc-add-member-form');
                $(document).off('click', '#sc-add-member-form-submit', scAddMemberDialogSubmitListener);
                $(document).off('click', '#sc-add-member-form-cancel', scAddMemberDialogCancelListener);
            }
        });

        var scAddClientDialogSubmitListener;
        var scAddClientDialogCancelListener;
        $("#sc-add-client-dialog").dialog({
            autoOpen:false,
            width:560,
            modal:true,
            resizable:false,
            dialogClass: 'wp-dialog',
            open: function () {
                scAddClientDialogSubmitListener = function () {
                    if ($('#sc-add-client-form').valid()) {
                        var globalContentEl = $('#sc-clients-content');
                        var globalContent = globalContentEl.val();

                        var sc = ShortCodeUtil.getShortCode('sc-add-client-form');
                        if ($.trim(globalContent).length > 0) {
                            globalContent = globalContent + '\n' + sc;
                        } else {
                            globalContent = sc;
                        }
                        globalContentEl.val(globalContent);
                        $("#sc-add-client-dialog").dialog("close");
                    }
                    return false;
                };
                scAddClientDialogCancelListener = function () {
                    $("#sc-add-client-dialog").dialog("close");
                    return false;
                };
                $(document).on('click', '#sc-add-client-form-submit', scAddClientDialogSubmitListener);
                $(document).on('click', '#sc-add-client-form-cancel', scAddClientDialogCancelListener);
            },
            close: function(){
                ShortCodeUtil.resetForm('sc-add-client-form');
                $(document).off('click', '#sc-add-client-form-submit', scAddClientDialogSubmitListener);
                $(document).off('click', '#sc-add-client-form-cancel', scAddClientDialogCancelListener);
            }
        });
    }

    //---------------------------------------------------- BUTTON ------------------------------------------------------

    $(document).on('click', '#sc-button-form-submit', function () {
        if ($('#sc-button-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-button-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //---------------------------------------------------- LIST --------------------------------------------------------
    $(document).on('click', '#sc-list-form-submit', function () {
        if ($('#sc-list-form').valid()) {
            var content = ShortCodeUtil.replaceAll('\n', '|', $('#sc-li-content').val());
            var indent = $('#sc-li-indent').is(':checked');
            var tagName;
            var type;

            if ($('#sc-li-ol-type').is(':checked')) {
                tagName = 'ol';
                type = $('#sc-li-ol-icon').val();
            } else {
                tagName = 'ul';
                type = $('#sc-li-ul-icon').val();
            }
            indent = indent ? 'true' : 'false';
            var sc = '[' + tagName + ' type="' + type + '" indent="' + indent + '" separator="|"]' + content + '[/' + tagName + ']';
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $(document).on('click', '.sc-li-type-selector', function () {
        $('#sc-li-ol-icon').toggle();
        $('#sc-li-ul-icon').toggle();
    });

    //--------------------------------------------------- INFO BOX -----------------------------------------------------

    $(document).on('click', '#sc-infobox-form-submit', function () {
        if ($('#sc-infobox-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-infobox-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //------------------------------------------------- ICON BOX -------------------------------------------------------

    $(document).on('click', '#sc-iconbox-form-submit', function () {
        if ($('#sc-iconbox-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-iconbox-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //--------------------------------------------------- IMAGE --------------------------------------------------------

    $(document).on('click', '#sc-img-form-submit', function () {
        if ($('#sc-img-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-img-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //----------------------------------------------- IMAGE GALLERY ----------------------------------------------------

    $(document).on('click', '#sc-imgg-form-submit', function () {
        if ($('#sc-imgg-form').valid()) {
            var slide = $('#sc-imgg-slidecap').is(':checked');
            var name = $('#sc-imgg-name').val();
            var caption = $('#sc-imgg-caption').val();
            var size = $('#sc-imgg-size').val();
            var lightbox = $('#sc-imgg-lightbox').is(':checked');
            var align = $('#sc-imgg-align').val();

            name = name.length > 0 ? ' name="' + name + '"' : '';
            size = ' size="' + size + '"';
            if (slide) {
                caption = '';
                align = '';
            } else {
                caption = caption.length > 0 ? ' caption="' + caption + '"' : '';
                align = align.length > 0 ? ' align="' + align + '"' : '';
            }
            slide = slide ? ' slide="true"' : ' slide="false"';
            lightbox = lightbox ? ' lightbox="true"' : ' lightbox="false"';

            var sc = '[gallery' + slide + name + caption + size + lightbox + align + ']';
            $('#sc-imgg-slides').find('li').each(function () {
                var $this = $(this);
                var imgSrc = $this.data('img');
                var thumbSrc = '';
                var title = '';

                if ($this.data('thumb')) {
                    thumbSrc = ' thumb_src="' + $this.data('thumb') + '"';
                }
                if ($this.data('title')) {
                    title = ' title="' + $this.data('title') + '"';
                }
                sc += '[gallery_item src="' + imgSrc + '"' + thumbSrc + title + ']';
            });
            sc += '[/gallery]';
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $(document).on('change', '#sc-imgg-slidecap', function () {
        var captionDivEl = $('#sc-imgg-caption').parent();
        var alignDivEl = $('#sc-imgg-align').parent();
        if ($(this).is(":checked")) {
            captionDivEl.hide();
            alignDivEl.hide();
        } else {
            captionDivEl.show();
            alignDivEl.show();
        }
    });

    $(document).on('click', '#sc-imgg-form-add', function () {
        $('#sc-imgg-slide-dialog').dialog('open');
        return false;
    });

    $(document).on('click', '.delete-gallery-item', function () {
        var slide = $(this).parent();
        slide.fadeOut(function () {
            $(this).remove();
        });
        return false;
    });

    //---------------------------------------------- EMBEDDED VIDEO ----------------------------------------------------

    $(document).on('click', '#sc-evideo-form-submit', function () {
        if ($('#sc-evideo-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-evideo-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //-------------------------------------------- SELF-HOSTED VIDEO ---------------------------------------------------

    $(document).on('click', '#sc-video-form-submit', function () {
        if ($('#sc-video-form').valid()) {
            var width = $('#sc-video-width').val();
            var height = $('#sc-video-height').val();
            var poster = $('#sc-video-poster').val();
            var isThumb = $('#sc-video-thumb').is(':checked');
            var content = $('#sc-video-content').val().replace('\n', '');
            if (content.indexOf('[video_source') >= 0) {
                width = ' width="' + width + '"';
                height = ' height="' + height + '"';
                poster = ' poster="' + poster + '"';
                var thumbnail = '';
                if (isThumb) {
                    thumbnail = ' is_thumb="true"';
                }
                var sc = '[video' + width + height + poster + thumbnail + ']' + content + '[/video]';
                ShortCodeUtil.addToEditor(sc);
            } else {
                alert('You should add at least one video source.');
            }
        }
        return false;
    });

    $(document).on('click', '#sc-video-form-add-src', function () {
        $('#sc-video-source-dialog').dialog('open');
        return false;
    });

    $(document).on('click', '#sc-video-form-add-track', function () {
        $('#sc-video-track-dialog').dialog('open');
        return false;
    });

    //-------------------------------------------------- AUDIO ---------------------------------------------------------

    $(document).on('click', '#sc-audio-form-submit', function () {
        if ($('#sc-audio-form').valid()) {
            var width = $('#sc-audio-width').val();
            var isThumb = $('#sc-audio-thumb').is(':checked');
            var content = $('#sc-audio-content').val().replace('\n', '');
            if (content.indexOf('[audio_source') >= 0) {
                width = ' width="' + width + '"';
                var thumbnail = '';
                if (isThumb) {
                    thumbnail = ' is_thumb="true"';
                }
                var sc = '[audio' + width + thumbnail + ']' + content + '[/audio]';
                ShortCodeUtil.addToEditor(sc);
            } else {
                alert('You should add at least one audio source.');
            }
        }
        return false;
    });

    $(document).on('click', '#sc-audio-form-add-src', function () {
        $('#sc-audio-source-dialog').dialog('open');
        return false;
    });

    //-------------------------------------------------- DROPCAPS ------------------------------------------------------

    $(document).on('click', '#sc-dropcap-form-submit', function () {
        if ($('#sc-dropcap-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-dropcap-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //------------------------------------------------- HIGHLIGHT ------------------------------------------------------

    $(document).on('click', '#sc-highlight-form-submit', function () {
        if ($('#sc-highlight-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-highlight-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //--------------------------------------------- PREFORMATTED TEXT --------------------------------------------------

    $(document).on('click', '#sc-pre-form-submit', function () {
        if ($('#sc-pre-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-pre-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //------------------------------------------------ BLOCK QUOTE -----------------------------------------------------

    $(document).on('click', '#sc-bq-form-submit', function () {
        if ($('#sc-bq-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-bq-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //----------------------------------------------- GRID COLUMNS -----------------------------------------------------

    $(document).on('click', '#sc-gc-form-submit', function () {
        if ($('#sc-gc-form').valid()) {
            var layout = $('#sc-gc-layout').val();
            var separator = $('#sc-gc-separator').val();
            var content = 'COLUMN_1'+' | COLUMN_2';
            if(layout == '3'){
                content = 'COLUMN_1'+' | COLUMN_2'+' | COLUMN_3';
            }else if(layout == '4'){
                content = 'COLUMN_1'+' | COLUMN_2'+' | COLUMN_3'+' | COLUMN_4';
            }
            var sc = '[gc layout="'+layout+'" separator="'+separator+'"]'+content+'[/gc]';
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //----------------------------------------------------- TABS -------------------------------------------------------

    $(document).on('change', '#sc-tabs-ex', function () {
        var selectedValue = $(this).val();
        if(selectedValue === ''){
            $('#sc-tabs-content').val('');
        }else{
            var example = $(this).data(selectedValue);
            example = ShortCodeUtil.replaceAll('&#91;', '[', example);
            example = ShortCodeUtil.replaceAll('&#93;', ']', example);
            $('#sc-tabs-content').val(example);
        }
        return false;
    });

    $(document).on('click', '#sc-tabs-form-submit', function () {
        if ($('#sc-tabs-form').valid()) {
            var sc = $('#sc-tabs-content').val();
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //--------------------------------------------------- TOGGLES ------------------------------------------------------

    $(document).on('change', '#sc-toggles-type', function () {
        var selectedValue = $(this).val();
        if(selectedValue === ''){
            $('#sc-toggles-content').val('');
        }else{
            var example = $(this).data(selectedValue);
            example = ShortCodeUtil.replaceAll('&#91;', '[', example);
            example = ShortCodeUtil.replaceAll('&#93;', ']', example);
            $('#sc-toggles-content').val(example);
        }
        return false;
    });

    $(document).on('click', '#sc-toggle-form-submit', function () {
        if ($('#sc-toggle-form').valid()) {
            var sc = $('#sc-toggles-content').val();
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //--------------------------------------------- NOTIFICATION BOX ---------------------------------------------------

    $(document).on('click', '#sc-notifbox-form-submit', function () {
        if ($('#sc-notifbox-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-notifbox-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //---------------------------------------------- POST CAROUSEL -----------------------------------------------------

    $(document).on('click', '#sc-post-carousel-form-submit', function () {
        if ($('#sc-post-carousel-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-post-carousel-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //-------------------------------------------- ICON-BOX CAROUSEL ---------------------------------------------------

    $(document).on('change', '#sc-ib-carousel-genmode', function () {
        var postidDiv = $('#sc-ib-carousel-postid').parent();
        var contentDiv = $('#sc-ib-carousel-content').parent();
        var addBtn = $('#sc-ib-carousel-form-add');
        if ($(this).val() == 'a') {
            postidDiv.show();
            contentDiv.hide();
            addBtn.hide();
        } else {
            postidDiv.hide();
            contentDiv.show();
            addBtn.show();
        }
        return false;
    });

    $(document).on('click', '#sc-ib-carousel-form-submit', function () {
        if ($('#sc-ib-carousel-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-ib-carousel-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $(document).on('click', '#sc-ib-carousel-form-add', function () {
        $('#sc-add-ib-carousel-item-dialog').dialog('open');
        return false;
    });

    //------------------------------------------ TESTIMONIALS CAROUSEL -------------------------------------------------

    $(document).on('click', '#sc-testimonials-carousel-form-submit', function () {
        if ($('#sc-testimonials-carousel-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-testimonials-carousel-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //------------------------------------------------- SITE MAP -------------------------------------------------------

    $(document).on('click', '#sc-sm-form-submit', function () {
        if ($('#sc-sm-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-sm-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //--------------------------------------------------- FORM ---------------------------------------------------------

    $(document).on('click', '#sc-form-form-submit', function () {
        if ($('#sc-form-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-form-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $(document).on('click', '#sc-form-form-add', function () {
        $('#sc-form-input-dialog').dialog('open');
        return false;
    });

    //------------------------------------------ NEWSLETTER SUBSCRIPTION -----------------------------------------------

    $(document).on('click', '#sc-nls-form-submit', function () {
        if ($('#sc-nls-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-nls-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //------------------------------------------------ TEAM MEMBERS ----------------------------------------------------

    $(document).on('click', '#sc-team-form-submit', function () {
        if ($('#sc-team-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-team-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $(document).on('click', '#sc-team-form-add', function () {
        $('#sc-add-member-dialog').dialog('open');
        return false;
    });

    //-------------------------------------------------- CLIENTS -------------------------------------------------------

    $(document).on('click', '#sc-clients-form-submit', function () {
        if ($('#sc-clients-form').valid()) {
            var sc = ShortCodeUtil.getShortCode('sc-clients-form');
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    $(document).on('click', '#sc-clients-form-add', function () {
        $('#sc-add-client-dialog').dialog('open');
        return false;
    });

    //-------------------------------------------------- CONTACT -------------------------------------------------------

    $(document).on('click', '#sc-contact-form-submit', function () {
        if ($('#sc-contact-form').valid()) {
            var title = $.trim($('#sc-contact-title').val());
            var phone = $.trim($('#sc-contact-phone').val());
            var fax = $.trim($('#sc-contact-fax').val());
            var email = $.trim($('#sc-contact-email').val());
            var tts = $.trim($('#sc-contact-tts').val());
            var tt = $.trim($('#sc-contact-tt').val());
            var addr = $.trim($('#sc-contact-addr').val());

            if(title.length > 0){
                title = ' title="'+title+'"';
            }
            if(phone.length > 0){
                phone = ' phone="'+phone+'"';
            }
            if(fax.length > 0){
                fax = ' fax="'+fax+'"';
            }
            if(email.length > 0){
                email = ' email="'+email+'"';
            }
            if(tt.length > 0){
                while (tt.indexOf('\n') >= 0) {
                    tt = tt.replace('\n', tts);
                }
                tt = ' timetable="'+tt+'"';
            }
            if(tts.length > 0){
                tts = ' timetable_sep="'+tts+'"';
            }

            var sc = '[contact' + title + phone + fax + email + tts + tt + ']'+addr+'[/contact]';
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

    //-------------------------------------------------- TABLE ---------------------------------------------------------

    $(document).on('click', '#sc-table-form-submit', function () {
        if ($('#sc-table-form').valid()) {
            var caption = $('#sc-table-caption').val();
            var footer = $('#sc-table-footer').val();
            var separator = $('#sc-table-separator').val();
            var columns = $('#sc-table-columns').val();
            var rows = $('#sc-table-rows').val();

            if (columns > 0 && rows > 0) {
                caption = caption.length > 0 ? ' caption="' + caption + '"' : '';
                footer = footer.length > 0 ? ' footer="' + footer + '"' : '';
                var sc = '[table' + caption + footer + ']';
                sc += '\n[table_header]';
                for (var c = 0; c < columns; c++) {
                    if (c > 0) {
                        sc += separator;
                    }
                    sc += 'HEADER-' + c;
                }
                sc += '[/table_header]';
                for (var r = 0; r < rows; r++) {
                    sc += '\n[table_row]';
                    for (c = 0; c < columns; c++) {
                        if (c > 0) {
                            sc += separator;
                        }
                        sc += 'ROW_' + r + '_COLUMN_' + c;
                    }
                    sc += '[/table_row]';
                }
                sc += '\n[/table]';
                ShortCodeUtil.addToEditor(sc);
            } else {
                alert('The number of columns and the number of rows must be greater that zero.');
            }

        }
        return false;
    });

    //---------------------------------------------- PRICING BOXES -----------------------------------------------------

    $(document).on('click', '#sc-pb-form-submit', function () {
        if ($('#sc-pb-form').valid()) {
            var columns = $('#sc-pb-columns').val();
            var rows = $('#sc-pb-rows').val();
            var hc = $('#sc-pb-hc').val();
            var separator = $('#sc-pb-separator').val();

            if (rows > 0) {
                if (hc >= 1 && hc <= columns) {
                    var sc = '[pricing_boxes columns="' + columns + '" highlighted_column="' + hc + '" separator="' + separator + '"]';
                    for (var c = 0; c < columns; c++) {
                        sc += '\n[pricing_box_column title="TITLE_COL_' + c + '" price="PRICE_(e.g. $20)" unit="UNIT_MEASURE_(e.g. month)" order_text="ORDER_TEXT" order_url="ORDER_URL"]\n';
                        for (var r = 0; r < rows; r++) {
                            if (r > 0) {
                                sc += separator;
                            }
                            sc += 'COL_' + c + '_ROW_' + r;
                        }
                        sc += '\n[/pricing_box_column]';
                    }
                    sc += '\n[/pricing_boxes]';
                    ShortCodeUtil.addToEditor(sc);
                } else {
                    alert('The highlighted column must be between 1 and ' + columns + '.');
                }
            } else {
                alert('The number of rows must be greater that zero.');
            }

        }
        return false;
    });

    //---------------------------------------------- PRICING TABLE -----------------------------------------------------

    $(document).on('click', '#sc-pt-form-submit', function () {
        if ($('#sc-pt-form').valid()) {
            var columns = $('#sc-pt-columns').val();
            var rows = $('#sc-pt-rows').val();
            var hc = $('#sc-pt-hc').val();
            var separator = $('#sc-pt-separator').val();

            if (columns > 0 && rows > 0) {
                if (hc >= 1 && hc <= columns) {
                    var sc = '[pricing_table highlighted_column="' + hc + '" separator="' + separator + '"]\n';

                    for (var c = 0; c < columns; c++) {
                        sc += '[pricing_table_header price="PRICE_(e.g. $20)" unit="UNIT_MEASURE_(e.g. month)"]TITLE_COL_' + c + '[/pricing_table_header]\n';
                    }

                    for (c = 0; c < columns; c++) {
                        sc += '[pricing_table_footer order_text="ORDER_TEXT" order_url="ORDER_URL"][/pricing_table_footer]\n';
                    }

                    for (var r = 0; r < rows; r++) {
                        sc += '[pricing_table_row title="ROW_TITLE_(e.g. Payment Integration)"]\n';
                        for (c = 0; c < columns; c++) {
                            if (c > 0) {
                                sc += separator;
                            }
                            sc += 'ROW_' + r + '_COL_' + c;
                        }
                        sc += '\n[/pricing_table_row]\n';
                    }

                    sc += '[/pricing_table]';
                    ShortCodeUtil.addToEditor(sc);
                } else {
                    alert('The highlighted column must be between 1 and ' + columns + '.');
                }
            } else {
                alert('The number of columns and the number of rows must be greater that zero.');
            }

        }
        return false;
    });

    //------------------------------------------------ GOOGLE MAP ------------------------------------------------------

    $(document).on('change', '#sc-gmap-type', function () {
        if(this.value === 'address'){
            $('#sc-gmap-lat-wrapper').hide();
            $('#sc-gmap-lng-wrapper').hide();
            $('#sc-gmap-address-wrapper').show();
        }else{
            $('#sc-gmap-address-wrapper').hide();
            $('#sc-gmap-lat-wrapper').show();
            $('#sc-gmap-lng-wrapper').show();
        }
        return false;
    });

    $(document).on('click', '#sc-gmap-form-submit', function () {
        if ($('#sc-gmap-form').valid()) {
            var locType = $('#sc-gmap-type').val();
            var address = $.trim($('#sc-gmap-address').val());
            var lat = $.trim($('#sc-gmap-lat').val());
            var lng = $.trim($('#sc-gmap-lng').val());
            var zoom = $.trim($('#sc-gmap-zoom').val());
            var height = $.trim($('#sc-gmap-height').val());

            if(locType === 'address' && address.length === 0){
                alert('The address must not be empty!');
                return false;
            }

            if(locType !== 'address' && (lat.length === 0 || lng.length === 0)){
                alert('The latitude and longitude must not be empty!');
                return false;
            }

            var locValue = '';
            if(locType === 'address'){
                locValue = 'address="'+address+'"';
            }else{
                locValue = 'lat="'+lat+'" lng="'+lng+'"';
            }

            var sc = '[gmap loc_type="'+locType+'" '+locValue+' zoom="'+zoom+'" height="'+height+'"][/gmap]';
            ShortCodeUtil.addToEditor(sc);
        }
        return false;
    });

});