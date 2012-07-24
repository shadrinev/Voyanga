import re

from scrapy.http import Request
from scrapy.spider import BaseSpider
from scrapy.selector import HtmlXPathSelector

from tripadvisor.items import HotelItem

class RatingSpider(BaseSpider):
    name="rating"
    allowed_domains = ["tripadvisor.ru"]

    def start_requests(self):
        """
        Return requests for every city 
        """
        for city in open('data/cities.txt'):
            yield Request(url="http://www.tripadvisor.ru/Search?q=%s" % city, callback=self.serp)

    def serp(self, response):
        """
        Handle city search results
        """
        hxs = HtmlXPathSelector(response)
        href = hxs.select("/html/body/div[3]/div[2]/div/div[3]/div[2]/div[3]/div/div/div[3]/div[2]/div/a/@href").extract()[0]
        if 'Hotels' in href:
            return  Request(url="http://www.tripadvisor.ru%s" % href, callback=self.hotel_index)

        
    def hotel_index(self, response):
        """
        Parse hotel index page for given city
        """
        result = list()
        hxs = HtmlXPathSelector(response)
        href = hxs.select("//a[contains(@class, 'sprite-pageNext')]/@href").extract()
        if len(href) == 2:
            href=href[0]
            result.append(Request(url="http://www.tripadvisor.ru%s" % href, callback=self.hotel_index))
        
        # links to hotel pages
        hrefs = hxs.select("/html/body/div[3]/div[2]/div/div[7]/div[2]/div[2]/div/div[3]/div/div[2]/a/@href").extract()
        for href in hrefs:
            result.append(Request(url="http://www.tripadvisor.ru%s" % href))
        return result

    def parse(self, response):
        """
        Parse hotel page
        """
        item = HotelItem()
        hxs = HtmlXPathSelector(response)
        item['name'] = hxs.select('//*[@id="HEADING"]/text()').extract()[0].strip()
        for css_class, prop in (("altHead", "name_alt"),):
            text = hxs.select("//*[contains(@class, '%s')]/text()" % css_class).extract()
            if len(text):
                text=text[0]
            else:
                text=""
            item[prop] = text.strip()

        for propname, prop, in (("v:street-address", "address"), ("v:extended-address", "address_ext"), ("v:locality", "locality"), ("v:postal-code", "postal"), ("v:country-name", "country"),):
            text = hxs.select("//*[@property='%s']/text()" % propname).extract()
            if len(text):
                text=text[0]
            else:
                text=""
            item[prop] = text.strip()


        text = hxs.select("//*[contains(@class, 'sprite-ratings')]/@alt").extract()
        if len(text):
            text=text[0]
        else:
            text=""
        item["rating"] = text.strip()

        m = re.findall(r"lat: (\d+\.\d+)", response.body)
        if len(m):
            item['lat'] = m[0]
        m = re.findall(r"lng: (\d+\.\d+)", response.body)
        if len(m):
            item['lng'] = m[0]
        item['url'] = response.url

        return item
