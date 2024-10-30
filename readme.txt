=== Broadcast to Telegram ===
Tags: Telegram, channel, notification, messenger, bot Api 
Requires at least: 4.4
Tested up to: 4.7.2
Version: 1.2.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows WordPress sites to send notifications to a Telegram channel. It's possible send notification to multiple channels.

== Description ==

<strong>Broadcast to Telegram</strong> is a very simple plugin that lets you automatically send post notification to your telegram channel via an admin BOT.<br />It allows you to enroll multiple Telegram channels and you can choose, when sending, which channels make the notification to. It also allows you to choose which type of document is to be used with. <br />It also gives the possibility, when a document is updated, to know which channels have already been sent a notification and edit directly the Telegram original message, marking it as updated by the <strong>(M)</strong> symbol.

== Installation ==
1. Download the plugin and install it in wordpress
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > Broadcast to Telegram page enter your channel admin bot token and channels list in the syntax shown on the side.

= How do I create a bot and link it to my channels? =

There's a bot... to create a new bot.<br />
Just talk to BotFather (as described below) and follow a few simple steps.

1. in your Telegram client window search for <strong>@BotFather</strong>
2. next you type the following commands:
<pre>
<strong>You:</strong>
<em>/newbot</em>

<strong>BotFather:</strong>
<em>Alright, a new bot. How are we going to call it?
Please choose a name for your bot.</em>

<strong>You:
<em>myTestBot</em></strong>

<strong>BotFather:</strong>
<em>Good. Now let's choose a username for your bot.
It must end in 'bot'. Like this, for example: TetrisBot or tetris_bot.</em>

<strong>You:
<em>myUserBot</em></strong>

<strong>BotFather:</strong>
<em>Done! Congratulations on your new bot. 
You will find it at <strong>t.me/mytestbot</strong>. 
You can now add a description, about section and profile picture 
for your bot, see /help for a list of commands. 
By the way, when you've finished creating your cool bot, 
ping our Bot Support if you want a better username for it. 
Just make sure the bot is fully operational before you do this.

Use this token to access the HTTP API:</em>
<strong>277260464:AAGPNnx_87k1ctokVMffprgzvPgVvrXRAqQ</strong>

<em>For a description of the Bot API, see this page: 
https://core.telegram.org/bots/api</em><br />
</pre>

Parameters of your just created BOT are:

* <em>name:</em> <strong>myTestBot</strong>
* <em>username:</em> <strong>@myUserBot</strong>
* <em>token:</em> <strong>277260464:AAGPNnx_87k1ctokVMffprgzvPgVvrXRAqQ</strong>
* <em>URL:</em> <strong>https://t.me/mytestbot</strong>

<strong>Warning!</strong> The data shown are just an example... They are therefore not working!

Now, if not already exist, let's create on the smartphone channels (or chats) which we want to send notifications to:<br />e.g. <strong>@channel_name1, @channel_name2,</strong> ...

Then on the smartphone add to every channel the BOT username (<strong>@myUserBot</strong>) as <em>administrator</em>. 

Finally insert <em>BOT token</em> and <em>channels list</em> in the plugin settings page and <em>"Voil&agrave;, les jeux sont faits!!"<em>

<strong>That's all folks!</strong>

== Screenshots ==

1. Settings page
2. Send metabox before and after sending (on the channel name side the number of members/followers is shown) 

== Changelog ==

= Versione 1.2.0 =

* Fixed error in localization 

= Versione 1.1.0 =

* Fixed error on HTML entities 

= Versione 1.0.0 =

* Initial Release on wordpress.org 

