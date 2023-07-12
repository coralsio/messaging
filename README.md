# Corals Messaging

With Messaging Module, website admin can keep all communication through an official channel without the need to communicate through not monitored or tracked channel, this is really helpfully especially when trying to resolve disputes, as conversations and threads can be used as a reference to find out the cause of the issues.

 

also website admin and end users in many cases won’t be comfortable to share their emails to others because of the spam, for example, if they wanted to be reached for a listing they posted or a classified ad, that’s why Laraship added this module by default as free to both modules as its a very commence necessity.

<p><img src="https://www.laraship.com/wp-content/uploads/2018/10/laraship_messaging_module.png" alt="" width="1615" height="823"></p>
<p>&nbsp;</p>

here are the configurable options when setting up the Messaging Module.

1. using Module Setting you can specify if the user can send message to multiple or single users.

2. Using Permissions you can specify whether the user has option to select the recipient from a drop down or the user will be redirected to discussion creation form with user hashed_id in the URL  like https://directory.laraship.com/messaging/discussions/create?user=W6op93z2nG in this case the user will be limited to reach the selected user with specific message.

3. Discussions can be marked as important, favorites. can also be sent to trashed and can track which message git deleted.

4. Individual messages can only be deleted if they are not seen buy the other party.

<p>&nbsp;</p>


## Installation

You can install the package via composer:

```bash
composer require corals/messaging
```

## Testing

```bash
vendor/bin/phpunit vendor/corals/messaging/tests 
```
