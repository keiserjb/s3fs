<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackdropS3FS\Symfony\Component\VarDumper\Cloner;

use BackdropS3FS\Symfony\Component\VarDumper\Caster\Caster;
use BackdropS3FS\Symfony\Component\VarDumper\Exception\ThrowingCasterException;
/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static array $defaultCasters = ['__PHP_Incomplete_Class' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\Caster', 'castPhpIncompleteClass'], 'AddressInfo' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\AddressInfoCaster', 'castAddressInfo'], 'Socket' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SocketCaster', 'castSocket'], 'BackdropS3FS\Symfony\Component\VarDumper\Caster\CutStub' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'castStub'], 'BackdropS3FS\Symfony\Component\VarDumper\Caster\CutArrayStub' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'castCutArray'], 'BackdropS3FS\Symfony\Component\VarDumper\Caster\ConstStub' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'castStub'], 'BackdropS3FS\Symfony\Component\VarDumper\Caster\EnumStub' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'castEnum'], 'BackdropS3FS\Symfony\Component\VarDumper\Caster\ScalarStub' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'castScalar'], 'Fiber' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\FiberCaster', 'castFiber'], 'Closure' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClosure'], 'Generator' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castGenerator'], 'ReflectionType' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castType'], 'ReflectionAttribute' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castAttribute'], 'ReflectionGenerator' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castReflectionGenerator'], 'ReflectionClass' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClass'], 'ReflectionClassConstant' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castClassConstant'], 'ReflectionFunctionAbstract' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castFunctionAbstract'], 'ReflectionMethod' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castMethod'], 'ReflectionParameter' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castParameter'], 'ReflectionProperty' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castProperty'], 'ReflectionReference' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castReference'], 'ReflectionExtension' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castExtension'], 'ReflectionZendExtension' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ReflectionCaster', 'castZendExtension'], 'BackdropS3FS\Doctrine\Common\Persistence\ObjectManager' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'BackdropS3FS\Doctrine\Common\Proxy\Proxy' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castCommonProxy'], 'BackdropS3FS\Doctrine\ORM\Proxy\Proxy' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castOrmProxy'], 'BackdropS3FS\Doctrine\ORM\PersistentCollection' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DoctrineCaster', 'castPersistentCollection'], 'BackdropS3FS\Doctrine\Persistence\ObjectManager' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'DOMException' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castException'], 'BackdropS3FS\Dom\Exception' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castException'], 'DOMStringList' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMNameList' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMImplementation' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castImplementation'], 'BackdropS3FS\Dom\Implementation' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castImplementation'], 'DOMImplementationList' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMNode' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'BackdropS3FS\Dom\Node' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMNameSpaceNode' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMDocument' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDocument'], 'BackdropS3FS\Dom\XMLDocument' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castXMLDocument'], 'BackdropS3FS\Dom\HTMLDocument' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castHTMLDocument'], 'DOMNodeList' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'BackdropS3FS\Dom\NodeList' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMNamedNodeMap' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'BackdropS3FS\Dom\DTDNamedNodeMap' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'DOMXPath' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'BackdropS3FS\Dom\XPath' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'BackdropS3FS\Dom\HTMLCollection' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'BackdropS3FS\Dom\TokenList' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DOMCaster', 'castDom'], 'XMLReader' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\XmlReaderCaster', 'castXmlReader'], 'ErrorException' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castErrorException'], 'Exception' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castException'], 'Error' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castError'], 'BackdropS3FS\Symfony\Bridge\Monolog\Logger' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'BackdropS3FS\Symfony\Component\DependencyInjection\ContainerInterface' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'BackdropS3FS\Symfony\Component\EventDispatcher\EventDispatcherInterface' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'BackdropS3FS\Symfony\Component\HttpClient\AmpHttpClient' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'], 'BackdropS3FS\Symfony\Component\HttpClient\CurlHttpClient' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'], 'BackdropS3FS\Symfony\Component\HttpClient\NativeHttpClient' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'], 'BackdropS3FS\Symfony\Component\HttpClient\Response\AmpResponse' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'], 'BackdropS3FS\Symfony\Component\HttpClient\Response\AmpResponseV4' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'], 'BackdropS3FS\Symfony\Component\HttpClient\Response\AmpResponseV5' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'], 'BackdropS3FS\Symfony\Component\HttpClient\Response\CurlResponse' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'], 'BackdropS3FS\Symfony\Component\HttpClient\Response\NativeResponse' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'], 'BackdropS3FS\Symfony\Component\HttpFoundation\Request' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castRequest'], 'BackdropS3FS\Symfony\Component\Uid\Ulid' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castUlid'], 'BackdropS3FS\Symfony\Component\Uid\Uuid' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castUuid'], 'BackdropS3FS\Symfony\Component\VarExporter\Internal\LazyObjectState' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SymfonyCaster', 'castLazyObjectState'], 'BackdropS3FS\Symfony\Component\VarDumper\Exception\ThrowingCasterException' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castThrowingCasterException'], 'BackdropS3FS\Symfony\Component\VarDumper\Caster\TraceStub' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castTraceStub'], 'BackdropS3FS\Symfony\Component\VarDumper\Caster\FrameStub' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castFrameStub'], 'BackdropS3FS\Symfony\Component\VarDumper\Cloner\AbstractCloner' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'BackdropS3FS\Symfony\Component\ErrorHandler\Exception\FlattenException' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castFlattenException'], 'BackdropS3FS\Symfony\Component\ErrorHandler\Exception\SilencedErrorContext' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ExceptionCaster', 'castSilencedErrorContext'], 'BackdropS3FS\Imagine\Image\ImageInterface' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ImagineCaster', 'castImage'], 'BackdropS3FS\Ramsey\Uuid\UuidInterface' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\UuidCaster', 'castRamseyUuid'], 'BackdropS3FS\ProxyManager\Proxy\ProxyInterface' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ProxyManagerCaster', 'castProxy'], 'PHPUnit_Framework_MockObject_MockObject' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'BackdropS3FS\PHPUnit\Framework\MockObject\MockObject' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'BackdropS3FS\PHPUnit\Framework\MockObject\Stub' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'BackdropS3FS\Prophecy\Prophecy\ProphecySubjectInterface' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'BackdropS3FS\Mockery\MockInterface' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\StubCaster', 'cutInternals'], 'PDO' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\PdoCaster', 'castPdo'], 'PDOStatement' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\PdoCaster', 'castPdoStatement'], 'AMQPConnection' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castConnection'], 'AMQPChannel' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castChannel'], 'AMQPQueue' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castQueue'], 'AMQPExchange' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castExchange'], 'AMQPEnvelope' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\AmqpCaster', 'castEnvelope'], 'ArrayObject' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SplCaster', 'castArrayObject'], 'ArrayIterator' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SplCaster', 'castArrayIterator'], 'SplDoublyLinkedList' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SplCaster', 'castDoublyLinkedList'], 'SplFileInfo' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SplCaster', 'castFileInfo'], 'SplFileObject' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SplCaster', 'castFileObject'], 'SplHeap' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SplCaster', 'castHeap'], 'SplObjectStorage' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SplCaster', 'castObjectStorage'], 'SplPriorityQueue' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SplCaster', 'castHeap'], 'OuterIterator' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SplCaster', 'castOuterIterator'], 'WeakMap' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SplCaster', 'castWeakMap'], 'WeakReference' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SplCaster', 'castWeakReference'], 'Redis' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedis'], 'BackdropS3FS\Relay\Relay' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedis'], 'RedisArray' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedisArray'], 'RedisCluster' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RedisCaster', 'castRedisCluster'], 'DateTimeInterface' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DateCaster', 'castDateTime'], 'DateInterval' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DateCaster', 'castInterval'], 'DateTimeZone' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DateCaster', 'castTimeZone'], 'DatePeriod' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DateCaster', 'castPeriod'], 'GMP' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\GmpCaster', 'castGmp'], 'MessageFormatter' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\IntlCaster', 'castMessageFormatter'], 'NumberFormatter' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\IntlCaster', 'castNumberFormatter'], 'IntlTimeZone' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlTimeZone'], 'IntlCalendar' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlCalendar'], 'IntlDateFormatter' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\IntlCaster', 'castIntlDateFormatter'], 'Memcached' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\MemcachedCaster', 'castMemcached'], 'BackdropS3FS\Ds\Collection' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DsCaster', 'castCollection'], 'BackdropS3FS\Ds\Map' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DsCaster', 'castMap'], 'BackdropS3FS\Ds\Pair' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DsCaster', 'castPair'], 'BackdropS3FS\Symfony\Component\VarDumper\Caster\DsPairStub' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\DsCaster', 'castPairStub'], 'mysqli_driver' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\MysqliCaster', 'castMysqliDriver'], 'CurlHandle' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\CurlCaster', 'castCurl'], 'BackdropS3FS\Dba\Connection' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'], ':dba' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'], ':dba persistent' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castDba'], 'GdImage' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\GdCaster', 'castGd'], 'SQLite3Result' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\SqliteCaster', 'castSqlite3Result'], 'BackdropS3FS\PgSql\Lob' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castLargeObject'], 'BackdropS3FS\PgSql\Connection' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castLink'], 'BackdropS3FS\PgSql\Result' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\PgSqlCaster', 'castResult'], ':process' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castProcess'], ':stream' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStream'], 'OpenSSLAsymmetricKey' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\OpenSSLCaster', 'castOpensslAsymmetricKey'], 'OpenSSLCertificateSigningRequest' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\OpenSSLCaster', 'castOpensslCsr'], 'OpenSSLCertificate' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\OpenSSLCaster', 'castOpensslX509'], ':persistent stream' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStream'], ':stream-context' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\ResourceCaster', 'castStreamContext'], 'XmlParser' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\XmlResourceCaster', 'castXml'], 'RdKafka' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castRdKafka'], 'BackdropS3FS\RdKafka\Conf' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castConf'], 'BackdropS3FS\RdKafka\KafkaConsumer' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castKafkaConsumer'], 'BackdropS3FS\RdKafka\Metadata\Broker' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castBrokerMetadata'], 'BackdropS3FS\RdKafka\Metadata\Collection' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castCollectionMetadata'], 'BackdropS3FS\RdKafka\Metadata\Partition' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castPartitionMetadata'], 'BackdropS3FS\RdKafka\Metadata\Topic' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicMetadata'], 'BackdropS3FS\RdKafka\Message' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castMessage'], 'BackdropS3FS\RdKafka\Topic' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopic'], 'BackdropS3FS\RdKafka\TopicPartition' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicPartition'], 'BackdropS3FS\RdKafka\TopicConf' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\RdKafkaCaster', 'castTopicConf'], 'BackdropS3FS\FFI\CData' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\FFICaster', 'castCTypeOrCData'], 'BackdropS3FS\FFI\CType' => ['BackdropS3FS\Symfony\Component\VarDumper\Caster\FFICaster', 'castCTypeOrCData']];
    protected int $maxItems = 2500;
    protected int $maxString = -1;
    protected int $minDepth = 1;
    /**
     * @var array<string, list<callable>>
     */
    private array $casters = [];
    /**
     * @var callable|null
     */
    private $prevErrorHandler;
    private array $classInfo = [];
    private int $filter = 0;
    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(?array $casters = null)
    {
        $this->addCasters($casters ?? static::$defaultCasters);
    }
    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or objects types to a callback.
     * Types are in the key, with a callable caster for value.
     * Resource types are to be prefixed with a `:`,
     * see e.g. static::$defaultCasters.
     *
     * @param callable[] $casters A map of casters
     */
    public function addCasters(array $casters): void
    {
        foreach ($casters as $type => $callback) {
            $this->casters[$type][] = $callback;
        }
    }
    /**
     * Sets the maximum number of items to clone past the minimum depth in nested structures.
     */
    public function setMaxItems(int $maxItems): void
    {
        $this->maxItems = $maxItems;
    }
    /**
     * Sets the maximum cloned length for strings.
     */
    public function setMaxString(int $maxString): void
    {
        $this->maxString = $maxString;
    }
    /**
     * Sets the minimum tree depth where we are guaranteed to clone all the items.  After this
     * depth is reached, only setMaxItems items will be cloned.
     */
    public function setMinDepth(int $minDepth): void
    {
        $this->minDepth = $minDepth;
    }
    /**
     * Clones a PHP variable.
     *
     * @param int $filter A bit field of Caster::EXCLUDE_* constants
     */
    public function cloneVar(mixed $var, int $filter = 0): Data
    {
        $this->prevErrorHandler = set_error_handler(function ($type, $msg, $file, $line, $context = []) {
            if (\E_RECOVERABLE_ERROR === $type || \E_USER_ERROR === $type) {
                // Cloner never dies
                throw new \ErrorException($msg, 0, $type, $file, $line);
            }
            if ($this->prevErrorHandler) {
                return ($this->prevErrorHandler)($type, $msg, $file, $line, $context);
            }
            return \false;
        });
        $this->filter = $filter;
        if ($gc = gc_enabled()) {
            gc_disable();
        }
        try {
            return new Data($this->doClone($var));
        } finally {
            if ($gc) {
                gc_enable();
            }
            restore_error_handler();
            $this->prevErrorHandler = null;
        }
    }
    /**
     * Effectively clones the PHP variable.
     */
    abstract protected function doClone(mixed $var): array;
    /**
     * Casts an object to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castObject(Stub $stub, bool $isNested): array
    {
        $obj = $stub->value;
        $class = $stub->class;
        if (str_contains($class, "@anonymous\x00")) {
            $stub->class = get_debug_type($obj);
        }
        if (isset($this->classInfo[$class])) {
            [$i, $parents, $hasDebugInfo, $fileInfo] = $this->classInfo[$class];
        } else {
            $i = 2;
            $parents = [$class];
            $hasDebugInfo = method_exists($class, '__debugInfo');
            foreach (class_parents($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            foreach (class_implements($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            $parents[] = '*';
            $r = new \ReflectionClass($class);
            $fileInfo = $r->isInternal() || $r->isSubclassOf(Stub::class) ? [] : ['file' => $r->getFileName(), 'line' => $r->getStartLine()];
            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }
        $stub->attr += $fileInfo;
        $a = Caster::castObject($obj, $class, $hasDebugInfo, $stub->class);
        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
    /**
     * Casts a resource to an array representation.
     *
     * @param bool $isNested True if the object is nested in the dumped structure
     */
    protected function castResource(Stub $stub, bool $isNested): array
    {
        $a = [];
        $res = $stub->value;
        $type = $stub->class;
        try {
            if (!empty($this->casters[':' . $type])) {
                foreach ($this->casters[':' . $type] as $callback) {
                    $a = $callback($res, $a, $stub, $isNested, $this->filter);
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '') . '⚠' => new ThrowingCasterException($e)] + $a;
        }
        return $a;
    }
}
