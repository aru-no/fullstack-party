# Great task for Great Fullstack Developer

## LIMITATIONS

* Back-end only
* For some reason GitHub API '/issues' endpoint is accessible by basic authorisation but no by oauth.
To get full list issues have to be fetched for each user repository. For the sake of simplicity 10 latest updated repos are fetched and top 100 latest updated issues for each repo are fetched to use as a full list.

## RUNNING

* Copy `config/config.php.dist` to `config/config.php`
* Register app on Github: 'Settings' -> 'Developer Settings' -> 'OAuth Apps' 
* Add obtained GitHub Client Id and Client secret to `config\config.php`

## USAGE

### Authorisation

Authorisation is saved in PHP session, before using API you must hit auth url:

`GET /auth`

### Logout

`GET /logout`

### Issue list

`GET /api/v1/issues?page=:page&page_size=:page_size`

```json
{
  "totals": {
    "total": 3,
    "open": 3,
    "closed": 0
  },
  "list": [
    {
      "url": "http://SERVER/api/v1/issues/repos/aru-no/test2/number/1",,
      "number": 1,
      "state": "open",
      "title": "first issue on test2",
      "body": "",
      "user": "aru-no",
      "assignee": "aru-no",
      "comments": 1,
      "created_at": "2018-04-22T19:57:06Z"
    },
    {
      "url": "http://SERVER/api/v1/issues/repos/aru-no/test1/number/2",
      "number": 2,
      "state": "open",
      "title": "second test issue",
      "body": "This is minor test issue",
      "user": "aru-no",
      "assignee": "aru-no",
      "comments": 0,
      "created_at": "2018-04-18T19:17:35Z"
    }
  ]
}
```

### Single issue

`GET /api/v1/issues/repos/:user/:repo/number/:number`

```json
{
  "url": "/api/v1/issues/repos/aru-no/test1/number/1",
  "number": 1,
  "state": "open",
  "title": "test issue 1",
  "body": "Very important very test issue",
  "user": "aru-no",
  "assignee": "aru-no",
  "comments": 0,
  "created_at": "2018-04-18T19:17:00Z"
}
```

## TODO

* More tests
* Add cache so that full issue list does not have to be retrieved from GitHub on every page
* Used lib `tan-tan-kanarek/github-php-client` does not fetch all fields provided by GitHub API, the needed ID and Labels are missing - need to fork lib to update fetching with these fields or to use other lib.
* Front-end