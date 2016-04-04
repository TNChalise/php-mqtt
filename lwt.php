<?php
    /**
     * Created by PhpStorm.
     * Company Anka Technologies
     * User: TNChalise <tnchalise99@gmail.com>
     * Time: 2:22 PM
     */
    $client = new Mosquitto\Client();
    $client->onConnect('connect');
    $client->onDisconnect('disconnect');
    $client->onSubscribe('subscribe');
    $client->onMessage('message');

    /*
      *//*
        |--------------------------------------------------------------------------
        | set will before connection.
        |--------------------------------------------------------------------------
        |
        | Will message are crucial while detecting client status. See, LWT for more.
        |
        */
    $client->setWill('mqtt://myapp/clients/123', "Client died :-(", 1, 0);

    $client->connect("192.168.0.130", 1883, 5);
    $client->subscribe('mqtt://mpapp/chat-messages/123/#', 1);
    $client->subscribe('mqtt://myapp/clients/#', 1);
    $client->loopForever();
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
        printf("Got a message on topic %s with payload:\n%s\n", $message->topic, $message->payload);
    }

    function disconnect()
    {
        echo "Disconnected cleanly\n";
    }