<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/19
 * Time: 3:07 PM
 */

namespace Tests\Utils;

use All\Utils\Http;
use All\Utils\HttpCode;
use PHPUnit\Framework\TestCase;

// class HttpTest extends TestCase
// {
//     protected $url = 'http://php-framework/curl.json';
//     protected $response = [
//         'flag' => 'success',
//         'code' => 0,
//         'message' => '成功',
//         'data' => []
//     ];

//     // public function testPost()
//     // {
//     //     $csrfToken = 'poi';
//     //     $params = ['g' => 'GG', 'i' => 'II'];
//     //     $data = ['a' => 'A', 'b' => 'B'];
//     //     $originData = [
//     //         'method' => 'post',
//     //         'params' => $params,
//     //         'data' => $data,
//     //         'input' => '',
//     //         'header' => [
//     //             'csrf_token' => $csrfToken,
//     //         ]
//     //     ];
//     //     $url = $this->url . '?' . http_build_query($params);

//     //     $Http = new Http();
//     //     $Http->setHeader('Csrf-Token: ' . $csrfToken);
//     //     $Http->formData($data)->post($url);
//     //     var_dump($url, $data);
//     //     $this->assertEquals(HttpCode::OK, $Http->getHttpCode());
        
//     //     $result = $Http->decode()->result();
//     //     foreach ($originData as $key => $item) {
//     //         $this->assertEquals($item, $result['data'][$key]);
//     //     }

//     //     $Http->setHeader('Csrf-Token: ' . $csrfToken . 'b');
//     //     $Http->formUrlencoded($data)->post($url);
//     //     $this->assertEquals(HttpCode::OK, $Http->getHttpCode());
//     //     $originData['input'] = http_build_query($data);
//     //     $originData['header']['csrf_token'] = $csrfToken . 'b';
//     //     $result = $Http->decode()->result();
//     //     foreach ($originData as $key => $item) {
//     //         $this->assertEquals($item, $result['data'][$key]);
//     //     }

//     //     $Http->setHeader('Csrf-Token: ' . $csrfToken . 'c');
//     //     $Http->rawData($data)->post($url);
//     //     $this->assertEquals(HttpCode::OK, $Http->getHttpCode());
//     //     $originData['data'] = [];
//     //     $originData['input'] = json_encode($data);
//     //     $originData['header']['csrf_token'] = $csrfToken . 'c';
//     //     $result = $Http->decode()->result();
//     //     foreach ($originData as $key => $item) {
//     //         $this->assertEquals($item, $result['data'][$key]);
//     //     }
//     // }

//     // public function testGet()
//     // {
//     //     $accessToken = 'eirow';
//     //     $params = ['g' => 'GG', 'i' => 'II'];
//     //     $url = $this->url . '?' . http_build_query($params);
//     //     $originData = [
//     //         'method' => 'get',
//     //         'params' => $params,
//     //         'header' => [
//     //             'access_token' => $accessToken,
//     //         ]
//     //     ];

//     //     $Http = new Http();
//     //     $Http->setHeader('Access-Token: ' . $accessToken);
//     //     $Http->get($url);
//     //     $this->assertEquals(HttpCode::OK, $Http->getHttpCode());
//     //     $result = $Http->decode()->result();
//     //     foreach ($originData as $key => $item) {
//     //         $this->assertEquals($item, $result['data'][$key]);
//     //     }
//     // }
// }
