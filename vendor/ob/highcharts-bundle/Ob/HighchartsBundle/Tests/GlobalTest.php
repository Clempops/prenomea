<?php

namespace Ob\HighchartsBundle\Tests;

use Ob\HighchartsBundle\Highcharts\Highchart;

/**
 * This class hold Unit tests for the global option
 */
class GlobalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * useUTC option (true/false)
     */
    public function testGlobal()
    {
        $chart = new Highchart();

        $chart->global->useUTC("true");
        $this->assertRegExp('/global: \{"useUTC":"true"\}/', $chart->render());

        $chart->global->useUTC("false");
        $this->assertRegExp('/global: \{"useUTC":"false"\}/', $chart->render());
    }
}