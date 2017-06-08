<?php declare(strict_types=1);

/**
 * @author  Vadim Bova <folour@gmail.com>
 * @link    https://bova.io
 */

namespace Folour\Regex;

/**
 * Simple abstraction for preg_* (match/match_all/replace/replace_callback/split)
 *
 * @package Folour\Regex
 */
class Regex
{
    /**
     * @var string
     */
    private $content;

    /**
     * Regex constructor.
     *
     * @param string $content subject string
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * Replace by regex and returns new instance with new content
     *
     * @param string $regex
     * @param string|\Closure $replacement
     * @return self
     * @throws \InvalidArgumentException
     */
    public function replace(string $regex, $replacement): self
    {
        if($replacement instanceof \Closure) {
            $content = preg_replace_callback(
                $regex, 
                $replacement,
                $this->getContent()
            );
        } else if(is_string($replacement)) {
            $content = preg_replace(
                $regex, 
                $replacement, 
                $this->getContent()
            );
        } else {
            throw new \InvalidArgumentException('Replacement must be an string or instance of \Closure');
        }

        return new static($content);
    }

    /**
     * Find one match
     *
     * @param string $regex
     * @return array
     */
    public function find(string $regex): array
    {
        if(preg_match($regex, $this->getContent(), $matches)) {
            return $this->rebuildMatches($matches);
        }

        return [];
    }

    /**
     * Find all matches
     *
     * @param string $regex
     * @return array
     */
    public function findAll(string $regex): array
    {
        if(preg_match_all($regex, $this->getContent(), $matches)) {
            return $this->rebuildMatches($matches);
        }

        return [];
    }

    /**
     * Split string by regex
     *
     * @param string $regex
     * @param int $limit
     * @return array
     */
    public function split(string $regex, int $limit = null): array
    {
        return preg_split($regex, $this->getContent(), $limit);
    }

    /**
     * Returns content
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getContent();
    }

    /**
     * Rebuild array with matches and remove duplicates for named captures
     *
     * @param array $matches
     * @return array
     */
    protected function rebuildMatches(array $matches): array
    {
        $prev_key = $prev_value = null;
        $return = [];

        foreach(array_slice($matches, 1) as $key => $value) {
            //Removing duplicates when using named capture
            if(
                   is_integer($key)
                && is_string($prev_key)
                && $prev_value === $value
            ) {
                continue;
            }

            if(is_array($value)) {
                foreach($value as $k => $v) {
                    $return[$k][$key] = ($v === '' ? null : $v);
                }
            } else {
                $return[$key] = ($value === '' ? null : $value);
            }

            $prev_key = $key;
            $prev_value = $value;
        }

        return $return;
    }

    /**
     * $content getter
     * 
     * @return string
     */
    protected function getContent(): string
    {
        return $this->content;
    }
}