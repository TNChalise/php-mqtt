# php-mqtt
PHP MQTT With LWT Explained

This article assumes user have basic information about MQTT Protocol and basic functionlity. The Server end is mantained in Linux Operating System And Client side in PHP.

###Active/InActive Status with LWT

LWT messages are not really concerned about detecting whether a client has gone offline or not (that task is handled by keepAlive messages). LWT messages are about what happens after the client has gone offline.

The analogy is that of a real last will: If a person dies, she can formulate a testament, in which she declares what actions should be taken after she has passed away. An executor will heed those wishes and execute them on her behalf. The analogy in the MQTT world is that a client can formulate a testament, in which it declares what message should be sent on it's behalf by the broker, after it has gone offline.

##Installation
###Step-1: Install MQTT Mosquitto Broker
```LINUX
1. wget http://repo.mosquitto.org/debian/mosquitto-repo.gpg.key
2. sudo apt-key add mosquitto-repo.gpg.key
3. cd /etc/apt/sources.list.d/
4. sudo wget http://repo.mosquitto.org/debian/mosquitto-wheezy.list
5. apt-get update
6. apt-cache search mosquitto
7. apt-get install mosquitto
8. Check whether installation is done or not: mosquitto
```

###Step-2: Install Mosquito-clients
1. $ sudo apt-get install mosquitto-clients

###Step-3: Install PECL Mosquitto Extension [https://github.com/mgdm/Mosquitto-PHP]
1. pecl install Mosquitto-alpha
2. Then add extension=mosquitto.so to your php.ini.
 This will allow you to write :
```php
$client = new Mosquitto\Client();
```
in any php files.
Or,
```php
use  \Mosquitto\Client as MosquittoClient;

$client = new MosquittoClient;
```
###Examples
1. Simple Publish
2. Simple Subscribe
3. Simple LWT

### How to do with Android as client and Linux server as MQTT Broker. (With Active/InActive Status)
For simplicity, I will connect broker from PHP-Client. This will remain same for android client.
Lets start with an example:
Open terminal and subscribe to any wild card topic. Say, I just want to track the client`s active and inactive status.
$ mosquitto_sub -v -t 'mqtt://myapp/clients/#'
This command will subscribe to the topic 'mqtt://myapp/clients/#', any other messages published from the topic followed by
the subscription topic will be now listened.

Now create a php client and set will message before connect, and publish a message saying that I am alive.

// Set will: Say broker what to do after you disconnect.
 $client->setWill('mqtt://myapp/clients/123', "broadcast my id to my connections to say I am inactive for now", 1, 0);

 In application you often require active/inactive status of any connected devices. So, subscribe to a wildcard topic
 $client->subscribe('mqtt://myapp/clients/#', 1);

 And the rule is, when a device has to made a connection, then, it must first set a will to the topic "myapp/clients",
 subscribe to that topic and publish online status on that channel for other users to note that he made connection successful.

 Basically from php client,
 $client->setWill('mqtt://myapp/clients/123', "Client died :-(", 1, 0);
 $client->connect("192.168.0.130", 1883, 5);
 $client->subscribe('mqtt://mpapp/chat-messages/123/#', 1);
 $client->subscribe('mqtt://myapp/clients/#', 1);
 $client->publish('mqtt://myapp/clients/userId', "I am live again/first time" . date('Y-m-d H:i:s'), 1, 0);
 $client->loopForever();

Here user Id will be useful to track particular client for you application.

If one of the client disconnected, broker will automatically broadcast a  message saying that the client is dead to the channel that all other users
are subscribed to. This is easy to really track down the active-inactive status of any users.

### Testing:
$ mosquitto_sub -v -t 'mqtt://myapp/clients/#'
$ mosquitto_sub -v -t 'mqtt://myapp/chat-messages/#'
$ php lwt.php and ctrl+c to disconnect.

messages will be automatically broadcast to 'mqtt://myapp/clients' once the client gets disconnected.

### Storing Messages broadcast on any channel/topic
You may want to store all the messages in database on the broadcast on every topics on your server.
1. You could create an MQTT client that subscribes to the topics you're interested in and inserts them into your database. This could run on the machine running the broker or the database.
2. Both Mosquitto and RSMB provide C client libraries that you could use, along with the appropriate library for your database.

 $client->onSubscribe('subscribe');
$client->subscribe('mqtt://topic/#', 1);

 function subscribe()
    {
       // Your mysql connection and process messages to store them.
    }

### Further, Sanitize ugly forever loop
The concept here is, we are listening to all broadcasting messages on the server, and we never want to hung up with
broker. So we have to loopForever() to process and record all messages.
To acheive this, Install Supervisor: [http://supervisord.org/] A process monitor.

And one important thing, while you are subscribed to a wildcard topic, messages will arrive too soon that, you may miss
them to process or to show to your actual users. I personally recommend writing some queues to handle the job.
There are several queue listeners.
    Amazon SQS: aws/aws-sdk-php ~3.0
    Beanstalkd: pda/pheanstalk ~3.0
    IronMQ: iron-io/iron_mq ~2.0|~4.0
    Redis: predis/predis ~1.0


###References
1. http://mqtt.org/documentation
2. http://www.hivemq.com/blog/mqtt-essentials-part-9-last-will-and-testament
3. https://github.com/mgdm/Mosquitto-PHP
3. https://github.com/mqtt/mqtt.github.io/wiki
4. http://stackoverflow.com/questions/6584444/sql-database-and-mqtt-mosquitto-or-rsmb
5. http://supervisord.org/
6. https://laravel.com/docs/5.1/queues
