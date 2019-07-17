<?php
namespace ARC\ProductConfigurator\Mask;

use RuntimeException;

class Mask
{
    const TOKEN_MATCHER = '/({{([A-Z]+)}})/i';

    /** @var string */
    private $mask;

    /**
     * Constructor.
     *
     * @param string $mask
     */
    public function __construct($mask)
    {
        $this->mask = $mask;
    }

    /**
     * Formats the mask with values
     *
     * @param array $values token=>value
     * @return string
     * @throws RuntimeException When mask doesn't have any tokens
     * @throws TokensMissingException When mask tokens are missing from selections
     */
    public function format(array $values) : string
    {
        $requiredTokens = $this->getTokens();
        if (array_diff($requiredTokens, array_keys($values))) {
            throw new TokensMissingException('Required tokens are missing from values.');
        }

        foreach ($requiredTokens as $token) {
            if (empty($values[$token])) {
                throw new TokensMissingException(sprintf('Token "%s" is empty.', $token));
            }
        }

        preg_match_all(self::TOKEN_MATCHER, $this->mask, $matches);

        return str_replace($matches[1], $values, $this->mask);
    }

    /**
     * Gets all tokens in this mask
     *
     * @return array
     */
    public function getTokens() : array
    {
        preg_match_all(self::TOKEN_MATCHER, $this->mask, $matches);

        if (empty($matches)) {
            throw new RuntimeException(sprintf('"%s" does not have any tokens.', $this->mask));
        }

        return $matches[2];
    }
}
