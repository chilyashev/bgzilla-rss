timeout = 300000 # Timeout in seconds
feed_url = "http://bgzilla.org/feed/"
entries = {}

formatDate = (date) ->
  day = ('0' + date.getDate()).slice(-2)
  month = ('0' + (date.getMonth() + 1)).slice(-2)
  hours = ('0' + date.getHours()).slice(-2)
  minutes = ('0' + date.getMinutes()).slice(-2)
  "#{day}.#{month}.#{date.getFullYear()} #{hours}:#{minutes}"

getTemplate = (template, vars) ->
  tpl = $('#' + template)
  if tpl.length < 1
    alert("template not found")
    return false
  html = tpl.html()
  for k,v of vars
    re = new RegExp("%" + k + "%", "g")
    html = html.replace(re, v)
  html


doStuff = (url) ->
  items = $('#items')
  loader = $('#loader')
  load_error = $("#load_error")
  items.html("")
  loader.show()
  loader.hide()
  #TODO: Figure out a way to use something other than google's feed proxy. Maybe host my own?
  feed = 'https://ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=10&callback=?&q=' + encodeURIComponent(url)
  $.ajax({
         url: feed,
         xhrFields: {
           mozSystem: true
         },
         dataType: 'json',
         timeout: timeout,
         success: (data) ->
           parseFeed(data.responseData.feed)
         error: (request, status, err) ->
           if(status == "timeout")
             alert('Вземането на данните отнема прекалено много време. Моля, питайте оново по-късно.')
             loader.hide()
             load_error.show()
           else
             alert("Грешка при вземане на данните. Моля, питайте оново по-късно.")
         })

parseFeed = (feed) ->
  items = $('#items')
  $('#loader').hide()
  entries = feed.entries
  items.html("")
  for k of entries
    console.log(k)
    entry = entries[k]
    date = new Date(entry.publishedDate)
    entry.date = formatDate(date)
    data = {title: entry.title, link: entry.link, date: entry.date, key: k}
    html = getTemplate('item', data)
    items.append(html)
    link = items.find('#title_link_'+k)
    link.data('article-id', k)
    link.on('click', () ->
      openArticle($(@)))
  items.find('.title_link').button().button('refresh')

###
  Opens the article
###
window.openArticle = (ob) ->
  key = ob.data('article-id')
  console.log("Opening article #{key}")
  if !(key of entries)
    alert("Възникна грешка! Моля, опитайте отново.")
    $.mobile.changePage ($("#home"))

  entry = entries[key]
  title = $(".entry_title")
  subtitle = $('.entry_subtitle')
  content = $(".entry_content")
  link = $('.entry_link')
  categories = $(".entry_categories")

  title.html(entry.title)
  # Set the title

  subtitle_html = "Публикувано от #{entry.author} "
  subtitle_html += " на " + formatDate(new Date(entry.publishedDate))
  subtitle.html(subtitle_html)

  content.html(entry.content)
  # Content

  categories.html("Публикувано в: " + entry.categories.join(', '))

  link.on "click", () ->
    viewLink(entry.link)
  # Fix all links so they don't open inside the application's screen
  content.find("a").each (a, b) ->
    link = $(b)
    old_href = link.attr('href')
    link.attr('href', '#')
    link.click () ->
      viewLink(old_href)
  $.mobile.changePage ($("#view-entry"))

###
  Open a link in the browser instead of inside the app
###
window.viewLink = (url) ->
  new MozActivity
    name: "view",
    data:
      type: "url",
      url: url

window.refresh = () ->
  doStuff(feed_url)

###
  Do the main stuff
###
jQuery ->
  doStuff(feed_url)
  $('#refresh').on 'click', () -> refresh()

