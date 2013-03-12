function resizePanel(arg) {

    $('.panelTable').each(function(index){
        var _panelTable = $(this);
        var _classThis;
        var _midWidth = 1130;
        var _minWidth = 1000;
        var _newMean;

        var _allWidthPanel;
        _panelTable.find('.tdCity').find('.data').css('width', 'auto');

        if (_panelTable.hasClass('avia')) {
            _classThis = 'avia';
            var _meanPanel = 850;
            var _standartData = 290;
            var _widthTdTumblr = _panelTable.find('.tdTumblr').innerWidth();
            var _widthTdPeople = _panelTable.find('.tdPeople').innerWidth();
            var _widthTdButton = _panelTable.find('.tdButton').innerWidth();
            var _widthTdCityStart = _panelTable.find('.tdCityStart').innerWidth();;
            var _widthTdAddTour = 0;
            var _howManyInput = 2;
        }  else if (_panelTable.hasClass('constructorTable')) {
            _classThis = 'constructorTable';
            if ($(this).find('.tdPeople').hasClass('notFinal')) {
                $(this).find('.tdPeople.notFinal').css('width', $('.tdPeople.final').width()+'px');
            }
            else if ($(this).find('.tdPeople').hasClass('final')) {
                //$(this).find('.tdPeople.final').css('width', 'auto');
            }
            var _meanPanel = 692;
            var _widthTdTumblr = 0;
            var _widthTdPeople = _panelTable.find('.tdPeople').innerWidth();
            var _widthTdButton = _panelTable.find('.tdButton').innerWidth();
            var _widthTdCityStart = _panelTable.find('.tdCityStart').innerWidth();
            var _widthTdAddTour = _panelTable.find('.tdAddTour').innerWidth();
            var _howManyInput = 1;
        }  else if (_panelTable.hasClass('hotel')) {
            _classThis = 'hotel';
            var _meanPanel = 692;
            var _widthTdTumblr = 0;
            var _widthTdPeople = _panelTable.find('.tdPeople').innerWidth();
            var _widthTdButton = _panelTable.find('.tdButton').innerWidth();
            var _widthTdCityStart = _panelTable.find('.tdCityStart').innerWidth();
            var _widthTdAddTour = 117;
            var _howManyInput = 1;
        }

        var _windowWidth = $(window).width();
        var _widthPanelTable = _panelTable.innerWidth();

        var _dataDiv = _panelTable.find('.tdCity').find('.data');
        var _dataInput = _panelTable.find('.tdCity').find('.data').find('input');

        if (_windowWidth <= _midWidth && _windowWidth >= _minWidth) {
            _allWidthPanel = _windowWidth - 230;
        }
        else if (_windowWidth < _minWidth) {
            _allWidthPanel = 1000 - 230;
        }
        else {
            _allWidthPanel = 900;
        }
        if (_widthPanelTable >= _allWidthPanel) {
            _newMean = (_allWidthPanel - _widthTdTumblr - _widthTdPeople - _widthTdButton - _widthTdCityStart - _widthTdAddTour) / _howManyInput;
        }
        else if (_widthPanelTable < _allWidthPanel) {
            _newMean = (_allWidthPanel - _widthTdTumblr - _widthTdPeople - _widthTdButton - _widthTdCityStart - _widthTdAddTour) / _howManyInput;
        }
        else {
            _newMean = (_widthPanelTable - _widthTdTumblr - _widthTdPeople - _widthTdButton - _widthTdCityStart - _widthTdAddTour) / _howManyInput;
        }

        _panelTable.find('.tdCity').find('.data').css('width', _newMean +'px');
        _panelTable.find('.tdCity').find('.data').find('input').css('width', (_newMean-20) +'px');
        _widthPanelTable = _panelTable.innerWidth();
    });
}

var _GoOnScroll = true;
var _jScrollingBootom = false;
var _jScrollNonBottomInitted = false;

function jsPaneScrollHeight() {
	
	var _issetMaps = $('#all-hotels-map').length > 0 && $('#all-hotels-map').is(':visible');
	var _issetLeftBlock = $('.left-block').length > 0 && $('.left-block').is(':visible');
	if (_issetMaps && ! _issetLeftBlock) {
	
	}
	else {
	
	var _content = $('#content');
	_content.css('height','auto');
	var _windowHeight = $(window).height();
	if (_windowHeight > 670) {
		_windowHeight = ($(window).height() - 132);
	}
	else {
		_windowHeight = (670 - 132);
	}
	var _contentHeight = _content.innerHeight();
	var _scrollPaneHeight = 0;
	$('.scrollBlock').find('.div-filter').each(function(e) {
		_scrollPaneHeight += $(this).innerHeight();
	});
	if (_scrollPaneHeight	> _contentHeight  &&
		_contentHeight		> _windowHeight && 
		_scrollPaneHeight 	> _windowHeight) {
		_content.css('height', _scrollPaneHeight + 'px');
		$('.filter-content').css('position','relative').css('top','auto').css('bottom','auto');
		$('.innerFilter').css('height', _scrollPaneHeight +'px');
		_GoOnScroll = false;
	}
	else if 
		(_scrollPaneHeight	< _contentHeight  && 
		_contentHeight		> _windowHeight && 
		_scrollPaneHeight 	> _windowHeight) {
		//console.log('=== 2 ===');
		_content.css('height', 'auto');
		$('.filter-content').css('position','relative').css('top','auto').css('bottom','auto');
		$('.innerFilter').css('height', _scrollPaneHeight +'px');
		_GoOnScroll = true;
	}
	else if 
		(_scrollPaneHeight	> _contentHeight  && 
		_contentHeight		< _windowHeight && 
		_scrollPaneHeight 	> _windowHeight) {
		//console.log('=== 3 ===');
		_content.css('height', _scrollPaneHeight + 'px');
		$('.filter-content').css('position','relative').css('top','auto').css('bottom','auto');
		$('.innerFilter').css('height', _scrollPaneHeight +'px');
		_GoOnScroll = false;
	}
	else if 
		(_scrollPaneHeight	> _contentHeight  && 
		_contentHeight		> _windowHeight && 
		_scrollPaneHeight 	< _windowHeight) {	
		//console.log('=== 4 ===');	
		_content.css('height', _scrollPaneHeight + 'px');
		$('.filter-content').css('position','relative').css('top','auto').css('bottom','auto');
		$('.innerFilter').css('height', _scrollPaneHeight +'px');
		_GoOnScroll = false;
	}
	else if 
		(_scrollPaneHeight	< _contentHeight  && 
		_contentHeight		< _windowHeight && 
		_scrollPaneHeight 	> _windowHeight) {	
		//console.log('=== 5 ===');
		_content.css('height', (_windowHeight - 70) + 'px');
		$('.filter-content').css('position','relative').css('top','auto').css('bottom','auto');
		$('.innerFilter').css('height', _scrollPaneHeight +'px');
		_GoOnScroll = false;
	}
	else if 
		(_scrollPaneHeight	> _contentHeight  && 
		_contentHeight		< _windowHeight && 
		_scrollPaneHeight 	< _windowHeight) {	
		//console.log('=== 6 ===');	
		_content.css('height', (_windowHeight - 70) + 'px');
		$('.filter-content').css('position','relative').css('top','auto').css('bottom','auto');
		$('.innerFilter').css('height', _scrollPaneHeight +'px');
		_GoOnScroll = false;
	}
	else if 
		(_scrollPaneHeight	< _contentHeight  && 
		_contentHeight		> _windowHeight && 
		_scrollPaneHeight 	< _windowHeight) {	
		//console.log('=== 7 ===');
		_content.css('height', 'auto');
		$('.filter-content').css('position','relative').css('top','auto').css('bottom','auto');
		$('.innerFilter').css('height', _scrollPaneHeight +'px');
		_GoOnScroll = true;
	}
	else if 
		(_scrollPaneHeight	< _contentHeight  && 
		_contentHeight		< _windowHeight && 
		_scrollPaneHeight 	< _windowHeight) {	
		//console.log('=== 8 ===');
		_content.css('height', (_windowHeight - 70) + 'px');
		$('.filter-content').css('position','relative').css('top','auto').css('bottom','auto');
		$('.innerFilter').css('height', _scrollPaneHeight +'px');
		_GoOnScroll = false;	
	}
	else {
		//console.log('=== 9 ===');
		$('.innerFilter').css('height', '100%');
		_GoOnScroll = true;
	}
	//console.log("==== * * * * ====");
	
	}
}


function scrollValue(what, event) {
    if (DetectMobileQuick() || DetectTierTablet()) {
        return;
    }
    else {
        var filterContent = $('.filter-content.'+ what);
        var isScrollPane;
        if(event.target == document)
            isScrollPane = false;
        else
            isScrollPane = $(event.target).is('#scroll-pane');
        if (filterContent.length > 0 && filterContent.is(':visible') && !isScrollPane) {
            var innerFilter = filterContent.find('.innerFilter');
            var var_marginTopSubHead = $('.sub-head').css('margin-top');
            var var_scrollValueTop = $(window).scrollTop();
            var var_heightWindow = $(window).height();
            var var_heightContent = $('#content').height();

            if (what == 'avia') {
                var var_topFilterContent = 73;
                if ($('.sub-head').css('margin-top') != '-67px') {
                    var diffrentScrollTop = 173;
                }
                else {
                    var diffrentScrollTop = 110;
                }
            }
            else {
                var var_topFilterContent = 23;
                if ($('.sub-head').css('margin-top') != '-67px') {
                    var diffrentScrollTop = 125;
                }
                else {
                    var diffrentScrollTop = 61 ;
                }
            }
            if (_GoOnScroll) {
                var needDel = false;
                if (var_scrollValueTop == 0) {
                    //is del
                    needDel = true;
                    filterContent.css('position','relative').css('top','auto').css('bottom','auto');
                }
                else if (var_scrollValueTop > 0 && var_scrollValueTop < diffrentScrollTop ) {
                    needDel = true;
                    filterContent.css('position','relative').css('top','auto').css('bottom','auto');
                }
                else if (var_scrollValueTop > diffrentScrollTop) {
                    if (var_scrollValueTop > (($('.wrapper').height() - var_heightWindow) - 30)) {
                        var var_minHeightBottom;
                        filterContent.css('position','fixed').css('top','-'+var_topFilterContent+'px').css('bottom','auto');
                        if ((var_scrollValueTop - (($('.wrapper').height() - var_heightWindow) - 30)) < 30) {
                            var_minHeightBottom = (var_scrollValueTop - (($('.wrapper').height() - var_heightWindow) - 30));
                        }
                        else {
                            var_minHeightBottom = 30;
                        }
                        innerFilter.css('height', (var_heightWindow - var_minHeightBottom) +'px');
                        if(!$('#scroll-pane').data('jsp')){
                            $('#scroll-pane').jScrollPane({contentWidth: innerFilter.width()});

                        }
                        $('#scroll-pane').jScrollPane({contentWidth: innerFilter.width()});
                        if(!_jScrollingBootom && var_scrollValueTop == ($('.wrapper').height() - $('body').height())){
                            _jScrollingBootom = true;
                            window.setTimeout(function(){
                                    $('#scroll-pane').data('jsp').scrollToBottom();
                                },
                                50
                            );

                            window.setTimeout(
                                function(){
                                    _jScrollingBootom = false;
                                    _jScrollNonBottomInitted = false;
                                    //scrollValue(what, event)
                                }
                                , 500
                            );

                        }
                        //
                    }
                    else {
                        filterContent.css('position','fixed').css('top','-'+var_topFilterContent+'px').css('bottom','auto');
                        innerFilter.css('height', var_heightWindow +'px');
                        if(!$('#scroll-pane').data('jsp')){
                            $('#scroll-pane').jScrollPane({contentWidth: innerFilter.width()});
                        }
                        if(!_jScrollNonBottomInitted){
                            _jScrollNonBottomInitted = true;
                            $('#scroll-pane').jScrollPane({contentWidth: innerFilter.width()});
                            //$('#scroll-pane').data('jsp').scrollToBottom();
                        }
                    }

                }
                if(needDel){
                    if($('#scroll-pane').data('jsp')){
                        $('#scroll-pane').data('jsp').destroy();
                    }
                }
            }
            else {
                var _issetMaps = $('#all-hotels-map').length > 0 && $('#all-hotels-map').is(':visible');
                var _issetLeftBlock = $('.left-block').length > 0 && $('.left-block').is(':visible');
                if (_issetMaps && ! _issetLeftBlock) {

                }
                else {
                    if($('#scroll-pane').data('jsp')){
                        $('#scroll-pane').data('jsp').destroy();
                    }

                }
                return false;
            }
        }
        else {
            return false;
        }
    }
}

function ifIpadLoad() {
    if (DetectMobileQuick() || DetectTierTablet()) {
        if($('body').hasClass('fixed')) {
            $('body').css('width','100%');
        }
        else {
            $('body').css('width','111%');
        }
        if ($('.maps').length > 0 && $('.maps').is(':visible')) {
            if ($('.maps').find('.layers').length < 1) {
                $('.maps').append('<div class="layers" style="position: absolute; width: 100%; height: 100%; z-index: 500; top:0px; left: 0px;"></div>');
            }
            else { return }
        }

        $('.loadWrapBg').css('position','absolute');
    }
}

function mapAllPageView() {
	var _map = $('#all-hotels-map');

    if (_map.length > 0 && _map.is(':visible')) {
        //console.log('!!!==== 6 ====!!!');

        var _isset = _map.length > 0 && _map.is(':visible');
        if (_isset) {
            var _contentWidth = $('#content').width();
            var _contentHeight = $('#content').height();
            var _mainWidth = $('.main-block').width();
            var _leftBlockIsset = $('.left-block').length > 0 && $('.left-block').is(':visible');

            if (_leftBlockIsset) {
                var _marginLeftMap = ((_mainWidth - _contentWidth) / 2);

                if ($(window).height() < 670) {
                    var _windowWidth = 670;
                }
                else {
                    var _windowWidth = $(window).height();
                }
                var offset = $('#content').offset();
                $('#content').css('height', (_windowWidth - 70)+'px');
                _map.css('height', (_windowWidth - 123)+'px');
                _map.css('width', _mainWidth+'px').css('margin-left', '-'+ _marginLeftMap +'px');
            }
            else {
                if ($(window).height() < 670) {
                    var _windowWidth = 670;
                }
                else {
                    var _windowWidth = $(window).height();
                }
                var offset = $('#content').offset();
                $('#content').css('height', (_windowWidth - 70)+'px');
                _map.css('height', (_windowWidth - 123)+'px');
                _map.css('width', $(window).width()+'px').css('margin-left', '-'+ offset.left +'px');
            }

        }
    }
}


