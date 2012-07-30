# Scrapy settings for tripadvisor project
#
# For simplicity, this file contains only the most important settings by
# default. All the other settings are documented here:
#
#     http://doc.scrapy.org/topics/settings.html
#

BOT_NAME = 'tripadvisor'
BOT_VERSION = '1.0'

SPIDER_MODULES = ['tripadvisor.spiders']
NEWSPIDER_MODULE = 'tripadvisor.spiders'
USER_AGENT = '%s/%s' % (BOT_NAME, BOT_VERSION)

