# Incremental Noir
An incremental writing game in the Noir genre.

This is a collaborative writing exercise in which a story is contributed line by line by many different authors. No author gets to see the entire work. Instead each author can only see a few of the lines that came before. In order to make things interesting, each author has to incorporate at least one of three randomly generated words that are provided in a list.

## Requirements
* Composer and PHP 7
* NPM

## To install
### Server setup instructions
* Copy, symlink or make `api` the root for the URL your apache/nginx/iis server serves for the API url
* Copy, symlink or make `interface` the root for the URL your apache/nginx/iis server serves for the interface url
* Set up a MySQL database with a new user and password, dedicated to running this app

### Inside the api directory
* Run `composer install`
* Set the database connection string in `.env` (ex DATABASE_URL=mysql://noir_user:noir_password@127.0.0.1:3306/noir_db)
* Set the CORS path regex to match the URL that will serve the interface (ex CORS_ALLOW_ORIGIN=^https?://interfaceurl.com:?[0-9]*$)
* Run `php bin/console doctrine:migrations:migrate` to set up DB structure

### Inside the interface directory
* Run `npm install`
* Run build script specifying the api url 
```REACT_APP_END_POINT={http://api.endpointurl.com} npm run-script build```
* If you want to run Google Analytics, run this instead, substituting your UA code 
```REACT_APP_END_POINT={http://api.endpointurl.com} REACT_APP_GOOGLE_ANALYTICS_UA={UA-xxxxxxxxx-x} npm run-script build```

### Database seeding instructions


* From command line or other mysql tool restore these two dumps:
```mysql -u noir_user -pnoir_password noir_db < setup.sql```
```mysql -u noir_user -pnoir_password noir_db < wordList.sql```