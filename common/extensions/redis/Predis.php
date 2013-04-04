<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Commands;

use Predis\Helpers;
use Predis\Iterators\MultiBulkResponseTuple;
use Predis\Distribution\INodeKeyGenerator;
use Predis\Iterators\MultiBulkResponse;

/**
 * Defines an abstraction representing a Redis command.
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ICommand
{
    /**
     * Gets the ID of a Redis command.
     *
     * @return string
     */
    public function getId();

    /**
     * Returns an hash of the command using the provided algorithm against the
     * key (used to calculate the distribution of keys with client-side sharding).
     *
     * @param INodeKeyGenerator $distributor Distribution algorithm.
     * @return int
     */
    public function getHash(INodeKeyGenerator $distributor);

    /**
     * Sets the arguments of the command.
     *
     * @param array $arguments List of arguments.
     */
    public function setArguments(Array $arguments);

    /**
     * Gets the arguments of the command.
     *
     * @return array
     */
    public function getArguments();

    /**
     * Parses a reply buffer and returns a PHP object.
     *
     * @param string $data Binary string containing the whole reply.
     * @return mixed
     */
    public function parseResponse($data);
}

/**
 * Base class for Redis commands.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class Command implements ICommand
{
    private $hash;
    private $arguments = array();

    /**
     * Returns a filtered array of the arguments.
     *
     * @param array $arguments List of arguments.
     * @return array
     */
    protected function filterArguments(Array $arguments)
    {
        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function setArguments(Array $arguments)
    {
        $this->arguments = $this->filterArguments($arguments);
        unset($this->hash);
    }

    /**
     * Sets the arguments array without filtering.
     *
     * @param array $arguments List of arguments.
     */
    public function setRawArguments(Array $arguments)
    {
        $this->arguments = $arguments;
        unset($this->hash);
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Gets the argument from the arguments list at the specified index.
     *
     * @param array $arguments Position of the argument.
     */
    public function getArgument($index = 0)
    {
        if (isset($this->arguments[$index]) === true) {
            return $this->arguments[$index];
        }
    }

    /**
     * Checks if the command can return an hash for client-side sharding.
     *
     * @return Boolean
     */
    protected function canBeHashed()
    {
        return isset($this->arguments[0]);
    }

    /**
     * Checks if the specified array of keys will generate the same hash.
     *
     * @param array $keys Array of keys.
     * @return Boolean
     */
    protected function checkSameHashForKeys(Array $keys)
    {
        if (($count = count($keys)) === 0) {
            return false;
        }

        $currentKey = Helpers::extractKeyTag($keys[0]);

        for ($i = 1; $i < $count; $i++) {
            $nextKey = Helpers::extractKeyTag($keys[$i]);
            if ($currentKey !== $nextKey) {
                return false;
            }
            $currentKey = $nextKey;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getHash(INodeKeyGenerator $distributor)
    {
        if (isset($this->hash)) {
            return $this->hash;
        }

        if ($this->canBeHashed()) {
            $key = Helpers::extractKeyTag($this->arguments[0]);
            $this->hash = $distributor->generateKey($key);

            return $this->hash;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return $data;
    }

    /**
     * Helper function used to reduce a list of arguments to a string.
     *
     * @param string $accumulator Temporary string.
     * @param string $argument Current argument.
     * @return string
     */
    protected function toStringArgumentReducer($accumulator, $argument)
    {
        if (strlen($argument) > 32) {
            $argument = substr($argument, 0, 32) . '[...]';
        }
        $accumulator .= " $argument";

        return $accumulator;
    }

    /**
     * Returns a partial string representation of the command with its arguments.
     *
     * @return string
     */
    public function __toString()
    {
        return array_reduce(
            $this->getArguments(),
            array($this, 'toStringArgumentReducer'),
            $this->getId()
        );
    }
}

/**
 * Defines a command whose keys can be prefixed.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IPrefixable
{
    /**
     * Prefixes all the keys found in the arguments of the command.
     *
     * @param string $prefix String used to prefix the keys.
     */
    public function prefixKeys($prefix);
}

/**
 * Base class for Redis commands with prefixable keys.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class PrefixableCommand extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        if ($arguments = $this->getArguments()) {
            $arguments[0] = "$prefix{$arguments[0]}";
            $this->setRawArguments($arguments);
        }
    }
}

/**
 * @link http://redis.io/commands/zrange
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetRange extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZRANGE';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        if (count($arguments) === 4) {
            $lastType = gettype($arguments[3]);

            if ($lastType === 'string' && strtolower($arguments[3]) === 'withscores') {
                // Used for compatibility with older versions
                $arguments[3] = array('WITHSCORES' => true);
                $lastType = 'array';
            }

            if ($lastType === 'array') {
                $options = $this->prepareOptions(array_pop($arguments));
                return array_merge($arguments, $options);
            }
        }

        return $arguments;
    }

    /**
     * Returns a list of options and modifiers compatible with Redis.
     *
     * @param array $options List of options.
     * @return array
     */
    protected function prepareOptions($options)
    {
        $opts = array_change_key_case($options, CASE_UPPER);
        $finalizedOpts = array();

        if (isset($opts['WITHSCORES'])) {
            $finalizedOpts[] = 'WITHSCORES';
        }

        return $finalizedOpts;
    }

    /**
     * Checks for the presence of the WITHSCORES modifier.
     *
     * @return Boolean
     */
    protected function withScores()
    {
        $arguments = $this->getArguments();

        if (count($arguments) < 4) {
            return false;
        }

        return strtoupper($arguments[3]) === 'WITHSCORES';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        if ($this->withScores()) {
            if ($data instanceof \Iterator) {
                return new MultiBulkResponseTuple($data);
            }

            $result = array();

            for ($i = 0; $i < count($data); $i++) {
                $result[] = array($data[$i], $data[++$i]);
            }

            return $result;
        }

        return $data;
    }
}

/**
 * @link http://redis.io/commands/sinterstore
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetIntersectionStore extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SINTERSTORE';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        if (count($arguments) === 2 && is_array($arguments[1])) {
            return array_merge(array($arguments[0]), $arguments[1]);
        }

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::all($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return $this->checkSameHashForKeys($this->getArguments());
    }
}

/**
 * @link http://redis.io/commands/eval
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerEval extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'EVAL';
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        $arguments = $this->getArguments();

        for ($i = 2; $i < $arguments[1] + 2; $i++) {
            $arguments[$i] = "$prefix{$arguments[$i]}";
        }

        $this->setRawArguments($arguments);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * Calculates the SHA1 hash of the body of the script.
     *
     * @return string SHA1 hash.
     */
    public function getScriptHash()
    {
        return sha1($this->getArgument(0));
    }
}

/**
 * @link http://redis.io/commands/sinter
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetIntersection extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SINTER';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        return Helpers::filterArrayArguments($arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::all($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return $this->checkSameHashForKeys($this->getArguments());
    }
}

/**
 * @link http://redis.io/commands/zrangebyscore
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetRangeByScore extends ZSetRange
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZRANGEBYSCORE';
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareOptions($options)
    {
        $opts = array_change_key_case($options, CASE_UPPER);
        $finalizedOpts = array();

        if (isset($opts['LIMIT']) && is_array($opts['LIMIT'])) {
            $limit = array_change_key_case($opts['LIMIT'], CASE_UPPER);

            $finalizedOpts[] = 'LIMIT';
            $finalizedOpts[] = isset($limit['OFFSET']) ? $limit['OFFSET'] : $limit[0];
            $finalizedOpts[] = isset($limit['COUNT']) ? $limit['COUNT'] : $limit[1];
        }

        return array_merge($finalizedOpts, parent::prepareOptions($options));
    }

    /**
     * {@inheritdoc}
     */
    protected function withScores()
    {
        $arguments = $this->getArguments();

        for ($i = 3; $i < count($arguments); $i++) {
            switch (strtoupper($arguments[$i])) {
                case 'WITHSCORES':
                    return true;

                case 'LIMIT':
                    $i += 2;
                    break;
            }
        }

        return false;
    }
}

/**
 * @link http://redis.io/commands/mset
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringSetMultiple extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'MSET';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        if (count($arguments) === 1 && is_array($arguments[0])) {
            $flattenedKVs = array();
            $args = $arguments[0];

            foreach ($args as $k => $v) {
                $flattenedKVs[] = $k;
                $flattenedKVs[] = $v;
            }

            return $flattenedKVs;
        }

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::interleaved($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        $args = $this->getArguments();
        $keys = array();

        for ($i = 0; $i < count($args); $i += 2) {
            $keys[] = $args[$i];
        }

        return $this->checkSameHashForKeys($keys);
    }
}

/**
 * @link http://redis.io/commands/setex
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringSetExpire extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SETEX';
    }
}

/**
 * @link http://redis.io/commands/rename
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyRename extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'RENAME';
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::all($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/expire
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyExpire extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'EXPIRE';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/expireat
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyExpireAt extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'EXPIREAT';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/subscribe
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PubSubSubscribe extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SUBSCRIBE';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        return Helpers::filterArrayArguments($arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::all($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/unsubscribe
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PubSubUnsubscribe extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'UNSUBSCRIBE';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        return Helpers::filterArrayArguments($arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::all($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/rpush
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListPushTail extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'RPUSH';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        return Helpers::filterVariadicValues($arguments);
    }
}

/**
 * @link http://redis.io/commands/zunionstore
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetUnionStore extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZUNIONSTORE';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        $options = array();
        $argc = count($arguments);

        if ($argc > 2 && is_array($arguments[$argc - 1])) {
            $options = $this->prepareOptions(array_pop($arguments));
        }

        if (is_array($arguments[1])) {
            $arguments = array_merge(
                array($arguments[0], count($arguments[1])),
                $arguments[1]
            );
        }

        return array_merge($arguments, $options);
    }

    /**
     * Returns a list of options and modifiers compatible with Redis.
     *
     * @param array $options List of options.
     * @return array
     */
    private function prepareOptions($options)
    {
        $opts = array_change_key_case($options, CASE_UPPER);
        $finalizedOpts = array();

        if (isset($opts['WEIGHTS']) && is_array($opts['WEIGHTS'])) {
            $finalizedOpts[] = 'WEIGHTS';
            foreach ($opts['WEIGHTS'] as $weight) {
                $finalizedOpts[] = $weight;
            }
        }

        if (isset($opts['AGGREGATE'])) {
            $finalizedOpts[] = 'AGGREGATE';
            $finalizedOpts[] = $opts['AGGREGATE'];
        }

        return $finalizedOpts;
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        $arguments = $this->getArguments();

        $arguments[0] = "$prefix{$arguments[0]}";
        $length = ((int) $arguments[1]) + 2;

        for ($i = 2; $i < $length; $i++) {
            $arguments[$i] = "$prefix{$arguments[$i]}";
        }

        $this->setRawArguments($arguments);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        $args = $this->getArguments();

        return $this->checkSameHashForKeys(
            array_merge(array($args[0]), array_slice($args, 2, $args[1]))
        );
    }
}

/**
 * @link http://redis.io/commands/ttl
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyTimeToLive extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'TTL';
    }
}

/**
 * @link http://redis.io/commands/keys
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyKeys extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'KEYS';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/info
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerInfo extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'INFO';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        $info      = array();
        $infoLines = preg_split('/\r?\n/', $data);

        foreach ($infoLines as $row) {
            @list($k, $v) = explode(':', $row);

            if ($row === '' || !isset($v)) {
                continue;
            }

            if (!preg_match('/^db\d+$/', $k)) {
                if ($k === 'allocation_stats') {
                    $info[$k] = $this->parseAllocationStats($v);
                    continue;
                }
                $info[$k] = $v;
            }
            else {
                $info[$k] = $this->parseDatabaseStats($v);
            }
        }

        return $info;
    }

    /**
     * Parses the reply buffer and extracts the statistics of each logical DB.
     *
     * @param string $str Reply buffer.
     * @return array
     */
    protected function parseDatabaseStats($str)
    {
        $db = array();

        foreach (explode(',', $str) as $dbvar) {
            list($dbvk, $dbvv) = explode('=', $dbvar);
            $db[trim($dbvk)] = $dbvv;
        }

        return $db;
    }

    /**
     * Parses the reply buffer and extracts the allocation statistics.
     *
     * @param string $str Reply buffer.
     * @return array
     */
    protected function parseAllocationStats($str)
    {
        $stats = array();

        foreach (explode(',', $str) as $kv) {
            @list($size, $objects, $extra) = explode('=', $kv);

            // hack to prevent incorrect values when parsing the >=256 key
            if (isset($extra)) {
                $size = ">=$objects";
                $objects = $extra;
            }
            $stats[$size] = $objects;
        }

        return $stats;
    }
}

/**
 * @link http://redis.io/commands/blpop
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListPopFirstBlocking extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'BLPOP';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        if (count($arguments) === 2 && is_array($arguments[0])) {
            list($arguments, $timeout) = $arguments;
            array_push($arguments, $timeout);
        }
        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::skipLast($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return $this->checkSameHashForKeys(
            array_slice(($args = $this->getArguments()), 0, count($args) - 1)
        );
    }
}

/**
 * @link http://redis.io/commands/zremrangebyscore
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetRemoveRangeByScore extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZREMRANGEBYSCORE';
    }
}

/**
 * @link http://redis.io/commands/publish
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PubSubPublish extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'PUBLISH';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/hkeys
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashKeys extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HKEYS';
    }
}

/**
 * @link http://redis.io/commands/sismember
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetIsMember extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SISMEMBER';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/renamenx
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyRenamePreserve extends KeyRename
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'RENAMENX';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/sdiffstore
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetDifferenceStore extends SetIntersectionStore
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SDIFFSTORE';
    }
}

/**
 * @link http://redis.io/commands/zinterstore
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetIntersectionStore extends ZSetUnionStore
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZINTERSTORE';
    }
}

/**
 * @link http://redis.io/commands/lpush
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListPushHead extends ListPushTail
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'LPUSH';
    }
}

/**
 * @link http://redis.io/commands/lrem
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListRemove extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'LREM';
    }
}

/**
 * @link http://redis.io/commands/setrange
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringSetRange extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SETRANGE';
    }
}

/**
 * @link http://redis.io/commands/append
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringAppend extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'APPEND';
    }
}

/**
 * @link http://redis.io/commands/set
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringSet extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SET';
    }
}

/**
 * @link http://redis.io/commands/decrby
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringDecrementBy extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'DECRBY';
    }
}

/**
 * @link http://redis.io/commands/hgetall
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashGetAll extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HGETALL';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        if ($data instanceof \Iterator) {
            return new MultiBulkResponseTuple($data);
        }

        $result = array();
        for ($i = 0; $i < count($data); $i++) {
            $result[$data[$i]] = $data[++$i];
        }

        return $result;
    }
}

/**
 * @link http://redis.io/commands/hincrbyfloat
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashIncrementByFloat extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HINCRBYFLOAT';
    }
}

/**
 * @link http://redis.io/commands/lrange
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListRange extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'LRANGE';
    }
}

/**
 * @link http://redis.io/commands/lpop
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListPopFirst extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'LPOP';
    }
}

/**
 * @link http://redis.io/commands/srandmember
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetRandomMember extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SRANDMEMBER';
    }
}

/**
 * @link http://redis.io/commands/incrby
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringIncrementBy extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'INCRBY';
    }
}

/**
 * @link http://redis.io/commands/info
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerInfoV26x extends ServerInfo
{
    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        $info = array();
        $current = null;
        $infoLines = preg_split('/\r?\n/', $data);

        if (isset($infoLines[0]) && $infoLines[0][0] !== '#') {
            return parent::parseResponse($data);
        }

        foreach ($infoLines as $row) {
            if ($row === '') {
                continue;
            }

            if (preg_match('/^# (\w+)$/', $row, $matches)) {
                $info[$matches[1]] = array();
                $current = &$info[$matches[1]];
                continue;
            }

            list($k, $v) = explode(':', $row);

            if (!preg_match('/^db\d+$/', $k)) {
                if ($k === 'allocation_stats') {
                    $current[$k] = $this->parseAllocationStats($v);
                    continue;
                }
                $current[$k] = $v;
            }
            else {
                $current[$k] = $this->parseDatabaseStats($v);
            }
        }

        return $info;
    }
}

/**
 * @link http://redis.io/commands/zscore
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetScore extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZSCORE';
    }
}

/**
 * @link http://redis.io/commands/smove
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetMove extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SMOVE';
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::skipLast($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/slaveof
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerSlaveOf extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SLAVEOF';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        if (count($arguments) === 0 || $arguments[0] === 'NO ONE') {
            return array('NO', 'ONE');
        }

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/rpushx
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListPushTailX extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'RPUSHX';
    }
}

/**
 * @link http://redis.io/commands/zrem
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetRemove extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZREM';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        return Helpers::filterVariadicValues($arguments);
    }
}

/**
 * @link http://redis.io/commands/brpop
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListPopLastBlocking extends ListPopFirstBlocking
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'BRPOP';
    }
}

/**
 * @link http://redis.io/commands/hsetnx
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashSetPreserve extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HSETNX';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/getbit
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringGetBit extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'GETBIT';
    }
}

/**
 * @link http://redis.io/commands/zrevrange
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetReverseRange extends ZSetRange
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZREVRANGE';
    }
}

/**
 * @link http://redis.io/commands/strlen
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringStrlen extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'STRLEN';
    }
}

/**
 * @link http://redis.io/commands/substr
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringSubstr extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SUBSTR';
    }
}

/**
 * @link http://redis.io/commands/client
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerClient extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'CLIENT';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        $args = array_change_key_case($this->getArguments(), CASE_UPPER);
        switch (strtoupper($args[0])) {
            case 'LIST':
                return $this->parseClientList($data);

            case 'KILL':
            default:
                return $data;
        }
    }

    /**
     * Parses the reply buffer and returns the list of clients returned by
     * the CLIENT LIST command.
     *
     * @param string $data Reply buffer
     * @return array
     */
    protected function parseClientList($data)
    {
        $clients = array();

        foreach (explode("\n", $data, -1) as $clientData) {
            $client = array();
            foreach (explode(' ', $clientData) as $kv) {
                @list($k, $v) = explode('=', $kv);
                $client[$k] = $v;
            }
            $clients[] = $client;
        }

        return $clients;
    }
}

/**
 * @link http://redis.io/commands/incr
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringIncrement extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'INCR';
    }
}

/**
 * @link http://redis.io/commands/hvals
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashValues extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HVALS';
    }
}

/**
 * @link http://redis.io/commands/hmset
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashSetMultiple extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HMSET';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        if (count($arguments) === 2 && is_array($arguments[1])) {
            $flattenedKVs = array($arguments[0]);
            $args = $arguments[1];

            foreach ($args as $k => $v) {
                $flattenedKVs[] = $k;
                $flattenedKVs[] = $v;
            }

            return $flattenedKVs;
        }

        return $arguments;
    }
}

/**
 * @link http://redis.io/commands/select
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ConnectionSelect extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SELECT';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * Base class used to implement an higher level abstraction for "virtual"
 * commands based on EVAL.
 *
 * @link http://redis.io/commands/eval
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class ScriptedCommand extends ServerEval
{
    /**
     * Gets the body of a Lua script.
     *
     * @return string
     */
    public abstract function getScript();

    /**
     * Specifies the number of arguments that should be considered as keys.
     *
     * The default behaviour for the base class is to return FALSE to indicate that
     * all the elements of the arguments array should be considered as keys, but
     * subclasses can enforce a static number of keys.
     *
     * @todo How about returning 1 by default to make scripted commands act like
     *       variadic ones where the first argument is the key (KEYS[1]) and the
     *       rest are values (ARGV)?
     *
     * @return int|Boolean
     */
    protected function getKeysCount()
    {
        return false;
    }

    /**
     * Returns the elements from the arguments that are identified as keys.
     *
     * @return array
     */
    public function getKeys()
    {
        return array_slice($this->getArguments(), 2, $this->getKeysCount());
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        $header = array($this->getScript(), ($keys = $this->getKeysCount()) !== false ? $keys : count($arguments));

        return array_merge($header, $arguments);
    }
}

/**
 * @link http://redis.io/commands/decr
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringDecrement extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'DECR';
    }
}

/**
 * @link http://redis.io/commands/getrange
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringGetRange extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'GETRANGE';
    }
}

/**
 * @link http://redis.io/commands/persist
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyPersist extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'PERSIST';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/srem
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetRemove extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SREM';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        return Helpers::filterVariadicValues($arguments);
    }
}

/**
 * @link http://redis.io/commands/msetnx
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringSetMultiplePreserve extends StringSetMultiple
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'MSETNX';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/unwatch
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class TransactionUnwatch extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'UNWATCH';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/watch
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class TransactionWatch extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'WATCH';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        if (isset($arguments[0]) && is_array($arguments[0])) {
            return $arguments[0];
        }

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::all($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/pttl
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyPreciseTimeToLive extends KeyTimeToLive
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'PTTL';
    }
}

/**
 * @link http://redis.io/commands/sort
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeySort extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SORT';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        if (count($arguments) === 1) {
            return $arguments;
        }

        $query = array($arguments[0]);
        $sortParams = array_change_key_case($arguments[1], CASE_UPPER);

        if (isset($sortParams['BY'])) {
            $query[] = 'BY';
            $query[] = $sortParams['BY'];
        }

        if (isset($sortParams['GET'])) {
            $getargs = $sortParams['GET'];
            if (is_array($getargs)) {
                foreach ($getargs as $getarg) {
                    $query[] = 'GET';
                    $query[] = $getarg;
                }
            }
            else {
                $query[] = 'GET';
                $query[] = $getargs;
            }
        }

        if (isset($sortParams['LIMIT']) && is_array($sortParams['LIMIT'])
            && count($sortParams['LIMIT']) == 2) {

            $query[] = 'LIMIT';
            $query[] = $sortParams['LIMIT'][0];
            $query[] = $sortParams['LIMIT'][1];
        }

        if (isset($sortParams['SORT'])) {
            $query[] = strtoupper($sortParams['SORT']);
        }

        if (isset($sortParams['ALPHA']) && $sortParams['ALPHA'] == true) {
            $query[] = 'ALPHA';
        }

        if (isset($sortParams['STORE'])) {
            $query[] = 'STORE';
            $query[] = $sortParams['STORE'];
        }

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        $arguments = $this->getArguments();
        $arguments[0] = "$prefix{$arguments[0]}";

        if (($count = count($arguments)) > 1) {
            for ($i = 1; $i < $count; $i++) {
                switch ($arguments[$i]) {
                    case 'BY':
                    case 'STORE':
                        $arguments[$i] = "$prefix{$arguments[++$i]}";
                        break;

                    case 'GET':
                        $value = $arguments[++$i];
                        if ($value !== '#') {
                            $arguments[$i] = "$prefix$value";
                        }
                        break;

                    case 'LIMIT';
                        $i += 2;
                        break;
                }
            }
        }

        $this->setRawArguments($arguments);
    }
}

/**
 * @link http://redis.io/commands/del
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyDelete extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'DEL';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        return Helpers::filterArrayArguments($arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::all($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        $args = $this->getArguments();
        if (count($args) === 1) {
            return true;
        }

        return $this->checkSameHashForKeys($args);
    }
}

/**
 * @link http://redis.io/commands/dbsize
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerDatabaseSize extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'DBSIZE';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/exists
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyExists extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'EXISTS';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/bgsave
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerBackgroundSave extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'BGSAVE';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        if ($data == 'Background saving started') {
            return true;
        }

        return $data;
    }
}

/**
 * @link http://redis.io/commands/smembers
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetMembers extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SMEMBERS';
    }
}

/**
 * @link http://redis.io/commands/monitor
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerMonitor extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'MONITOR';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/move
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyMove extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'MOVE';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/save
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerSave extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SAVE';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/shutdown
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerShutdown extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SHUTDOWN';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/hexists
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashExists extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HEXISTS';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/exec
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class TransactionExec extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'EXEC';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/ltrim
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListTrim extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'LTRIM';
    }
}

/**
 * @link http://redis.io/commands/evalsha
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerEvalSHA extends ServerEval
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'EVALSHA';
    }
}

/**
 * @link http://redis.io/commands/rpop
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListPopLast extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'RPOP';
    }
}

/**
 * @link http://redis.io/commands/spop
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetPop extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SPOP';
    }
}

/**
 * @link http://redis.io/commands/lastsave
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerLastSave extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'LASTSAVE';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/lpushx
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListPushHeadX extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'LPUSHX';
    }
}

/**
 * @link http://redis.io/commands/discard
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class TransactionDiscard extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'DISCARD';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/script
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerScript extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SCRIPT';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/hlen
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashLength extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HLEN';
    }
}

/**
 * @link http://redis.io/commands/getset
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringGetSet extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'GETSET';
    }
}

/**
 * Class that defines a few helpers method for prefixing keys.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PrefixHelpers
{
    /**
     * Applies the specified prefix only the first argument.
     *
     * @param ICommand $command Command instance.
     * @param string $prefix Prefix string.
     */
    public static function first(ICommand $command, $prefix)
    {
        if ($arguments = $command->getArguments()) {
            $arguments[0] = "$prefix{$arguments[0]}";
            $command->setRawArguments($arguments);
        }
    }

    /**
     * Applies the specified prefix to all the arguments.
     *
     * @param ICommand $command Command instance.
     * @param string $prefix Prefix string.
     */
    public static function all(ICommand $command, $prefix)
    {
        $arguments = $command->getArguments();

        foreach ($arguments as &$key) {
            $key = "$prefix$key";
        }

        $command->setRawArguments($arguments);
    }

    /**
     * Applies the specified prefix only to even arguments in the list.
     *
     * @param ICommand $command Command instance.
     * @param string $prefix Prefix string.
     */
    public static function interleaved(ICommand $command, $prefix)
    {
        $arguments = $command->getArguments();
        $length = count($arguments);

        for ($i = 0; $i < $length; $i += 2) {
            $arguments[$i] = "$prefix{$arguments[$i]}";
        }

        $command->setRawArguments($arguments);
    }

    /**
     * Applies the specified prefix to all the arguments but the last one.
     *
     * @param ICommand $command Command instance.
     * @param string $prefix Prefix string.
     */
    public static function skipLast(ICommand $command, $prefix)
    {
        $arguments = $command->getArguments();
        $length = count($arguments);

        for ($i = 0; $i < $length - 1; $i++) {
            $arguments[$i] = "$prefix{$arguments[$i]}";
        }

        $command->setRawArguments($arguments);
    }
}

/**
 * @link http://redis.io/commands/zrank
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetRank extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZRANK';
    }
}

/**
 * @link http://redis.io/commands/randomkey
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyRandom extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'RANDOMKEY';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return $data !== '' ? $data : null;
    }
}

/**
 * @link http://redis.io/commands/time
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerTime extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'TIME';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return $data instanceof \Iterator ? iterator_to_array($data) : $data;
    }
}

/**
 * @link http://redis.io/commands/get
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringGet extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'GET';
    }
}

/**
 * @link http://redis.io/commands/hmget
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashGetMultiple extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HMGET';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        return Helpers::filterVariadicValues($arguments);
    }
}

/**
 * @link http://redis.io/commands/setbit
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringSetBit extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SETBIT';
    }
}

/**
 * @link http://redis.io/commands/rpoplpush
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListPopLastPushHead extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'RPOPLPUSH';
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::all($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return $this->checkSameHashForKeys($this->getArguments());
    }
}

/**
 * @link http://redis.io/commands/lset
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListSet extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'LSET';
    }
}

/**
 * @link http://redis.io/commands/auth
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ConnectionAuth extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'AUTH';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/sdiff
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetDifference extends SetIntersection
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SDIFF';
    }
}

/**
 * @link http://redis.io/commands/config-set
 * @link http://redis.io/commands/config-get
 * @link http://redis.io/commands/config-resetstat
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerConfig extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'CONFIG';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        if ($data instanceof \Iterator) {
            return new MultiBulkResponseTuple($data);
        }

        if (is_array($data)) {
            $result = array();
            for ($i = 0; $i < count($data); $i++) {
                $result[$data[$i]] = $data[++$i];
            }

            return $result;
        }

        return $data;
    }
}

/**
 * @link http://redis.io/commands/object
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerObject extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'OBJECT';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/zcard
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetCardinality extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZCARD';
    }
}

/**
 * @link http://redis.io/commands/zadd
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetAdd extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZADD';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        if (count($arguments) === 2 && is_array($arguments[1])) {
            $flattened = array($arguments[0]);
            foreach($arguments[1] as $member => $score) {
                $flattened[] = $score;
                $flattened[] = $member;
            }

            return $flattened;
        }

        return $arguments;
    }
}

/**
 * @link http://redis.io/commands/sunion
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetUnion extends SetIntersection
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SUNION';
    }
}

/**
 * @link http://redis.io/commands/psubscribe
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PubSubSubscribeByPattern extends PubSubSubscribe
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'PSUBSCRIBE';
    }
}

/**
 * @link http://redis.io/commands/psetex
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringPreciseSetExpire extends StringSetExpire
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'PSETEX';
    }
}

/**
 * @link http://redis.io/commands/mget
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringGetMultiple extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'MGET';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        return Helpers::filterArrayArguments($arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::all($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return $this->checkSameHashForKeys($this->getArguments());
    }
}

/**
 * @link http://redis.io/commands/lindex
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListIndex extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'LINDEX';
    }
}

/**
 * @link http://redis.io/commands/sadd
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetAdd extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SADD';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        return Helpers::filterVariadicValues($arguments);
    }
}

/**
 * @link http://redis.io/commands/punsubscribe
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PubSubUnsubscribeByPattern extends PubSubUnsubscribe
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'PUNSUBSCRIBE';
    }
}

/**
 * @link http://redis.io/commands/brpoplpush
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListPopLastPushHeadBlocking extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'BRPOPLPUSH';
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::skipLast($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return $this->checkSameHashForKeys(
            array_slice($args = $this->getArguments(), 0, count($args) - 1)
        );
    }
}

/**
 * @link http://redis.io/commands/pexpire
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyPreciseExpire extends KeyExpire
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'PEXPIRE';
    }
}

/**
 * @link http://redis.io/commands/type
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyType extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'TYPE';
    }
}

/**
 * @link http://redis.io/commands/hget
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashGet extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HGET';
    }
}

/**
 * @link http://redis.io/commands/ping
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ConnectionPing extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'PING';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return $data === 'PONG' ? true : false;
    }
}

/**
 * @link http://redis.io/commands/llen
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListLength extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'LLEN';
    }
}

/**
 * @link http://redis.io/commands/sunionstore
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetUnionStore extends SetIntersectionStore
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SUNIONSTORE';
    }
}

/**
 * @link http://redis.io/commands/quit
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ConnectionQuit extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'QUIT';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/scard
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SetCardinality extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SCARD';
    }
}

/**
 * @link http://redis.io/commands/linsert
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ListInsert extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'LINSERT';
    }
}

/**
 * @link http://redis.io/commands/hdel
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashDelete extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HDEL';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        return Helpers::filterVariadicValues($arguments);
    }
}

/**
 * @link http://redis.io/commands/flushdb
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerFlushDatabase extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'FLUSHDB';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/slowlog
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerSlowlog extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SLOWLOG';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        if (($iterable = $data instanceof \Iterator) || is_array($data)) {
            // NOTE: we consume iterable multibulk replies inplace since it is not
            // possible to do anything fancy on sub-elements.
            $log = array();

            foreach ($data as $index => $entry) {
                if ($iterable) {
                    $entry = iterator_to_array($entry);
                }

                $log[$index] = array(
                    'id' => $entry[0],
                    'timestamp' => $entry[1],
                    'duration' => $entry[2],
                    'command' => $iterable ? iterator_to_array($entry[3]) : $entry[3],
                );
            }

            if ($iterable === true) {
                unset($data);
            }

            return $log;
        }

        return $data;
    }
}

/**
 * @link http://redis.io/commands/bgrewriteaof
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerBackgroundRewriteAOF extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'BGREWRITEAOF';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return $data == 'Background append only file rewriting started';
    }
}

/**
 * @link http://redis.io/commands/zrevrangebyscore
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetReverseRangeByScore extends ZSetRangeByScore
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZREVRANGEBYSCORE';
    }
}

/**
 * @link http://redis.io/commands/hset
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashSet extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HSET';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/**
 * @link http://redis.io/commands/multi
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class TransactionMulti extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'MULTI';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/pexpireat
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyPreciseExpireAt extends KeyExpireAt
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'PEXPIREAT';
    }
}

/**
 * @link http://redis.io/commands/zincrby
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetIncrementBy extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZINCRBY';
    }
}

/**
 * @link http://redis.io/commands/incrbyfloat
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringIncrementByFloat extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'INCRBYFLOAT';
    }
}

/**
 * @link http://redis.io/commands/flushall
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerFlushAll extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'FLUSHALL';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/keys
 * @author Daniele Alessandri <suppakilla@gmail.com>
 * @deprecated
 */
class KeyKeysV12x extends KeyKeys
{
    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return explode(' ', $data);
    }
}

/**
 * @link http://redis.io/commands/hincrby
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class HashIncrementBy extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'HINCRBY';
    }
}

/**
 * @link http://redis.io/commands/zcount
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetCount extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZCOUNT';
    }
}

/**
 * @link http://redis.io/commands/echo
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ConnectionEcho extends Command
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ECHO';
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return false;
    }
}

/**
 * @link http://redis.io/commands/zremrangebyrank
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetRemoveRangeByRank extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZREMRANGEBYRANK';
    }
}

/**
 * @link http://redis.io/commands/zrevrank
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ZSetReverseRank extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'ZREVRANK';
    }
}

/**
 * @link http://redis.io/commands/setnx
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringSetPreserve extends PrefixableCommand
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'SETNX';
    }

    /**
     * {@inheritdoc}
     */
    public function parseResponse($data)
    {
        return (bool) $data;
    }
}

/* --------------------------------------------------------------------------- */

namespace Predis\Network;

use Predis\Commands\ICommand;
use Predis\Distribution\IDistributionStrategy;
use Predis\Helpers;
use Predis\ClientException;
use Predis\NotSupportedException;
use Predis\Distribution\HashRing;
use Predis\IReplyObject;
use Predis\IConnectionParameters;
use Predis\Protocol\ProtocolException;
use Predis\Protocol\IProtocolProcessor;
use Predis\Protocol\Text\TextProtocol;
use Predis\ResponseError;
use Predis\ResponseQueued;
use Predis\ServerException;
use Predis\Iterators\MultiBulkResponseSimple;
use Predis\Network\ConnectionException;
use Predis\CommunicationException;

/**
 * Defines a connection object used to communicate with one or multiple
 * Redis servers.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IConnection
{
    /**
     * Opens the connection.
     */
    public function connect();

    /**
     * Closes the connection.
     */
    public function disconnect();

    /**
     * Returns if the connection is open.
     *
     * @return Boolean
     */
    public function isConnected();

    /**
     * Write a Redis command on the connection.
     *
     * @param ICommand $command Instance of a Redis command.
     */
    public function writeCommand(ICommand $command);

    /**
     * Reads the reply for a Redis command from the connection.
     *
     * @param ICommand $command Instance of a Redis command.
     * @return mixed
     */
    public function readResponse(ICommand $command);

    /**
     * Writes a Redis command to the connection and reads back the reply.
     *
     * @param ICommand $command Instance of a Redis command.
     * @return mixed
     */
    public function executeCommand(ICommand $command);
}

/**
 * Defines a connection object used to communicate with a single Redis server.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IConnectionSingle extends IConnection
{
    /**
     * Returns a string representation of the connection.
     *
     * @return string
     */
    public function __toString();

    /**
     * Returns the underlying resource used to communicate with a Redis server.
     *
     * @return mixed
     */
    public function getResource();

    /**
     * Gets the parameters used to initialize the connection object.
     *
     * @return IConnectionParameters
     */
    public function getParameters();

    /**
     * Pushes the instance of a Redis command to the queue of commands executed
     * when the actual connection to a server is estabilished.
     *
     * @param ICommand $command Instance of a Redis command.
     * @return IConnectionParameters
     */
    public function pushInitCommand(ICommand $command);

    /**
     * Reads a reply from the server.
     *
     * @return mixed
     */
    public function read();
}

/**
 * Base class with the common logic used by connection classes to communicate with Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class ConnectionBase implements IConnectionSingle
{
    private $resource;
    private $cachedId;

    protected $parameters;
    protected $initCmds;

    /**
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     */
    public function __construct(IConnectionParameters $parameters)
    {
        $this->initCmds = array();
        $this->parameters = $this->checkParameters($parameters);
        $this->initializeProtocol($parameters);
    }

    /**
     * Disconnects from the server and destroys the underlying resource when
     * PHP's garbage collector kicks in.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Checks some of the parameters used to initialize the connection.
     *
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     */
    protected function checkParameters(IConnectionParameters $parameters)
    {
        switch ($parameters->scheme) {
            case 'unix':
                if (!isset($parameters->path)) {
                    throw new \InvalidArgumentException('Missing UNIX domain socket path');
                }

            case 'tcp':
                return $parameters;

            default:
                throw new \InvalidArgumentException("Invalid scheme: {$parameters->scheme}");
        }
    }

    /**
     * Initializes some common configurations of the underlying protocol processor
     * from the connection parameters.
     *
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     */
    protected function initializeProtocol(IConnectionParameters $parameters)
    {
        // NOOP
    }

    /**
     * Creates the underlying resource used to communicate with Redis.
     *
     * @return mixed
     */
    protected abstract function createResource();

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        return isset($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        if ($this->isConnected()) {
            throw new ClientException('Connection already estabilished');
        }
        $this->resource = $this->createResource();
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        unset($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function pushInitCommand(ICommand $command)
    {
        $this->initCmds[] = $command;
    }

    /**
     * {@inheritdoc}
     */
    public function executeCommand(ICommand $command)
    {
        $this->writeCommand($command);
        return $this->readResponse($command);
    }

    /**
     * {@inheritdoc}
     */
    public function readResponse(ICommand $command)
    {
        $reply = $this->read();

        if ($reply instanceof IReplyObject) {
            return $reply;
        }

        return $command->parseResponse($reply);
    }

    /**
     * Helper method to handle connection errors.
     *
     * @param string $message Error message.
     * @param int $code Error code.
     */
    protected function onConnectionError($message, $code = null)
    {
        Helpers::onCommunicationException(new ConnectionException($this, $message, $code));
    }

    /**
     * Helper method to handle protocol errors.
     *
     * @param string $message Error message.
     */
    protected function onProtocolError($message)
    {
        Helpers::onCommunicationException(new ProtocolException($this, $message));
    }

    /**
     * Helper method to handle not supported connection parameters.
     *
     * @param string $option Name of the option.
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     */
    protected function onInvalidOption($option, $parameters = null)
    {
        $message = "Invalid option: $option";
        if (isset($parameters)) {
            $message .= " [$parameters]";
        }

        throw new NotSupportedException($message);
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        if (isset($this->resource)) {
            return $this->resource;
        }

        $this->connect();

        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Gets an identifier for the connection.
     *
     * @return string
     */
    protected function getIdentifier()
    {
        if ($this->parameters->scheme === 'unix') {
            return $this->parameters->path;
        }

        return "{$this->parameters->host}:{$this->parameters->port}";
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if (!isset($this->cachedId)) {
            $this->cachedId = $this->getIdentifier();
        }

        return $this->cachedId;
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return array('parameters', 'initCmds');
    }
}

/**
 * Defines a connection object used to communicate with a single Redis server
 * that leverages an external protocol processor to handle pluggable protocol
 * handlers.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IConnectionComposable extends IConnectionSingle
{
    /**
     * Sets the protocol processor used by the connection.
     *
     * @param IProtocolProcessor $protocol Protocol processor.
     */
    public function setProtocol(IProtocolProcessor $protocol);

    /**
     * Gets the protocol processor used by the connection.
     */
    public function getProtocol();

    /**
     * Writes a buffer that contains a serialized Redis command.
     *
     * @param string $buffer Serialized Redis command.
     */
    public function writeBytes($buffer);

    /**
     * Reads a specified number of bytes from the connection.
     *
     * @param string
     */
    public function readBytes($length);

    /**
     * Reads a line from the connection.
     *
     * @param string
     */
    public function readLine();
}

/**
 * Defines a group of Redis servers in a master/slave replication configuration.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IConnectionReplication extends IConnection
{
    /**
     * Adds a connection instance to the cluster.
     *
     * @param IConnectionSingle $connection Instance of a connection.
     */
    public function add(IConnectionSingle $connection);

    /**
     * Removes the specified connection instance from the cluster.
     *
     * @param IConnectionSingle $connection Instance of a connection.
     * @return Boolean Returns true if the connection was in the pool.
     */
    public function remove(IConnectionSingle $connection);

    /**
     * Gets the actual connection instance in charge of the specified command.
     *
     * @param ICommand $command Instance of a Redis command.
     * @return IConnectionSingle
     */
    public function getConnection(ICommand $command);

    /**
     * Retrieves a connection instance from the cluster using an alias.
     *
     * @param string $connectionId Alias of a connection
     * @return IConnectionSingle
     */
    public function getConnectionById($connectionId);

    /**
     * Switches the internal connection object being used.
     *
     * @param string $connection Alias of a connection
     */
    public function switchTo($connection);

    /**
     * Retrieves the connection object currently being used.
     *
     * @return IConnectionSingle
     */
    public function getCurrent();

    /**
     * Retrieves the connection object to the master Redis server.
     *
     * @return IConnectionSingle
     */
    public function getMaster();

    /**
     * Retrieves a list of connection objects to slaves Redis servers.
     *
     * @return IConnectionSingle
     */
    public function getSlaves();
}

/**
 * Connection abstraction to Redis servers based on PHP's streams.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StreamConnection extends ConnectionBase
{
    private $mbiterable;
    private $throwErrors;

    /**
     * Disconnects from the server and destroys the underlying resource when
     * PHP's garbage collector kicks in only if the connection has not been
     * marked as persistent.
     */
    public function __destruct()
    {
        if (!$this->parameters->connection_persistent) {
            $this->disconnect();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeProtocol(IConnectionParameters $parameters)
    {
        $this->throwErrors = $parameters->throw_errors;
        $this->mbiterable = $parameters->iterable_multibulk;
    }

    /**
     * {@inheritdoc}
     */
    protected function createResource()
    {
        $parameters = $this->parameters;
        $initializer = "{$parameters->scheme}StreamInitializer";

        return $this->$initializer($parameters);
    }

    /**
     * Initializes a TCP stream resource.
     *
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     * @return resource
     */
    private function tcpStreamInitializer(IConnectionParameters $parameters)
    {
        $uri = "tcp://{$parameters->host}:{$parameters->port}/";

        $flags = STREAM_CLIENT_CONNECT;
        if ($parameters->connection_async) {
            $flags |= STREAM_CLIENT_ASYNC_CONNECT;
        }
        if ($parameters->connection_persistent) {
            $flags |= STREAM_CLIENT_PERSISTENT;
        }

        $resource = @stream_socket_client(
            $uri, $errno, $errstr, $parameters->connection_timeout, $flags
        );

        if (!$resource) {
            $this->onConnectionError(trim($errstr), $errno);
        }

        if (isset($parameters->read_write_timeout)) {
            $rwtimeout = $parameters->read_write_timeout;
            $rwtimeout = $rwtimeout > 0 ? $rwtimeout : -1;
            $timeoutSeconds  = floor($rwtimeout);
            $timeoutUSeconds = ($rwtimeout - $timeoutSeconds) * 1000000;
            stream_set_timeout($resource, $timeoutSeconds, $timeoutUSeconds);
        }

        return $resource;
    }

    /**
     * Initializes a UNIX stream resource.
     *
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     * @return resource
     */
    private function unixStreamInitializer(IConnectionParameters $parameters)
    {
        $uri = "unix://{$parameters->path}";

        $flags = STREAM_CLIENT_CONNECT;
        if ($parameters->connection_persistent) {
            $flags |= STREAM_CLIENT_PERSISTENT;
        }

        $resource = @stream_socket_client(
            $uri, $errno, $errstr, $parameters->connection_timeout, $flags
        );

        if (!$resource) {
            $this->onConnectionError(trim($errstr), $errno);
        }

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        parent::connect();

        if (count($this->initCmds) > 0){
            $this->sendInitializationCommands();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        if ($this->isConnected()) {
            fclose($this->getResource());

            parent::disconnect();
        }
    }

    /**
     * Sends the initialization commands to Redis when the connection is opened.
     */
    private function sendInitializationCommands()
    {
        foreach ($this->initCmds as $command) {
            $this->writeCommand($command);
        }
        foreach ($this->initCmds as $command) {
            $this->readResponse($command);
        }
    }

    /**
     * Performs a write operation on the stream of the buffer containing a
     * command serialized with the Redis wire protocol.
     *
     * @param string $buffer Redis wire protocol representation of a command.
     */
    protected function writeBytes($buffer)
    {
        $socket = $this->getResource();

        while (($length = strlen($buffer)) > 0) {
            $written = fwrite($socket, $buffer);
            if ($length === $written) {
                return;
            }
            if ($written === false || $written === 0) {
                $this->onConnectionError('Error while writing bytes to the server');
            }
            $buffer = substr($buffer, $written);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read() {
        $socket = $this->getResource();

        $chunk  = fgets($socket);
        if ($chunk === false || $chunk === '') {
            $this->onConnectionError('Error while reading line from the server');
        }

        $prefix  = $chunk[0];
        $payload = substr($chunk, 1, -2);

        switch ($prefix) {
            case '+':    // inline
                switch ($payload) {
                    case 'OK':
                        return true;

                    case 'QUEUED':
                        return new ResponseQueued();

                    default:
                        return $payload;
                }

            case '$':    // bulk
                $size = (int) $payload;
                if ($size === -1) {
                    return null;
                }

                $bulkData = '';
                $bytesLeft = ($size += 2);

                do {
                    $chunk = fread($socket, min($bytesLeft, 4096));
                    if ($chunk === false || $chunk === '') {
                        $this->onConnectionError(
                            'Error while reading bytes from the server'
                        );
                    }
                    $bulkData .= $chunk;
                    $bytesLeft = $size - strlen($bulkData);
                } while ($bytesLeft > 0);

                return substr($bulkData, 0, -2);

            case '*':    // multi bulk
                $count = (int) $payload;
                if ($count === -1) {
                    return null;
                }

                if ($this->mbiterable === true) {
                    return new MultiBulkResponseSimple($this, $count);
                }

                $multibulk = array();
                for ($i = 0; $i < $count; $i++) {
                    $multibulk[$i] = $this->read();
                }

                return $multibulk;

            case ':':    // integer
                return (int) $payload;

            case '-':    // error
                if ($this->throwErrors) {
                    throw new ServerException($payload);
                }
                return new ResponseError($payload);

            default:
                $this->onProtocolError("Unknown prefix: '$prefix'");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeCommand(ICommand $command)
    {
        $commandId = $command->getId();
        $arguments = $command->getArguments();

        $cmdlen = strlen($commandId);
        $reqlen = count($arguments) + 1;

        $buffer = "*{$reqlen}\r\n\${$cmdlen}\r\n{$commandId}\r\n";

        for ($i = 0; $i < $reqlen - 1; $i++) {
            $argument = $arguments[$i];
            $arglen = strlen($argument);
            $buffer .= "\${$arglen}\r\n{$argument}\r\n";
        }

        $this->writeBytes($buffer);
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return array_merge(parent::__sleep(), array('mbiterable', 'throwErrors'));
    }
}

/**
 * Defines a cluster of Redis servers formed by aggregating multiple
 * connection objects.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IConnectionCluster extends IConnection
{
    /**
     * Adds a connection instance to the cluster.
     *
     * @param IConnectionSingle $connection Instance of a connection.
     */
    public function add(IConnectionSingle $connection);

    /**
     * Removes the specified connection instance from the cluster.
     *
     * @param IConnectionSingle $connection Instance of a connection.
     * @return Boolean Returns true if the connection was in the pool.
     */
    public function remove(IConnectionSingle $connection);

    /**
     * Gets the actual connection instance in charge of the specified command.
     *
     * @param ICommand $command Instance of a Redis command.
     * @return IConnectionSingle
     */
    public function getConnection(ICommand $command);

    /**
     * Retrieves a connection instance from the cluster using an alias.
     *
     * @param string $connectionId Alias of a connection
     * @return IConnectionSingle
     */
    public function getConnectionById($connectionId);
}

/**
 * Exception class that identifies connection-related errors.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ConnectionException extends CommunicationException
{
}

/**
 * This class provides the implementation of a Predis connection that uses the
 * PHP socket extension for network communication and wraps the phpiredis C
 * extension (PHP bindings for hiredis) to parse the Redis protocol. Everything
 * is highly experimental (even the very same phpiredis since it is quite new),
 * so use it at your own risk.
 *
 * This class is mainly intended to provide an optional low-overhead alternative
 * for processing replies from Redis compared to the standard pure-PHP classes.
 * Differences in speed when dealing with short inline replies are practically
 * nonexistent, the actual speed boost is for long multibulk replies when this
 * protocol processor can parse and return replies very fast.
 *
 * For instructions on how to build and install the phpiredis extension, please
 * consult the repository of the project.
 *
 * @link http://github.com/seppo0010/phpiredis
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PhpiredisConnection extends ConnectionBase
{
    private $reader;

    /**
     * {@inheritdoc}
     */
    public function __construct(IConnectionParameters $parameters)
    {
        if (!function_exists('socket_create')) {
            throw new NotSupportedException(
                'The socket extension must be loaded in order to be able to ' .
                'use this connection class'
            );
        }

        parent::__construct($parameters);
    }

    /**
     * Disconnects from the server and destroys the underlying resource and the
     * protocol reader resource when PHP's garbage collector kicks in.
     */
    public function __destruct()
    {
        phpiredis_reader_destroy($this->reader);

        parent::__destruct();
    }

    /**
     * {@inheritdoc}
     */
    protected function checkParameters(IConnectionParameters $parameters)
    {
        if ($parameters->isSetByUser('iterable_multibulk')) {
            $this->onInvalidOption('iterable_multibulk', $parameters);
        }
        if ($parameters->isSetByUser('connection_persistent')) {
            $this->onInvalidOption('connection_persistent', $parameters);
        }

        return parent::checkParameters($parameters);
    }

    /**
     * Initializes the protocol reader resource.
     *
     * @param Boolean $throw_errors Specify if Redis errors throw exceptions.
     */
    private function initializeReader($throw_errors = true)
    {
        if (!function_exists('phpiredis_reader_create')) {
            throw new NotSupportedException(
                'The phpiredis extension must be loaded in order to be able to ' .
                'use this connection class'
            );
        }

        $reader = phpiredis_reader_create();

        phpiredis_reader_set_status_handler($reader, $this->getStatusHandler());
        phpiredis_reader_set_error_handler($reader, $this->getErrorHandler($throw_errors));

        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeProtocol(IConnectionParameters $parameters)
    {
        $this->initializeReader($parameters->throw_errors);
    }

    /**
     * Gets the handler used by the protocol reader to handle status replies.
     *
     * @return \Closure
     */
    private function getStatusHandler()
    {
        return function($payload) {
            switch ($payload) {
                case 'OK':
                    return true;

                case 'QUEUED':
                    return new ResponseQueued();

                default:
                    return $payload;
            }
        };
    }

    /**
     * Gets the handler used by the protocol reader to handle Redis errors.
     *
     * @param Boolean $throw_errors Specify if Redis errors throw exceptions.
     * @return \Closure
     */
    private function getErrorHandler($throwErrors = true)
    {
        if ($throwErrors) {
            return function($errorMessage) {
                throw new ServerException($errorMessage);
            };
        }

        return function($errorMessage) {
            return new ResponseError($errorMessage);
        };
    }

    /**
     * Helper method used to throw exceptions on socket errors.
     */
    private function emitSocketError()
    {
        $errno  = socket_last_error();
        $errstr = socket_strerror($errno);

        $this->disconnect();

        $this->onConnectionError(trim($errstr), $errno);
    }

    /**
     * {@inheritdoc}
     */
    protected function createResource()
    {
        $parameters = $this->parameters;

        $isUnix = $this->parameters->scheme === 'unix';
        $domain = $isUnix ? AF_UNIX : AF_INET;
        $protocol = $isUnix ? 0 : SOL_TCP;

        $socket = @call_user_func('socket_create', $domain, SOCK_STREAM, $protocol);
        if (!is_resource($socket)) {
            $this->emitSocketError();
        }

        $this->setSocketOptions($socket, $parameters);

        return $socket;
    }

    /**
     * Sets options on the socket resource from the connection parameters.
     *
     * @param resource $socket Socket resource.
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     */
    private function setSocketOptions($socket, IConnectionParameters $parameters)
    {
        if ($parameters->scheme !== 'tcp') {
            return;
        }

        if (!socket_set_option($socket, SOL_TCP, TCP_NODELAY, 1)) {
            $this->emitSocketError();
        }

        if (!socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1)) {
            $this->emitSocketError();
        }

        if (isset($parameters->read_write_timeout)) {
            $rwtimeout = $parameters->read_write_timeout;
            $timeoutSec = floor($rwtimeout);
            $timeoutUsec = ($rwtimeout - $timeoutSec) * 1000000;

            $timeout = array(
                'sec' => $timeoutSec,
                'usec' => $timeoutUsec,
            );

            if (!socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, $timeout)) {
                $this->emitSocketError();
            }

            if (!socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $timeout)) {
                $this->emitSocketError();
            }
        }
    }

    /**
     * Gets the address from the connection parameters.
     *
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     * @return string
     */
    private function getAddress(IConnectionParameters $parameters)
    {
        if ($parameters->scheme === 'unix') {
            return $parameters->path;
        }

        $host = $parameters->host;

        if (ip2long($host) === false) {
            if (($address = gethostbyname($host)) === $host) {
                $this->onConnectionError("Cannot resolve the address of $host");
            }
            return $address;
        }

        return $host;
    }

    /**
     * Opens the actual connection to the server with a timeout.
     *
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     * @return string
     */
    private function connectWithTimeout(IConnectionParameters $parameters) {
        $host = self::getAddress($parameters);
        $socket = $this->getResource();

        socket_set_nonblock($socket);

        if (@socket_connect($socket, $host, $parameters->port) === false) {
            $error = socket_last_error();
            if ($error != SOCKET_EINPROGRESS && $error != SOCKET_EALREADY) {
                $this->emitSocketError();
            }
        }

        socket_set_block($socket);

        $null = null;
        $selectable = array($socket);

        $timeout = $parameters->connection_timeout;
        $timeoutSecs = floor($timeout);
        $timeoutUSecs = ($timeout - $timeoutSecs) * 1000000;

        $selected = socket_select($selectable, $selectable, $null, $timeoutSecs, $timeoutUSecs);

        if ($selected === 2) {
            $this->onConnectionError('Connection refused', SOCKET_ECONNREFUSED);
        }
        if ($selected === 0) {
            $this->onConnectionError('Connection timed out', SOCKET_ETIMEDOUT);
        }
        if ($selected === false) {
            $this->emitSocketError();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        parent::connect();

        $this->connectWithTimeout($this->parameters);
        if (count($this->initCmds) > 0) {
            $this->sendInitializationCommands();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        if ($this->isConnected()) {
            socket_close($this->getResource());

            parent::disconnect();
        }
    }

    /**
     * Sends the initialization commands to Redis when the connection is opened.
     */
    private function sendInitializationCommands()
    {
        foreach ($this->initCmds as $command) {
            $this->writeCommand($command);
        }
        foreach ($this->initCmds as $command) {
            $this->readResponse($command);
        }
    }

    /**
     * {@inheritdoc}
     */
    private function write($buffer)
    {
        $socket = $this->getResource();

        while (($length = strlen($buffer)) > 0) {
            $written = socket_write($socket, $buffer, $length);

            if ($length === $written) {
                return;
            }
            if ($written === false) {
                $this->onConnectionError('Error while writing bytes to the server');
            }

            $buffer = substr($buffer, $written);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $socket = $this->getResource();
        $reader = $this->reader;

        while (($state = phpiredis_reader_get_state($reader)) === PHPIREDIS_READER_STATE_INCOMPLETE) {
            if (@socket_recv($socket, $buffer, 4096, 0) === false || $buffer === '') {
                $this->emitSocketError();
            }

            phpiredis_reader_feed($reader, $buffer);
        }

        if ($state === PHPIREDIS_READER_STATE_COMPLETE) {
            return phpiredis_reader_get_reply($reader);
        }
        else {
            $this->onProtocolError(phpiredis_reader_get_error($reader));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeCommand(ICommand $command)
    {
        $cmdargs = $command->getArguments();
        array_unshift($cmdargs, $command->getId());
        $this->write(phpiredis_format_command($cmdargs));
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
        $this->initializeProtocol($this->getParameters());
    }
}

/**
 * Abstraction for a cluster of aggregated connections to various Redis servers
 * implementing client-side sharding based on pluggable distribution strategies.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 * @todo Add the ability to remove connections from pool.
 */
class PredisCluster implements IConnectionCluster, \IteratorAggregate, \Countable
{
    private $pool;
    private $distributor;

    /**
     * @param IDistributionStrategy $distributor Distribution strategy used by the cluster.
     */
    public function __construct(IDistributionStrategy $distributor = null)
    {
        $this->pool = array();
        $this->distributor = $distributor ?: new HashRing();
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        foreach ($this->pool as $connection) {
            if ($connection->isConnected()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        foreach ($this->pool as $connection) {
            $connection->connect();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        foreach ($this->pool as $connection) {
            $connection->disconnect();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add(IConnectionSingle $connection)
    {
        $parameters = $connection->getParameters();

        if (isset($parameters->alias)) {
            $this->pool[$parameters->alias] = $connection;
        }
        else {
            $this->pool[] = $connection;
        }

        $weight = isset($parameters->weight) ? $parameters->weight : null;
        $this->distributor->add($connection, $weight);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(IConnectionSingle $connection)
    {
        if (($id = array_search($connection, $this->pool, true)) !== false) {
            unset($this->pool[$id]);
            $this->distributor->remove($connection);

            return true;
        }

        return false;
    }

    /**
     * Removes a connection instance using its alias or index.
     *
     * @param string $connectionId Alias or index of a connection.
     * @return Boolean Returns true if the connection was in the pool.
     */
    public function removeById($connectionId)
    {
        if ($connection = $this->getConnectionById($connectionId)) {
            return $this->remove($connection);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection(ICommand $command)
    {
        $cmdHash = $command->getHash($this->distributor);

        if (isset($cmdHash)) {
            return $this->distributor->get($cmdHash);
        }

        $message = sprintf("Cannot send '%s' commands to a cluster of connections", $command->getId());
        throw new NotSupportedException($message);
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionById($id = null)
    {
        $alias = $id ?: 0;

        return isset($this->pool[$alias]) ? $this->pool[$alias] : null;
    }


    /**
     * Retrieves a connection instance from the cluster using a key.
     *
     * @param string $key Key of a Redis value.
     * @return IConnectionSingle
     */
    public function getConnectionByKey($key)
    {
        $hashablePart = Helpers::extractKeyTag($key);
        $keyHash = $this->distributor->generateKey($hashablePart);

        return $this->distributor->get($keyHash);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->pool);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->pool);
    }

    /**
     * {@inheritdoc}
     */
    public function writeCommand(ICommand $command)
    {
        $this->getConnection($command)->writeCommand($command);
    }

    /**
     * {@inheritdoc}
     */
    public function readResponse(ICommand $command)
    {
        return $this->getConnection($command)->readResponse($command);
    }

    /**
     * {@inheritdoc}
     */
    public function executeCommand(ICommand $command)
    {
        return $this->getConnection($command)->executeCommand($command);
    }
}

/**
 * Connection abstraction to Redis servers based on PHP's stream that uses an
 * external protocol processor defining the protocol used for the communication.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ComposableStreamConnection extends StreamConnection implements IConnectionComposable
{
    private $protocol;

    /**
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     * @param IProtocolProcessor $protocol A protocol processor.
     */
    public function __construct(IConnectionParameters $parameters, IProtocolProcessor $protocol = null)
    {
        $this->setProtocol($protocol ?: new TextProtocol());

        parent::__construct($parameters);
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeProtocol(IConnectionParameters $parameters)
    {
        $this->protocol->setOption('throw_errors', $parameters->throw_errors);
        $this->protocol->setOption('iterable_multibulk', $parameters->iterable_multibulk);
    }

    /**
     * {@inheritdoc}
     */
    public function setProtocol(IProtocolProcessor $protocol)
    {
        if ($protocol === null) {
            throw new \InvalidArgumentException("The protocol instance cannot be a null value");
        }
        $this->protocol = $protocol;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * {@inheritdoc}
     */
    public function writeBytes($buffer)
    {
        parent::writeBytes($buffer);
    }

    /**
     * {@inheritdoc}
     */
    public function readBytes($length)
    {
        if ($length <= 0) {
            throw new \InvalidArgumentException('Length parameter must be greater than 0');
        }

        $value  = '';
        $socket = $this->getResource();

        do {
            $chunk = fread($socket, $length);
            if ($chunk === false || $chunk === '') {
                $this->onConnectionError('Error while reading bytes from the server');
            }
            $value .= $chunk;
        }
        while (($length -= strlen($chunk)) > 0);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function readLine()
    {
        $value  = '';
        $socket = $this->getResource();

        do {
            $chunk = fgets($socket);
            if ($chunk === false || $chunk === '') {
                $this->onConnectionError('Error while reading line from the server');
            }
            $value .= $chunk;
        }
        while (substr($value, -2) !== "\r\n");

        return substr($value, 0, -2);
    }

    /**
     * {@inheritdoc}
     */
    public function writeCommand(ICommand $command)
    {
        $this->protocol->write($this, $command);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        return $this->protocol->read($this);
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return array_merge(parent::__sleep(), array('protocol'));
    }
}

/**
 * Defines the standard virtual connection class that is used
 * by Predis to handle replication with a group of servers in
 * a master/slave configuration.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class MasterSlaveReplication implements IConnectionReplication
{
    private $disallowed = array();
    private $readonly = array();
    private $readonlySHA1 = array();
    private $current = null;
    private $master = null;
    private $slaves = array();

    /**
     *
     */
    public function __construct()
    {
        $this->disallowed = $this->getDisallowedOperations();
        $this->readonly = $this->getReadOnlyOperations();
    }

    /**
     * Checks if one master and at least one slave have been defined.
     */
    protected function check()
    {
        if (!isset($this->master) || !$this->slaves) {
            throw new \RuntimeException('Replication needs a master and at least one slave.');
        }
    }

    /**
     * Resets the connection state.
     */
    protected function reset()
    {
        $this->current = null;
    }

    /**
     * {@inheritdoc}
     */
    public function add(IConnectionSingle $connection)
    {
        $alias = $connection->getParameters()->alias;

        if ($alias === 'master') {
            $this->master = $connection;
        }
        else {
            $this->slaves[$alias ?: count($this->slaves)] = $connection;
        }

        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function remove(IConnectionSingle $connection)
    {
        if ($connection->getParameters()->alias === 'master') {
            $this->master = null;
            $this->reset();

            return true;
        }
        else {
            if (($id = array_search($connection, $this->slaves, true)) !== false) {
                unset($this->slaves[$id]);
                $this->reset();

                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection(ICommand $command)
    {
        if ($this->current === null) {
            $this->check();
            $this->current = $this->isReadOperation($command) ? $this->pickSlave() : $this->master;

            return $this->current;
        }

        if ($this->current === $this->master) {
            return $this->current;
        }

        if (!$this->isReadOperation($command)) {
            $this->current = $this->master;
        }

        return $this->current;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectionById($connectionId)
    {
        if ($connectionId === 'master') {
            return $this->master;
        }
        if (isset($this->slaves[$connectionId])) {
            return $this->slaves[$connectionId];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function switchTo($connection)
    {
        $this->check();

        if (!$connection instanceof IConnectionSingle) {
            $connection = $this->getConnectionById($connection);
        }
        if ($connection !== $this->master && !in_array($connection, $this->slaves, true)) {
            throw new \InvalidArgumentException('The specified connection is not valid.');
        }

        $this->current = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaster()
    {
        return $this->master;
    }

    /**
     * {@inheritdoc}
     */
    public function getSlaves()
    {
        return array_values($this->slaves);
    }

    /**
     * Returns a random slave.
     *
     * @return IConnectionSingle
     */
    protected function pickSlave()
    {
        return $this->slaves[array_rand($this->slaves)];
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        return $this->current ? $this->current->isConnected() : false;
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        if ($this->current === null) {
            $this->check();
            $this->current = $this->pickSlave();
        }

        $this->current->connect();
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        if ($this->master) {
            $this->master->disconnect();
        }
        foreach ($this->slaves as $connection) {
            $connection->disconnect();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeCommand(ICommand $command)
    {
        $this->getConnection($command)->writeCommand($command);
    }

    /**
     * {@inheritdoc}
     */
    public function readResponse(ICommand $command)
    {
        return $this->getConnection($command)->readResponse($command);
    }

    /**
     * {@inheritdoc}
     */
    public function executeCommand(ICommand $command)
    {
        return $this->getConnection($command)->executeCommand($command);
    }

    /**
     * Returns if the specified command performs a read-only operation
     * against a key stored on Redis.
     *
     * @param ICommand $command Instance of Redis command.
     * @return Boolean
     */
    protected function isReadOperation(ICommand $command)
    {
        if (isset($this->disallowed[$id = $command->getId()])) {
            throw new NotSupportedException("The command $id is not allowed in replication mode");
        }

        if (isset($this->readonly[$id])) {
            if (true === $readonly = $this->readonly[$id]) {
                return true;
            }

            return call_user_func($readonly, $command);
        }

        if (($eval = $id === 'EVAL') || $id === 'EVALSHA') {
            $sha1 = $eval ? sha1($command->getArgument(0)) : $command->getArgument(0);

            if (isset($this->readonlySHA1[$sha1])) {
                if (true === $readonly = $this->readonlySHA1[$sha1]) {
                    return true;
                }

                return call_user_func($readonly, $command);
            }
        }

        return false;
    }

    /**
     * Checks if a SORT command is a readable operation by parsing the arguments
     * array of the specified commad instance.
     *
     * @param ICommand $command Instance of Redis command.
     * @return Boolean
     */
    private function isSortReadOnly(ICommand $command)
    {
        $arguments = $command->getArguments();
        return ($c = count($arguments)) === 1 ? true : $arguments[$c - 2] !== 'STORE';
    }

    /**
     * Marks a command as a read-only operation. When the behaviour of a
     * command can be decided only at runtime depending on its arguments,
     * a callable object can be provided to dinamically check if the passed
     * instance of a command performs write operations or not.
     *
     * @param string $commandID ID of the command.
     * @param mixed $readonly A boolean or a callable object.
     */
    public function setCommandReadOnly($commandID, $readonly = true)
    {
        $commandID = strtoupper($commandID);

        if ($readonly) {
            $this->readonly[$commandID] = $readonly;
        }
        else {
            unset($this->readonly[$commandID]);
        }
    }

    /**
     * Marks a Lua script for EVAL and EVALSHA as a read-only operation. When
     * the behaviour of a script can be decided only at runtime depending on
     * its arguments, a callable object can be provided to dinamically check
     * if the passed instance of EVAL or EVALSHA performs write operations or
     * not.
     *
     * @param string $script Body of the Lua script.
     * @param mixed $readonly A boolean or a callable object.
     */
    public function setScriptReadOnly($script, $readonly = true)
    {
        $sha1 = sha1($script);

        if ($readonly) {
            $this->readonlySHA1[$sha1] = $readonly;
        }
        else {
            unset($this->readonlySHA1[$sha1]);
        }
    }

    /**
     * Returns the default list of disallowed commands.
     *
     * @return array
     */
    protected function getDisallowedOperations()
    {
        return array(
            'SHUTDOWN'          => true,
            'INFO'              => true,
            'DBSIZE'            => true,
            'LASTSAVE'          => true,
            'CONFIG'            => true,
            'MONITOR'           => true,
            'SLAVEOF'           => true,
            'SAVE'              => true,
            'BGSAVE'            => true,
            'BGREWRITEAOF'      => true,
            'SLOWLOG'           => true,
        );
    }

    /**
     * Returns the default list of commands performing read-only operations.
     *
     * @return array
     */
    protected function getReadOnlyOperations()
    {
        return array(
            'EXISTS'            => true,
            'TYPE'              => true,
            'KEYS'              => true,
            'RANDOMKEY'         => true,
            'TTL'               => true,
            'GET'               => true,
            'MGET'              => true,
            'SUBSTR'            => true,
            'STRLEN'            => true,
            'GETRANGE'          => true,
            'GETBIT'            => true,
            'LLEN'              => true,
            'LRANGE'            => true,
            'LINDEX'            => true,
            'SCARD'             => true,
            'SISMEMBER'         => true,
            'SINTER'            => true,
            'SUNION'            => true,
            'SDIFF'             => true,
            'SMEMBERS'          => true,
            'SRANDMEMBER'       => true,
            'ZRANGE'            => true,
            'ZREVRANGE'         => true,
            'ZRANGEBYSCORE'     => true,
            'ZREVRANGEBYSCORE'  => true,
            'ZCARD'             => true,
            'ZSCORE'            => true,
            'ZCOUNT'            => true,
            'ZRANK'             => true,
            'ZREVRANK'          => true,
            'HGET'              => true,
            'HMGET'             => true,
            'HEXISTS'           => true,
            'HLEN'              => true,
            'HKEYS'             => true,
            'HVELS'             => true,
            'HGETALL'           => true,
            'PING'              => true,
            'AUTH'              => true,
            'SELECT'            => true,
            'ECHO'              => true,
            'QUIT'              => true,
            'OBJECT'            => true,
            'SORT'              => array($this, 'isSortReadOnly'),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return array('master', 'slaves', 'disallowed', 'readonly', 'readonlySHA1');
    }
}

const ERR_MSG_EXTENSION = 'The %s extension must be loaded in order to be able to use this connection class';

/**
 * This class implements a Predis connection that actually talks with Webdis
 * instead of connecting directly to Redis. It relies on the cURL extension to
 * communicate with the web server and the phpiredis extension to parse the
 * protocol of the replies returned in the http response bodies.
 *
 * Some features are not yet available or they simply cannot be implemented:
 *   - Pipelining commands.
 *   - Publish / Subscribe.
 *   - MULTI / EXEC transactions (not yet supported by Webdis).
 *
 * @link http://webd.is
 * @link http://github.com/nicolasff/webdis
 * @link http://github.com/seppo0010/phpiredis
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class WebdisConnection implements IConnectionSingle
{
    private $parameters;
    private $resource;
    private $reader;

    /**
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     */
    public function __construct(IConnectionParameters $parameters)
    {
        $this->parameters = $parameters;

        if ($parameters->scheme !== 'http') {
            throw new \InvalidArgumentException("Invalid scheme: {$parameters->scheme}");
        }

        $this->checkExtensions();
        $this->resource = $this->initializeCurl($parameters);
        $this->reader = $this->initializeReader($parameters);
    }

    /**
     * Frees the underlying cURL and protocol reader resources when PHP's
     * garbage collector kicks in.
     */
    public function __destruct()
    {
        curl_close($this->resource);
        phpiredis_reader_destroy($this->reader);
    }

    /**
     * Helper method used to throw on unsupported methods.
     */
    private function throwNotSupportedException($function)
    {
        $class = __CLASS__;
        throw new NotSupportedException("The method $class::$function() is not supported");
    }

    /**
     * Checks if the cURL and phpiredis extensions are loaded in PHP.
     */
    private function checkExtensions()
    {
        if (!function_exists('curl_init')) {
            throw new NotSupportedException(sprintf(ERR_MSG_EXTENSION, 'curl'));
        }
        if (!function_exists('phpiredis_reader_create')) {
            throw new NotSupportedException(sprintf(ERR_MSG_EXTENSION, 'phpiredis'));
        }
    }

    /**
     * Initializes cURL.
     *
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     * @return resource
     */
    private function initializeCurl(IConnectionParameters $parameters)
    {
        $options = array(
            CURLOPT_FAILONERROR => true,
            CURLOPT_CONNECTTIMEOUT_MS => $parameters->connection_timeout * 1000,
            CURLOPT_URL => "{$parameters->scheme}://{$parameters->host}:{$parameters->port}",
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_WRITEFUNCTION => array($this, 'feedReader'),
        );

        if (isset($parameters->user, $parameters->pass)) {
            $options[CURLOPT_USERPWD] = "{$parameters->user}:{$parameters->pass}";
        }

        $resource = curl_init();
        curl_setopt_array($resource, $options);

        return $resource;
    }

    /**
     * Initializes phpiredis' protocol reader.
     *
     * @param IConnectionParameters $parameters Parameters used to initialize the connection.
     * @return resource
     */
    private function initializeReader(IConnectionParameters $parameters)
    {
        $reader = phpiredis_reader_create();

        phpiredis_reader_set_status_handler($reader, $this->getStatusHandler());
        phpiredis_reader_set_error_handler($reader, $this->getErrorHandler($parameters->throw_errors));

        return $reader;
    }

    /**
     * Gets the handler used by the protocol reader to handle status replies.
     *
     * @return \Closure
     */
    protected function getStatusHandler()
    {
        return function($payload) {
            return $payload === 'OK' ? true : $payload;
        };
    }

    /**
     * Gets the handler used by the protocol reader to handle Redis errors.
     *
     * @param Boolean $throwErrors Specify if Redis errors throw exceptions.
     * @return \Closure
     */
    protected function getErrorHandler($throwErrors)
    {
        if ($throwErrors) {
            return function($errorMessage) {
                throw new ServerException($errorMessage);
            };
        }

        return function($errorMessage) {
            return new ResponseError($errorMessage);
        };
    }

    /**
     * Feeds phpredis' reader resource with the data read from the network.
     *
     * @param resource $resource Reader resource.
     * @param string $buffer Buffer with the reply read from the network.
     * @return int
     */
    protected function feedReader($resource, $buffer)
    {
        phpiredis_reader_feed($this->reader, $buffer);

        return strlen($buffer);
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        // NOOP
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        // NOOP
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        return true;
    }

    /**
     * Checks if the specified command is supported by this connection class.
     *
     * @param ICommand $command The instance of a Redis command.
     * @return string
     */
    protected function getCommandId(ICommand $command)
    {
        switch (($commandId = $command->getId())) {
            case 'AUTH':
            case 'SELECT':
            case 'MULTI':
            case 'EXEC':
            case 'WATCH':
            case 'UNWATCH':
            case 'DISCARD':
            case 'MONITOR':
                throw new NotSupportedException("Disabled command: {$command->getId()}");

            default:
                return $commandId;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeCommand(ICommand $command)
    {
        $this->throwNotSupportedException(__FUNCTION__);
    }

    /**
     * {@inheritdoc}
     */
    public function readResponse(ICommand $command)
    {
        $this->throwNotSupportedException(__FUNCTION__);
    }

    /**
     * {@inheritdoc}
     */
    public function executeCommand(ICommand $command)
    {
        $resource = $this->resource;
        $commandId = $this->getCommandId($command);

        if ($arguments = $command->getArguments()) {
            $arguments = implode('/', array_map('urlencode', $arguments));
            $serializedCommand = "$commandId/$arguments.raw";
        }
        else {
            $serializedCommand = "$commandId.raw";
        }

        curl_setopt($resource, CURLOPT_POSTFIELDS, $serializedCommand);

        if (curl_exec($resource) === false) {
            $error = curl_error($resource);
            $errno = curl_errno($resource);
            throw new ConnectionException($this, trim($error), $errno);
        }

        $readerState = phpiredis_reader_get_state($this->reader);

        if ($readerState === PHPIREDIS_READER_STATE_COMPLETE) {
            $reply = phpiredis_reader_get_reply($this->reader);
            if ($reply instanceof IReplyObject) {
                return $reply;
            }
            return $command->parseResponse($reply);
        }
        else {
            $error = phpiredis_reader_get_error($this->reader);
            throw new ProtocolException($this, $error);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function pushInitCommand(ICommand $command)
    {
        $this->throwNotSupportedException(__FUNCTION__);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        $this->throwNotSupportedException(__FUNCTION__);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "{$this->parameters->host}:{$this->parameters->port}";
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return array('parameters');
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
        $this->checkExtensions();
        $parameters = $this->getParameters();

        $this->resource = $this->initializeCurl($parameters);
        $this->reader = $this->initializeReader($parameters);
    }
}

/* --------------------------------------------------------------------------- */

namespace Predis;

use Predis\Commands\ICommand;
use Predis\Options\IClientOptions;
use Predis\Network\IConnection;
use Predis\Network\IConnectionSingle;
use Predis\Profiles\IServerProfile;
use Predis\Options\ClientOptions;
use Predis\Profiles\ServerProfile;
use Predis\PubSub\PubSubContext;
use Predis\Pipeline\PipelineContext;
use Predis\Transaction\MultiExecContext;
use Predis\Network\IConnectionCluster;
use Predis\Network\IConnectionReplication;
use Predis\IConnectionParameters;
use Predis\Options\IOption;

/**
 * Base exception class for Predis-related errors.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class PredisException extends \Exception
{
}

/**
 * Represents a complex reply object from Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IReplyObject
{
}

/**
 * Base exception class for network-related errors.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class CommunicationException extends PredisException
{
    private $connection;

    /**
     * @param IConnectionSingle $connection Connection that generated the exception.
     * @param string $message Error message.
     * @param int $code Error code.
     * @param \Exception $innerException Inner exception for wrapping the original error.
     */
    public function __construct(IConnectionSingle $connection,
        $message = null, $code = null, \Exception $innerException = null)
    {
        parent::__construct($message, $code, $innerException);

        $this->connection = $connection;
    }

    /**
     * Gets the connection that generated the exception.
     *
     * @return IConnectionSingle
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Indicates if the receiver should reset the underlying connection.
     *
     * @return Boolean
     */
    public function shouldResetConnection()
    {
        return true;
    }
}

/**
 * Represents an error returned by Redis (replies identified by "-" in the
 * Redis response protocol) during the execution of an operation on the server.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IRedisServerError extends IReplyObject
{
    /**
     * Returns the error message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Returns the error type (e.g. ERR, ASK, MOVED)
     *
     * @return string
     */
    public function getErrorType();
}

/**
 * Interface that must be implemented by classes that provide their own mechanism
 * to create and initialize new instances of Predis\Network\IConnectionSingle.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IConnectionFactory
{
    /**
     * Defines or overrides the connection class identified by a scheme prefix.
     *
     * @param string $scheme URI scheme identifying the connection class.
     * @param mixed $initializer FQN of a connection class or a callable object for lazy initialization.
     */
    public function define($scheme, $initializer);

    /**
     * Undefines the connection identified by a scheme prefix.
     *
     * @param string $scheme Parameters for the connection.
     */
    public function undefine($scheme);

    /**
     * Creates a new connection object.
     *
     * @param mixed $parameters Parameters for the connection.
     * @return Predis\Network\IConnectionSingle
     */
    public function create($parameters, IServerProfile $profile = null);

    /**
     * Prepares a cluster of connection objects.
     *
     * @param IConnectionCluster Instance of a connection cluster class.
     * @param array $parameters List of parameters for each connection object.
     * @return Predis\Network\IConnectionCluster
     */
    public function createCluster(IConnectionCluster $cluster, $parameters, IServerProfile $profile = null);

    /**
     * Prepares a master / slave replication configuration.
     *
     * @param IConnectionReplication Instance of a connection cluster class.
     * @param array $parameters List of parameters for each connection object.
     * @return Predis\Network\IConnectionReplication
     */
    public function createReplication(IConnectionReplication $replication, $parameters, IServerProfile $profile = null);
}

/**
 * Interface that must be implemented by classes that provide their own mechanism
 * to parse and handle connection parameters.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IConnectionParameters
{
    /**
     * Checks if the specified parameters is set.
     *
     * @param string $property Name of the property.
     * @return Boolean
     */
    public function __isset($parameter);

    /**
     * Returns the value of the specified parameter.
     *
     * @param string $parameter Name of the parameter.
     * @return mixed
     */
    public function __get($parameter);

    /**
     * Returns an array representation of the connection parameters.
     *
     * @return array
     */
    public function toArray();
}

/**
 * Exception class that identifies server-side Redis errors.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerException extends PredisException implements IRedisServerError
{
    /**
     * Gets the type of the error returned by Redis.
     *
     * @return string
     */
    public function getErrorType()
    {
        list($errorType, ) = explode(' ', $this->getMessage(), 2);
        return $errorType;
    }

    /**
     * Converts the exception to an instance of ResponseError.
     *
     * @return ResponseError
     */
    public function toResponseError()
    {
        return new ResponseError($this->getMessage());
    }
}

/**
 * Provides a default factory for Redis connections that maps URI schemes
 * to connection classes implementing the Predis\Network\IConnectionSingle
 * interface.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ConnectionFactory implements IConnectionFactory
{
    private $schemes;

    /**
     * Initializes a new instance of the default connection factory class used by Predis.
     */
    public function __construct()
    {
        $this->schemes = $this->getDefaultSchemes();
    }

    /**
     * Returns a named array that maps URI schemes to connection classes.
     *
     * @return array Map of URI schemes and connection classes.
     */
    protected function getDefaultSchemes()
    {
        return array(
            'tcp' => 'Predis\Network\StreamConnection',
            'unix' => 'Predis\Network\StreamConnection',
            'http' => 'Predis\Network\WebdisConnection',
        );
    }

    /**
     * Checks if the provided argument represents a valid connection class
     * implementing the Predis\Network\IConnectionSingle interface. Optionally,
     * callable objects are used for lazy initialization of connection objects.
     *
     * @param mixed $initializer FQN of a connection class or a callable for lazy initialization.
     * @return mixed
     */
    protected function checkInitializer($initializer)
    {
        if (is_callable($initializer)) {
            return $initializer;
        }

        $initializerReflection = new \ReflectionClass($initializer);

        if (!$initializerReflection->isSubclassOf('Predis\Network\IConnectionSingle')) {
            throw new \InvalidArgumentException(
                'A connection initializer must be a valid connection class or a callable object'
            );
        }

        return $initializer;
    }

    /**
     * {@inheritdoc}
     */
    public function define($scheme, $initializer)
    {
        $this->schemes[$scheme] = $this->checkInitializer($initializer);
    }

    /**
     * {@inheritdoc}
     */
    public function undefine($scheme)
    {
        unset($this->schemes[$scheme]);
    }

    /**
     * {@inheritdoc}
     */
    public function create($parameters, IServerProfile $profile = null)
    {
        if (!$parameters instanceof IConnectionParameters) {
            $parameters = new ConnectionParameters($parameters ?: array());
        }

        $scheme = $parameters->scheme;
        if (!isset($this->schemes[$scheme])) {
            throw new \InvalidArgumentException("Unknown connection scheme: $scheme");
        }

        $initializer = $this->schemes[$scheme];
        if (!is_callable($initializer)) {
            $connection = new $initializer($parameters);
            $this->prepareConnection($connection, $profile ?: ServerProfile::getDefault());

            return $connection;
        }

        $connection = call_user_func($initializer, $parameters, $profile);
        if (!$connection instanceof IConnectionSingle) {
            throw new \InvalidArgumentException(
                'Objects returned by connection initializers must implement ' .
                'the Predis\Network\IConnectionSingle interface'
            );
        }

        return $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function createCluster(IConnectionCluster $cluster, $parameters, IServerProfile $profile = null)
    {
        foreach ($parameters as $node) {
            $cluster->add($node instanceof IConnectionSingle ? $node : $this->create($node, $profile));
        }

        return $cluster;
    }

    /**
     * {@inheritdoc}
     */
    public function createReplication(IConnectionReplication $replication, $parameters, IServerProfile $profile = null)
    {
        foreach ($parameters as $node) {
            $replication->add($node instanceof IConnectionSingle ? $node : $this->create($node, $profile));
        }

        return $replication;
    }

    /**
     * Prepares a connection object after its initialization.
     *
     * @param IConnectionSingle $connection Instance of a connection object.
     * @param IServerProfile $profile $connection Instance of a connection object.
     */
    protected function prepareConnection(IConnectionSingle $connection, IServerProfile $profile)
    {
        $parameters = $connection->getParameters();

        if (isset($parameters->password)) {
            $command = $profile->createCommand('auth', array($parameters->password));
            $connection->pushInitCommand($command);
        }

        if (isset($parameters->database)) {
            $command = $profile->createCommand('select', array($parameters->database));
            $connection->pushInitCommand($command);
        }
    }
}

/**
 * Implements a lightweight PSR-0 compliant autoloader.
 *
 * @author Eric Naeseth <eric@thumbtack.com>
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class Autoloader
{
    private $directory;
    private $prefix;
    private $prefixLength;

    /**
     * @param string $baseDirectory Base directory where the source files are located.
     */
    public function __construct($baseDirectory = __DIR__)
    {
        $this->directory = $baseDirectory;
        $this->prefix = __NAMESPACE__ . '\\';
        $this->prefixLength = strlen($this->prefix);
    }

    /**
     * Registers the autoloader class with the PHP SPL autoloader.
     *
     * @param boolean $prepend Prepend the autoloader on the stack instead of appending it.
     */
    public static function register($prepend = false)
    {
        spl_autoload_register(array(new self, 'autoload'), true, $prepend);
    }

    /**
     * Loads a class from a file using its fully qualified name.
     *
     * @param string $className Fully qualified name of a class.
     */
    public function autoload($className)
    {
        if (0 === strpos($className, $this->prefix)) {
            $parts = explode('\\', substr($className, $this->prefixLength));
            require($this->directory.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $parts).'.php');
        }
    }
}

/**
 * Represents a +QUEUED response returned by Redis as a reply to each command
 * executed inside a MULTI/ EXEC transaction.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseQueued implements IReplyObject
{
    /**
     * Converts the object to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return 'QUEUED';
    }

    /**
     * Returns the value of the specified property.
     *
     * @param string $property Name of the property.
     * @return mixed
     */
    public function __get($property)
    {
        return $property === 'queued';
    }

    /**
     * Checks if the specified property is set.
     *
     * @param string $property Name of the property.
     * @return Boolean
     */
    public function __isset($property)
    {
        return $property === 'queued';
    }
}

/**
 * Client-side abstraction of a Redis MONITOR context.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class MonitorContext implements \Iterator
{
    private $client;
    private $isValid;
    private $position;

    /**
     * @param Client Client instance used by the context.
     */
    public function __construct(Client $client)
    {
        $this->checkCapabilities($client);
        $this->client = $client;
        $this->openContext();
    }

    /**
     * Automatically closes the context when PHP's garbage collector kicks in.
     */
    public function __destruct()
    {
        $this->closeContext();
    }

    /**
     * Checks if the passed client instance satisfies the required conditions
     * needed to initialize a monitor context.
     *
     * @param Client Client instance used by the context.
     */
    private function checkCapabilities(Client $client)
    {
        if (Helpers::isCluster($client->getConnection())) {
            throw new NotSupportedException('Cannot initialize a monitor context over a cluster of connections');
        }

        if ($client->getProfile()->supportsCommand('monitor') === false) {
            throw new NotSupportedException('The current profile does not support the MONITOR command');
        }
    }

    /**
     * Initializes the context and sends the MONITOR command to the server.
     *
     * @param Client Client instance used by the context.
     */
    protected function openContext()
    {
        $this->isValid = true;
        $monitor = $this->client->createCommand('monitor');
        $this->client->executeCommand($monitor);
    }

    /**
     * Closes the context. Internally this is done by disconnecting from server
     * since there is no way to terminate the stream initialized by MONITOR.
     */
    public function closeContext()
    {
        $this->client->disconnect();
        $this->isValid = false;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        // NOOP
    }

    /**
     * Returns the last message payload retrieved from the server.
     *
     * @return Object
     */
    public function current()
    {
        return $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Checks if the the context is still in a valid state to continue.
     *
     * @return Boolean
     */
    public function valid()
    {
        return $this->isValid;
    }

    /**
     * Waits for a new message from the server generated by MONITOR and
     * returns it when available.
     *
     * @return Object
     */
    private function getValue()
    {
        $database = 0;
        $client = null;
        $event = $this->client->getConnection()->read();

        $callback = function($matches) use (&$database, &$client) {
            if (2 === $count = count($matches)) {
                // Redis <= 2.4
                $database = (int) $matches[1];
            }
            if (4 === $count) {
                // Redis >= 2.6
                $database = (int) $matches[2];
                $client = $matches[3];
            }
            return ' ';
        };

        $event = preg_replace_callback('/ \(db (\d+)\) | \[(\d+) (.*?)\] /', $callback, $event, 1);
        @list($timestamp, $command, $arguments) = explode(' ', $event, 3);

        return (object) array(
            'timestamp' => (float) $timestamp,
            'database'  => $database,
            'client'    => $client,
            'command'   => substr($command, 1, -1),
            'arguments' => $arguments,
        );
    }
}

/**
 * Exception class that identifies client-side errors.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ClientException extends PredisException
{
}

/**
 * Represents an error returned by Redis (-ERR replies) during the execution
 * of a command on the server.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseError implements IRedisServerError
{
    private $message;

    /**
     * @param string $message Error message returned by Redis
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorType()
    {
        list($errorType, ) = explode(' ', $this->getMessage(), 2);
        return $errorType;
    }

    /**
     * Converts the object to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getMessage();
    }
}

/**
 * Exception class generated when trying to use features not
 * supported by certain classes or abstractions.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class NotSupportedException extends PredisException
{
}

/**
 * Handles parsing and validation of connection parameters.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ConnectionParameters implements IConnectionParameters
{
    private static $defaultParameters;
    private static $validators;

    private $parameters;
    private $userDefined;

    /**
     * @param string|array Connection parameters in the form of an URI string or a named array.
     */
    public function __construct($parameters = array())
    {
        self::ensureDefaults();

        if (!is_array($parameters)) {
            $parameters = $this->parseURI($parameters);
        }

        $this->userDefined = array_keys($parameters);
        $this->parameters = $this->filter($parameters) + self::$defaultParameters;
    }

    /**
     * Ensures that the default values and validators are initialized.
     */
    private static function ensureDefaults()
    {
        if (!isset(self::$defaultParameters)) {
            self::$defaultParameters = array(
                'scheme' => 'tcp',
                'host' => '127.0.0.1',
                'port' => 6379,
                'database' => null,
                'password' => null,
                'connection_async' => false,
                'connection_persistent' => false,
                'connection_timeout' => 5.0,
                'read_write_timeout' => null,
                'alias' => null,
                'weight' => null,
                'path' => null,
                'iterable_multibulk' => false,
                'throw_errors' => true,
            );
        }

        if (!isset(self::$validators)) {
            $bool = function($value) { return (bool) $value; };
            $float = function($value) { return (float) $value; };
            $int = function($value) { return (int) $value; };

            self::$validators = array(
                'port' => $int,
                'connection_async' => $bool,
                'connection_persistent' => $bool,
                'connection_timeout' => $float,
                'read_write_timeout' => $float,
                'iterable_multibulk' => $bool,
                'throw_errors' => $bool,
            );
        }
    }

    /**
     * Defines a default value and a validator for the specified parameter.
     *
     * @param string $parameter Name of the parameter.
     * @param mixed $default Default value or an instance of IOption.
     * @param mixed $callable A validator callback.
     */
    public static function define($parameter, $default, $callable = null)
    {
        self::ensureDefaults();
        self::$defaultParameters[$parameter] = $default;

        if ($default instanceof IOption) {
            self::$validators[$parameter] = $default;
            return;
        }

        if (!isset($callable)) {
            unset(self::$validators[$parameter]);
            return;
        }

        if (!is_callable($callable)) {
            throw new \InvalidArgumentException(
                "The validator for $parameter must be a callable object"
            );
        }

        self::$validators[$parameter] = $callable;
    }

    /**
     * Undefines the default value and validator for the specified parameter.
     *
     * @param string $parameter Name of the parameter.
     */
    public static function undefine($parameter)
    {
        self::ensureDefaults();
        unset(self::$defaultParameters[$parameter], self::$validators[$parameter]);
    }

    /**
     * Parses an URI string and returns an array of connection parameters.
     *
     * @param string $uri Connection string.
     * @return array
     */
    private function parseURI($uri)
    {
        if (stripos($uri, 'unix') === 0) {
            // Hack to support URIs for UNIX sockets with minimal effort.
            $uri = str_ireplace('unix:///', 'unix://localhost/', $uri);
        }

        if (($parsed = @parse_url($uri)) === false || !isset($parsed['host'])) {
            throw new ClientException("Invalid URI: $uri");
        }

        if (isset($parsed['query'])) {
            foreach (explode('&', $parsed['query']) as $kv) {
                @list($k, $v) = explode('=', $kv);
                $parsed[$k] = $v;
            }
            unset($parsed['query']);
        }

        return $parsed;
    }

    /**
     * Validates and converts each value of the connection parameters array.
     *
     * @param array $parameters Connection parameters.
     * @return array
     */
    private function filter(Array $parameters)
    {
        if (count($parameters) > 0) {
            $validators = array_intersect_key(self::$validators, $parameters);
            foreach ($validators as $parameter => $validator) {
                $parameters[$parameter] = $validator($parameters[$parameter]);
            }
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function __get($parameter)
    {
        $value = $this->parameters[$parameter];

        if ($value instanceof IOption) {
            $this->parameters[$parameter] = ($value = $value->getDefault());
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($parameter)
    {
        return isset($this->parameters[$parameter]);
    }

    /**
     * Checks if the specified parameter has been set by the user.
     *
     * @param string $parameter Name of the parameter.
     * @return Boolean
     */
    public function isSetByUser($parameter)
    {
        return in_array($parameter, $this->userDefined);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBaseURI()
    {
        if ($this->scheme === 'unix') {
            return "{$this->scheme}://{$this->path}";
        }

        return "{$this->scheme}://{$this->host}:{$this->port}";
    }

    /**
     * Returns the URI parts that must be omitted when calling __toString().
     *
     * @return array
     */
    protected function getDisallowedURIParts()
    {
        return array('scheme', 'host', 'port', 'password', 'path');
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->parameters;
    }

    /**
     * Returns a string representation of the parameters.
     *
     * @return string
     */
    public function __toString()
    {
        $query = array();
        $parameters = $this->toArray();
        $reject = $this->getDisallowedURIParts();

        foreach ($this->userDefined as $param) {
            if (in_array($param, $reject) || !isset($parameters[$param])) {
                continue;
            }
            $value = $parameters[$param];
            $query[] = "$param=" . ($value === false ? '0' : $value);
        }

        if (count($query) === 0) {
            return $this->getBaseURI();
        }

        return $this->getBaseURI() . '/?' . implode('&', $query);
    }

    /**
     * {@inheritdoc}
     */
    public function __sleep()
    {
        return array('parameters', 'userDefined');
    }

    /**
     * {@inheritdoc}
     */
    public function __wakeup()
    {
        self::ensureDefaults();
    }
}

/**
 * Main class that exposes the most high-level interface to interact with Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class Client
{
    const VERSION = '0.7.2';

    private $options;
    private $profile;
    private $connection;
    private $connections;

    /**
     * Initializes a new client with optional connection parameters and client options.
     *
     * @param mixed $parameters Connection parameters for one or multiple servers.
     * @param mixed $options Options that specify certain behaviours for the client.
     */
    public function __construct($parameters = null, $options = null)
    {
        $options = $this->filterOptions($options);
        $this->options = $options;
        $this->profile = $options->profile;
        $this->connections = $options->connections;
        $this->connection = $this->initializeConnection($parameters);
    }

    /**
     * Creates an instance of Predis\Options\ClientOptions from various types of
     * arguments (string, array, Predis\Profiles\ServerProfile) or returns the
     * passed object if it is an instance of Predis\Options\ClientOptions.
     *
     * @param mixed $options Client options.
     * @return ClientOptions
     */
    protected function filterOptions($options)
    {
        if ($options === null) {
            return new ClientOptions();
        }
        if (is_array($options)) {
            return new ClientOptions($options);
        }
        if ($options instanceof IClientOptions) {
            return $options;
        }
        if ($options instanceof IServerProfile || is_string($options)) {
            return new ClientOptions(array('profile' => $options));
        }

        throw new \InvalidArgumentException("Invalid type for client options");
    }

    /**
     * Initializes one or multiple connection (cluster) objects from various
     * types of arguments (string, array) or returns the passed object if it
     * implements the Predis\Network\IConnection interface.
     *
     * @param mixed $parameters Connection parameters or instance.
     * @return IConnection
     */
    protected function initializeConnection($parameters)
    {
        if ($parameters instanceof IConnection) {
            return $parameters;
        }

        if (is_array($parameters) && isset($parameters[0])) {
            $replication = isset($this->options->replication) && $this->options->replication;

            $connection = $this->options->{$replication ? 'replication' : 'cluster'};
            $initializer = $replication ? 'createReplication' : 'createCluster';

            return $this->connections->$initializer($connection, $parameters, $this->profile);
        }

        return $this->connections->create($parameters, $this->profile);
    }

    /**
     * Returns the server profile used by the client.
     *
     * @return IServerProfile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Returns the client options specified upon initialization.
     *
     * @return ClientOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns the connection factory object used by the client.
     *
     * @return IConnectionFactory
     */
    public function getConnectionFactory()
    {
        return $this->connections;
    }

    /**
     * Returns a new instance of a client for the specified connection when the
     * client is connected to a cluster. The new instance will use the same
     * options of the original client.
     *
     * @return Client
     */
    public function getClientFor($connectionAlias)
    {
        if (($connection = $this->getConnection($connectionAlias)) === null) {
            throw new \InvalidArgumentException("Invalid connection alias: '$connectionAlias'");
        }

        return new Client($connection, $this->options);
    }

    /**
     * Opens the connection to the server.
     */
    public function connect()
    {
        $this->connection->connect();
    }

    /**
     * Disconnects from the server.
     */
    public function disconnect()
    {
        $this->connection->disconnect();
    }

    /**
     * Disconnects from the server.
     *
     * This method is an alias of disconnect().
     */
    public function quit()
    {
        $this->disconnect();
    }

    /**
     * Checks if the underlying connection is connected to Redis.
     *
     * @return Boolean True means that the connection is open.
     *                 False means that the connection is closed.
     */
    public function isConnected()
    {
        return $this->connection->isConnected();
    }

    /**
     * Returns the underlying connection instance or, when connected to a cluster,
     * one of the connection instances identified by its alias.
     *
     * @param string $id The alias of a connection when connected to a cluster.
     * @return IConnection
     */
    public function getConnection($id = null)
    {
        if (isset($id)) {
            if (!Helpers::isAggregated($this->connection)) {
                $message = 'Retrieving connections by alias is supported only with aggregated connections (cluster or replication)';
                throw new NotSupportedException($message);
            }
            return $this->connection->getConnectionById($id);
        }

        return $this->connection;
    }

    /**
     * Dinamically invokes a Redis command with the specified arguments.
     *
     * @param string $method The name of a Redis command.
     * @param array $arguments The arguments for the command.
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $command = $this->profile->createCommand($method, $arguments);
        return $this->connection->executeCommand($command);
    }

    /**
     * Creates a new instance of the specified Redis command.
     *
     * @param string $method The name of a Redis command.
     * @param array $arguments The arguments for the command.
     * @return ICommand
     */
    public function createCommand($method, $arguments = array())
    {
        return $this->profile->createCommand($method, $arguments);
    }

    /**
     * Executes the specified Redis command.
     *
     * @param ICommand $command A Redis command.
     * @return mixed
     */
    public function executeCommand(ICommand $command)
    {
        return $this->connection->executeCommand($command);
    }

    /**
     * Executes the specified Redis command on all the nodes of a cluster.
     *
     * @param ICommand $command A Redis command.
     * @return array
     */
    public function executeCommandOnShards(ICommand $command)
    {
        if (Helpers::isCluster($this->connection)) {
            $replies = array();

            foreach ($this->connection as $connection) {
                $replies[] = $connection->executeCommand($command);
            }

            return $replies;
        }

        return array($this->connection->executeCommand($command));
    }

    /**
     * Calls the specified initializer method on $this with 0, 1 or 2 arguments.
     *
     * TODO: Invert $argv and $initializer.
     *
     * @param array $argv Arguments for the initializer.
     * @param string $initializer The initializer method.
     * @return mixed
     */
    private function sharedInitializer($argv, $initializer)
    {
        switch (count($argv)) {
            case 0:
                return $this->$initializer();

            case 1:
                list($arg0) = $argv;
                return is_array($arg0) ? $this->$initializer($arg0) : $this->$initializer(null, $arg0);

            case 2:
                list($arg0, $arg1) = $argv;
                return $this->$initializer($arg0, $arg1);

            default:
                return $this->$initializer($this, $argv);
        }
    }

    /**
     * Creates a new pipeline context and returns it, or returns the results of
     * a pipeline executed inside the optionally provided callable object.
     *
     * @param mixed $arg,... Options for the context, a callable object, or both.
     * @return PipelineContext|array
     */
    public function pipeline(/* arguments */)
    {
        return $this->sharedInitializer(func_get_args(), 'initPipeline');
    }

    /**
     * Pipeline context initializer.
     *
     * @param array $options Options for the context.
     * @param mixed $callable Optional callable object used to execute the context.
     * @return PipelineContext|array
     */
    protected function initPipeline(Array $options = null, $callable = null)
    {
        $pipeline = new PipelineContext($this, $options);
        return $this->pipelineExecute($pipeline, $callable);
    }

    /**
     * Executes a pipeline context when a callable object is passed.
     *
     * @param array $options Options of the context initialization.
     * @param mixed $callable Optional callable object used to execute the context.
     * @return PipelineContext|array
     */
    private function pipelineExecute(PipelineContext $pipeline, $callable)
    {
        return isset($callable) ? $pipeline->execute($callable) : $pipeline;
    }

    /**
     * Creates a new transaction context and returns it, or returns the results of
     * a transaction executed inside the optionally provided callable object.
     *
     * @param mixed $arg,... Options for the context, a callable object, or both.
     * @return MultiExecContext|array
     */
    public function multiExec(/* arguments */)
    {
        return $this->sharedInitializer(func_get_args(), 'initMultiExec');
    }

    /**
     * Transaction context initializer.
     *
     * @param array $options Options for the context.
     * @param mixed $callable Optional callable object used to execute the context.
     * @return MultiExecContext|array
     */
    protected function initMultiExec(Array $options = null, $callable = null)
    {
        $transaction = new MultiExecContext($this, $options ?: array());
        return isset($callable) ? $transaction->execute($callable) : $transaction;
    }

    /**
     * Creates a new Publish / Subscribe context and returns it, or executes it
     * inside the optionally provided callable object.
     *
     * @param mixed $arg,... Options for the context, a callable object, or both.
     * @return MultiExecContext|array
     */
    public function pubSub(/* arguments */)
    {
        return $this->sharedInitializer(func_get_args(), 'initPubSub');
    }

    /**
     * Publish / Subscribe context initializer.
     *
     * @param array $options Options for the context.
     * @param mixed $callable Optional callable object used to execute the context.
     * @return PubSubContext
     */
    protected function initPubSub(Array $options = null, $callable = null)
    {
        $pubsub = new PubSubContext($this, $options);

        if (!isset($callable)) {
            return $pubsub;
        }

        foreach ($pubsub as $message) {
            if (call_user_func($callable, $pubsub, $message) === false) {
                $pubsub->closeContext();
            }
        }
    }

    /**
     * Returns a new monitor context.
     *
     * @return MonitorContext
     */
    public function monitor()
    {
        return new MonitorContext($this);
    }
}

/**
 * Defines a few helper methods.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class Helpers
{
    /**
     * Checks if the specified connection represents an aggregation of connections.
     *
     * @param IConnection $connection Connection object.
     * @return Boolean
     */
    public static function isAggregated(IConnection $connection)
    {
        return $connection instanceof IConnectionCluster || $connection instanceof IConnectionReplication;
    }

    /**
     * Checks if the specified connection represents a cluster.
     *
     * @param IConnection $connection Connection object.
     * @return Boolean
     */
    public static function isCluster(IConnection $connection)
    {
        return $connection instanceof IConnectionCluster;
    }

    /**
     * Offers a generic and reusable method to handle exceptions generated by
     * a connection object.
     *
     * @param CommunicationException $exception Exception.
     */
    public static function onCommunicationException(CommunicationException $exception)
    {
        if ($exception->shouldResetConnection()) {
            $connection = $exception->getConnection();
            if ($connection->isConnected()) {
                $connection->disconnect();
            }
        }

        throw $exception;
    }

    /**
     * Normalizes the arguments array passed to a Redis command.
     *
     * @param array $arguments Arguments for a command.
     * @return array
     */
    public static function filterArrayArguments(Array $arguments)
    {
        if (count($arguments) === 1 && is_array($arguments[0])) {
            return $arguments[0];
        }

        return $arguments;
    }

    /**
     * Normalizes the arguments array passed to a variadic Redis command.
     *
     * @param array $arguments Arguments for a command.
     * @return array
     */
    public static function filterVariadicValues(Array $arguments)
    {
        if (count($arguments) === 2 && is_array($arguments[1])) {
            return array_merge(array($arguments[0]), $arguments[1]);
        }

        return $arguments;
    }

    /**
     * Returns only the hashable part of a key (delimited by "{...}"), or the
     * whole key if a key tag is not found in the string.
     *
     * @param string $key A key.
     * @return string
     */
    public static function extractKeyTag($key)
    {
        $start = strpos($key, '{');
        if ($start !== false) {
            $end = strpos($key, '}', $start);
            if ($end !== false) {
                $key = substr($key, ++$start, $end - $start);
            }
        }

        return $key;
    }
}

/* --------------------------------------------------------------------------- */

namespace Predis\Options;

use Predis\IConnectionFactory;
use Predis\ConnectionFactory;
use Predis\Network\IConnectionReplication;
use Predis\Network\MasterSlaveReplication;
use Predis\Commands\Processors\KeyPrefixProcessor;
use Predis\Network\IConnectionCluster;
use Predis\Network\PredisCluster;
use Predis\Profiles\ServerProfile;
use Predis\Profiles\IServerProfile;

/**
 * Interface that defines a client option.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IOption
{
    /**
     * Filters (and optionally converts) the passed value.
     *
     * @param mixed $value Input value.
     * @return mixed
     */
    public function filter(IClientOptions $options, $value);

    /**
     * Returns a default value for the option.
     *
     * @param mixed $value Input value.
     * @return mixed
     */
    public function getDefault(IClientOptions $options);

    /**
     * Filters a value and, if no value is specified, returns
     * the default one defined by the option.
     *
     * @param mixed $value Input value.
     * @return mixed
     */
    public function __invoke(IClientOptions $options, $value);
}

/**
 * Implements a client option.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class Option implements IOption
{
    /**
     * {@inheritdoc}
     */
    public function filter(IClientOptions $options, $value)
    {
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(IClientOptions $options)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(IClientOptions $options, $value)
    {
        if (isset($value)) {
            return $this->filter($options, $value);
        }

        return $this->getDefault($options);
    }
}

/**
 * Marker interface defining a client options bag.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IClientOptions
{
}

/**
 * Option class that handles server profiles to be used by a client.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ClientProfile extends Option
{
    /**
     * {@inheritdoc}
     */
    public function filter(IClientOptions $options, $value)
    {
        if (is_string($value)) {
            $value = ServerProfile::get($value);
            if (isset($options->prefix)) {
                $value->setProcessor($options->prefix);
            }
        }

        if (is_callable($value)) {
            $value = call_user_func($value, $options);
        }

        if (!$value instanceof IServerProfile) {
            throw new \InvalidArgumentException('Invalid value for the profile option');
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(IClientOptions $options)
    {
        $profile = ServerProfile::getDefault();
        if (isset($options->prefix)) {
            $profile->setProcessor($options->prefix);
        }

        return $profile;
    }
}

/**
 * Implements a generic class used to dinamically define a client option.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class CustomOption implements IOption
{
    private $filter;
    private $default;

    /**
     * @param array $options List of options
     */
    public function __construct(Array $options = array())
    {
        $this->filter = $this->ensureCallable($options, 'filter');
        $this->default  = $this->ensureCallable($options, 'default');
    }

    /**
     * Checks if the specified value in the options array is a callable object.
     *
     * @param array $options Array of options
     * @param string $key Target option.
     */
    private function ensureCallable($options, $key)
    {
        if (!isset($options[$key])) {
            return;
        }

        $callable = $options[$key];
        if (is_callable($callable)) {
            return $callable;
        }

        throw new \InvalidArgumentException("The parameter $key must be callable");
    }

    /**
     * {@inheritdoc}
     */
    public function filter(IClientOptions $options, $value)
    {
        if (isset($value)) {
            if ($this->filter === null) {
                return $value;
            }
            $validator = $this->filter;

            return $validator($options, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(IClientOptions $options)
    {
        if (!isset($this->default)) {
            return;
        }
        $default = $this->default;

        return $default($options);
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(IClientOptions $options, $value)
    {
        if (isset($value)) {
            return $this->filter($options, $value);
        }

        return $this->getDefault($options);
    }
}

/**
 * Option class that handles the prefixing of keys in commands.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ClientPrefix extends Option
{
    /**
     * {@inheritdoc}
     */
    public function filter(IClientOptions $options, $value)
    {
        return new KeyPrefixProcessor($value);
    }
}

/**
 * Option class that returns a connection factory to be used by a client.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ClientConnectionFactory extends Option
{
    /**
     * {@inheritdoc}
     */
    public function filter(IClientOptions $options, $value)
    {
        if ($value instanceof IConnectionFactory) {
            return $value;
        }
        if (is_array($value)) {
            $factory = $this->getDefault($options);
            foreach ($value as $scheme => $initializer) {
                $factory->define($scheme, $initializer);
            }
            return $factory;
        }
        if (is_string($value) && class_exists($value)) {
            if (!($factory = new $value()) && !$factory instanceof IConnectionFactory) {
                throw new \InvalidArgumentException("Class $value must be an instance of Predis\IConnectionFactory");
            }
            return $factory;
        }

        throw new \InvalidArgumentException('Invalid value for the connections option');
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(IClientOptions $options)
    {
        return new ConnectionFactory();
    }
}

/**
 * Option class that returns a replication connection be used by a client.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ClientReplication extends Option
{
    /**
     * Checks if the specified value is a valid instance of IConnectionReplication.
     *
     * @param IConnectionReplication $cluster Instance of a connection cluster.
     * @return IConnectionReplication
     */
    protected function checkInstance($connection)
    {
        if (!$connection instanceof IConnectionReplication) {
            throw new \InvalidArgumentException('Instance of Predis\Network\IConnectionReplication expected');
        }

        return $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(IClientOptions $options, $value)
    {
        if (is_callable($value)) {
            $connection = call_user_func($value, $options);
            if (!$connection instanceof IConnectionReplication) {
                throw new \InvalidArgumentException('Instance of Predis\Network\IConnectionReplication expected');
            }
            return $connection;
        }

        if (is_string($value)) {
            if (!class_exists($value)) {
                throw new \InvalidArgumentException("Class $value does not exist");
            }
            if (!($connection = new $value()) instanceof IConnectionReplication) {
                throw new \InvalidArgumentException('Instance of Predis\Network\IConnectionReplication expected');
            }
            return $connection;
        }

        if ($value == true) {
            return $this->getDefault($options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(IClientOptions $options)
    {
        return new MasterSlaveReplication();
    }
}

/**
 * Class that manages client options with filtering and conversion.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ClientOptions implements IClientOptions
{
    private $handlers;
    private $defined;
    private $options = array();

    /**
     * @param array $options Array of client options.
     */
    public function __construct(Array $options = array())
    {
        $this->handlers = $this->initialize($options);
        $this->defined = array_fill_keys(array_keys($options), true);
    }

    /**
     * Ensures that the default options are initialized.
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'profile' => new ClientProfile(),
            'connections' => new ClientConnectionFactory(),
            'cluster' => new ClientCluster(),
            'replication' => new ClientReplication(),
            'prefix' => new ClientPrefix(),
        );
    }

    /**
     * Initializes client options handlers.
     *
     * @param array $options List of client options values.
     * @return array
     */
    protected function initialize(Array $options)
    {
        $handlers = $this->getDefaultOptions();

        foreach ($options as $option => $value) {
            if (isset($handlers[$option])) {
                $handler = $handlers[$option];
                $handlers[$option] = function($options) use($handler, $value) {
                    return $handler->filter($options, $value);
                };
            }
            else {
                $this->options[$option] = $value;
            }
        }

        return $handlers;
    }

    /**
     * Checks if the specified option is set.
     *
     * @param string $option Name of the option.
     * @return Boolean
     */
    public function __isset($option)
    {
        return isset($this->defined[$option]);
    }

    /**
     * Returns the value of the specified option.
     *
     * @param string $option Name of the option.
     * @return mixed
     */
    public function __get($option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }

        if (isset($this->handlers[$option])) {
            $handler = $this->handlers[$option];
            $value = $handler instanceof IOption ? $handler->getDefault($this) : $handler($this);
            $this->options[$option] = $value;

            return $value;
        }
    }
}

/**
 * Option class that returns a connection cluster to be used by a client.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ClientCluster extends Option
{
    /**
     * Checks if the specified value is a valid instance of IConnectionCluster.
     *
     * @param IConnectionCluster $cluster Instance of a connection cluster.
     * @return IConnectionCluster
     */
    protected function checkInstance($cluster)
    {
        if (!$cluster instanceof IConnectionCluster) {
            throw new \InvalidArgumentException('Instance of Predis\Network\IConnectionCluster expected');
        }

        return $cluster;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(IClientOptions $options, $value)
    {
        if (is_callable($value)) {
            return $this->checkInstance(call_user_func($value, $options));
        }
        $initializer = $this->getInitializer($options, $value);

        return $this->checkInstance($initializer());
    }

    /**
     * Returns an initializer for the specified FQN or type.
     *
     * @param string $fqnOrType Type of cluster or FQN of a class implementing IConnectionCluster.
     * @param IClientOptions $options Instance of the client options.
     * @return \Closure
     */
    protected function getInitializer(IClientOptions $options, $fqnOrType)
    {
        switch ($fqnOrType) {
            case 'predis':
                return function() { return new PredisCluster(); };

            default:
                // TODO: we should not even allow non-string values here.
                if (is_string($fqnOrType) && !class_exists($fqnOrType)) {
                    throw new \InvalidArgumentException("Class $fqnOrType does not exist");
                }
                return function() use($fqnOrType) {
                    return new $fqnOrType();
                };
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault(IClientOptions $options)
    {
        return new PredisCluster();
    }
}

/* --------------------------------------------------------------------------- */

namespace Predis\Protocol;

use Predis\Network\IConnectionComposable;
use Predis\CommunicationException;
use Predis\Commands\ICommand;

/**
 * Interface that defines an handler able to parse a reply.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IResponseHandler
{
    /**
     * Parses a type of reply returned by Redis and reads more data from the
     * connection if needed.
     *
     * @param IConnectionComposable $connection Connection to Redis.
     * @param string $payload Initial payload of the reply.
     * @return mixed
     */
    function handle(IConnectionComposable $connection, $payload);
}

/**
 * Interface that defines a response reader able to parse replies returned by
 * Redis and deserialize them to PHP objects.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IResponseReader
{
    /**
     * Reads replies from a connection to Redis and deserializes them.
     *
     * @param IConnectionComposable $connection Connection to Redis.
     * @return mixed
     */
    public function read(IConnectionComposable $connection);
}

/**
 * Interface that defines a protocol processor that serializes Redis commands
 * and parses replies returned by the server to PHP objects.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IProtocolProcessor extends IResponseReader
{
    /**
     * Writes a Redis command on the specified connection.
     *
     * @param IConnectionComposable $connection Connection to Redis.
     * @param ICommand $command Redis command.
     */
    public function write(IConnectionComposable $connection, ICommand $command);

    /**
     * Sets the options for the protocol processor.
     *
     * @param string $option Name of the option.
     * @param mixed $value Value of the option.
     */
    public function setOption($option, $value);
}

/**
 * Interface that defines a custom serializer for Redis commands.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ICommandSerializer
{
    /**
     * Serializes a Redis command.
     *
     * @param ICommand $command Redis command.
     * @return string
     */
    public function serialize(ICommand $command);
}

/**
 * Interface that defines a customizable protocol processor that serializes
 * Redis commands and parses replies returned by the server to PHP objects
 * using a pluggable set of classes defining the underlying wire protocol.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IComposableProtocolProcessor extends IProtocolProcessor
{
    /**
     * Sets the command serializer to be used by the protocol processor.
     *
     * @param ICommandSerializer $serializer Command serializer.
     */
    public function setSerializer(ICommandSerializer $serializer);

    /**
     * Returns the command serializer used by the protocol processor.
     *
     * @return ICommandSerializer
     */
    public function getSerializer();

    /**
     * Sets the response reader to be used by the protocol processor.
     *
     * @param IResponseReader $reader Response reader.
     */
    public function setReader(IResponseReader $reader);

    /**
     * Returns the response reader used by the protocol processor.
     *
     * @return IResponseReader
     */
    public function getReader();
}

/**
 * Exception class that identifies errors encountered while
 * handling the Redis wire protocol.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ProtocolException extends CommunicationException
{
}

/* --------------------------------------------------------------------------- */

namespace Predis\Profiles;

use Predis\ClientException;
use Predis\Commands\Processors\ICommandProcessor;
use Predis\Commands\Processors\IProcessingSupport;

/**
 * A server profile defines features and commands supported by certain
 * versions of Redis. Instances of Predis\Client should use a server
 * profile matching the version of Redis in use.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IServerProfile
{
    /**
     * Gets a profile version corresponding to a Redis version.
     *
     * @return string
     */
    public function getVersion();

    /**
     * Checks if the profile supports the specified command.
     *
     * @param string $command Command ID.
     * @return Boolean
     */
    public function supportsCommand($command);

    /**
     * Checks if the profile supports the specified list of commands.
     *
     * @param array $commands List of command IDs.
     * @return string
     */
    public function supportsCommands(Array $commands);

    /**
     * Creates a new command instance.
     *
     * @param string $method Command ID.
     * @param array $arguments Arguments for the command.
     * @return Predis\Commands\ICommand
     */
    public function createCommand($method, $arguments = array());
}

/**
 * Base class that implements common functionalities of server profiles.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class ServerProfile implements IServerProfile, IProcessingSupport
{
    private static $profiles;

    private $commands;
    private $processor;

    /**
     *
     */
    public function __construct()
    {
        $this->commands = $this->getSupportedCommands();
    }

    /**
     * Returns a map of all the commands supported by the profile and their
     * actual PHP classes.
     *
     * @return array
     */
    protected abstract function getSupportedCommands();

    /**
     * Returns the default server profile.
     *
     * @return IServerProfile
     */
    public static function getDefault()
    {
        return self::get('default');
    }

    /**
     * Returns the development server profile.
     *
     * @return IServerProfile
     */
    public static function getDevelopment()
    {
        return self::get('dev');
    }

    /**
     * Returns a map of all the server profiles supported by default and their
     * actual PHP classes.
     *
     * @return array
     */
    private static function getDefaultProfiles()
    {
        return array(
            '1.2'     => 'Predis\Profiles\ServerVersion12',
            '2.0'     => 'Predis\Profiles\ServerVersion20',
            '2.2'     => 'Predis\Profiles\ServerVersion22',
            '2.4'     => 'Predis\Profiles\ServerVersion24',
            '2.6'     => 'Predis\Profiles\ServerVersion26',
            'default' => 'Predis\Profiles\ServerVersion24',
            'dev'     => 'Predis\Profiles\ServerVersionNext',
        );
    }

    /**
     * Registers a new server profile.
     *
     * @param string $alias Profile version or alias.
     * @param string $profileClass FQN of a class implementing Predis\Profiles\IServerProfile.
     */
    public static function define($alias, $profileClass)
    {
        if (!isset(self::$profiles)) {
            self::$profiles = self::getDefaultProfiles();
        }

        $profileReflection = new \ReflectionClass($profileClass);

        if (!$profileReflection->isSubclassOf('Predis\Profiles\IServerProfile')) {
            throw new \InvalidArgumentException("Cannot register '$profileClass' as it is not a valid profile class");
        }

        self::$profiles[$alias] = $profileClass;
    }

    /**
     * Returns the specified server profile.
     *
     * @param string $version Profile version or alias.
     * @return IServerProfile
     */
    public static function get($version)
    {
        if (!isset(self::$profiles)) {
            self::$profiles = self::getDefaultProfiles();
        }
        if (!isset(self::$profiles[$version])) {
            throw new ClientException("Unknown server profile: $version");
        }

        $profile = self::$profiles[$version];

        return new $profile();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsCommands(Array $commands)
    {
        foreach ($commands as $command) {
            if ($this->supportsCommand($command) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsCommand($command)
    {
        return isset($this->commands[strtolower($command)]);
    }

    /**
     * Returns the FQN of the class that represent the specified command ID
     * registered in the current server profile.
     *
     * @param string $command Command ID.
     * @return string
     */
    public function getCommandClass($command)
    {
        if (isset($this->commands[$command = strtolower($command)])) {
            return $this->commands[$command];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createCommand($method, $arguments = array())
    {
        $method = strtolower($method);
        if (!isset($this->commands[$method])) {
            throw new ClientException("'$method' is not a registered Redis command");
        }

        $commandClass = $this->commands[$method];
        $command = new $commandClass();
        $command->setArguments($arguments);

        if (isset($this->processor)) {
            $this->processor->process($command);
        }

        return $command;
    }

    /**
     * Defines new commands in the server profile.
     *
     * @param array $commands Named list of command IDs and their classes.
     */
    public function defineCommands(Array $commands)
    {
        foreach ($commands as $alias => $command) {
            $this->defineCommand($alias, $command);
        }
    }

    /**
     * Defines a new commands in the server profile.
     *
     * @param string $alias Command ID.
     * @param string $command FQN of a class implementing Predis\Commands\ICommand.
     */
    public function defineCommand($alias, $command)
    {
        $commandReflection = new \ReflectionClass($command);
        if (!$commandReflection->isSubclassOf('Predis\Commands\ICommand')) {
            throw new \InvalidArgumentException("Cannot register '$command' as it is not a valid Redis command");
        }
        $this->commands[strtolower($alias)] = $command;
    }

    /**
     * {@inheritdoc}
     */
    public function setProcessor(ICommandProcessor $processor = null)
    {
        $this->processor = $processor;
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessor()
    {
        return $this->processor;
    }

    /**
     * Returns the version of server profile as its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getVersion();
    }
}

/**
 * Server profile for Redis v2.6.x.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerVersion26 extends ServerProfile
{
    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.6';
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedCommands()
    {
        return array(
            /* ---------------- Redis 1.2 ---------------- */

            /* commands operating on the key space */
            'exists'                    => 'Predis\Commands\KeyExists',
            'del'                       => 'Predis\Commands\KeyDelete',
            'type'                      => 'Predis\Commands\KeyType',
            'keys'                      => 'Predis\Commands\KeyKeys',
            'randomkey'                 => 'Predis\Commands\KeyRandom',
            'rename'                    => 'Predis\Commands\KeyRename',
            'renamenx'                  => 'Predis\Commands\KeyRenamePreserve',
            'expire'                    => 'Predis\Commands\KeyExpire',
            'expireat'                  => 'Predis\Commands\KeyExpireAt',
            'ttl'                       => 'Predis\Commands\KeyTimeToLive',
            'move'                      => 'Predis\Commands\KeyMove',
            'sort'                      => 'Predis\Commands\KeySort',

            /* commands operating on string values */
            'set'                       => 'Predis\Commands\StringSet',
            'setnx'                     => 'Predis\Commands\StringSetPreserve',
            'mset'                      => 'Predis\Commands\StringSetMultiple',
            'msetnx'                    => 'Predis\Commands\StringSetMultiplePreserve',
            'get'                       => 'Predis\Commands\StringGet',
            'mget'                      => 'Predis\Commands\StringGetMultiple',
            'getset'                    => 'Predis\Commands\StringGetSet',
            'incr'                      => 'Predis\Commands\StringIncrement',
            'incrby'                    => 'Predis\Commands\StringIncrementBy',
            'decr'                      => 'Predis\Commands\StringDecrement',
            'decrby'                    => 'Predis\Commands\StringDecrementBy',

            /* commands operating on lists */
            'rpush'                     => 'Predis\Commands\ListPushTail',
            'lpush'                     => 'Predis\Commands\ListPushHead',
            'llen'                      => 'Predis\Commands\ListLength',
            'lrange'                    => 'Predis\Commands\ListRange',
            'ltrim'                     => 'Predis\Commands\ListTrim',
            'lindex'                    => 'Predis\Commands\ListIndex',
            'lset'                      => 'Predis\Commands\ListSet',
            'lrem'                      => 'Predis\Commands\ListRemove',
            'lpop'                      => 'Predis\Commands\ListPopFirst',
            'rpop'                      => 'Predis\Commands\ListPopLast',
            'rpoplpush'                 => 'Predis\Commands\ListPopLastPushHead',

            /* commands operating on sets */
            'sadd'                      => 'Predis\Commands\SetAdd',
            'srem'                      => 'Predis\Commands\SetRemove',
            'spop'                      => 'Predis\Commands\SetPop',
            'smove'                     => 'Predis\Commands\SetMove',
            'scard'                     => 'Predis\Commands\SetCardinality',
            'sismember'                 => 'Predis\Commands\SetIsMember',
            'sinter'                    => 'Predis\Commands\SetIntersection',
            'sinterstore'               => 'Predis\Commands\SetIntersectionStore',
            'sunion'                    => 'Predis\Commands\SetUnion',
            'sunionstore'               => 'Predis\Commands\SetUnionStore',
            'sdiff'                     => 'Predis\Commands\SetDifference',
            'sdiffstore'                => 'Predis\Commands\SetDifferenceStore',
            'smembers'                  => 'Predis\Commands\SetMembers',
            'srandmember'               => 'Predis\Commands\SetRandomMember',

            /* commands operating on sorted sets */
            'zadd'                      => 'Predis\Commands\ZSetAdd',
            'zincrby'                   => 'Predis\Commands\ZSetIncrementBy',
            'zrem'                      => 'Predis\Commands\ZSetRemove',
            'zrange'                    => 'Predis\Commands\ZSetRange',
            'zrevrange'                 => 'Predis\Commands\ZSetReverseRange',
            'zrangebyscore'             => 'Predis\Commands\ZSetRangeByScore',
            'zcard'                     => 'Predis\Commands\ZSetCardinality',
            'zscore'                    => 'Predis\Commands\ZSetScore',
            'zremrangebyscore'          => 'Predis\Commands\ZSetRemoveRangeByScore',

            /* connection related commands */
            'ping'                      => 'Predis\Commands\ConnectionPing',
            'auth'                      => 'Predis\Commands\ConnectionAuth',
            'select'                    => 'Predis\Commands\ConnectionSelect',
            'echo'                      => 'Predis\Commands\ConnectionEcho',
            'quit'                      => 'Predis\Commands\ConnectionQuit',

            /* remote server control commands */
            'info'                      => 'Predis\Commands\ServerInfo',
            'slaveof'                   => 'Predis\Commands\ServerSlaveOf',
            'monitor'                   => 'Predis\Commands\ServerMonitor',
            'dbsize'                    => 'Predis\Commands\ServerDatabaseSize',
            'flushdb'                   => 'Predis\Commands\ServerFlushDatabase',
            'flushall'                  => 'Predis\Commands\ServerFlushAll',
            'save'                      => 'Predis\Commands\ServerSave',
            'bgsave'                    => 'Predis\Commands\ServerBackgroundSave',
            'lastsave'                  => 'Predis\Commands\ServerLastSave',
            'shutdown'                  => 'Predis\Commands\ServerShutdown',
            'bgrewriteaof'              => 'Predis\Commands\ServerBackgroundRewriteAOF',


            /* ---------------- Redis 2.0 ---------------- */

            /* commands operating on string values */
            'setex'                     => 'Predis\Commands\StringSetExpire',
            'append'                    => 'Predis\Commands\StringAppend',
            'substr'                    => 'Predis\Commands\StringSubstr',

            /* commands operating on lists */
            'blpop'                     => 'Predis\Commands\ListPopFirstBlocking',
            'brpop'                     => 'Predis\Commands\ListPopLastBlocking',

            /* commands operating on sorted sets */
            'zunionstore'               => 'Predis\Commands\ZSetUnionStore',
            'zinterstore'               => 'Predis\Commands\ZSetIntersectionStore',
            'zcount'                    => 'Predis\Commands\ZSetCount',
            'zrank'                     => 'Predis\Commands\ZSetRank',
            'zrevrank'                  => 'Predis\Commands\ZSetReverseRank',
            'zremrangebyrank'           => 'Predis\Commands\ZSetRemoveRangeByRank',

            /* commands operating on hashes */
            'hset'                      => 'Predis\Commands\HashSet',
            'hsetnx'                    => 'Predis\Commands\HashSetPreserve',
            'hmset'                     => 'Predis\Commands\HashSetMultiple',
            'hincrby'                   => 'Predis\Commands\HashIncrementBy',
            'hget'                      => 'Predis\Commands\HashGet',
            'hmget'                     => 'Predis\Commands\HashGetMultiple',
            'hdel'                      => 'Predis\Commands\HashDelete',
            'hexists'                   => 'Predis\Commands\HashExists',
            'hlen'                      => 'Predis\Commands\HashLength',
            'hkeys'                     => 'Predis\Commands\HashKeys',
            'hvals'                     => 'Predis\Commands\HashValues',
            'hgetall'                   => 'Predis\Commands\HashGetAll',

            /* transactions */
            'multi'                     => 'Predis\Commands\TransactionMulti',
            'exec'                      => 'Predis\Commands\TransactionExec',
            'discard'                   => 'Predis\Commands\TransactionDiscard',

            /* publish - subscribe */
            'subscribe'                 => 'Predis\Commands\PubSubSubscribe',
            'unsubscribe'               => 'Predis\Commands\PubSubUnsubscribe',
            'psubscribe'                => 'Predis\Commands\PubSubSubscribeByPattern',
            'punsubscribe'              => 'Predis\Commands\PubSubUnsubscribeByPattern',
            'publish'                   => 'Predis\Commands\PubSubPublish',

            /* remote server control commands */
            'config'                    => 'Predis\Commands\ServerConfig',


            /* ---------------- Redis 2.2 ---------------- */

            /* commands operating on the key space */
            'persist'                   => 'Predis\Commands\KeyPersist',

            /* commands operating on string values */
            'strlen'                    => 'Predis\Commands\StringStrlen',
            'setrange'                  => 'Predis\Commands\StringSetRange',
            'getrange'                  => 'Predis\Commands\StringGetRange',
            'setbit'                    => 'Predis\Commands\StringSetBit',
            'getbit'                    => 'Predis\Commands\StringGetBit',

            /* commands operating on lists */
            'rpushx'                    => 'Predis\Commands\ListPushTailX',
            'lpushx'                    => 'Predis\Commands\ListPushHeadX',
            'linsert'                   => 'Predis\Commands\ListInsert',
            'brpoplpush'                => 'Predis\Commands\ListPopLastPushHeadBlocking',

            /* commands operating on sorted sets */
            'zrevrangebyscore'          => 'Predis\Commands\ZSetReverseRangeByScore',

            /* transactions */
            'watch'                     => 'Predis\Commands\TransactionWatch',
            'unwatch'                   => 'Predis\Commands\TransactionUnwatch',

            /* remote server control commands */
            'object'                    => 'Predis\Commands\ServerObject',
            'slowlog'                   => 'Predis\Commands\ServerSlowlog',


            /* ---------------- Redis 2.4 ---------------- */

            /* remote server control commands */
            'client'                    => 'Predis\Commands\ServerClient',


            /* ---------------- Redis 2.6 ---------------- */

            /* commands operating on the key space */
            'pttl'                      => 'Predis\Commands\KeyPreciseTimeToLive',
            'pexpire'                   => 'Predis\Commands\KeyPreciseExpire',
            'pexpireat'                 => 'Predis\Commands\KeyPreciseExpireAt',

            /* commands operating on string values */
            'psetex'                    => 'Predis\Commands\StringPreciseSetExpire',
            'incrbyfloat'               => 'Predis\Commands\StringIncrementByFloat',

            /* commands operating on hashes */
            'hincrbyfloat'              => 'Predis\Commands\HashIncrementByFloat',

            /* scripting */
            'eval'                      => 'Predis\Commands\ServerEval',
            'evalsha'                   => 'Predis\Commands\ServerEvalSHA',
            'script'                    => 'Predis\Commands\ServerScript',

            /* remote server control commands */
            'info'                      => 'Predis\Commands\ServerInfoV26x',
            'time'                      => 'Predis\Commands\ServerTime',
        );
    }
}

/**
 * Server profile for the current unstable version of Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerVersionNext extends ServerVersion26
{
    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.8';
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedCommands()
    {
        return array_merge(parent::getSupportedCommands(), array(
        ));
    }
}

/**
 * Server profile for Redis v1.2.x.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerVersion12 extends ServerProfile
{
    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '1.2';
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedCommands()
    {
        return array(
            /* ---------------- Redis 1.2 ---------------- */

            /* commands operating on the key space */
            'exists'                    => 'Predis\Commands\KeyExists',
            'del'                       => 'Predis\Commands\KeyDelete',
            'type'                      => 'Predis\Commands\KeyType',
            'keys'                      => 'Predis\Commands\KeyKeysV12x',
            'randomkey'                 => 'Predis\Commands\KeyRandom',
            'rename'                    => 'Predis\Commands\KeyRename',
            'renamenx'                  => 'Predis\Commands\KeyRenamePreserve',
            'expire'                    => 'Predis\Commands\KeyExpire',
            'expireat'                  => 'Predis\Commands\KeyExpireAt',
            'ttl'                       => 'Predis\Commands\KeyTimeToLive',
            'move'                      => 'Predis\Commands\KeyMove',
            'sort'                      => 'Predis\Commands\KeySort',

            /* commands operating on string values */
            'set'                       => 'Predis\Commands\StringSet',
            'setnx'                     => 'Predis\Commands\StringSetPreserve',
            'mset'                      => 'Predis\Commands\StringSetMultiple',
            'msetnx'                    => 'Predis\Commands\StringSetMultiplePreserve',
            'get'                       => 'Predis\Commands\StringGet',
            'mget'                      => 'Predis\Commands\StringGetMultiple',
            'getset'                    => 'Predis\Commands\StringGetSet',
            'incr'                      => 'Predis\Commands\StringIncrement',
            'incrby'                    => 'Predis\Commands\StringIncrementBy',
            'decr'                      => 'Predis\Commands\StringDecrement',
            'decrby'                    => 'Predis\Commands\StringDecrementBy',

            /* commands operating on lists */
            'rpush'                     => 'Predis\Commands\ListPushTail',
            'lpush'                     => 'Predis\Commands\ListPushHead',
            'llen'                      => 'Predis\Commands\ListLength',
            'lrange'                    => 'Predis\Commands\ListRange',
            'ltrim'                     => 'Predis\Commands\ListTrim',
            'lindex'                    => 'Predis\Commands\ListIndex',
            'lset'                      => 'Predis\Commands\ListSet',
            'lrem'                      => 'Predis\Commands\ListRemove',
            'lpop'                      => 'Predis\Commands\ListPopFirst',
            'rpop'                      => 'Predis\Commands\ListPopLast',
            'rpoplpush'                 => 'Predis\Commands\ListPopLastPushHead',

            /* commands operating on sets */
            'sadd'                      => 'Predis\Commands\SetAdd',
            'srem'                      => 'Predis\Commands\SetRemove',
            'spop'                      => 'Predis\Commands\SetPop',
            'smove'                     => 'Predis\Commands\SetMove',
            'scard'                     => 'Predis\Commands\SetCardinality',
            'sismember'                 => 'Predis\Commands\SetIsMember',
            'sinter'                    => 'Predis\Commands\SetIntersection',
            'sinterstore'               => 'Predis\Commands\SetIntersectionStore',
            'sunion'                    => 'Predis\Commands\SetUnion',
            'sunionstore'               => 'Predis\Commands\SetUnionStore',
            'sdiff'                     => 'Predis\Commands\SetDifference',
            'sdiffstore'                => 'Predis\Commands\SetDifferenceStore',
            'smembers'                  => 'Predis\Commands\SetMembers',
            'srandmember'               => 'Predis\Commands\SetRandomMember',

            /* commands operating on sorted sets */
            'zadd'                      => 'Predis\Commands\ZSetAdd',
            'zincrby'                   => 'Predis\Commands\ZSetIncrementBy',
            'zrem'                      => 'Predis\Commands\ZSetRemove',
            'zrange'                    => 'Predis\Commands\ZSetRange',
            'zrevrange'                 => 'Predis\Commands\ZSetReverseRange',
            'zrangebyscore'             => 'Predis\Commands\ZSetRangeByScore',
            'zcard'                     => 'Predis\Commands\ZSetCardinality',
            'zscore'                    => 'Predis\Commands\ZSetScore',
            'zremrangebyscore'          => 'Predis\Commands\ZSetRemoveRangeByScore',

            /* connection related commands */
            'ping'                      => 'Predis\Commands\ConnectionPing',
            'auth'                      => 'Predis\Commands\ConnectionAuth',
            'select'                    => 'Predis\Commands\ConnectionSelect',
            'echo'                      => 'Predis\Commands\ConnectionEcho',
            'quit'                      => 'Predis\Commands\ConnectionQuit',

            /* remote server control commands */
            'info'                      => 'Predis\Commands\ServerInfo',
            'slaveof'                   => 'Predis\Commands\ServerSlaveOf',
            'monitor'                   => 'Predis\Commands\ServerMonitor',
            'dbsize'                    => 'Predis\Commands\ServerDatabaseSize',
            'flushdb'                   => 'Predis\Commands\ServerFlushDatabase',
            'flushall'                  => 'Predis\Commands\ServerFlushAll',
            'save'                      => 'Predis\Commands\ServerSave',
            'bgsave'                    => 'Predis\Commands\ServerBackgroundSave',
            'lastsave'                  => 'Predis\Commands\ServerLastSave',
            'shutdown'                  => 'Predis\Commands\ServerShutdown',
            'bgrewriteaof'              => 'Predis\Commands\ServerBackgroundRewriteAOF',
        );
    }
}

/**
 * Server profile for Redis v2.2.x.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerVersion22 extends ServerProfile
{
    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.2';
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedCommands()
    {
        return array(
            /* ---------------- Redis 1.2 ---------------- */

            /* commands operating on the key space */
            'exists'                    => 'Predis\Commands\KeyExists',
            'del'                       => 'Predis\Commands\KeyDelete',
            'type'                      => 'Predis\Commands\KeyType',
            'keys'                      => 'Predis\Commands\KeyKeys',
            'randomkey'                 => 'Predis\Commands\KeyRandom',
            'rename'                    => 'Predis\Commands\KeyRename',
            'renamenx'                  => 'Predis\Commands\KeyRenamePreserve',
            'expire'                    => 'Predis\Commands\KeyExpire',
            'expireat'                  => 'Predis\Commands\KeyExpireAt',
            'ttl'                       => 'Predis\Commands\KeyTimeToLive',
            'move'                      => 'Predis\Commands\KeyMove',
            'sort'                      => 'Predis\Commands\KeySort',

            /* commands operating on string values */
            'set'                       => 'Predis\Commands\StringSet',
            'setnx'                     => 'Predis\Commands\StringSetPreserve',
            'mset'                      => 'Predis\Commands\StringSetMultiple',
            'msetnx'                    => 'Predis\Commands\StringSetMultiplePreserve',
            'get'                       => 'Predis\Commands\StringGet',
            'mget'                      => 'Predis\Commands\StringGetMultiple',
            'getset'                    => 'Predis\Commands\StringGetSet',
            'incr'                      => 'Predis\Commands\StringIncrement',
            'incrby'                    => 'Predis\Commands\StringIncrementBy',
            'decr'                      => 'Predis\Commands\StringDecrement',
            'decrby'                    => 'Predis\Commands\StringDecrementBy',

            /* commands operating on lists */
            'rpush'                     => 'Predis\Commands\ListPushTail',
            'lpush'                     => 'Predis\Commands\ListPushHead',
            'llen'                      => 'Predis\Commands\ListLength',
            'lrange'                    => 'Predis\Commands\ListRange',
            'ltrim'                     => 'Predis\Commands\ListTrim',
            'lindex'                    => 'Predis\Commands\ListIndex',
            'lset'                      => 'Predis\Commands\ListSet',
            'lrem'                      => 'Predis\Commands\ListRemove',
            'lpop'                      => 'Predis\Commands\ListPopFirst',
            'rpop'                      => 'Predis\Commands\ListPopLast',
            'rpoplpush'                 => 'Predis\Commands\ListPopLastPushHead',

            /* commands operating on sets */
            'sadd'                      => 'Predis\Commands\SetAdd',
            'srem'                      => 'Predis\Commands\SetRemove',
            'spop'                      => 'Predis\Commands\SetPop',
            'smove'                     => 'Predis\Commands\SetMove',
            'scard'                     => 'Predis\Commands\SetCardinality',
            'sismember'                 => 'Predis\Commands\SetIsMember',
            'sinter'                    => 'Predis\Commands\SetIntersection',
            'sinterstore'               => 'Predis\Commands\SetIntersectionStore',
            'sunion'                    => 'Predis\Commands\SetUnion',
            'sunionstore'               => 'Predis\Commands\SetUnionStore',
            'sdiff'                     => 'Predis\Commands\SetDifference',
            'sdiffstore'                => 'Predis\Commands\SetDifferenceStore',
            'smembers'                  => 'Predis\Commands\SetMembers',
            'srandmember'               => 'Predis\Commands\SetRandomMember',

            /* commands operating on sorted sets */
            'zadd'                      => 'Predis\Commands\ZSetAdd',
            'zincrby'                   => 'Predis\Commands\ZSetIncrementBy',
            'zrem'                      => 'Predis\Commands\ZSetRemove',
            'zrange'                    => 'Predis\Commands\ZSetRange',
            'zrevrange'                 => 'Predis\Commands\ZSetReverseRange',
            'zrangebyscore'             => 'Predis\Commands\ZSetRangeByScore',
            'zcard'                     => 'Predis\Commands\ZSetCardinality',
            'zscore'                    => 'Predis\Commands\ZSetScore',
            'zremrangebyscore'          => 'Predis\Commands\ZSetRemoveRangeByScore',

            /* connection related commands */
            'ping'                      => 'Predis\Commands\ConnectionPing',
            'auth'                      => 'Predis\Commands\ConnectionAuth',
            'select'                    => 'Predis\Commands\ConnectionSelect',
            'echo'                      => 'Predis\Commands\ConnectionEcho',
            'quit'                      => 'Predis\Commands\ConnectionQuit',

            /* remote server control commands */
            'info'                      => 'Predis\Commands\ServerInfo',
            'slaveof'                   => 'Predis\Commands\ServerSlaveOf',
            'monitor'                   => 'Predis\Commands\ServerMonitor',
            'dbsize'                    => 'Predis\Commands\ServerDatabaseSize',
            'flushdb'                   => 'Predis\Commands\ServerFlushDatabase',
            'flushall'                  => 'Predis\Commands\ServerFlushAll',
            'save'                      => 'Predis\Commands\ServerSave',
            'bgsave'                    => 'Predis\Commands\ServerBackgroundSave',
            'lastsave'                  => 'Predis\Commands\ServerLastSave',
            'shutdown'                  => 'Predis\Commands\ServerShutdown',
            'bgrewriteaof'              => 'Predis\Commands\ServerBackgroundRewriteAOF',


            /* ---------------- Redis 2.0 ---------------- */

            /* commands operating on string values */
            'setex'                     => 'Predis\Commands\StringSetExpire',
            'append'                    => 'Predis\Commands\StringAppend',
            'substr'                    => 'Predis\Commands\StringSubstr',

            /* commands operating on lists */
            'blpop'                     => 'Predis\Commands\ListPopFirstBlocking',
            'brpop'                     => 'Predis\Commands\ListPopLastBlocking',

            /* commands operating on sorted sets */
            'zunionstore'               => 'Predis\Commands\ZSetUnionStore',
            'zinterstore'               => 'Predis\Commands\ZSetIntersectionStore',
            'zcount'                    => 'Predis\Commands\ZSetCount',
            'zrank'                     => 'Predis\Commands\ZSetRank',
            'zrevrank'                  => 'Predis\Commands\ZSetReverseRank',
            'zremrangebyrank'           => 'Predis\Commands\ZSetRemoveRangeByRank',

            /* commands operating on hashes */
            'hset'                      => 'Predis\Commands\HashSet',
            'hsetnx'                    => 'Predis\Commands\HashSetPreserve',
            'hmset'                     => 'Predis\Commands\HashSetMultiple',
            'hincrby'                   => 'Predis\Commands\HashIncrementBy',
            'hget'                      => 'Predis\Commands\HashGet',
            'hmget'                     => 'Predis\Commands\HashGetMultiple',
            'hdel'                      => 'Predis\Commands\HashDelete',
            'hexists'                   => 'Predis\Commands\HashExists',
            'hlen'                      => 'Predis\Commands\HashLength',
            'hkeys'                     => 'Predis\Commands\HashKeys',
            'hvals'                     => 'Predis\Commands\HashValues',
            'hgetall'                   => 'Predis\Commands\HashGetAll',

            /* transactions */
            'multi'                     => 'Predis\Commands\TransactionMulti',
            'exec'                      => 'Predis\Commands\TransactionExec',
            'discard'                   => 'Predis\Commands\TransactionDiscard',

            /* publish - subscribe */
            'subscribe'                 => 'Predis\Commands\PubSubSubscribe',
            'unsubscribe'               => 'Predis\Commands\PubSubUnsubscribe',
            'psubscribe'                => 'Predis\Commands\PubSubSubscribeByPattern',
            'punsubscribe'              => 'Predis\Commands\PubSubUnsubscribeByPattern',
            'publish'                   => 'Predis\Commands\PubSubPublish',

            /* remote server control commands */
            'config'                    => 'Predis\Commands\ServerConfig',


            /* ---------------- Redis 2.2 ---------------- */

            /* commands operating on the key space */
            'persist'                   => 'Predis\Commands\KeyPersist',

            /* commands operating on string values */
            'strlen'                    => 'Predis\Commands\StringStrlen',
            'setrange'                  => 'Predis\Commands\StringSetRange',
            'getrange'                  => 'Predis\Commands\StringGetRange',
            'setbit'                    => 'Predis\Commands\StringSetBit',
            'getbit'                    => 'Predis\Commands\StringGetBit',

            /* commands operating on lists */
            'rpushx'                    => 'Predis\Commands\ListPushTailX',
            'lpushx'                    => 'Predis\Commands\ListPushHeadX',
            'linsert'                   => 'Predis\Commands\ListInsert',
            'brpoplpush'                => 'Predis\Commands\ListPopLastPushHeadBlocking',

            /* commands operating on sorted sets */
            'zrevrangebyscore'          => 'Predis\Commands\ZSetReverseRangeByScore',

            /* transactions */
            'watch'                     => 'Predis\Commands\TransactionWatch',
            'unwatch'                   => 'Predis\Commands\TransactionUnwatch',

            /* remote server control commands */
            'object'                    => 'Predis\Commands\ServerObject',
            'slowlog'                   => 'Predis\Commands\ServerSlowlog',
        );
    }
}

/**
 * Server profile for Redis v2.0.x.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerVersion20 extends ServerProfile
{
    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedCommands()
    {
        return array(
            /* ---------------- Redis 1.2 ---------------- */

            /* commands operating on the key space */
            'exists'                    => 'Predis\Commands\KeyExists',
            'del'                       => 'Predis\Commands\KeyDelete',
            'type'                      => 'Predis\Commands\KeyType',
            'keys'                      => 'Predis\Commands\KeyKeys',
            'randomkey'                 => 'Predis\Commands\KeyRandom',
            'rename'                    => 'Predis\Commands\KeyRename',
            'renamenx'                  => 'Predis\Commands\KeyRenamePreserve',
            'expire'                    => 'Predis\Commands\KeyExpire',
            'expireat'                  => 'Predis\Commands\KeyExpireAt',
            'ttl'                       => 'Predis\Commands\KeyTimeToLive',
            'move'                      => 'Predis\Commands\KeyMove',
            'sort'                      => 'Predis\Commands\KeySort',

            /* commands operating on string values */
            'set'                       => 'Predis\Commands\StringSet',
            'setnx'                     => 'Predis\Commands\StringSetPreserve',
            'mset'                      => 'Predis\Commands\StringSetMultiple',
            'msetnx'                    => 'Predis\Commands\StringSetMultiplePreserve',
            'get'                       => 'Predis\Commands\StringGet',
            'mget'                      => 'Predis\Commands\StringGetMultiple',
            'getset'                    => 'Predis\Commands\StringGetSet',
            'incr'                      => 'Predis\Commands\StringIncrement',
            'incrby'                    => 'Predis\Commands\StringIncrementBy',
            'decr'                      => 'Predis\Commands\StringDecrement',
            'decrby'                    => 'Predis\Commands\StringDecrementBy',

            /* commands operating on lists */
            'rpush'                     => 'Predis\Commands\ListPushTail',
            'lpush'                     => 'Predis\Commands\ListPushHead',
            'llen'                      => 'Predis\Commands\ListLength',
            'lrange'                    => 'Predis\Commands\ListRange',
            'ltrim'                     => 'Predis\Commands\ListTrim',
            'lindex'                    => 'Predis\Commands\ListIndex',
            'lset'                      => 'Predis\Commands\ListSet',
            'lrem'                      => 'Predis\Commands\ListRemove',
            'lpop'                      => 'Predis\Commands\ListPopFirst',
            'rpop'                      => 'Predis\Commands\ListPopLast',
            'rpoplpush'                 => 'Predis\Commands\ListPopLastPushHead',

            /* commands operating on sets */
            'sadd'                      => 'Predis\Commands\SetAdd',
            'srem'                      => 'Predis\Commands\SetRemove',
            'spop'                      => 'Predis\Commands\SetPop',
            'smove'                     => 'Predis\Commands\SetMove',
            'scard'                     => 'Predis\Commands\SetCardinality',
            'sismember'                 => 'Predis\Commands\SetIsMember',
            'sinter'                    => 'Predis\Commands\SetIntersection',
            'sinterstore'               => 'Predis\Commands\SetIntersectionStore',
            'sunion'                    => 'Predis\Commands\SetUnion',
            'sunionstore'               => 'Predis\Commands\SetUnionStore',
            'sdiff'                     => 'Predis\Commands\SetDifference',
            'sdiffstore'                => 'Predis\Commands\SetDifferenceStore',
            'smembers'                  => 'Predis\Commands\SetMembers',
            'srandmember'               => 'Predis\Commands\SetRandomMember',

            /* commands operating on sorted sets */
            'zadd'                      => 'Predis\Commands\ZSetAdd',
            'zincrby'                   => 'Predis\Commands\ZSetIncrementBy',
            'zrem'                      => 'Predis\Commands\ZSetRemove',
            'zrange'                    => 'Predis\Commands\ZSetRange',
            'zrevrange'                 => 'Predis\Commands\ZSetReverseRange',
            'zrangebyscore'             => 'Predis\Commands\ZSetRangeByScore',
            'zcard'                     => 'Predis\Commands\ZSetCardinality',
            'zscore'                    => 'Predis\Commands\ZSetScore',
            'zremrangebyscore'          => 'Predis\Commands\ZSetRemoveRangeByScore',

            /* connection related commands */
            'ping'                      => 'Predis\Commands\ConnectionPing',
            'auth'                      => 'Predis\Commands\ConnectionAuth',
            'select'                    => 'Predis\Commands\ConnectionSelect',
            'echo'                      => 'Predis\Commands\ConnectionEcho',
            'quit'                      => 'Predis\Commands\ConnectionQuit',

            /* remote server control commands */
            'info'                      => 'Predis\Commands\ServerInfo',
            'slaveof'                   => 'Predis\Commands\ServerSlaveOf',
            'monitor'                   => 'Predis\Commands\ServerMonitor',
            'dbsize'                    => 'Predis\Commands\ServerDatabaseSize',
            'flushdb'                   => 'Predis\Commands\ServerFlushDatabase',
            'flushall'                  => 'Predis\Commands\ServerFlushAll',
            'save'                      => 'Predis\Commands\ServerSave',
            'bgsave'                    => 'Predis\Commands\ServerBackgroundSave',
            'lastsave'                  => 'Predis\Commands\ServerLastSave',
            'shutdown'                  => 'Predis\Commands\ServerShutdown',
            'bgrewriteaof'              => 'Predis\Commands\ServerBackgroundRewriteAOF',


            /* ---------------- Redis 2.0 ---------------- */

            /* commands operating on string values */
            'setex'                     => 'Predis\Commands\StringSetExpire',
            'append'                    => 'Predis\Commands\StringAppend',
            'substr'                    => 'Predis\Commands\StringSubstr',

            /* commands operating on lists */
            'blpop'                     => 'Predis\Commands\ListPopFirstBlocking',
            'brpop'                     => 'Predis\Commands\ListPopLastBlocking',

            /* commands operating on sorted sets */
            'zunionstore'               => 'Predis\Commands\ZSetUnionStore',
            'zinterstore'               => 'Predis\Commands\ZSetIntersectionStore',
            'zcount'                    => 'Predis\Commands\ZSetCount',
            'zrank'                     => 'Predis\Commands\ZSetRank',
            'zrevrank'                  => 'Predis\Commands\ZSetReverseRank',
            'zremrangebyrank'           => 'Predis\Commands\ZSetRemoveRangeByRank',

            /* commands operating on hashes */
            'hset'                      => 'Predis\Commands\HashSet',
            'hsetnx'                    => 'Predis\Commands\HashSetPreserve',
            'hmset'                     => 'Predis\Commands\HashSetMultiple',
            'hincrby'                   => 'Predis\Commands\HashIncrementBy',
            'hget'                      => 'Predis\Commands\HashGet',
            'hmget'                     => 'Predis\Commands\HashGetMultiple',
            'hdel'                      => 'Predis\Commands\HashDelete',
            'hexists'                   => 'Predis\Commands\HashExists',
            'hlen'                      => 'Predis\Commands\HashLength',
            'hkeys'                     => 'Predis\Commands\HashKeys',
            'hvals'                     => 'Predis\Commands\HashValues',
            'hgetall'                   => 'Predis\Commands\HashGetAll',

            /* transactions */
            'multi'                     => 'Predis\Commands\TransactionMulti',
            'exec'                      => 'Predis\Commands\TransactionExec',
            'discard'                   => 'Predis\Commands\TransactionDiscard',

            /* publish - subscribe */
            'subscribe'                 => 'Predis\Commands\PubSubSubscribe',
            'unsubscribe'               => 'Predis\Commands\PubSubUnsubscribe',
            'psubscribe'                => 'Predis\Commands\PubSubSubscribeByPattern',
            'punsubscribe'              => 'Predis\Commands\PubSubUnsubscribeByPattern',
            'publish'                   => 'Predis\Commands\PubSubPublish',

            /* remote server control commands */
            'config'                    => 'Predis\Commands\ServerConfig',
        );
    }
}

/**
 * Server profile for Redis v2.4.x.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerVersion24 extends ServerProfile
{
    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '2.4';
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedCommands()
    {
        return array(
            /* ---------------- Redis 1.2 ---------------- */

            /* commands operating on the key space */
            'exists'                    => 'Predis\Commands\KeyExists',
            'del'                       => 'Predis\Commands\KeyDelete',
            'type'                      => 'Predis\Commands\KeyType',
            'keys'                      => 'Predis\Commands\KeyKeys',
            'randomkey'                 => 'Predis\Commands\KeyRandom',
            'rename'                    => 'Predis\Commands\KeyRename',
            'renamenx'                  => 'Predis\Commands\KeyRenamePreserve',
            'expire'                    => 'Predis\Commands\KeyExpire',
            'expireat'                  => 'Predis\Commands\KeyExpireAt',
            'ttl'                       => 'Predis\Commands\KeyTimeToLive',
            'move'                      => 'Predis\Commands\KeyMove',
            'sort'                      => 'Predis\Commands\KeySort',

            /* commands operating on string values */
            'set'                       => 'Predis\Commands\StringSet',
            'setnx'                     => 'Predis\Commands\StringSetPreserve',
            'mset'                      => 'Predis\Commands\StringSetMultiple',
            'msetnx'                    => 'Predis\Commands\StringSetMultiplePreserve',
            'get'                       => 'Predis\Commands\StringGet',
            'mget'                      => 'Predis\Commands\StringGetMultiple',
            'getset'                    => 'Predis\Commands\StringGetSet',
            'incr'                      => 'Predis\Commands\StringIncrement',
            'incrby'                    => 'Predis\Commands\StringIncrementBy',
            'decr'                      => 'Predis\Commands\StringDecrement',
            'decrby'                    => 'Predis\Commands\StringDecrementBy',

            /* commands operating on lists */
            'rpush'                     => 'Predis\Commands\ListPushTail',
            'lpush'                     => 'Predis\Commands\ListPushHead',
            'llen'                      => 'Predis\Commands\ListLength',
            'lrange'                    => 'Predis\Commands\ListRange',
            'ltrim'                     => 'Predis\Commands\ListTrim',
            'lindex'                    => 'Predis\Commands\ListIndex',
            'lset'                      => 'Predis\Commands\ListSet',
            'lrem'                      => 'Predis\Commands\ListRemove',
            'lpop'                      => 'Predis\Commands\ListPopFirst',
            'rpop'                      => 'Predis\Commands\ListPopLast',
            'rpoplpush'                 => 'Predis\Commands\ListPopLastPushHead',

            /* commands operating on sets */
            'sadd'                      => 'Predis\Commands\SetAdd',
            'srem'                      => 'Predis\Commands\SetRemove',
            'spop'                      => 'Predis\Commands\SetPop',
            'smove'                     => 'Predis\Commands\SetMove',
            'scard'                     => 'Predis\Commands\SetCardinality',
            'sismember'                 => 'Predis\Commands\SetIsMember',
            'sinter'                    => 'Predis\Commands\SetIntersection',
            'sinterstore'               => 'Predis\Commands\SetIntersectionStore',
            'sunion'                    => 'Predis\Commands\SetUnion',
            'sunionstore'               => 'Predis\Commands\SetUnionStore',
            'sdiff'                     => 'Predis\Commands\SetDifference',
            'sdiffstore'                => 'Predis\Commands\SetDifferenceStore',
            'smembers'                  => 'Predis\Commands\SetMembers',
            'srandmember'               => 'Predis\Commands\SetRandomMember',

            /* commands operating on sorted sets */
            'zadd'                      => 'Predis\Commands\ZSetAdd',
            'zincrby'                   => 'Predis\Commands\ZSetIncrementBy',
            'zrem'                      => 'Predis\Commands\ZSetRemove',
            'zrange'                    => 'Predis\Commands\ZSetRange',
            'zrevrange'                 => 'Predis\Commands\ZSetReverseRange',
            'zrangebyscore'             => 'Predis\Commands\ZSetRangeByScore',
            'zcard'                     => 'Predis\Commands\ZSetCardinality',
            'zscore'                    => 'Predis\Commands\ZSetScore',
            'zremrangebyscore'          => 'Predis\Commands\ZSetRemoveRangeByScore',

            /* connection related commands */
            'ping'                      => 'Predis\Commands\ConnectionPing',
            'auth'                      => 'Predis\Commands\ConnectionAuth',
            'select'                    => 'Predis\Commands\ConnectionSelect',
            'echo'                      => 'Predis\Commands\ConnectionEcho',
            'quit'                      => 'Predis\Commands\ConnectionQuit',

            /* remote server control commands */
            'info'                      => 'Predis\Commands\ServerInfo',
            'slaveof'                   => 'Predis\Commands\ServerSlaveOf',
            'monitor'                   => 'Predis\Commands\ServerMonitor',
            'dbsize'                    => 'Predis\Commands\ServerDatabaseSize',
            'flushdb'                   => 'Predis\Commands\ServerFlushDatabase',
            'flushall'                  => 'Predis\Commands\ServerFlushAll',
            'save'                      => 'Predis\Commands\ServerSave',
            'bgsave'                    => 'Predis\Commands\ServerBackgroundSave',
            'lastsave'                  => 'Predis\Commands\ServerLastSave',
            'shutdown'                  => 'Predis\Commands\ServerShutdown',
            'bgrewriteaof'              => 'Predis\Commands\ServerBackgroundRewriteAOF',


            /* ---------------- Redis 2.0 ---------------- */

            /* commands operating on string values */
            'setex'                     => 'Predis\Commands\StringSetExpire',
            'append'                    => 'Predis\Commands\StringAppend',
            'substr'                    => 'Predis\Commands\StringSubstr',

            /* commands operating on lists */
            'blpop'                     => 'Predis\Commands\ListPopFirstBlocking',
            'brpop'                     => 'Predis\Commands\ListPopLastBlocking',

            /* commands operating on sorted sets */
            'zunionstore'               => 'Predis\Commands\ZSetUnionStore',
            'zinterstore'               => 'Predis\Commands\ZSetIntersectionStore',
            'zcount'                    => 'Predis\Commands\ZSetCount',
            'zrank'                     => 'Predis\Commands\ZSetRank',
            'zrevrank'                  => 'Predis\Commands\ZSetReverseRank',
            'zremrangebyrank'           => 'Predis\Commands\ZSetRemoveRangeByRank',

            /* commands operating on hashes */
            'hset'                      => 'Predis\Commands\HashSet',
            'hsetnx'                    => 'Predis\Commands\HashSetPreserve',
            'hmset'                     => 'Predis\Commands\HashSetMultiple',
            'hincrby'                   => 'Predis\Commands\HashIncrementBy',
            'hget'                      => 'Predis\Commands\HashGet',
            'hmget'                     => 'Predis\Commands\HashGetMultiple',
            'hdel'                      => 'Predis\Commands\HashDelete',
            'hexists'                   => 'Predis\Commands\HashExists',
            'hlen'                      => 'Predis\Commands\HashLength',
            'hkeys'                     => 'Predis\Commands\HashKeys',
            'hvals'                     => 'Predis\Commands\HashValues',
            'hgetall'                   => 'Predis\Commands\HashGetAll',

            /* transactions */
            'multi'                     => 'Predis\Commands\TransactionMulti',
            'exec'                      => 'Predis\Commands\TransactionExec',
            'discard'                   => 'Predis\Commands\TransactionDiscard',

            /* publish - subscribe */
            'subscribe'                 => 'Predis\Commands\PubSubSubscribe',
            'unsubscribe'               => 'Predis\Commands\PubSubUnsubscribe',
            'psubscribe'                => 'Predis\Commands\PubSubSubscribeByPattern',
            'punsubscribe'              => 'Predis\Commands\PubSubUnsubscribeByPattern',
            'publish'                   => 'Predis\Commands\PubSubPublish',

            /* remote server control commands */
            'config'                    => 'Predis\Commands\ServerConfig',


            /* ---------------- Redis 2.2 ---------------- */

            /* commands operating on the key space */
            'persist'                   => 'Predis\Commands\KeyPersist',

            /* commands operating on string values */
            'strlen'                    => 'Predis\Commands\StringStrlen',
            'setrange'                  => 'Predis\Commands\StringSetRange',
            'getrange'                  => 'Predis\Commands\StringGetRange',
            'setbit'                    => 'Predis\Commands\StringSetBit',
            'getbit'                    => 'Predis\Commands\StringGetBit',

            /* commands operating on lists */
            'rpushx'                    => 'Predis\Commands\ListPushTailX',
            'lpushx'                    => 'Predis\Commands\ListPushHeadX',
            'linsert'                   => 'Predis\Commands\ListInsert',
            'brpoplpush'                => 'Predis\Commands\ListPopLastPushHeadBlocking',

            /* commands operating on sorted sets */
            'zrevrangebyscore'          => 'Predis\Commands\ZSetReverseRangeByScore',

            /* transactions */
            'watch'                     => 'Predis\Commands\TransactionWatch',
            'unwatch'                   => 'Predis\Commands\TransactionUnwatch',

            /* remote server control commands */
            'object'                    => 'Predis\Commands\ServerObject',
            'slowlog'                   => 'Predis\Commands\ServerSlowlog',


            /* ---------------- Redis 2.4 ---------------- */

            /* remote server control commands */
            'client'                    => 'Predis\Commands\ServerClient',
        );
    }
}

/* --------------------------------------------------------------------------- */

namespace Predis\Commands\Processors;

use Predis\Commands\ICommand;
use Predis\Commands\IPrefixable;

/**
 * Defines an object that can process commands using command processors.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IProcessingSupport
{
    /**
     * Associates a command processor.
     *
     * @param ICommandProcessor $processor The command processor.
     */
    public function setProcessor(ICommandProcessor $processor);

    /**
     * Returns the associated command processor.
     *
     * @return ICommandProcessor
     */
    public function getProcessor();
}

/**
 * A command processor processes commands before they are sent to Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ICommandProcessor
{
    /**
     * Processes a Redis command.
     *
     * @param ICommand $command Redis command.
     */
    public function process(ICommand $command);
}

/**
 * A command processor chain processes a command using multiple chained command
 * processor before it is sent to Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface ICommandProcessorChain extends ICommandProcessor, \IteratorAggregate, \Countable
{
    /**
     * Adds a command processor.
     *
     * @param ICommandProcessor $processor A command processor.
     */
    public function add(ICommandProcessor $processor);

    /**
     * Removes a command processor from the chain.
     *
     * @param ICommandProcessor $processor A command processor.
     */
    public function remove(ICommandProcessor $processor);

    /**
     * Returns an ordered list of the command processors in the chain.
     *
     * @return array
     */
    public function getProcessors();
}

/**
 * Default implementation of a command processors chain.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ProcessorChain implements ICommandProcessorChain, \ArrayAccess
{
    private $processors = array();

    /**
     * @param array $processors List of instances of ICommandProcessor.
     */
    public function __construct($processors = array())
    {
        foreach ($processors as $processor) {
            $this->add($processor);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function add(ICommandProcessor $processor)
    {
        $this->processors[] = $processor;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ICommandProcessor $processor)
    {
        $index = array_search($processor, $this->processors, true);
        if ($index !== false) {
            unset($this[$index]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function process(ICommand $command)
    {
        $count = count($this->processors);
        for ($i = 0; $i < $count; $i++) {
            $this->processors[$i]->process($command);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Returns an iterator over the list of command processor in the chain.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->processors);
    }

    /**
     * Returns the number of command processors in the chain.
     *
     * @return int
     */
    public function count()
    {
        return count($this->processors);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($index)
    {
        return isset($this->processors[$index]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($index)
    {
        return $this->processors[$index];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($index, $processor)
    {
        if (!$processor instanceof ICommandProcessor) {
            throw new \InvalidArgumentException(
                'A processor chain can hold only instances of classes implementing '.
                'the Predis\Commands\Preprocessors\ICommandProcessor interface'
            );
        }

        $this->processors[$index] = $processor;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($index)
    {
        unset($this->processors[$index]);
        $this->processors = array_values($this->processors);
    }
}

/**
 * Command processor that is used to prefix the keys contained in the arguments
 * of a Redis command.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class KeyPrefixProcessor implements ICommandProcessor
{
    private $prefix;

    /**
     * @param string $prefix Prefix for the keys.
     */
    public function __construct($prefix)
    {
        $this->setPrefix($prefix);
    }

    /**
     * Sets a prefix that is applied to all the keys.
     *
     * @param string $prefix Prefix for the keys.
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Gets the current prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ICommand $command)
    {
        if ($command instanceof IPrefixable) {
            $command->prefixKeys($this->prefix);
        }
    }
}

/* --------------------------------------------------------------------------- */

namespace Predis\Distribution;

/**
 * A generator of node keys implements the logic used to calculate the hash of
 * a key to distribute the respective operations among nodes.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface INodeKeyGenerator
{
    /**
     * Generates an hash that is used by the distributor algorithm
     *
     * @param string $value Value used to generate the hash.
     * @return int
     */
    public function generateKey($value);
}

/**
 * A distributor implements the logic to automatically distribute
 * keys among several nodes for client-side sharding.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IDistributionStrategy extends INodeKeyGenerator
{
    /**
     * Adds a node to the distributor with an optional weight.
     *
     * @param mixed $node Node object.
     * @param int $weight Weight for the node.
     */
    public function add($node, $weight = null);

    /**
     * Removes a node from the distributor.
     *
     * @param mixed $node Node object.
     */
    public function remove($node);

    /**
     * Gets a node from the distributor using the computed hash of a key.
     *
     * @return mixed
     */
    public function get($key);
}

/**
 * This class implements an hashring-based distributor that uses the same
 * algorithm of memcache to distribute keys in a cluster using client-side
 * sharding.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 * @author Lorenzo Castelli <lcastelli@gmail.com>
 */
class HashRing implements IDistributionStrategy
{
    const DEFAULT_REPLICAS = 128;
    const DEFAULT_WEIGHT   = 100;

    private $nodes;
    private $ring;
    private $ringKeys;
    private $ringKeysCount;
    private $replicas;

    /**
     * @param int $replicas Number of replicas in the ring.
     */
    public function __construct($replicas = self::DEFAULT_REPLICAS)
    {
        $this->replicas = $replicas;
        $this->nodes    = array();
    }

    /**
     * Adds a node to the ring with an optional weight.
     *
     * @param mixed $node Node object.
     * @param int $weight Weight for the node.
     */
    public function add($node, $weight = null)
    {
        // In case of collisions in the hashes of the nodes, the node added
        // last wins, thus the order in which nodes are added is significant.
        $this->nodes[] = array('object' => $node, 'weight' => (int) $weight ?: $this::DEFAULT_WEIGHT);
        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($node)
    {
        // A node is removed by resetting the ring so that it's recreated from
        // scratch, in order to reassign possible hashes with collisions to the
        // right node according to the order in which they were added in the
        // first place.
        for ($i = 0; $i < count($this->nodes); ++$i) {
            if ($this->nodes[$i]['object'] === $node) {
                array_splice($this->nodes, $i, 1);
                $this->reset();
                break;
            }
        }
    }

    /**
     * Resets the distributor.
     */
    private function reset()
    {
        unset(
            $this->ring,
            $this->ringKeys,
            $this->ringKeysCount
        );
    }

    /**
     * Returns the initialization status of the distributor.
     *
     * @return Boolean
     */
    private function isInitialized()
    {
        return isset($this->ringKeys);
    }

    /**
     * Calculates the total weight of all the nodes in the distributor.
     *
     * @return int
     */
    private function computeTotalWeight()
    {
        $totalWeight = 0;
        foreach ($this->nodes as $node) {
            $totalWeight += $node['weight'];
        }

        return $totalWeight;
    }

    /**
     * Initializes the distributor.
     */
    private function initialize()
    {
        if ($this->isInitialized()) {
            return;
        }

        if (count($this->nodes) === 0) {
            throw new EmptyRingException('Cannot initialize empty hashring');
        }

        $this->ring = array();
        $totalWeight = $this->computeTotalWeight();
        $nodesCount  = count($this->nodes);

        foreach ($this->nodes as $node) {
            $weightRatio = $node['weight'] / $totalWeight;
            $this->addNodeToRing($this->ring, $node, $nodesCount, $this->replicas, $weightRatio);
        }
        ksort($this->ring, SORT_NUMERIC);

        $this->ringKeys = array_keys($this->ring);
        $this->ringKeysCount = count($this->ringKeys);
    }

    /**
     * Implements the logic needed to add a node to the hashring.
     *
     * @param array $ring Source hashring.
     * @param mixed $node Node object to be added.
     * @param int $totalNodes Total number of nodes.
     * @param int $replicas Number of replicas in the ring.
     * @param float $weightRatio Weight ratio for the node.
     */
    protected function addNodeToRing(&$ring, $node, $totalNodes, $replicas, $weightRatio)
    {
        $nodeObject = $node['object'];
        $nodeHash = $this->getNodeHash($nodeObject);
        $replicas = (int) round($weightRatio * $totalNodes * $replicas);

        for ($i = 0; $i < $replicas; $i++) {
            $key = crc32("$nodeHash:$i");
            $ring[$key] = $nodeObject;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getNodeHash($nodeObject)
    {
        return (string) $nodeObject;
    }

    /**
     * Calculates the hash for the specified value.
     *
     * @param string $value Input value.
     * @return int
     */
    public function generateKey($value)
    {
        return crc32($value);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->ring[$this->getNodeKey($key)];
    }

    /**
     * Calculates the corrisponding key of a node distributed in the hashring.
     *
     * @param int $key Computed hash of a key.
     * @return int
     */
    private function getNodeKey($key)
    {
        $this->initialize();
        $ringKeys = $this->ringKeys;
        $upper = $this->ringKeysCount - 1;
        $lower = 0;

        while ($lower <= $upper) {
            $index = ($lower + $upper) >> 1;
            $item  = $ringKeys[$index];
            if ($item > $key) {
                $upper = $index - 1;
            }
            else if ($item < $key) {
                $lower = $index + 1;
            }
            else {
                return $item;
            }
        }

        return $ringKeys[$this->wrapAroundStrategy($upper, $lower, $this->ringKeysCount)];
    }

    /**
     * Implements a strategy to deal with wrap-around errors during binary searches.
     *
     * @param int $upper
     * @param int $lower
     * @param int $ringKeysCount
     * @return int
     */
    protected function wrapAroundStrategy($upper, $lower, $ringKeysCount)
    {
        // Binary search for the last item in ringkeys with a value less or
        // equal to the key. If no such item exists, return the last item.
        return $upper >= 0 ? $upper : $ringKeysCount - 1;
    }
}

/**
 * Exception class that identifies empty rings.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class EmptyRingException extends \Exception
{
}

/**
 * This class implements an hashring-based distributor that uses the same
 * algorithm of libketama to distribute keys in a cluster using client-side
 * sharding.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 * @author Lorenzo Castelli <lcastelli@gmail.com>
 */
class KetamaPureRing extends HashRing
{
    const DEFAULT_REPLICAS = 160;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct($this::DEFAULT_REPLICAS);
    }

    /**
     * {@inheritdoc}
     */
    protected function addNodeToRing(&$ring, $node, $totalNodes, $replicas, $weightRatio)
    {
        $nodeObject = $node['object'];
        $nodeHash = $this->getNodeHash($nodeObject);
        $replicas = (int) floor($weightRatio * $totalNodes * ($replicas / 4));

        for ($i = 0; $i < $replicas; $i++) {
            $unpackedDigest = unpack('V4', md5("$nodeHash-$i", true));
            foreach ($unpackedDigest as $key) {
                $ring[$key] = $nodeObject;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generateKey($value)
    {
        $hash = unpack('V', md5($value, true));
        return $hash[1];
    }

    /**
     * {@inheritdoc}
     */
    protected function wrapAroundStrategy($upper, $lower, $ringKeysCount)
    {
        // Binary search for the first item in _ringkeys with a value greater
        // or equal to the key. If no such item exists, return the first item.
        return $lower < $ringKeysCount ? $lower : 0;
    }
}

/* --------------------------------------------------------------------------- */

namespace Predis\Protocol\Text;

use Predis\Helpers;
use Predis\ResponseError;
use Predis\ResponseQueued;
use Predis\ServerException;
use Predis\Commands\ICommand;
use Predis\Protocol\IProtocolProcessor;
use Predis\Protocol\ProtocolException;
use Predis\Network\IConnectionComposable;
use Predis\Iterators\MultiBulkResponseSimple;
use Predis\Protocol\IResponseHandler;
use Predis\Protocol\IResponseReader;
use Predis\Protocol\ICommandSerializer;
use Predis\Protocol\IComposableProtocolProcessor;

/**
 * Implements a response handler for error replies using the standard wire
 * protocol defined by Redis.
 *
 * This handler returns a reply object to notify the user that an error has
 * occurred on the server.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseErrorSilentHandler implements IResponseHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(IConnectionComposable $connection, $errorMessage)
    {
        return new ResponseError($errorMessage);
    }
}

/**
 * Implements a response handler for status replies using the standard wire
 * protocol defined by Redis.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseStatusHandler implements IResponseHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(IConnectionComposable $connection, $status)
    {
        switch ($status) {
            case 'OK':
                return true;

            case 'QUEUED':
                return new ResponseQueued();

            default:
                return $status;
        }
    }
}

/**
 * Implements a response handler for integer replies using the standard wire
 * protocol defined by Redis.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseIntegerHandler implements IResponseHandler
{
    /**
     * Handles an integer reply returned by Redis.
     *
     * @param IConnectionComposable $connection Connection to Redis.
     * @param string $number String representation of an integer.
     * @return int
     */
    public function handle(IConnectionComposable $connection, $number)
    {
        if (is_numeric($number)) {
            return (int) $number;
        }

        if ($number !== 'nil') {
            Helpers::onCommunicationException(new ProtocolException(
                $connection, "Cannot parse '$number' as numeric response"
            ));
        }

        return null;
    }
}

/**
 * Implements a pluggable response reader using the standard wire protocol
 * defined by Redis.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class TextResponseReader implements IResponseReader
{
    private $handlers;

    /**
     *
     */
    public function __construct()
    {
        $this->handlers = $this->getDefaultHandlers();
    }

    /**
     * Returns the default set of response handlers for all the type of replies
     * that can be returned by Redis.
     */
    private function getDefaultHandlers()
    {
        return array(
            TextProtocol::PREFIX_STATUS     => new ResponseStatusHandler(),
            TextProtocol::PREFIX_ERROR      => new ResponseErrorHandler(),
            TextProtocol::PREFIX_INTEGER    => new ResponseIntegerHandler(),
            TextProtocol::PREFIX_BULK       => new ResponseBulkHandler(),
            TextProtocol::PREFIX_MULTI_BULK => new ResponseMultiBulkHandler(),
        );
    }

    /**
     * Sets a response handler for a certain prefix that identifies a type of
     * reply that can be returned by Redis.
     *
     * @param string $prefix Identifier for a type of reply.
     * @param IResponseHandler $handler Response handler for the reply.
     */
    public function setHandler($prefix, IResponseHandler $handler)
    {
        $this->handlers[$prefix] = $handler;
    }

    /**
     * Returns the response handler associated to a certain type of reply that
     * can be returned by Redis.
     *
     * @param string $prefix Identifier for a type of reply.
     * @return IResponseHandler
     */
    public function getHandler($prefix)
    {
        if (isset($this->handlers[$prefix])) {
            return $this->handlers[$prefix];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read(IConnectionComposable $connection)
    {
        $header = $connection->readLine();
        if ($header === '') {
            $this->protocolError($connection, 'Unexpected empty header');
        }

        $prefix = $header[0];
        if (!isset($this->handlers[$prefix])) {
            $this->protocolError($connection, "Unknown prefix '$prefix'");
        }

        $handler = $this->handlers[$prefix];

        return $handler->handle($connection, substr($header, 1));
    }

    /**
     * Helper method used to handle a protocol error generated while reading a
     * reply from a connection to Redis.
     *
     * @param IConnectionComposable $connection Connection to Redis that generated the error.
     * @param string $message Error message.
     */
    private function protocolError(IConnectionComposable $connection, $message)
    {
        Helpers::onCommunicationException(new ProtocolException($connection, $message));
    }
}

/**
 * Implements a pluggable command serializer using the standard  wire protocol
 * defined by Redis.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class TextCommandSerializer implements ICommandSerializer
{
    /**
     * {@inheritdoc}
     */
    public function serialize(ICommand $command)
    {
        $commandId = $command->getId();
        $arguments = $command->getArguments();

        $cmdlen = strlen($commandId);
        $reqlen = count($arguments) + 1;

        $buffer = "*{$reqlen}\r\n\${$cmdlen}\r\n{$commandId}\r\n";

        for ($i = 0; $i < $reqlen - 1; $i++) {
            $argument = $arguments[$i];
            $arglen = strlen($argument);
            $buffer .= "\${$arglen}\r\n{$argument}\r\n";
        }

        return $buffer;
    }
}

/**
 * Implements a customizable protocol processor that uses the standard Redis
 * wire protocol to serialize Redis commands and parse replies returned by
 * the server using a pluggable set of classes.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ComposableTextProtocol implements IComposableProtocolProcessor
{
    private $serializer;
    private $reader;

    /**
     * @param array $options Set of options used to initialize the protocol processor.
     */
    public function __construct(Array $options = array())
    {
        $this->setSerializer(new TextCommandSerializer());
        $this->setReader(new TextResponseReader());

        if (count($options) > 0) {
            $this->initializeOptions($options);
        }
    }

    /**
     * Initializes the protocol processor using a set of options.
     *
     * @param array $options Set of options.
     */
    private function initializeOptions(Array $options)
    {
        foreach ($options as $k => $v) {
            $this->setOption($k, $v);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($option, $value)
    {
        switch ($option) {
            case 'iterable_multibulk':
                $handler = $value ? new ResponseMultiBulkStreamHandler() : new ResponseMultiBulkHandler();
                $this->reader->setHandler(TextProtocol::PREFIX_MULTI_BULK, $handler);
                break;

            case 'throw_errors':
                $handler = $value ? new ResponseErrorHandler() : new ResponseErrorSilentHandler();
                $this->reader->setHandler(TextProtocol::PREFIX_ERROR, $handler);
                break;

            default:
                throw new \InvalidArgumentException("The option $option is not supported by the current protocol");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(ICommand $command)
    {
        return $this->serializer->serialize($command);
    }

    /**
     * {@inheritdoc}
     */
    public function write(IConnectionComposable $connection, ICommand $command)
    {
        $connection->writeBytes($this->serializer->serialize($command));
    }

    /**
     * {@inheritdoc}
     */
    public function read(IConnectionComposable $connection)
    {
        return $this->reader->read($connection);
    }

    /**
     * {@inheritdoc}
     */
    public function setSerializer(ICommandSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function setReader(IResponseReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function getReader()
    {
        return $this->reader;
    }
}

/**
 * Implements a response handler for bulk replies using the standard wire
 * protocol defined by Redis.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseBulkHandler implements IResponseHandler
{
    /**
     * Handles a bulk reply returned by Redis.
     *
     * @param IConnectionComposable $connection Connection to Redis.
     * @param string $lengthString Bytes size of the bulk reply.
     * @return string
     */
    public function handle(IConnectionComposable $connection, $lengthString)
    {
        $length = (int) $lengthString;

        if ($length != $lengthString) {
            Helpers::onCommunicationException(new ProtocolException(
                $connection, "Cannot parse '$length' as data length"
            ));
        }

        if ($length >= 0) {
            return substr($connection->readBytes($length + 2), 0, -2);
        }

        if ($length == -1) {
            return null;
        }
    }
}

/**
 * Implements a response handler for error replies using the standard wire
 * protocol defined by Redis.
 *
 * This handler throws an exception to notify the user that an error has
 * occurred on the server.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseErrorHandler implements IResponseHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(IConnectionComposable $connection, $errorMessage)
    {
        throw new ServerException($errorMessage);
    }
}

/**
 * Implements a response handler for iterable multi-bulk replies using the
 * standard wire protocol defined by Redis.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseMultiBulkStreamHandler implements IResponseHandler
{
    /**
     * Handles a multi-bulk reply returned by Redis in a streamable fashion.
     *
     * @param IConnectionComposable $connection Connection to Redis.
     * @param string $lengthString Number of items in the multi-bulk reply.
     * @return MultiBulkResponseSimple
     */
    public function handle(IConnectionComposable $connection, $lengthString)
    {
        $length = (int) $lengthString;

        if ($length != $lengthString) {
            Helpers::onCommunicationException(new ProtocolException(
                $connection, "Cannot parse '$length' as data length"
            ));
        }

        return new MultiBulkResponseSimple($connection, $length);
    }
}

/**
 * Implements a response handler for multi-bulk replies using the standard
 * wire protocol defined by Redis.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ResponseMultiBulkHandler implements IResponseHandler
{
    /**
     * Handles a multi-bulk reply returned by Redis.
     *
     * @param IConnectionComposable $connection Connection to Redis.
     * @param string $lengthString Number of items in the multi-bulk reply.
     * @return array
     */
    public function handle(IConnectionComposable $connection, $lengthString)
    {
        $length = (int) $lengthString;

        if ($length != $lengthString) {
            Helpers::onCommunicationException(new ProtocolException(
                $connection, "Cannot parse '$length' as data length"
            ));
        }

        if ($length === -1) {
            return null;
        }

        $list = array();

        if ($length > 0) {
            $handlersCache = array();
            $reader = $connection->getProtocol()->getReader();

            for ($i = 0; $i < $length; $i++) {
                $header = $connection->readLine();
                $prefix = $header[0];

                if (isset($handlersCache[$prefix])) {
                    $handler = $handlersCache[$prefix];
                }
                else {
                    $handler = $reader->getHandler($prefix);
                    $handlersCache[$prefix] = $handler;
                }

                $list[$i] = $handler->handle($connection, substr($header, 1));
            }
        }

        return $list;
    }
}

/**
 * Implements a protocol processor for the standard wire protocol defined by Redis.
 *
 * @link http://redis.io/topics/protocol
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class TextProtocol implements IProtocolProcessor
{
    const NEWLINE = "\r\n";
    const OK      = 'OK';
    const ERROR   = 'ERR';
    const QUEUED  = 'QUEUED';
    const NULL    = 'nil';

    const PREFIX_STATUS     = '+';
    const PREFIX_ERROR      = '-';
    const PREFIX_INTEGER    = ':';
    const PREFIX_BULK       = '$';
    const PREFIX_MULTI_BULK = '*';

    const BUFFER_SIZE = 4096;

    private $mbiterable;
    private $throwErrors;
    private $serializer;

    /**
     *
     */
    public function __construct()
    {
        $this->mbiterable  = false;
        $this->throwErrors = true;
        $this->serializer  = new TextCommandSerializer();
    }

    /**
     * {@inheritdoc}
     */
    public function write(IConnectionComposable $connection, ICommand $command)
    {
        $connection->writeBytes($this->serializer->serialize($command));
    }

    /**
     * {@inheritdoc}
     */
    public function read(IConnectionComposable $connection)
    {
        $chunk = $connection->readLine();
        $prefix = $chunk[0];
        $payload = substr($chunk, 1);

        switch ($prefix) {
            case '+':    // inline
                switch ($payload) {
                    case 'OK':
                        return true;

                    case 'QUEUED':
                        return new ResponseQueued();

                    default:
                        return $payload;
                }

            case '$':    // bulk
                $size = (int) $payload;
                if ($size === -1) {
                    return null;
                }
                return substr($connection->readBytes($size + 2), 0, -2);

            case '*':    // multi bulk
                $count = (int) $payload;

                if ($count === -1) {
                    return null;
                }
                if ($this->mbiterable == true) {
                    return new MultiBulkResponseSimple($connection, $count);
                }

                $multibulk = array();
                for ($i = 0; $i < $count; $i++) {
                    $multibulk[$i] = $this->read($connection);
                }

                return $multibulk;

            case ':':    // integer
                return (int) $payload;

            case '-':    // error
                if ($this->throwErrors) {
                    throw new ServerException($payload);
                }
                return new ResponseError($payload);

            default:
                Helpers::onCommunicationException(new ProtocolException(
                    $connection, "Unknown prefix: '$prefix'"
                ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setOption($option, $value)
    {
        switch ($option) {
            case 'iterable_multibulk':
                $this->mbiterable = (bool) $value;
                break;

            case 'throw_errors':
                $this->throwErrors = (bool) $value;
                break;
        }
    }
}

/* --------------------------------------------------------------------------- */

namespace Predis\Pipeline;

use Predis\ServerException;
use Predis\CommunicationException;
use Predis\Network\IConnection;
use Predis\Client;
use Predis\Helpers;
use Predis\ClientException;
use Predis\Commands\ICommand;
use Predis\Network\IConnectionReplication;

/**
 * Defines a strategy to write a list of commands to the network
 * and read back their replies.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
interface IPipelineExecutor
{
    /**
     * Writes a list of commands to the network and reads back their replies.
     *
     * @param IConnection $connection Connection to Redis.
     * @param array $commands List of commands.
     * @return array
     */
    public function execute(IConnection $connection, &$commands);
}

/**
 * Abstraction of a pipeline context where write and read operations
 * of commands and their replies over the network are pipelined.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PipelineContext
{
    private $client;
    private $executor;

    private $pipeline = array();
    private $replies  = array();
    private $running  = false;

    /**
     * @param Client Client instance used by the context.
     * @param array Options for the context initialization.
     */
    public function __construct(Client $client, Array $options = null)
    {
        $this->client = $client;
        $this->executor = $this->createExecutor($client, $options ?: array());
    }

    /**
     * Returns a pipeline executor depending on the kind of the underlying
     * connection and the passed options.
     *
     * @param Client Client instance used by the context.
     * @param array Options for the context initialization.
     * @return IPipelineExecutor
     */
    protected function createExecutor(Client $client, Array $options)
    {
        if (!$options) {
            return new StandardExecutor();
        }

        if (isset($options['executor'])) {
            $executor = $options['executor'];
            if (!$executor instanceof IPipelineExecutor) {
                throw new \InvalidArgumentException(
                    'The executor option accepts only instances ' .
                    'of Predis\Pipeline\IPipelineExecutor'
                );
            }
            return $executor;
        }

        if (isset($options['safe']) && $options['safe'] == true) {
            $isCluster = Helpers::isCluster($client->getConnection());
            return $isCluster ? new SafeClusterExecutor() : new SafeExecutor();
        }

        return new StandardExecutor();
    }

    /**
     * Queues a command into the pipeline buffer.
     *
     * @param string $method Command ID.
     * @param array $arguments Arguments for the command.
     * @return PipelineContext
     */
    public function __call($method, $arguments)
    {
        $command = $this->client->createCommand($method, $arguments);
        $this->recordCommand($command);

        return $this;
    }

    /**
     * Queues a command instance into the pipeline buffer.
     *
     * @param ICommand $command Command to queue in the buffer.
     */
    protected function recordCommand(ICommand $command)
    {
        $this->pipeline[] = $command;
    }

    /**
     * Queues a command instance into the pipeline buffer.
     *
     * @param ICommand $command Command to queue in the buffer.
     */
    public function executeCommand(ICommand $command)
    {
        $this->recordCommand($command);
    }

    /**
     * Flushes the buffer that holds the queued commands.
     *
     * @param Boolean $send Specifies if the commands in the buffer should be sent to Redis.
     * @return PipelineContext
     */
    public function flushPipeline($send = true)
    {
        if (count($this->pipeline) > 0) {
            if ($send) {
                $connection = $this->client->getConnection();

                // TODO: it would be better to use a dedicated pipeline executor
                //       for classes implementing master/slave replication.
                if ($connection instanceof IConnectionReplication) {
                    $connection->switchTo('master');
                }

                $replies = $this->executor->execute($connection, $this->pipeline);
                $this->replies = array_merge($this->replies, $replies);
            }
            $this->pipeline = array();
        }

        return $this;
    }

    /**
     * Marks the running status of the pipeline.
     *
     * @param Boolean $bool True if the pipeline is running.
     *                      False if the pipeline is not running.
     */
    private function setRunning($bool)
    {
        if ($bool === true && $this->running === true) {
            throw new ClientException("This pipeline is already opened");
        }
        $this->running = $bool;
    }

    /**
     * Handles the actual execution of the whole pipeline.
     *
     * @param mixed $callable Callback for execution.
     * @return array
     */
    public function execute($callable = null)
    {
        if ($callable && !is_callable($callable)) {
            throw new \InvalidArgumentException('Argument passed must be a callable object');
        }

        $this->setRunning(true);
        $pipelineBlockException = null;

        try {
            if ($callable !== null) {
                call_user_func($callable, $this);
            }
            $this->flushPipeline();
        }
        catch (\Exception $exception) {
            $pipelineBlockException = $exception;
        }

        $this->setRunning(false);

        if ($pipelineBlockException !== null) {
            throw $pipelineBlockException;
        }

        return $this->replies;
    }

    /**
     * Returns the underlying client instance used by the pipeline object.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns the underlying pipeline executor used by the pipeline object.
     *
     * @return IPipelineExecutor
     */
    public function getExecutor()
    {
        return $this->executor;
    }
}

/**
 * Implements the standard pipeline executor strategy used
 * to write a list of commands and read their replies over
 * a connection to Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StandardExecutor implements IPipelineExecutor
{
    /**
     * {@inheritdoc}
     */
    public function execute(IConnection $connection, &$commands)
    {
        $sizeofPipe = count($commands);
        $values = array();

        foreach ($commands as $command) {
            $connection->writeCommand($command);
        }

        try {
            for ($i = 0; $i < $sizeofPipe; $i++) {
                $response = $connection->readResponse($commands[$i]);
                $values[] = $response instanceof \Iterator
                    ? iterator_to_array($response)
                    : $response;
                unset($commands[$i]);
            }
        }
        catch (ServerException $exception) {
            // Force disconnection to prevent protocol desynchronization.
            $connection->disconnect();
            throw $exception;
        }

        return $values;
    }
}

/**
 * Implements a pipeline executor strategy that does not fail when an error is
 * encountered, but adds the returned error in the replies array.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SafeExecutor implements IPipelineExecutor
{
    /**
     * {@inheritdoc}
     */
    public function execute(IConnection $connection, &$commands)
    {
        $sizeofPipe = count($commands);
        $values = array();

        foreach ($commands as $command) {
            try {
                $connection->writeCommand($command);
            }
            catch (CommunicationException $exception) {
                return array_fill(0, $sizeofPipe, $exception);
            }
        }

        for ($i = 0; $i < $sizeofPipe; $i++) {
            $command = $commands[$i];
            unset($commands[$i]);

            try {
                $response = $connection->readResponse($command);
                $values[] = $response instanceof \Iterator ? iterator_to_array($response) : $response;
            }
            catch (ServerException $exception) {
                $values[] = $exception->toResponseError();
            }
            catch (CommunicationException $exception) {
                $toAdd = count($commands) - count($values);
                $values = array_merge($values, array_fill(0, $toAdd, $exception));
                break;
            }
        }

        return $values;
    }
}

/**
 * Implements a pipeline executor strategy for connection clusters that does
 * not fail when an error is encountered, but adds the returned error in the
 * replies array.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SafeClusterExecutor implements IPipelineExecutor
{
    /**
     * {@inheritdoc}
     */
    public function execute(IConnection $connection, &$commands)
    {
        $connectionExceptions = array();
        $sizeofPipe = count($commands);
        $values = array();

        foreach ($commands as $command) {
            $cmdConnection = $connection->getConnection($command);

            if (isset($connectionExceptions[spl_object_hash($cmdConnection)])) {
                continue;
            }

            try {
                $cmdConnection->writeCommand($command);
            }
            catch (CommunicationException $exception) {
                $connectionExceptions[spl_object_hash($cmdConnection)] = $exception;
            }
        }

        for ($i = 0; $i < $sizeofPipe; $i++) {
            $command = $commands[$i];
            unset($commands[$i]);

            $cmdConnection = $connection->getConnection($command);
            $connectionObjectHash = spl_object_hash($cmdConnection);

            if (isset($connectionExceptions[$connectionObjectHash])) {
                $values[] = $connectionExceptions[$connectionObjectHash];
                continue;
            }

            try {
                $response = $cmdConnection->readResponse($command);
                $values[] = $response instanceof \Iterator ? iterator_to_array($response) : $response;
            }
            catch (ServerException $exception) {
                $values[] = $exception->toResponseError();
            }
            catch (CommunicationException $exception) {
                $values[] = $exception;
                $connectionExceptions[$connectionObjectHash] = $exception;
            }
        }

        return $values;
    }
}

/**
 * Implements a pipeline executor strategy that writes a list of commands to
 * the connection object but does not read back their replies.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class FireAndForgetExecutor implements IPipelineExecutor
{
    /**
     * {@inheritdoc}
     */
    public function execute(IConnection $connection, &$commands)
    {
        foreach ($commands as $command) {
            $connection->writeCommand($command);
        }

        $connection->disconnect();

        return array();
    }
}

/* --------------------------------------------------------------------------- */

namespace Predis\Iterators;

use Predis\Network\IConnection;
use Predis\Network\IConnectionSingle;

/**
 * Iterator that abstracts the access to multibulk replies and allows
 * them to be consumed by user's code in a streaming fashion.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
abstract class MultiBulkResponse implements \Iterator, \Countable
{
    protected $position;
    protected $current;
    protected $replySize;

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        // NOOP
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        if (++$this->position < $this->replySize) {
            $this->current = $this->getValue();
        }

        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->position < $this->replySize;
    }

    /**
     * Returns the number of items of the whole multibulk reply.
     *
     * This method should be used to get the size of the current multibulk
     * reply without using iterator_count, which actually consumes the
     * iterator to calculate the size (rewinding is not supported).
     *
     * @return int
     */
    public function count()
    {
        return $this->replySize;
    }

    /**
     * {@inheritdoc}
     */
    protected abstract function getValue();
}

/**
 * Abstracts the access to a streamable list of tuples represented
 * as a multibulk reply that alternates keys and values.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class MultiBulkResponseTuple extends MultiBulkResponse implements \OuterIterator
{
    private $iterator;

    /**
     * @param MultiBulkResponseSimple $iterator Multibulk reply iterator.
     */
    public function __construct(MultiBulkResponseSimple $iterator)
    {
        $virtualSize = count($iterator) / 2;
        $this->iterator = $iterator;
        $this->position = 0;
        $this->current = $virtualSize > 0 ? $this->getValue() : null;
        $this->replySize = $virtualSize;
    }

    /**
     * {@inheritdoc}
     */
    public function getInnerIterator()
    {
        return $this->iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function __destruct()
    {
        $this->iterator->sync(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function getValue()
    {
        $k = $this->iterator->current();
        $this->iterator->next();

        $v = $this->iterator->current();
        $this->iterator->next();

        return array($k, $v);
    }
}

/**
 * Streams a multibulk reply.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class MultiBulkResponseSimple extends MultiBulkResponse
{
    private $connection;

    /**
     * @param IConnectionSingle $connection Connection to Redis.
     * @param int $size Number of elements of the multibulk reply.
     */
    public function __construct(IConnectionSingle $connection, $size)
    {
        $this->connection = $connection;
        $this->position   = 0;
        $this->current    = $size > 0 ? $this->getValue() : null;
        $this->replySize  = $size;
    }

    /**
     * Handles the synchronization of the client with the Redis protocol
     * then PHP's garbage collector kicks in (e.g. then the iterator goes
     * out of the scope of a foreach).
     */
    public function __destruct()
    {
        $this->sync(true);
    }

    /**
     * Synchronizes the client with the queued elements that have not been
     * read from the connection by consuming the rest of the multibulk reply,
     * or simply by dropping the connection.
     *
     * @param Boolean $drop True to synchronize the client by dropping the connection.
     *                      False to synchronize the client by consuming the multibulk reply.
     */
    public function sync($drop = false)
    {
        if ($drop == true) {
            if ($this->valid()) {
                $this->position = $this->replySize;
                $this->connection->disconnect();
            }
        }
        else {
            while ($this->valid()) {
                $this->next();
            }
        }
    }

    /**
     * Reads the next item of the multibulk reply from the server.
     *
     * @return mixed
     */
    protected function getValue()
    {
        return $this->connection->read();
    }
}

/* --------------------------------------------------------------------------- */

namespace Predis\Transaction;

use Predis\Client;
use Predis\Helpers;
use Predis\ResponseQueued;
use Predis\ClientException;
use Predis\ServerException;
use Predis\Commands\ICommand;
use Predis\NotSupportedException;
use Predis\CommunicationException;
use Predis\Protocol\ProtocolException;
use Predis\PredisException;

/**
 * Exception class that identifies MULTI / EXEC transactions aborted by Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class AbortedMultiExecException extends PredisException
{
    private $transaction;

    /**
     * @param MultiExecContext $transaction Transaction that generated the exception.
     * @param string $message Error message.
     * @param int $code Error code.
     */
    public function __construct(MultiExecContext $transaction, $message, $code = null)
    {
        parent::__construct($message, $code);

        $this->transaction = $transaction;
    }

    /**
     * Returns the transaction that generated the exception.
     *
     * @return MultiExecContext
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}

/**
 * Client-side abstraction of a Redis transaction based on MULTI / EXEC.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class MultiExecContext
{
    const STATE_RESET       = 0x00000;
    const STATE_INITIALIZED = 0x00001;
    const STATE_INSIDEBLOCK = 0x00010;
    const STATE_DISCARDED   = 0x00100;
    const STATE_CAS         = 0x01000;
    const STATE_WATCH       = 0x10000;

    private $state;
    private $canWatch;

    protected $client;
    protected $options;
    protected $commands;

    /**
     * @param Client Client instance used by the context.
     * @param array Options for the context initialization.
     */
    public function __construct(Client $client, Array $options = null)
    {
        $this->checkCapabilities($client);
        $this->options = $options ?: array();
        $this->client = $client;
        $this->reset();
    }

    /**
     * Sets the internal state flags.
     *
     * @param int $flags Set of flags
     */
    protected function setState($flags)
    {
        $this->state = $flags;
    }

    /**
     * Gets the internal state flags.
     *
     * @return int
     */
    protected function getState()
    {
        return $this->state;
    }

    /**
     * Sets one or more flags.
     *
     * @param int $flags Set of flags
     */
    protected function flagState($flags)
    {
        $this->state |= $flags;
    }

    /**
     * Resets one or more flags.
     *
     * @param int $flags Set of flags
     */
    protected function unflagState($flags)
    {
        $this->state &= ~$flags;
    }

    /**
     * Checks is a flag is set.
     *
     * @param int $flags Flag
     * @return Boolean
     */
    protected function checkState($flags)
    {
        return ($this->state & $flags) === $flags;
    }

    /**
     * Checks if the passed client instance satisfies the required conditions
     * needed to initialize a transaction context.
     *
     * @param Client Client instance used by the context.
     */
    private function checkCapabilities(Client $client)
    {
        if (Helpers::isCluster($client->getConnection())) {
            throw new NotSupportedException('Cannot initialize a MULTI/EXEC context over a cluster of connections');
        }

        $profile = $client->getProfile();
        if ($profile->supportsCommands(array('multi', 'exec', 'discard')) === false) {
            throw new NotSupportedException('The current profile does not support MULTI, EXEC and DISCARD');
        }

        $this->canWatch = $profile->supportsCommands(array('watch', 'unwatch'));
    }

    /**
     * Checks if WATCH and UNWATCH are supported by the server profile.
     */
    private function isWatchSupported()
    {
        if ($this->canWatch === false) {
            throw new NotSupportedException('The current profile does not support WATCH and UNWATCH');
        }
    }

    /**
     * Resets the state of a transaction.
     */
    protected function reset()
    {
        $this->setState(self::STATE_RESET);
        $this->commands = array();
    }

    /**
     * Initializes a new transaction.
     */
    protected function initialize()
    {
        if ($this->checkState(self::STATE_INITIALIZED)) {
            return;
        }

        $options = $this->options;

        if (isset($options['cas']) && $options['cas']) {
            $this->flagState(self::STATE_CAS);
        }
        if (isset($options['watch'])) {
            $this->watch($options['watch']);
        }

        $cas = $this->checkState(self::STATE_CAS);
        $discarded = $this->checkState(self::STATE_DISCARDED);

        if (!$cas || ($cas && $discarded)) {
            $this->client->multi();
            if ($discarded) {
                $this->unflagState(self::STATE_CAS);
            }
        }

        $this->unflagState(self::STATE_DISCARDED);
        $this->flagState(self::STATE_INITIALIZED);
    }

    /**
     * Dinamically invokes a Redis command with the specified arguments.
     *
     * @param string $method Command ID.
     * @param array $arguments Arguments for the command.
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $command = $this->client->createCommand($method, $arguments);
        $response = $this->executeCommand($command);

        return $response;
    }

    /**
     * Executes the specified Redis command.
     *
     * @param ICommand $command A Redis command.
     * @return mixed
     */
    public function executeCommand(ICommand $command)
    {
        $this->initialize();

        $response = $this->client->executeCommand($command);

        if ($this->checkState(self::STATE_CAS)) {
            return $response;
        }
        if (!$response instanceof ResponseQueued) {
            $this->onProtocolError('The server did not respond with a QUEUED status reply');
        }

        $this->commands[] = $command;

        return $this;
    }

    /**
     * Executes WATCH on one or more keys.
     *
     * @param string|array $keys One or more keys.
     * @return mixed
     */
    public function watch($keys)
    {
        $this->isWatchSupported();

        if ($this->checkState(self::STATE_INITIALIZED) && !$this->checkState(self::STATE_CAS)) {
            throw new ClientException('WATCH after MULTI is not allowed');
        }

        $watchReply = $this->client->watch($keys);
        $this->flagState(self::STATE_WATCH);

        return $watchReply;
    }

    /**
     * Finalizes the transaction on the server by executing MULTI on the server.
     *
     * @return MultiExecContext
     */
    public function multi()
    {
        if ($this->checkState(self::STATE_INITIALIZED | self::STATE_CAS)) {
            $this->unflagState(self::STATE_CAS);
            $this->client->multi();
        }
        else {
            $this->initialize();
        }

        return $this;
    }

    /**
     * Executes UNWATCH.
     *
     * @return MultiExecContext
     */
    public function unwatch()
    {
        $this->isWatchSupported();
        $this->unflagState(self::STATE_WATCH);
        $this->__call('unwatch', array());

        return $this;
    }

    /**
     * Resets a transaction by UNWATCHing the keys that are being WATCHed and
     * DISCARDing the pending commands that have been already sent to the server.
     *
     * @return MultiExecContext
     */
    public function discard()
    {
        if ($this->checkState(self::STATE_INITIALIZED)) {
            $command = $this->checkState(self::STATE_CAS) ? 'unwatch' : 'discard';
            $this->client->$command();
            $this->reset();
            $this->flagState(self::STATE_DISCARDED);
        }

        return $this;
    }

    /**
     * Executes the whole transaction.
     *
     * @return mixed
     */
    public function exec()
    {
        return $this->execute();
    }

    /**
     * Checks the state of the transaction before execution.
     *
     * @param mixed $callable Callback for execution.
     */
    private function checkBeforeExecution($callable)
    {
        if ($this->checkState(self::STATE_INSIDEBLOCK)) {
            throw new ClientException("Cannot invoke 'execute' or 'exec' inside an active client transaction block");
        }

        if ($callable) {
            if (!is_callable($callable)) {
                throw new \InvalidArgumentException('Argument passed must be a callable object');
            }

            if (count($this->commands) > 0) {
                $this->discard();
                throw new ClientException('Cannot execute a transaction block after using fluent interface');
            }
        }

        if (isset($this->options['retry']) && !isset($callable)) {
            $this->discard();
            throw new \InvalidArgumentException('Automatic retries can be used only when a transaction block is provided');
        }
    }

    /**
     * Handles the actual execution of the whole transaction.
     *
     * @param mixed $callable Callback for execution.
     * @return array
     */
    public function execute($callable = null)
    {
        $this->checkBeforeExecution($callable);

        $reply = null;
        $returnValues = array();
        $attemptsLeft = isset($this->options['retry']) ? (int)$this->options['retry'] : 0;

        do {
            if ($callable !== null) {
                $this->executeTransactionBlock($callable);
            }

            if (count($this->commands) === 0) {
                if ($this->checkState(self::STATE_WATCH)) {
                    $this->discard();
                }
                return;
            }

            $reply = $this->client->exec();

            if ($reply === null) {
                if ($attemptsLeft === 0) {
                    $message = 'The current transaction has been aborted by the server';
                    throw new AbortedMultiExecException($this, $message);
                }

                $this->reset();

                if (isset($this->options['on_retry']) && is_callable($this->options['on_retry'])) {
                    call_user_func($this->options['on_retry'], $this, $attemptsLeft);
                }

                continue;
            }

            break;
        } while ($attemptsLeft-- > 0);

        $execReply = $reply instanceof \Iterator ? iterator_to_array($reply) : $reply;
        $sizeofReplies = count($execReply);
        $commands = $this->commands;

        if ($sizeofReplies !== count($commands)) {
            $this->onProtocolError("EXEC returned an unexpected number of replies");
        }

        for ($i = 0; $i < $sizeofReplies; $i++) {
            $commandReply = $execReply[$i];

            if ($commandReply instanceof \Iterator) {
                $commandReply = iterator_to_array($commandReply);
            }

            $returnValues[$i] = $commands[$i]->parseResponse($commandReply);
            unset($commands[$i]);
        }

        return $returnValues;
    }

    /**
     * Passes the current transaction context to a callable block for execution.
     *
     * @param mixed $callable Callback.
     */
    protected function executeTransactionBlock($callable)
    {
        $blockException = null;
        $this->flagState(self::STATE_INSIDEBLOCK);

        try {
            call_user_func($callable, $this);
        }
        catch (CommunicationException $exception) {
            $blockException = $exception;
        }
        catch (ServerException $exception) {
            $blockException = $exception;
        }
        catch (\Exception $exception) {
            $blockException = $exception;
            $this->discard();
        }

        $this->unflagState(self::STATE_INSIDEBLOCK);

        if ($blockException !== null) {
            throw $blockException;
        }
    }

    /**
     * Helper method that handles protocol errors encountered inside a transaction.
     *
     * @param string $message Error message.
     */
    private function onProtocolError($message)
    {
        // Since a MULTI/EXEC block cannot be initialized over a clustered
        // connection, we can safely assume that Predis\Client::getConnection()
        // will always return an instance of Predis\Network\IConnectionSingle.
        Helpers::onCommunicationException(new ProtocolException(
            $this->client->getConnection(), $message
        ));
    }
}

/* --------------------------------------------------------------------------- */

namespace Predis\PubSub;

use Predis\Client;
use Predis\Helpers;
use Predis\ClientException;
use Predis\NotSupportedException;

/**
 * Client-side abstraction of a Publish / Subscribe context.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class PubSubContext implements \Iterator
{
    const SUBSCRIBE    = 'subscribe';
    const UNSUBSCRIBE  = 'unsubscribe';
    const PSUBSCRIBE   = 'psubscribe';
    const PUNSUBSCRIBE = 'punsubscribe';
    const MESSAGE      = 'message';
    const PMESSAGE     = 'pmessage';

    const STATUS_VALID       = 0x0001;
    const STATUS_SUBSCRIBED  = 0x0010;
    const STATUS_PSUBSCRIBED = 0x0100;

    private $client;
    private $position;
    private $options;

    /**
     * @param Client Client instance used by the context.
     * @param array Options for the context initialization.
     */
    public function __construct(Client $client, Array $options = null)
    {
        $this->checkCapabilities($client);
        $this->options = $options ?: array();
        $this->client = $client;
        $this->statusFlags = self::STATUS_VALID;

        $this->genericSubscribeInit('subscribe');
        $this->genericSubscribeInit('psubscribe');
    }

    /**
     * Automatically closes the context when PHP's garbage collector kicks in.
     */
    public function __destruct()
    {
        $this->closeContext(true);
    }

    /**
     * Checks if the passed client instance satisfies the required conditions
     * needed to initialize a Publish / Subscribe context.
     *
     * @param Client Client instance used by the context.
     */
    private function checkCapabilities(Client $client)
    {
        if (Helpers::isCluster($client->getConnection())) {
            throw new NotSupportedException('Cannot initialize a PUB/SUB context over a cluster of connections');
        }

        $commands = array('publish', 'subscribe', 'unsubscribe', 'psubscribe', 'punsubscribe');
        if ($client->getProfile()->supportsCommands($commands) === false) {
            throw new NotSupportedException('The current profile does not support PUB/SUB related commands');
        }
    }

    /**
     * This method shares the logic to handle both SUBSCRIBE and PSUBSCRIBE.
     *
     * @param string $subscribeAction Type of subscription.
     */
    private function genericSubscribeInit($subscribeAction)
    {
        if (isset($this->options[$subscribeAction])) {
            $this->$subscribeAction($this->options[$subscribeAction]);
        }
    }

    /**
     * Checks if the specified flag is valid in the state of the context.
     *
     * @param int $value Flag.
     * @return Boolean
     */
    private function isFlagSet($value)
    {
        return ($this->statusFlags & $value) === $value;
    }

    /**
     * Subscribes to the specified channels.
     *
     * @param mixed $arg,... One or more channel names.
     */
    public function subscribe(/* arguments */)
    {
        $this->writeCommand(self::SUBSCRIBE, func_get_args());
        $this->statusFlags |= self::STATUS_SUBSCRIBED;
    }

    /**
     * Unsubscribes from the specified channels.
     *
     * @param mixed $arg,... One or more channel names.
     */
    public function unsubscribe(/* arguments */)
    {
        $this->writeCommand(self::UNSUBSCRIBE, func_get_args());
    }

    /**
     * Subscribes to the specified channels using a pattern.
     *
     * @param mixed $arg,... One or more channel name patterns.
     */
    public function psubscribe(/* arguments */)
    {
        $this->writeCommand(self::PSUBSCRIBE, func_get_args());
        $this->statusFlags |= self::STATUS_PSUBSCRIBED;
    }

    /**
     * Unsubscribes from the specified channels using a pattern.
     *
     * @param mixed $arg,... One or more channel name patterns.
     */
    public function punsubscribe(/* arguments */)
    {
        $this->writeCommand(self::PUNSUBSCRIBE, func_get_args());
    }

    /**
     * Closes the context by unsubscribing from all the subscribed channels.
     * Optionally, the context can be forcefully closed by dropping the
     * underlying connection.
     *
     * @param Boolean $force Forcefully close the context by closing the connection.
     * @return Boolean Returns false if there are no pending messages.
     */
    public function closeContext($force = false)
    {
        if (!$this->valid()) {
            return false;
        }

        if ($force) {
            $this->invalidate();
            $this->client->disconnect();
        }
        else {
            if ($this->isFlagSet(self::STATUS_SUBSCRIBED)) {
                $this->unsubscribe();
            }
            if ($this->isFlagSet(self::STATUS_PSUBSCRIBED)) {
                $this->punsubscribe();
            }
        }

        return !$force;
    }

    /**
     * Writes a Redis command on the underlying connection.
     *
     * @param string $method ID of the command.
     * @param array $arguments List of arguments.
     */
    private function writeCommand($method, $arguments)
    {
        $arguments = Helpers::filterArrayArguments($arguments);
        $command = $this->client->createCommand($method, $arguments);
        $this->client->getConnection()->writeCommand($command);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        // NOOP
    }

    /**
     * Returns the last message payload retrieved from the server and generated
     * by one of the active subscriptions.
     *
     * @return array
     */
    public function current()
    {
        return $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        if ($this->valid()) {
            $this->position++;
        }

        return $this->position;
    }

    /**
     * Checks if the the context is still in a valid state to continue.
     *
     * @return Boolean
     */
    public function valid()
    {
        $isValid = $this->isFlagSet(self::STATUS_VALID);
        $subscriptionFlags = self::STATUS_SUBSCRIBED | self::STATUS_PSUBSCRIBED;
        $hasSubscriptions = ($this->statusFlags & $subscriptionFlags) > 0;

        return $isValid && $hasSubscriptions;
    }

    /**
     * Resets the state of the context.
     */
    private function invalidate()
    {
        $this->statusFlags = 0x0000;
    }

    /**
     * Waits for a new message from the server generated by one of the active
     * subscriptions and returns it when available.
     *
     * @return array
     */
    private function getValue()
    {
        $response = $this->client->getConnection()->read();

        switch ($response[0]) {
            case self::SUBSCRIBE:
            case self::UNSUBSCRIBE:
            case self::PSUBSCRIBE:
            case self::PUNSUBSCRIBE:
                if ($response[2] === 0) {
                    $this->invalidate();
                }

            case self::MESSAGE:
                return (object) array(
                    'kind'    => $response[0],
                    'channel' => $response[1],
                    'payload' => $response[2],
                );

            case self::PMESSAGE:
                return (object) array(
                    'kind'    => $response[0],
                    'pattern' => $response[1],
                    'channel' => $response[2],
                    'payload' => $response[3],
                );

            default:
                $message = "Received an unknown message type {$response[0]} inside of a pubsub context";
                throw new ClientException($message);
        }
    }
}

/**
 * Method-dispatcher loop built around the client-side abstraction of a Redis
 * Publish / Subscribe context.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class DispatcherLoop
{
    private $client;
    private $pubSubContext;
    private $callbacks;
    private $defaultCallback;
    private $subscriptionCallback;

    /**
     * @param Client Client instance used by the context.
     */
    public function __construct(Client $client)
    {
        $this->callbacks = array();
        $this->client = $client;
        $this->pubSubContext = $client->pubSub();
    }

    /**
     * Checks if the passed argument is a valid callback.
     *
     * @param mixed A callback.
     */
    protected function validateCallback($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException("A valid callable object must be provided");
        }
    }

    /**
     * Returns the underlying Publish / Subscribe context.
     *
     * @return PubSubContext
     */
    public function getPubSubContext()
    {
        return $this->pubSubContext;
    }

    /**
     * Sets a callback that gets invoked upon new subscriptions.
     *
     * @param mixed $callable A callback.
     */
    public function subscriptionCallback($callable = null)
    {
        if (isset($callable)) {
            $this->validateCallback($callable);
        }
        $this->subscriptionCallback = $callable;
    }

    /**
     * Sets a callback that gets invoked when a message is received on a
     * channel that does not have an associated callback.
     *
     * @param mixed $callable A callback.
     */
    public function defaultCallback($callable = null)
    {
        if (isset($callable)) {
            $this->validateCallback($callable);
        }
        $this->subscriptionCallback = $callable;
    }

    /**
     * Binds a callback to a channel.
     *
     * @param string $channel Channel name.
     * @param Callable $callback A callback.
     */
    public function attachCallback($channel, $callback)
    {
        $this->validateCallback($callback);
        $this->callbacks[$channel] = $callback;
        $this->pubSubContext->subscribe($channel);
    }

    /**
     * Stops listening to a channel and removes the associated callback.
     *
     * @param string $channel Redis channel.
     */
    public function detachCallback($channel)
    {
        if (isset($this->callbacks[$channel])) {
            unset($this->callbacks[$channel]);
            $this->pubSubContext->unsubscribe($channel);
        }
    }

    /**
     * Starts the dispatcher loop.
     */
    public function run()
    {
        foreach ($this->pubSubContext as $message) {
            $kind = $message->kind;

            if ($kind !== PubSubContext::MESSAGE && $kind !== PubSubContext::PMESSAGE) {
                if (isset($this->subscriptionCallback)) {
                    $callback = $this->subscriptionCallback;
                    call_user_func($callback, $message);
                }
                continue;
            }

            if (isset($this->callbacks[$message->channel])) {
                $callback = $this->callbacks[$message->channel];
                call_user_func($callback, $message->payload);
            }
            else if (isset($this->defaultCallback)) {
                $callback = $this->defaultCallback;
                call_user_func($callback, $message);
            }
        }
    }

    /**
     * Terminates the dispatcher loop.
     */
    public function stop()
    {
        $this->pubSubContext->closeContext();
    }
}

/* --------------------------------------------------------------------------- */

