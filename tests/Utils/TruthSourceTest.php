<?php

namespace Tests\Utils;

use App\Utils\TruthSource;
use PHPUnit\Framework\TestCase;

class TruthSourceTest extends TestCase
{
    public function test_setters_and_getters_work_correctly()
    {
        $truthSource = new TruthSource();

        $company = (object)['name' => 'Test Company'];
        $user = (object)['name' => 'Test User'];
        $companyUser = (object)['role' => 'admin'];
        $companyToken = 'token123';
        $premiumHosted = true;

        // Test setters
        $this->assertSame($truthSource, $truthSource->setCompany($company));
        $this->assertSame($truthSource, $truthSource->setUser($user));
        $this->assertSame($truthSource, $truthSource->setCompanyUser($companyUser));
        $this->assertSame($truthSource, $truthSource->setCompanyToken($companyToken));
        $this->assertSame($truthSource, $truthSource->setPremiumHosted($premiumHosted));

        // Test getters
        $this->assertSame($company, $truthSource->getCompany());
        $this->assertSame($user, $truthSource->getUser());
        $this->assertSame($companyUser, $truthSource->getCompanyUser());
        $this->assertSame($companyToken, $truthSource->getCompanyToken());
        $this->assertSame($premiumHosted, $truthSource->getPremiumHosted());
    }
}

