<?php
declare(strict_types=1);

namespace App\Tests\Acceptance;


use App\Tests\Support\AcceptanceTester;
use Codeception\Util\HttpCode;
class WorldControllerCest
{
    public function testWorldHello(AcceptanceTester $I)
    {
        $I->amOnPage('/world/hello');
        $I->see('Hello world');
    }
}