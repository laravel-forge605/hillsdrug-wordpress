<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * YAAC adapter for this plugin's HTTP-only domain validation.
 */
class EHSSL_ACME_Client extends \Afosto\Acme\Client {

    public function authorize(\Afosto\Acme\Data\Order $order): array {
        $authorizations = array();

        foreach ($order->getAuthorizationURLs() as $authorization_url) {
            $response = $this->request(
                $authorization_url,
                $this->signPayloadKid(null, $authorization_url)
            );

            $data = json_decode((string) $response->getBody(), true);

            if (
                !is_array($data) || !isset($data['identifier']['value'], $data['expires'], $data['challenges'])
                || !is_array($data['challenges'])
            ) {
                throw new \RuntimeException('Invalid ACME authorization response.');
            }

            $authorization = new \Afosto\Acme\Data\Authorization(
                $data['identifier']['value'],
                $data['expires'],
                $this->getDigest()
            );

            foreach ($data['challenges'] as $challenge_data) {
                // New challenge types, such as dns-persist-01, need not have a token.
                if (($challenge_data['type'] ?? '') !== self::VALIDATION_HTTP) {
                    continue;
                }

                foreach (array('status', 'url', 'token') as $field) {
                    if (
                        !isset($challenge_data[$field]) || !is_string($challenge_data[$field])
                        || $challenge_data[$field] === ''
                    ) {
                        // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception text is data; the admin error renderer escapes it with esc_html().
                        throw new \RuntimeException('Invalid ACME HTTP-01 challenge: missing or invalid ' . $field . '.');
                    }
                }
                
                $authorization->addChallenge(new \Afosto\Acme\Data\Challenge(
                    $authorization_url,
                    self::VALIDATION_HTTP,
                    $challenge_data['status'],
                    $challenge_data['url'],
                    $challenge_data['token']
                ));
            }

            if ($authorization->getHttpChallenge() === false) {
                // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Exception text is data; the admin error renderer escapes it with esc_html().
                throw new \RuntimeException('The ACME server did not offer an HTTP-01 challenge for ' . $authorization->getDomain() . '.');
            }

            $authorizations[] = $authorization;
        }

        return $authorizations;
    }
}
