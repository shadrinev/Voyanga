/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 29.08.12
 * Time: 16:33
 * To change this template use File | Settings | File Templates.
 */
(function ($) {
    $.selectSlider = {
        init: function (obj) {
            var _this = $(obj);
            var mainClass = _this.attr('class');
            var aOptions = Array();
            var replacement = $(
                '<div class="' + mainClass + '">' +
                    '<div class="' + mainClass + ' selectList" />' +
                    '<span class="' + mainClass + ' currentItem" />' +
                    '<span class="' + mainClass + ' moreButton" />' +
                    '</div>'
            );
            replacement.data('select',_this);
            _this.data('selectDiv',replacement);

            //List elements events
            //console.log(_this);
            //console.log('multi_init');
            var multiSelectCloseButton = '<div style="text-align: center; padding-top: 6px;"><input type="button" value="Готово" onclick="Controls.selectboxFilter.hideList( $(\'.\' + Controls.selectboxUI.airlinesCommonClass + \'.selectList\') );" style="background-color: #f8ad44; border-color: #ffffff; font-size: 12px; line-height: 22px; padding: 0 12px 1px; width: 96%; margin: 0 0 3px 0;" class="formButton"></div>';
            _this.find('option').each(function () {
                var selectId = $(this).parent().attr('id');
                //console.log(selectId);
                var bDisabled = $(this).attr('disabled') == 'disabled';
                //console.log(bDisabled);
                var sSelectedClass = $(this).attr('selected') == 'selected' ? ' checkboxSelected':'';

                if(($(this).attr('label') != undefined) && ($(this).attr('label') != '')){
                    $(this).parent().data('all_option',$(this)[0]);
                    var listElement = $('<div class="allOption" style="cursor: pointer; width: 100%; padding: 2px 0;" onclick="Controls.multiSelectClickAll(\''+selectId+'\');"><div style="float: left;" class="' + mainClass + ' listOption' + (bDisabled?' disabledOptionFilter':'') + ' value-' + $(this).val() + '">' + $(this).text() + '</div><div style="float:right; margin-right: 6px;" class="multiSelectCheckBox"><div style="display: none;" class="selectCheckboxMark'+sSelectedClass+'"></div></div><div style="clear:both;"></div></div>');
                } else {
                    var listElement = $('<div class="multiselectElement" style="cursor: pointer; width: 100%; padding: 2px 0;" onclick="Controls.multiSelectChangeOption(this,\''+selectId+'\','+$(this)[0].index+');"><div style="float: left;" class="' + mainClass + ' listOption' + (bDisabled?' disabledOptionFilter':'') + ' value-' + $(this).val() + '">' + $(this).text() + '</div><div style="float:right; margin-right: 6px;" class="multiSelectCheckBox"><div style="display: none;" class="selectCheckboxMark'+sSelectedClass+'"></div></div><div style="clear:both;"></div></div>');
                }

                $(this).data('refererDiv',listElement);
                //console.log($(this).data());
                listElement.data('original_option',$(this));
                listElement.click(function () {
                    var thisListElement = $(this);
                    var bDisabled = thisListElement.data('original_option').attr('disabled') == 'disabled';
                    var thisReplacment = thisListElement.parents('.' + mainClass);
                    var thisIndex = thisListElement[0].className.split(' ');
                    if (bDisabled) {
                        //thisListElement.data('original_option').click();
                    } else {
                        var thisSublist = thisReplacment.find('.' + mainClass + '.selectList');
                        if(thisSublist.filter(":visible").length > 0) {
                            //hideList( thisSublist );
                        }else{
                            Controls.selectboxFilter.showList( thisSublist );
                        }
                        /*_this.val(listElement.data('original_option').val());
                         replacement.find('.' + mainClass + '.currentItem').html(listElement.text());
                         replacement.find('.' + mainClass + '.selectList').fadeOut();
                         _this.change();*/
                    }
                });
                listElement.bind('mouseenter',function () {
                    $(this).addClass('hover');
                });
                listElement.bind('mouseleave',function () {
                    $(this).removeClass('hover');
                });
                replacement.find('.' + mainClass + '.selectList').append(listElement);

                if(listElement.data('original_option').filter(':selected').length > 0) {
                    $('.'+ mainClass + '.currentItem', replacement).text($(this).text());
                }
                Controls.multipleSelectRefresh(_this);
            });
            //console.log(replacement.find('.listOption:first'));
            ///if no selected elements then we choose first option


            ///blur event
            Utils.elementBlur.init(replacement.find('.' + mainClass + '.selectList'),function (elementObj) {Controls.selectboxFilter.hideList(elementObj);/*.fadeOut();*/});
            //TODO: Нужно отчиститть наблюдение за удаленными элементами, и ставить в очередь новые для наблюдения.
            ///current item click
            replacement.find('.' + mainClass + '.currentItem').click(function () {

                var status = replacement.find('.' + mainClass + '.selectList').css('display') == 'block';

                window.setTimeout(function() {
                    var selectValue = replacement.data('select').val();
                    if(selectValue == null){
                        selectValue = '';
                    }else{
                        selectValue = selectValue.toString();
                    }
                    replacement.find('.' + mainClass + '.selectList').data('selectValue',selectValue);
                    if (!status) {
                        replacement.find('.' + mainClass + '.selectList').fadeIn();
                    } else {
                        replacement.find('.' + mainClass + '.selectList').fadeOut();
                    }
                },10);
            });

            //moreButton click
            replacement.find('.' + mainClass + '.moreButton').click(function () {

                var status = replacement.find('.' + mainClass + '.selectList').css('display') == 'block';
                window.setTimeout(function() {
                    if (!status) {
                        replacement.find('.' + mainClass + '.selectList').fadeIn();
                    } else {
                        replacement.find('.' + mainClass + '.selectList').fadeOut();
                    }
                },10);

                window.setTimeout(function() {var selectValue = replacement.data('select').val();
                    if(selectValue == null){
                        selectValue = '';
                    }else{
                        selectValue = selectValue.toString();
                    }
                    replacement.find('.' + mainClass + '.selectList').data('selectValue',selectValue);
                    if (!status) {
                        replacement.find('.' + mainClass + '.selectList').fadeIn();
                    } else {
                        replacement.find('.' + mainClass + '.selectList').fadeOut();
                    }
                },10);
            });

            //Select appearance fixes
            if (_this.css('width') == '160px') {
                replacement.find('.' + mainClass + '.currentItem').css('width', (parseInt(_this[0].clientWidth) - 20) + 'px');
                replacement.find('.' + mainClass + '.selectList').css('width', (parseInt(_this[0].clientWidth)) + 'px');
            } else {
                replacement.find('.' + mainClass + '.currentItem').css('width', (parseInt(_this.css('width')) - 26) + 'px');
                replacement.find('.' + mainClass + '.selectList').css('width', (parseInt(_this.css('width'))) + 'px');
                if ($.browser.webkit || $.browser.safari) {
                    if(parseInt(replacement.find('.' + mainClass + '.currentItem').css('width')) < 130) {
                        replacement.find('.' + mainClass + '.currentItem').css('width', (parseInt(_this.css('width')) + 6) + 'px');
                        replacement.find('.' + mainClass + '.selectList').css('width', (parseInt(_this.css('width'))) + 26 + 'px');
                    }
                }
            }
            //Rewrite DATA
            var selectData = _this.data();
            //Display select
            _this.hide();
            //console.log(_this.find( 'option:eq(1)').data());
            //_this.replaceWith(replacement);
            //_this.appendTo(replacement);
            replacement.insertBefore(_this);
            replacement.data('select',_this);
            //console.log(replacement.data());
            //console.log(_this.find( 'option:eq(1)').data());
            //_this.data(selectData);
            //console.log(_this);
            Controls.multipleSelectRefresh(_this);
            $(multiSelectCloseButton).appendTo(replacement.find('.' + mainClass + '.selectList'));

        },
        reSize: function (obj) {
            $(this).show();
            var thisWidth = $(this)[0].clientWidth;
            var thisHeight = $(this)[0].clientHeight;
        }
    }
    $.fn.selectSlider = function (options) {
        $(this).each(function () {
            $.selectSlider.init(this);
        });
    }
})(jQuery);