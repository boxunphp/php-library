<?php

namespace All\Router;

use All\Router\Interfaces\ParserInterface;

/**
 * 路由字符串解析器
 */
class Parser implements ParserInterface
{
    public const VARIABLE_REGEX = <<<'REGEX'
\{
    \s* ([a-zA-Z_][a-zA-Z0-9_-]*) \s*
    (?:
        : \s* ([^{}]*(?:\{(?-1)\}[^{}]*)*)
    )?
\}
REGEX;

    private const REGEX_MAPPER = [
        'number'        => '[0-9]+',            // 数字
        'word'          => '[a-zA-Z]+',         // 字母
        'alphanum'      => '[a-zA-Z0-9]+',      // 字母+数字
        'string'        => '[a-zA-Z0-9-_]+',    // 字母+数字+短横+下划线
        'any'           => '[^/]+',             // 任意字符
    ];

    public function parse($route)
    {
        // 可选的配置规则/aaa[/bbb[/ccc[/ddd]]]，这样就有3层的可选值
        $routeWithoutClosingOptionals = rtrim($route, ']');
        $numOptionals = strlen($route) - strlen($routeWithoutClosingOptionals);

        // 根据 [ 分段, 用于匹配 ] 个数
        $segments = preg_split('~' . self::VARIABLE_REGEX . '(*SKIP)(*F) | \[~x', $routeWithoutClosingOptionals);
        if ($numOptionals !== count($segments) - 1) {
            // 可选项必须居于最右边
            if (preg_match('~' . self::VARIABLE_REGEX . '(*SKIP)(*F) | \]~x', $routeWithoutClosingOptionals)) {
                throw new \InvalidArgumentException('Optional segments can only occur at the end of a route');
            }
            throw new \InvalidArgumentException("Number of opening '[' and closing ']' does not match");
        }

        $currentRoute = '';
        $routeDatas = [];
        foreach ($segments as $n => $segment) {
            // 严格按配置来，不能有 [[x]]等两次以上配置
            if ($segment === '' && $n !== 0) {
                throw new \InvalidArgumentException('Empty optional part');
            }

            $currentRoute .= $segment;
            $routeDatas[] = $this->parsePlaceholders($currentRoute);
        }

        return $routeDatas;
    }

    /**
     * 解析占位符，根据可选项拆分为多种组合，并且提取占位符
     *
     * @param string
     * @return mixed[]
     */
    private function parsePlaceholders($route)
    {
        // (?: pattern)是非捕获型括号
        // (pattern)是捕获型括号
        // (?<name> pattern) 匹配pattern，  匹配pattern并捕获结果，设置name为组名
        // if (!preg_match_all('~\{([a-zA-Z_][a-zA-Z0-9_-]*)(?::([number|string]*))?\}~x', $route, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
        if (!preg_match_all('~' . self::VARIABLE_REGEX . '~x', $route, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            return [$route];
        }

        $offset = 0;
        $routeData = [];
        foreach ($matches as $set) {
            if ($set[0][1] > $offset) {
                $routeData[] = substr($route, $offset, $set[0][1] - $offset);
            }
            // 默认any
            $re = isset($set[2]) ? trim($set[2][0]) : 'any';
            $regex = self::REGEX_MAPPER[$re] ?? $re;
            $routeData[] = [
                $set[1][0],
                $regex
            ];
            $offset = $set[0][1] + strlen($set[0][0]);
        }

        if ($offset !== strlen($route)) {
            $routeData[] = substr($route, $offset);
        }

        return $routeData;
    }
}
