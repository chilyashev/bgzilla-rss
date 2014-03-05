// Generated by CoffeeScript 1.6.3
(function() {
  var doStuff, entries, feed_url, formatDate, getTemplate, parseFeed, timeout;

  timeout = 300000;

  feed_url = "http://bgzilla.org/feed/";

  entries = {};

  formatDate = function(date) {
    var day, hours, minutes, month;
    day = ('0' + date.getDate()).slice(-2);
    month = ('0' + (date.getMonth() + 1)).slice(-2);
    hours = ('0' + date.getHours()).slice(-2);
    minutes = ('0' + date.getMinutes()).slice(-2);
    return "" + day + "." + month + "." + (date.getFullYear()) + " " + hours + ":" + minutes;
  };

  getTemplate = function(template, vars) {
    var html, k, re, tpl, v;
    tpl = $('#' + template);
    if (tpl.length < 1) {
      alert("template not found");
      return false;
    }
    html = tpl.html();
    for (k in vars) {
      v = vars[k];
      re = new RegExp("%" + k + "%", "g");
      html = html.replace(re, v);
    }
    return html;
  };

  doStuff = function(url) {
    var feed, items, load_error, loader;
    items = $('#items');
    loader = $('#loader');
    load_error = $("#load_error");
    items.html("");
    loader.show();
    loader.hide();
    feed = 'https://ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=10&callback=?&q=' + encodeURIComponent(url);
    return $.ajax({
      url: feed,
      dataType: 'json',
      timeout: timeout,
      success: function(data) {
        return parseFeed(data.responseData.feed);
      },
      error: function(request, status, err) {
        if (status === "timeout") {
          alert('Вземането на данните отнема прекалено много време. Моля, питайте оново по-късно.');
          loader.hide();
          return load_error.show();
        } else {
          return alert("Грешка при вземане на данните. Моля, питайте оново по-късно.");
        }
      }
    });
  };

  parseFeed = function(feed) {
    var data, date, entry, html, items, k;
    items = $('#items');
    $('#loader').hide();
    entries = feed.entries;
    items.html("");
    for (k in entries) {
      entry = entries[k];
      date = new Date(entry.publishedDate);
      entry.date = formatDate(date);
      data = {
        title: entry.title,
        link: entry.link,
        date: entry.date,
        key: k
      };
      html = getTemplate('item', data);
      items.append(html);
      items.find('#title_link_' + k).on('click', function() {
        return openArticle($(this), k);
      });
    }
    return items.find('.title_link').button().button('refresh');
  };

  /*
    Opens the article
  */


  window.openArticle = function(ob, key) {
    var categories, content, entry, link, subtitle, subtitle_html, title;
    if (!(key in entries)) {
      alert("Възникна грешка! Моля, опитайте отново.");
      $.mobile.changePage($("#home"));
    }
    entry = entries[key];
    title = $(".entry_title");
    subtitle = $('.entry_subtitle');
    content = $(".entry_content");
    link = $('.entry_link');
    categories = $(".entry_categories");
    title.html(entry.title);
    subtitle_html = "Публикувано от " + entry.author + " ";
    subtitle_html += " на " + formatDate(new Date(entry.publishedDate));
    subtitle.html(subtitle_html);
    content.html(entry.content);
    categories.html("Публикувано в: " + entry.categories.join(', '));
    link.on("click", function() {
      return viewLink(entry.link);
    });
    content.find("a").each(function(a, b) {
      var old_href;
      link = $(b);
      old_href = link.attr('href');
      link.attr('href', '#');
      return link.click(function() {
        return viewLink(old_href);
      });
    });
    return $.mobile.changePage($("#view-entry"));
  };

  /*
    Open a link in the browser instead of inside the app
  */


  window.viewLink = function(url) {
    return new MozActivity({
      name: "view",
      data: {
        type: "url",
        url: url
      }
    });
  };

  window.refresh = function() {
    return doStuff(feed_url);
  };

  /*
    Do the main stuff
  */


  jQuery(function() {
    doStuff(feed_url);
    return $('#refresh').on('click', function() {
      return refresh();
    });
  });

}).call(this);
