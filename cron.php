<?php

/**
 * cron.php — Syncs YouTube uploads for a channel into the local database.
 *
 * Run periodically (e.g. daily) via cron:
 *   php /path/to/cron.php
 */

date_default_timezone_set('UTC');

require __DIR__ . '/app/config.php';
require __DIR__ . '/app/lib/Database.php';
require __DIR__ . '/app/lib/rp_youtube.php';

/**
 * VideoSync — fetches all uploads for a YouTube channel and stores
 * any previously unseen videos in the database.
 */
class VideoSync
{
    private YoutubePub $yt;
    private Database   $db;

    public function __construct(YoutubePub $yt, Database $db)
    {
        $this->yt = $yt;
        $this->db = $db;
    }

    /**
     * Entry point: resolve the uploads playlist then page through it.
     *
     * @throws \RuntimeException  On API or database error
     */
    public function run(): void
    {
        $channel = $this->yt->get('channels', [
            'part'        => 'snippet,contentDetails',
            'forUsername' => YOUTUBE_USER,
            'maxResults'  => '50',
        ]);

        $playlistId = $channel['items'][0]['contentDetails']['relatedPlaylists']['uploads'];

        $this->syncPlaylist($playlistId);
    }

    /**
     * Pages through a playlist and stores new videos until a duplicate is found.
     *
     * @param string $playlistId  YouTube playlist ID to iterate
     */
    private function syncPlaylist(string $playlistId): void
    {
        $params = [
            'part'       => 'snippet',
            'maxResults' => '50',
            'playlistId' => $playlistId,
        ];

        $page = 0;

        do {
            $page++;
            echo "Loading page: $page.\n";

            $playlist  = $this->yt->get('playlistItems', $params);
            $hasMore   = $this->storeItems($playlist['items'] ?? []);
            $nextToken = $playlist['nextPageToken'] ?? null;

            if ($hasMore && $nextToken !== null) {
                $params['pageToken'] = $nextToken;
            } else {
                break;
            }
        } while (true);
    }

    /**
     * Inserts each item into the database.
     * Returns false as soon as a video already exists (signals stop-paging).
     *
     * @param  array<int, array<string, mixed>>  $items  Playlist items from API
     * @return bool  true if all items were new; false if a duplicate was found
     */
    private function storeItems(array $items): bool
    {
        foreach ($items as $item) {
            $snippet = $item['snippet'];
            $ytid    = $snippet['resourceId']['videoId'];

            $existing = $this->db->query(
                'SELECT id FROM videos WHERE ytid = ?',
                [$ytid]
            );

            if ($existing !== []) {
                return false;
            }

            $publishedAt = new DateTimeImmutable($snippet['publishedAt']);

            $this->db->execute(
                'INSERT INTO videos (user, date, ytid, title, description)
                 VALUES (?, ?, ?, ?, ?)',
                [
                    $snippet['channelTitle'],
                    $publishedAt->format('Y-m-d H:i:s'),
                    $ytid,
                    $snippet['title'],
                    $snippet['description'],
                ]
            );
        }

        return true;
    }
}

// Bootstrap
$yt   = new YoutubePub(YOUTUBE_KEY);
$db   = Database::getInstance();
$sync = new VideoSync($yt, $db);
$sync->run();
