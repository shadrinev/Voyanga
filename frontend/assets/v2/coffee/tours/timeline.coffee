class Timeline
  constructor: (@toursData)->
    @timelinePosition = ko.observable 0
    # INTENTIONALLY NOT OBSERVABLE 
    @termsActive = false

    @data = ko.computed =>
      spans = []
      avia_map = {}
      hotel_map = {}
      for item in @toursData()
#        obj =  {start: moment(item.timelineStart()), end: moment(item.timelineEnd())}
        obj =  {start: moment(item.timelineStart()).clone().hours(0).minutes(5), end: moment(item.timelineEnd()).clone().hours(0).minutes(5)}
        spans.push obj
        if item.isHotel()
          hotel_map[obj.start.format('M.D')] = {duration:obj.end.diff(obj.start, 'days'), item: item}
        else
          avia_map[obj.start.format('M.D')] = {duration:obj.end.diff(obj.start, 'days'), item: item}
      start_date = spans[0].start
      end_date = spans[spans.length-1].end
      # FIXME FIXME FIXME
      if true#@searchParams.returnBack
        item = @toursData()[0]
        if item.isAvia()
          if item.rt()
            end_date = moment(item.rtTimelineStart()).clone().hours(0).minutes(5)
            avia_map[end_date.format('M.D')] = {duration: 1, item: item}
      timeline_length = end_date.diff(start_date, 'days')
      middle_date = start_date.clone().add('days', timeline_length/2)
      if timeline_length < 23
        timeline_length = 23
      left = Math.round(timeline_length / 2)
      right = Math.round(timeline_length /2)
      results = []
      has_first_avia = false
      has_first_hotel = false
      for x in [2..left]
        obj =  {date: middle_date.clone().subtract('days', left-x+1)}
        obj.day = obj.date.format('D')
        obj.hotel = false
        obj.avia = false
        obj.first = false
        item_avia = avia_map[obj.date.format('M.D')]
        item_hotel = hotel_map[obj.date.format('M.D')]
        if item_hotel
          obj.hotel = item_hotel
          if has_first_hotel == false
            obj.first = true
            has_first_hotel = true

        if item_avia
          obj.avia = item_avia
          if has_first_avia == false
            obj.first = true
            has_first_avia = true
        results.push obj
      for x in [0..right]
        obj =  {date: middle_date.clone().add('days', x)}
        obj.day = obj.date.format('D')
        obj.hotel = false
        obj.avia = false
        obj.first = false
        item_avia = avia_map[obj.date.format('M.D')]
        item_hotel = hotel_map[obj.date.format('M.D')]
        if item_hotel
          obj.hotel = item_hotel
          if has_first_hotel == false
            obj.first = true
            has_first_hotel = true

        if item_avia
          obj.avia = item_avia
          if has_first_avia == false
            obj.first = true
            has_first_avia = true
        results.push obj
      return results
                    
  showConditions: (context, event) =>
    el = $(event.currentTarget)

    if !el.hasClass('active')
      $('.btn-timeline-and-condition a').removeClass('active')
      el.addClass('active')

      $('.divTimeline').addClass('hide')
      $('.divTimeline').animate(
        {'top': '-'+$('.divTimeline').height()+'px'},
        400,
        =>
          $('.slide-tmblr').css('overflow','visible')
          @termsActive = true)
      $('.divCondition').animate({'top': '-16px'},400).removeClass('hide')

  showTimeline: (context, event) =>
    el = $(event.currentTarget)
    if ! el.hasClass('active')
      $('.slide-tmblr').css('overflow','hidden')
      $('.btn-timeline-and-condition a').removeClass('active')
      el.addClass('active')
      $('.divTimeline').animate({'top': '0px'},400).removeClass('hide')
      $('.divCondition').animate({'top': '68px'},400,
      =>
        @termsActive = false
      ).addClass('hide')

  scrollTimelineRight: =>
    scrollableFrame = @data().length* 32 - 23*32
    if scrollableFrame < 0
      return
    @timelinePosition @timelinePosition() + 32
    if @timelinePosition() > scrollableFrame
      @timelinePosition scrollableFrame
    

  scrollTimelineLeft: =>
    scrollableFrame = @data().length* 32 - 23*32
    if scrollableFrame < 0
      return
            
    @timelinePosition @timelinePosition() - 32

    if @timelinePosition() < 0
      @timelinePosition 0


