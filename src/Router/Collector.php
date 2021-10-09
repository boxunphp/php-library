<?php

namespace All\Router;

use All\Router\Interfaces\CollectorInterface;
use All\Router\Interfaces\GeneratorInterface;
use All\Router\Interfaces\ParserInterface;
use All\Router\Traits\CollectorTrait;

class Collector implements CollectorInterface
{
    use CollectorTrait;

    /** @var ParserInterface */
    protected $parser;

    /** @var GeneratorInterface */
    protected $generator;

    /** @var string */
    protected $groupPrefix;

    /**
     * Constructs a route collector.
     *
     * @param ParserInterface|null   $parser
     * @param GeneratorInterface|null $generator
     */
    public function __construct(?ParserInterface $parser = null, GeneratorInterface $generator = null)
    {
        $this->parser = $parser ?? new Parser;
        $this->generator = $generator ?? new Generator;
        $this->groupPrefix = '';
    }

    /**
     * Adds a route to the collection.
     *
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string|string[] $httpMethod
     * @param string $route
     * @param mixed  $handler
     */
    public function addRoute($httpMethod, $route, $handler)
    {
        $route = $this->groupPrefix . $route;
        $routeDatas = $this->parser->parse($route);
        foreach ((array) $httpMethod as $method) {
            foreach ($routeDatas as $routeData) {
                $this->generator->addRoute($method, $routeData, $handler);
            }
        }
    }

    /**
     * Create a route group with a common prefix.
     *
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string $prefix
     * @param callable $callback
     */
    public function addGroup($prefix, callable $callback)
    {
        $previousGroupPrefix = $this->groupPrefix;
        $this->groupPrefix = $previousGroupPrefix . $prefix;
        $callback($this);
        $this->groupPrefix = $previousGroupPrefix;
    }

    /**
     * Returns the collected route data, as provided by the data generator.
     *
     * @return array
     */
    public function getData()
    {
        return $this->generator->getData();
    }
}
