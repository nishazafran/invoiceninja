<?php

namespace Tests\Jobs\Company;

use App\Jobs\Company\CreateCompanyPaymentTerms;
use App\Models\PaymentTerm;
use Mockery;
use PHPUnit\Framework\TestCase;

class CreateCompanyPaymentTermsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function test_handle_inserts_all_payment_terms()
    {
        $company = (object) ['id' => 1];
        $user = (object) ['id' => 2];

        // Mock the static insert method
        $paymentTermMock = Mockery::mock('alias:' . PaymentTerm::class);
        $paymentTermMock->shouldReceive('insert')
            ->once()
            ->withArgs(function ($paymentTerms) use ($company, $user) {
                $this->assertCount(8, $paymentTerms, "Should insert 8 payment terms");

                $first = $paymentTerms[0];
                $this->assertEquals(0, $first['num_days']);
                $this->assertEquals('Net 0', $first['name']);
                $this->assertEquals($company->id, $first['company_id']);
                $this->assertEquals($user->id, $first['user_id']);
                $this->assertArrayHasKey('created_at', $first);
                $this->assertArrayHasKey('updated_at', $first);

                return true;
            })
            ->andReturnTrue();

        $job = new CreateCompanyPaymentTerms($company, $user);
        $job->handle();
    }

    /** @test */
    public function test_constructor_sets_company_and_user()
    {
        $company = (object) ['id' => 1];
        $user = (object) ['id' => 2];

        $job = new CreateCompanyPaymentTerms($company, $user);

        $reflection = new \ReflectionClass($job);
        $companyProp = $reflection->getProperty('company');
        $companyProp->setAccessible(true);
        $userProp = $reflection->getProperty('user');
        $userProp->setAccessible(true);

        $this->assertSame($company, $companyProp->getValue($job));
        $this->assertSame($user, $userProp->getValue($job));
    }

    /** @test */
    public function test_handle_inserts_payment_terms()
    {
        $company = (object) ['id' => 1];
        $user = (object) ['id' => 2];

        $paymentTermMock = Mockery::mock('alias:' . PaymentTerm::class);
        $paymentTermMock->shouldReceive('insert')
            ->once()
            ->withArgs(function ($paymentTerms) use ($company, $user) {
                $this->assertCount(8, $paymentTerms, "Should insert 8 payment terms");
                return true;
            })
            ->andReturnTrue();

        $job = new CreateCompanyPaymentTerms($company, $user);
        $job->handle();

        // Simple assertion to satisfy PHPUnit
        $this->assertTrue(true);
    }
}

