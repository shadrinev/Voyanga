resizePanel =  ->



ResizeFun = ->


ResizeAvia = ->
#  return
#  do ResizeCenter
  do CenterIMGResize
  do resizePanel
  do jsPaneScrollHeight
  do ifIpadLoad
  do smallIMGresizeIndex
  do mapAllPageView
  do inTheTwoLines

# jsPaneScrollHeight = ->

# scrollValue = ->


var_widthMAX = 1390
var_widthMID = 1290
var_widthMIN = 1000

var_valueMAX = var_widthMAX - var_widthMID
var_valueMIN = var_widthMID - var_widthMIN

var_widthLeftBlockMAX = 295
var_widthLeftBlockMID = 295
var_widthLeftBlockMIN = 255

var_widthMiddleBlockOneMAX = 935
var_widthMiddleBlockMAX = 855
var_widthMiddleBlockMID = 755
var_widthMiddleBlockMIN = 585

var_widthFilterMAX = 240
var_widthFilterMID = 240
var_widthFilterMIN = 200

var_paddingLeft = 12

var_paddingRightSlideMAX = 305
var_paddingRightSlideMID = 305
var_paddingRightSlideMIN = 65

var_paddingLeftTelefonMAX = 250
var_paddingLeftTelefonMID = 250
var_paddingLeftTelefonMIN = 220

var_widthMainBlockMAX = 695
var_widthMainBlockMIN = 530
var_iphone = 0


ResizeCenter = ->
  block = $('.center-block')
  if(!block.length)
    return

  var_leftBlock = $('.left-block')
  var_mainBlock = block.find('.main-block')
  var_content = block.find('.main-block').find('#content')
  var_filterBlock = block.find('.filter-block')
  var_logoBlock = block.find('.logo')
  var_aboutBlock = block.find('.about')
  var_slideBlock = $('.slide-turn-mode')
  var_telefonBlock = $('.telefon')
  var_ticketsItems = $('.ticket-content')
  var_hotelItems = $('.hotels-tickets')
  var_calendarGridVoyanga = $('.calenderWindow')
  var_allTripInfo = $('.allTrip .info')
  var_descrItems = $('#descr')
  paddingLeftLogo = var_paddingLeft

  widthBlock = block.width()
  var_leftBlockIsset = var_leftBlock.length > 0 && var_leftBlock.is(':visible')
  var_mainBlockIsset = var_mainBlock.length > 0 && var_mainBlock.is(':visible')
  var_filterBlockIsset = var_filterBlock.length > 0 && var_filterBlock.is(':visible')
  var_calendarGridVoyangaIsset = var_calendarGridVoyanga.length > 0 && var_calendarGridVoyanga.is(':visible')
  var_descrIsset = var_descrItems.length > 0 && var_descrItems.is(':visible')


  if (! var_leftBlockIsset &&  ! var_filterBlockIsset && var_mainBlockIsset)
    widthMainBlock = var_widthMiddleBlockOneMAX
    marginLeftMainBlock = 'auto'
    marginRightMainBlock = 'auto'
    widthContent = widthMainBlock

    if (widthBlock >= var_widthMAX)
      paddingRightSlide = var_paddingRightSlideMAX
      paddingLeftTel = var_paddingLeftTelefonMAX


      paddingRightSlide += 165
    else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID)
      paddingRightSlide = var_paddingRightSlideMID
      paddingLeftTel = var_paddingLeftTelefonMID

      paddingRightSlide += 165
    else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN)
      paddingRightSlide = Math.floor(var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN))) )
      paddingLeftTel = Math.floor(var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN))) )

      paddingRightSlide += 100
  else if (! var_leftBlockIsset &&  var_filterBlockIsset && var_mainBlockIsset)
    var_margin = Math.floor((widthBlock - (widthMainBlock + widthFilterBlock)) / 2)
    marginLeftMainBlock = var_margin
    marginRightMainBlock = widthFilterBlock + var_margin
    marginRightFilterBlock = var_margin

    widthContent = var_widthMainBlockMAX
    marginLeftContent = 'auto'
    marginRightContent = 'auto'

    if (widthBlock >= var_widthMAX)
      widthMainBlock = var_widthMiddleBlockMAX
      widthFilterBlock = var_widthFilterMAX

      paddingRightSlide = var_paddingRightSlideMAX
      paddingLeftTel = var_paddingLeftTelefonMAX

      paddingRightSlide += 165
    else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID)
      widthMainBlock = Math.floor(var_widthMiddleBlockMID + (widthBlock - var_widthMID))
      widthFilterBlock = var_widthFilterMID

      paddingRightSlide = var_paddingRightSlideMID
      paddingLeftTel = var_paddingLeftTelefonMID

      paddingRightSlide += 165;
    else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN)
      widthFilterBlock = Math.floor(220 + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_widthFilterMID - 220))) )
      var_margin = 20
      widthMainBlock = Math.floor((widthBlock  - widthFilterBlock) - (var_margin * 2))
      if (widthMainBlock > var_widthMiddleBlockMID)
          widthMainBlock = var_widthMiddleBlockMID

      var_margin = Math.floor((widthBlock - (widthMainBlock + widthFilterBlock)) / 2)
      marginLeftMainBlock = var_margin
      marginRightMainBlock = widthFilterBlock + var_margin
      marginRightFilterBlock = var_margin

      paddingRightSlide = Math.floor(var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN))) );
      paddingLeftTel = Math.floor(var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN))) );

      paddingRightSlide += 100
  else if (var_leftBlockIsset &&  var_filterBlockIsset && var_mainBlockIsset)
    widthContent = var_widthMainBlockMAX
    marginRightFilterBlock = 0
    marginLeftLeftBlock = 0
    marginLeftMainBlock = widthLeftBlock
    marginRightMainBlock = widthFilterBlock

    if (widthBlock >= var_widthMAX)
      widthLeftBlock = var_widthLeftBlockMAX
      widthMainBlock = var_widthMiddleBlockMAX
      widthFilterBlock = var_widthFilterMAX


      paddingRightSlide = var_paddingRightSlideMAX;
      paddingLeftTel = var_paddingLeftTelefonMAX;
      paddingRightSlide += 165;
    else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID)
      widthLeftBlock = var_widthLeftBlockMID
      widthMainBlock = Math.floor(var_widthMiddleBlockMID + ((widthBlock - var_widthMID) / 1))
      widthFilterBlock = var_widthFilterMID

      paddingRightSlide = var_paddingRightSlideMID
      paddingLeftTel = var_paddingLeftTelefonMID

      paddingRightSlide += 165
    else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN)
      widthLeftBlock = Math.floor(220 + ( (widthBlock - var_widthMIN) / (var_valueMIN / (var_widthLeftBlockMID - 220))) )
      widthMainBlock = Math.floor(var_widthMiddleBlockMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_widthMiddleBlockMID - var_widthMiddleBlockMIN))) )
      widthFilterBlock = Math.floor(var_widthFilterMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_widthFilterMID - var_widthFilterMIN))) )

      paddingRightSlide = Math.floor(var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN))) )
      paddingLeftTel = Math.floor(var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN))) )

      widthContent = Math.floor(var_widthMainBlockMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_widthMainBlockMAX - var_widthMainBlockMIN))) )

      paddingRightSlide += 100
  else if (var_leftBlockIsset && var_mainBlockIsset &&  ! var_filterBlockIsset )
    if (widthBlock >= var_widthMAX)
      widthLeftBlock = var_widthLeftBlockMAX
      widthMainBlock = var_widthMiddleBlockOneMAX
      var_margin = 80
      marginRightMainBlock = var_margin
      marginLeftMainBlock = widthLeftBlock + var_margin
      marginLeftLeftBlock = 0

      paddingRightSlide = var_paddingRightSlideMAX
      paddingLeftTel = var_paddingLeftTelefonMAX

      marginLeftContent = 0
      widthContent = widthMainBlock - marginLeftContent
      marginRightContent = 0
      var_widthStreet = 'auto'

      paddingRightSlide += 165

      widthAllTripInfo = 'auto'
      paddingLeftInfo = '112px'
    else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID)
      widthMainBlock = Math.floor(910 + ( (widthBlock - var_widthMID) / (var_valueMAX / (935 - 910))) )
      widthLeftBlock = var_widthLeftBlockMID
      var_margin = Math.floor(30 + ( (widthBlock - var_widthMID) / (var_valueMAX / (80 - 30))) )
      marginRightMainBlock = var_margin
      marginLeftMainBlock = widthLeftBlock + var_margin
      marginLeftLeftBlock = 0

      paddingRightSlide = var_paddingRightSlideMID
      paddingLeftTel = var_paddingLeftTelefonMID

      marginLeftContent = 0
      widthContent = widthMainBlock - marginLeftContent

      marginRightContent = 0
      var_widthStreet = 'auto'

      paddingRightSlide += 165

      widthAllTripInfo = 'auto'
      paddingLeftInfo = '112px'
    else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN)
      widthLeftBlock = Math.floor( (220 + ( (widthBlock - var_widthMIN) / (var_valueMIN / (var_widthLeftBlockMID - 220)))) - 3 )

      widthMainBlock = Math.floor(685 + ( (widthBlock - var_widthMIN) / (var_valueMIN / (910 - 685))) )

      var_margin = 39
      marginRightMainBlock = var_margin
      marginLeftMainBlock = widthLeftBlock + var_margin
      marginLeftLeftBlock = 0

      paddingRightSlide = Math.floor(var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN))) )
      paddingLeftTel = Math.floor(var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN))) )

      marginLeftContent = 0
      widthContent = widthMainBlock - marginLeftContent
      marginRightContent = 0


      var_widthStreet = '210px'

      paddingRightSlide += 100

      widthAllTripInfo = Math.floor(585 + ((widthBlock - var_widthMIN) / (var_valueMIN / (734 - 585))) )
      widthAllTripInfo = widthAllTripInfo+'px'
      paddingLeftInfo = Math.floor(36 + ((widthBlock - var_widthMIN) / (var_valueMIN / (112 - 36))) )

  if (widthBlock >= var_widthMAX)
      paddingRightSlide = var_paddingRightSlideMAX
      paddingLeftTel = var_paddingLeftTelefonMAX

      paddingRightSlide += 165
    else if (widthBlock < var_widthMAX && widthBlock >= var_widthMID)
      paddingRightSlide = var_paddingRightSlideMID
      paddingLeftTel = var_paddingLeftTelefonMID

      paddingRightSlide += 165
    else if (widthBlock < var_widthMID && widthBlock >= var_widthMIN)
      paddingRightSlide = Math.floor(var_paddingRightSlideMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingRightSlideMID - var_paddingRightSlideMIN))) );
      paddingLeftTel = Math.floor(var_paddingLeftTelefonMIN + ((widthBlock - var_widthMIN) / (var_valueMIN / (var_paddingLeftTelefonMID - var_paddingLeftTelefonMIN))) );

      paddingRightSlide += 100
  if (marginLeftMainBlock != 'auto')
      marginLeftMainBlock = marginLeftMainBlock+'px'

  if (marginRightMainBlock != 'auto')
      marginRightMainBlock = marginRightMainBlock+'px'

  if (marginLeftContent != 'auto')
      marginLeftContent = marginLeftContent+'px'

  if (marginRightContent != 'auto')
      marginRightContent = marginRightContent+'px'

  if (marginRightFilterBlock != 'auto')
      marginRightFilterBlock = marginRightFilterBlock+'px'

  if (marginLeftLeftBlock != 'auto')
      marginLeftLeftBlock = marginLeftLeftBlock+'px'

  if (var_mainBlockIsset)
    var_mainBlock.css('width', widthMainBlock+'px').css('margin-left', marginLeftMainBlock).css('margin-right', marginRightMainBlock);
    var_content.css('width', widthContent+'px').css('margin-left', marginLeftContent).css('margin-right', marginRightContent);
    var_allTripInfo.css('width', widthAllTripInfo);
    $('.costItAll').css('padding-right', paddingLeftInfo);
    $('.calToursInner').css('padding-right', paddingLeftInfo);

  if (var_filterBlockIsset)
      var_filterBlock.css('width', widthFilterBlock+'px').css('margin-right', marginRightFilterBlock)

  if (var_leftBlockIsset)
      var_leftBlock.css('width', widthLeftBlock+'px').css('margin-left', marginLeftLeftBlock)

  if (var_calendarGridVoyangaIsset)
      $('.innerCalendar, #voyanga-calendar').css('width', widthBlock+'px')

  if (var_descrIsset)
      $('#descr').find('.left').find(".descr-text .text").dotdotdot({watch: 'window'});
      $('#content .place-buy .street').css('width', var_widthStreet);


  if ($('.description .text').length > 0 && $('.description .text').is(':visible'))
      $(".description .text").dotdotdot({watch: 'window'});

  var_logoBlock.css('left', paddingLeftLogo+'px')
  var_aboutBlock.css('left', (122 + paddingLeftLogo)+'px')
  var_slideBlock.css('right', paddingRightSlide+'px')
  var_leftBlock.find('.left-content').css('margin-left', paddingLeftLogo+'px')
  var_telefonBlock.css('left', paddingLeftTel+'px')

  if (widthContent < 690)
      mathWidthRicket = Math.floor(253 + ((widthBlock - var_widthMIN) / (var_valueMIN / (318 - 253))) )
      $('.recommended-ticket').css('width', mathWidthRicket+'px')
      $('.recommended-ticket').find('.ticket-items').addClass('small')
      var_content.find('h1').find('.hideTitle').hide()
      var_ticketsItems.find('.ticket-items').addClass('small')
      $('.block').find('.ticket-items').addClass('small')
      var_hotelItems.addClass('small')
  else
      $('.recommended-ticket').find('.ticket-items').removeClass('small')
      $('.recommended-ticket').css('width', '318px')
      var_ticketsItems.find('.ticket-items').removeClass('small')
      $('.block').find('.ticket-items').removeClass('small')
      var_hotelItems.removeClass('small')
      var_content.find('h1').find('.hideTitle').show()
  if ($('body').width() < 1000)
    $('body').addClass('scrollYes')
  else
    $('body').removeClass('scrollYes')

#  resizeLeftStage();
#  resizeMainStage();




# INIT ?
#    $(document).on('focus', ".second-path", function (e) {
#        $(e.currentTarget).select();
#    }).on('mouseup', ".second-path", function(e){
#        e.preventDefault();
#    });

#    $('.voyasha td').hover(function() {
#	$('.ico-voyasha').addClass('active');
#    }, function() {
#	$('.ico-voyasha').removeClass('active');
#    });

