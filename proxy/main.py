# coding: utf-8
import sys

from twisted.python import log

from twisted.internet import reactor
from twisted.internet.protocol import Protocol
from twisted.internet.ssl import ClientContextFactory

from twisted.web import server
from twisted.web.client import Agent
from twisted.web.server import Site
from twisted.web.resource import Resource


class ResponseWriter(Protocol):
    def __init__(self, original_request):
        self.original_request = original_request

    def dataReceived(self, data):
        self.original_request.write(data)

    def connectionLost(self, reason):
        log.msg("CONNECTION LoST", reason)
        if self.original_request._disconnected or self.original_request.finished:
            # Скорее всего юзер закрыл браузер или чтото с сетью.
            return
        self.original_request.finish()


class FooContextFactory(ClientContextFactory):
    """
    Для поддержки https без проверки сертификатов
    """
    def getContext(self, *args, **kwargs):
        return ClientContextFactory.getContext(self)

agent = Agent(reactor, FooContextFactory())


class VoyangaApiProxyServer(Resource):
    isLeaf = True

    def render_GET(self, request):
        log.msg("New connection from {}".format(request.getClientIP()))
        api_request_deffered = agent.request("GET", "https://api.voyanga.com{}".format(request.uri))
        api_request_deffered.addCallback(self.handleResponse, original_request=request)
        api_request_deffered.addErrback(self.handleError, original_request=request)
        return server.NOT_DONE_YET

    def handleResponse(self, response, original_request):
        response.deliverBody(ResponseWriter(original_request))

    def handleError(self, error, original_request):
        error.printTraceback()
        log.err("Api call failed")
        original_request.write("FAIL")
        original_request.finish()


def get_site():
    resource = VoyangaApiProxyServer()
    return Site(resource)

if __name__ == '__main__':
    log.startLogging(sys.stdout)
    factory = get_site()
    reactor.listenTCP(8010, factory)
    reactor.run()
