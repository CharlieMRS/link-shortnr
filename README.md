# link-shortnr
A bit.ly like service built on Symfony and jQuery

## Schema
redirect\
&nbsp;&nbsp;id\
&nbsp;&nbsp;long_url\
&nbsp;&nbsp;short_url
  
migration_versions\
&nbsp;&nbsp;version\
&nbsp;&nbsp;executed_at

## Requests

#### Post request (AJAX)

Upon valid url input, AJAX post the form, controller checks if its unique and throws exception if not. It then generates a random 9-character random alphanumeric string and persists it as property on Redirect data object (entity). A rendered twig template is returned to AJAX success handler, which appends it to empty div and hides the home page

#### View Link (Http Request)

Simple lookup using Doctrine ORM. Renders same template used in Ajax request but from within a twig template that extends the base template since it's a standard http request

#### Delete link (Http Request)

Another Simple lookup using Doctrine ORM, removes entity from db, serves RedirectResponse and sets a success flash message to session, user is redirected to homepage and sees the success message

#### Redirect Link

Router matches identifier that follows 'r' uriFragment, and looks up longUrl using that identifier and serves a 302 redirect response. (should probably be 301).
