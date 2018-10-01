<?php
require_once "vendor/autoload.php";

use PlayStation\Client;
use DiscordWebhooks\Embed;

$client = new Client();

$webhook = new \DiscordWebhooks\Client(getenv('BO4_WEBHOOK_URL'));

$client->login(getenv("PSN_PHP_TOKEN"));

$game = $client->game('CUSA02290_00');

if ($game->hasTrophies() && !file_exists('.found')) {

    $embed = new Embed();

    $groups = $game->trophyGroups();

    foreach ($groups as $group) {
        $embed->field($group->name(), "{$group->trophyCount()} trophies");
    }
    
    $embed->image($game->imageUrl());

    $webhook->username('Bot')->message("{$game->name()} has trophies!")->embed($embed)->send();

    file_put_contents('.found', '');
    
    // sleep(1);

    // foreach ($groups as $group) {
    //     foreach ($group->trophies() as $trophy) {
    //         $embed = new Embed();
    //         $embed->thumbnail($trophy->iconUrl());
    //         $embed->author($trophy->name());
    //         $embed->description($trophy->detail());
    //         $embed->field('Rarity', $trophy->type());
    //         $webhook->username('Bot')->embed($embed)->send();
    //         sleep(1);
    //     }
    //     die();
    // }
}