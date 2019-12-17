<?php
/**
 * Created by PhpStorm.
 * User: Jordy
 * Date: 2019/12/13
 * Time: 5:20 PM
 */

namespace Tests\View;

use All\View\View;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    /**
     * @var View
     */
    protected $view;

    protected function setUp()
    {
        $this->view = View::getInstance();
        $this->view->setRootPath(__DIR__ . '/views');
    }

    public function testRender()
    {
        $tpl = 'Default';

        $this->view->assign('name', 'ABC');
        $html = $this->view->fetch($tpl);

        $this->assertEquals('I\'m ABC', $html);
    }

    public function testTemplate()
    {
        $this->assertEquals(__DIR__ . '/views/Common/Header.phtml', $this->view->template('Common/Header'));
    }
}