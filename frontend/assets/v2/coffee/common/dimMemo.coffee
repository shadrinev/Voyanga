# Memoizing width/height of elements
class DimMemoizer
  constructor: ->
    @storage = {}

  getHeight: (selector) =>
    if @storage[selector]?
      return @storage[selector]
      
    @storage[selector] = $(selector).height()
    return @storage[selector]

  # Forget stored dimensions after window resize
  onResize: =>
    @storage = {}


dimMemo = new DimMemoizer

$(window).resize(dimMemo.onResize)