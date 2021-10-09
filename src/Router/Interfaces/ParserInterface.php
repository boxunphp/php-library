<?php

namespace All\Router\Interfaces;

interface ParserInterface
{
    /**
     * 将路由字符串解析为多个路由数据数组
     *
     * 使用一个示例定义预期输出
     *
     * 对于路由字符串 "/fixedRoutePart/{varName}[/moreFixed/{varName2:number}]"
     * 如果 {varName} 被解释为占位符而 [...] 被解释为可选的路由部分
     * 则预期的结果是：
     * [
     *     // 第一次路由：没有可选部分
     *     [
     *         "/fixedRoutePart/",
     *         ["varName", "[^/]+"],
     *     ],
     *     // 第二次路由：有可选部分
     *     [
     *         "/fixedRoutePart/",
     *         ["varName", "[^/]+"],
     *         "/moreFixed/",
     *         ["varName2", "[0-9]+"],
     *     ],
     * ]
     *
     * 把一个路由字符串被转换成二维路由数据数组
     *
     * @param string $route 路由字符串
     *
     * @return mixed[][] 二维的路由数据数组
     */
    public function parse($route);
}
