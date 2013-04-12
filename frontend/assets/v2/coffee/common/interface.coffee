# Наивная имплементация проверки следования интерфейсу классов.
#
# Инструкция по пременению:
#
# Создаем класс интерфейса
# 
# class IInterface
#    @methodToImplement = "Description".
#
# Проверяем кореектность реализации интерфейса:
#
# implement(Class, IInterface)
implement = (class_, interface_) ->
  source = class_.prototype || class_
  for key, val of interface_
    if !source[key] || typeof(source[key]) != "function"
      throw "Implement method `#{key}` of interface `#{interface_.name}` on  `#{class_.name}`"