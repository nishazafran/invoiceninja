<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Transformers\ProductTransformer;
use App\Models\Product;
use App\Models\User;
use App\Models\Company;
use App\Models\Document;

class ProductTransformerTest extends TestCase
{
    /** @test */
public function transform_returns_array()
{
    // Create a fake Product model
    $product = new Product();
    $product->id = 1;
    $product->user_id = 2;
    $product->assigned_user_id = 3;
    $product->product_key = 'PROD001';
    $product->notes = 'Test product';
    $product->cost = 50;
    $product->price = 100;
    $product->quantity = 5;
    $product->tax_name1 = 'VAT';
    $product->tax_rate1 = 15;
    $product->tax_name2 = null;
    $product->tax_rate2 = 0;
    $product->tax_name3 = null;
    $product->tax_rate3 = 0;
    $product->created_at = time();
    $product->updated_at = time();
    $product->deleted_at = null;
    $product->custom_value1 = 'CV1';
    $product->custom_value2 = null;
    $product->custom_value3 = null;
    $product->custom_value4 = null;
    $product->is_deleted = false;
    $product->in_stock_quantity = 20;
    $product->stock_notification = true;
    $product->stock_notification_threshold = 5;
    $product->max_quantity = 100;
    $product->product_image = 'image.png';
    $product->tax_id = '1';

    $transformer = new ProductTransformer();

    $result = $transformer->transform($product);

    $this->assertIsArray($result);

    // Assert that the encoded IDs match what the transformer produces
    $this->assertEquals($transformer->encodePrimaryKey(1), $result['id']);
    $this->assertEquals($transformer->encodePrimaryKey(2), $result['user_id']);
    $this->assertEquals($transformer->encodePrimaryKey(3), $result['assigned_user_id']);

    // Check normal fields
    $this->assertEquals('PROD001', $result['product_key']);
    $this->assertEquals(100.0, $result['price']);
    $this->assertEquals(5.0, $result['quantity']);
    $this->assertEquals('VAT', $result['tax_name1']);
    $this->assertEquals(15.0, $result['tax_rate1']);
}

    /** @test */
    public function include_user_returns_item()
    {
        $user = new User();
        $product = new Product();
        $product->user = $user;

        $transformer = new ProductTransformer();
        $response = $transformer->includeUser($product);

        $this->assertNotNull($response);
    }

    /** @test */
    public function include_company_returns_item()
    {
        $company = new Company();
        $product = new Product();
        $product->company = $company;

        $transformer = new ProductTransformer();
        $response = $transformer->includeCompany($product);

        $this->assertNotNull($response);
    }

    /** @test */
    public function include_documents_returns_collection()
    {
        $doc1 = new Document();
        $doc2 = new Document();

        $product = new Product();
        $product->documents = collect([$doc1, $doc2]);

        $transformer = new ProductTransformer();
        $response = $transformer->includeDocuments($product);

        $this->assertNotNull($response);
    }
}

