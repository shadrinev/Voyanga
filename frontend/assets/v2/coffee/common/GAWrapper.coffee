#
# Оберта над _gaq.push, для простоты отладки. Единая точка входа в которой можно оставить логгинг
GAPush = (data)->
#  console.log "GA LOGGING", data
  _gaq.push(data)

  