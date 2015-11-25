var express = require('express'),
    router = express.Router();

/**
 * Get the rendered version of the page content for specified page. Returns the HTML for the page.
 *
 * @param pagename
 * @param callback
 */
function getRenderedPage(pagename, callback) {
  console.log(pagename);
  var fs = require('fs');
  //  @todo: sanitize pagename input
  fs.readFile("public/content/" + pagename + ".md", 'utf8', function (err, data) {
    if (err) {
      return getRenderedPage('404', callback);
    }
    var page = {},
        title = pagename,
        showdown = require('showdown'),
        converter = new showdown.Converter();

    console.log(data);

    page.url = title;
    title = title.replace("-", " ");
    page.name = title.charAt(0).toUpperCase() + title.slice(1);
    page.content = converter.makeHtml(data);

    callback(page);
  });
}

/**
 * Get the raw page content for the specified page. Returns the page Markdown.
 *
 * @param pagename
 * @param callback
 */
function getRawPage(pagename, callback) {
  console.log("edit: " + pagename);
  var fs = require('fs');
  //  @todo: sanitize pagename input
  fs.readFile("public/content/" + pagename + ".md", 'utf8', function (err, data) {
    if (err) {
      return getRenderedPage('404', callback);
    }
    var page = {},
        title = pagename;

    page.url = title;
    title = title.replace("-", " ");
    page.name = title.charAt(0).toUpperCase() + title.slice(1);
    page.content = data;

    callback(page);
  });
}

/**
 * Standard page route
 */
router.get('/:pagename', function (req, res) {
  getRenderedPage(req.params.pagename, function (page) {
    res.render('default', page);
  });
});

/**
 * Home page route
 */
router.get('/', function (req, res, next) {
  getRenderedPage('home', function (page) {
    res.render('home', page);
  });
});

/**
 * Edit page route
 */
router.get('/:pagename/edit', function (req, res) {
  getRawPage(req.params.pagename, function (page) {
    res.render('_edit', page);
  });
});

/**
 * POST save page route
 */
router.post('/:pagename/save', function (req, res) {
  var fs = require('fs');
  var pagename = req.params.pagename;

  fs.writeFile('public/content/'+pagename+'.md', req.body.content, function(err){
    if(err) {
      console.err(err);
      return;
    }
    getRenderedPage(pagename, function (page) {
      res.render('default', page);
    });
  });
})


module.exports = router;
