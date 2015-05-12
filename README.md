SocialStatsBundle
=================

##Installation
Load the bundle in app/AppKernel.php

````
public function registerBundles()
    {
        $bundles = array(
			...
			
            new SocialStatsBundle\SocialStatsBundle()
        );
     ....
        
````

Run composer update or install to make sure you have all necessary vendors installed.

Update your doctrine schema

````
php app/console doctrine:schema:update --force
````
You are now ready to configure the bundle.

##Bundle configuration
The following is required in your config.yml file. 

````
social_stats:
    twitter:
        api_key: %social_stats.twitter.api_key%
        api_secret: %social_stats.twitter.api_secret%
        access_token: %social_stats.twitter.access_token%
        access_token_secret: %social_stats.twitter.access_token_secret%
        owner_id: %social_stats.twitter.api_owner_id%
    facebook:
        app_id: %social_stats.facebook.app_id%
        api_secret: %social_stats.facebook.api_secret%

````

You need to specify your Facebook pages and Twitter names in the parameters.yml file. Your API authentication info goes here too.

````
    social_stats.twitter.api_key: "EDIT ME"
    social_stats.twitter.api_secret: "EDIT ME"
    social_stats.twitter.access_token: "EDIT ME"
    social_stats.twitter.access_token_secret: "EDIT ME"
    social_stats.twitter.api_owner_id: "1234567890"
    social_stats.twitter.usernames:
        - "Intracto"
        - "..."

    social_stats.facebook.app_id: "123456789012345"
    social_stats.facebook.api_secret: "EDIT ME"
    social_stats.facebook.pages:
        - "intracto"
        - "..."

````


##Data structure

Every log has following properties.

| Property        | Description|
| ------------- |:-------------:| 
| ID      | A unique identifier | 
| Timestamp     | A datetime field       |
| Source | Source of data. E.g. Facebook|
| Account | Account of which we logged data. E.g. Intracto|
| Type | Type of data we logged E.g. likes|
| Content | Actual data. E.g. 15695 |

##Logging social media

Set up a cron job to execute these commands from the Symfony console. You can choose how often you log, but every 12 hours is recommended.

````
php app/console socialstats:log:facebook:page-likes-count
php app/console socialstats:log:twitter:follower-count
````

These commands will use the usernames or pagenames set up in your parameters.yml file, so make sure these are correct. 

##Generating dummy data
When you have set up a few usernames or pagenames, you can create some dummy data to check out the functionality of this bundle. You do this by executing following command.

````
php app/console socialstats:generator:log-dummy-data
````
This will generate dummy data logs, for each account specified in parameters.yml. 100 logs will be created for each type (Likes, Follower count) of each social network (Facebook and Twitter).

````
php app/console socialstats:generator:log-dummy-data --quantity=50 Twitter
````

You can change the quantity and the social network (a.k.a. source) as you desire. 

** Keep in mind that the command will not generate 50 logs in total. **

It will generate 50 logs for each username/page of all the logging types available for the source type.
