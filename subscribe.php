<?php

    use \Mosquitto\Client as MosquittoClient;

    /**
     * Created by PhpStorm.
     * Company Anka Technologies
     * User: TNChalise <tnchalise99@gmail.com>
     * Time: 1:00 PM
     */

    $client = new MosquittoClient();

    $client->onConnect('connect');
    $client->onDisconnect('disconnect');
    $client->onSubscribe('subscribe');
    $client->onMessage('message');
    $client->connect("192.168.0.130", 1883, 5); //Change Accordingly. TCP/IP connection is supported by mqtt.

    //Please refer to topic/channel semantics [http://mosquitto.org/man/mqtt-7.html]
    $client->subscribe('mqtt://my-topic/messages/#', 1);
    
    while (true) {
        $client->loop();
        $mid = $client->publish('mqtt://my-topic/messages/userId', "Hello from PHP at " . date('Y-m-d H:i:s'), 1, 0);
        echo "Sent message ID: {$mid}\n";
        $client->loop();
        sleep(2);
    }

    $client->disconnect();

    unset($client);

    function connect($r)
    {
        echo "I got code {$r}\n";
    }

    function subscribe()
    {
        echo "Subscribed to a topic\n";
    }

    function message($message)
    {
        printf("Got a message ID %d on topic %s with payload:\n%s\n\n", $message->mid, $message->topic, $message->payload);
    }

    function disconnect()
    {
        echo "Disconnected cleanly\n";
    }