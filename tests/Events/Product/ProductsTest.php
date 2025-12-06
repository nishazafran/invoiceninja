<?php

namespace Tests\Events\Product;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Company;
use App\Events\Product\ProductWasUpdated;
use App\Events\Product\ProductWasRestored;
use App\Events\Product\ProductWasDeleted;
use App\Events\Product\ProductWasCreated;
use App\Events\Product\ProductWasArchived;

class ProductsTest extends TestCase
{
    private function fakeProduct()
    {
        return new Product();
    }

    private function fakeCompany()
    {
        return new Company();
    }

    private function fakeEventVars()
    {
        return ['key' => 'value'];
    }

    // ------------------- ProductWasUpdated.php -------------------
    public function test_product_was_updated_event()
    {
        $product = $this->fakeProduct();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new ProductWasUpdated($product, $company, $vars);

        $this->assertSame($product, $event->product);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- ProductWasRestored.php -------------------
    public function test_product_was_restored_event()
    {
        $product = $this->fakeProduct();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();
        $fromDeleted = true;

        $event = new ProductWasRestored($product, $fromDeleted, $company, $vars);

        $this->assertSame($product, $event->product);
        $this->assertTrue($event->fromDeleted);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- ProductWasDeleted.php -------------------
    public function test_product_was_deleted_event()
    {
        $product = $this->fakeProduct();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new ProductWasDeleted($product, $company, $vars);

        $this->assertSame($product, $event->product);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- ProductWasCreated.php -------------------
    public function test_product_was_created_event()
    {
        $product = $this->fakeProduct();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();
        $input = ['name' => 'Test Product'];

        $event = new ProductWasCreated($product, $input, $company, $vars);

        $this->assertSame($product, $event->product);
        $this->assertSame($input, $event->input);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }

    // ------------------- ProductWasArchived.php -------------------
    public function test_product_was_archived_event()
    {
        $product = $this->fakeProduct();
        $company = $this->fakeCompany();
        $vars = $this->fakeEventVars();

        $event = new ProductWasArchived($product, $company, $vars);

        $this->assertSame($product, $event->product);
        $this->assertSame($company, $event->company);
        $this->assertSame($vars, $event->event_vars);
    }
}

