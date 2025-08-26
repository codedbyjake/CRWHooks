<?php

namespace MediaWiki\Extension\CRWHooks;

use MediaWiki\User\User;

class Hooks {

    // Webhook for registrations only
    private const REG_WEBHOOK = '{{Webhooks}}';

    /**
     * Send registration notices to the registrations-only webhook.
     *
     * @param User $user
     * @param bool $autocreated
     * @return bool
     */
    public static function onLocalUserCreated( $user, $autocreated ): bool {
        $name = $user->getName();
        $u    = rawurlencode( $name );

        $payload = [
            'username'   => 'Wiki updates',
            'avatar_url' => 'https://consumerrights.wiki/images/2/2b/Whlogo.webp',
            'content'    => "[{$name}](https://consumerrights.wiki/User:{$u}) "
                          . "([t](https://consumerrights.wiki/User_talk:{$u})|"
                          . "[c](https://consumerrights.wiki/Special:Contributions/{$u})) registered",
			'flags'      => 4,
        ];

        self::send( self::REG_WEBHOOK, $payload );
        return true;
    }

    private static function send(string $url, array $payload): void {
    $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => $json,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 10,
    ]);
    curl_exec($ch);
    curl_close($ch);
	}
}