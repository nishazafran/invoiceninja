<?php

namespace Tests\Unit\Transformers;

use Tests\TestCase;
use App\Models\Vendor;
use App\Models\Activity;
use App\Models\Document;
use App\Models\Location;
use App\Models\VendorContact;
use App\Transformers\VendorTransformer;
use League\Fractal\Resource\Collection;

class VendorTransformerTest extends TestCase
{
    /** @test */
    public function it_transforms_vendor_and_includes_all_relations()
    {
        $vendor = new Vendor();
        $vendor->id = 1;
        $vendor->user_id = 2;
        $vendor->assigned_user_id = 3;
        $vendor->name = 'Acme Inc';
        $vendor->website = 'https://acme.com';
        $vendor->contacts = [new VendorContact()];
        $vendor->documents = [new Document()];
        $vendor->activities = [new Activity()];
        $vendor->locations = [new Location()];

        $transformer = new VendorTransformer();

        // Test transform
        $data = $transformer->transform($vendor);
        $this->assertEquals('Acme Inc', $data['name']);
        $this->assertEquals('https://acme.com', $data['website']);

        // Test includes
        $this->assertInstanceOf(Collection::class, $transformer->includeActivities($vendor));
        $this->assertInstanceOf(Collection::class, $transformer->includeContacts($vendor));
        $this->assertInstanceOf(Collection::class, $transformer->includeDocuments($vendor));
        $this->assertInstanceOf(Collection::class, $transformer->includeLocations($vendor));
    }
}

