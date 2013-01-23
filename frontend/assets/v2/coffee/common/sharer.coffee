Socialite.setup facebook:
  lang: "ru"
  appId: 123456789
  onlike: (url) -> # ...

  onunlike: (url) -> # ...

  onsend: (url) -> # ...

Socialite.setup vkontakte:
  apiId: null
  group:
    id: 0
    mode: 0
    width: 300
    height: 290

  like:
    type: "mini"

$ ->
  html = $('#socialSource').html();
  $('#socialSource').empty();
  $('.shareSocial').html(html);
  Socialite.load($('#social'));