# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/topics/items.html

from scrapy.item import Item, Field

class HotelItem(Item):
    name = Field()
    name_alt = Field()
    address = Field()
    address_ext = Field()
    locality = Field()
    postal = Field()
    country = Field()
    rating = Field()
    lat = Field()
    lng = Field()
    url = Field()
    
