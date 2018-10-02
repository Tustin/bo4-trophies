<?php
require_once "vendor/autoload.php";

use PlayStation\Client;
use DiscordWebhooks\Embed;

$client = new Client();

$webhook = new \DiscordWebhooks\Client(getenv('BO4_WEBHOOK_URL'));

$client->login(getenv("PSN_PHP_TOKEN"));

$game = $client->game('CUSA11100_00');

$markdown = "";

if ($game->hasTrophies() && !file_exists('.found')) {

    $embed = new Embed();

    $groups = $game->trophyGroups();

    foreach ($groups as $group) {
        $markdown .= sprintf("# %s - %d trophies\n\n", $group->name(), $group->trophyCount());
        $embed->field($group->name(), "{$group->trophyCount()} trophies");
        // Build table.
        $tableBuilder = new \MaddHatter\MarkdownTable\Builder();
        $tableBuilder->headers(['Icon', 'Name', 'Detail', 'Rarity']);
        $tableBuilder->align(['L','C','L', 'C']);

        foreach ($group->trophies() as $trophy) {
            $tableBuilder->row([
                sprintf("![%s](%s)", $trophy->name(), $trophy->iconUrl()),
                $trophy->name(),
                $trophy->detail(),
                $trophy->type()
            ]);
        }

        $markdown .= $tableBuilder->render();
        $markdown .= "\n\n";
    }
    
    $embed->image($game->imageUrl());

    $webhook->username('Bot')->message("{$game->name()} has trophies!")->embed($embed)->send();

    file_put_contents('.found', '');
    file_put_contents('trophy.md', $markdown);
}

$out = sprintf("[%d] Ran check.\n", time());
file_put_contents('log.txt', $out, FILE_APPEND);
echo $out;