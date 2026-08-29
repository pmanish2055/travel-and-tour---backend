<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PackageDeparture;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Region;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function createPackageWithDeparture(): array
    {
        $region = Region::create(['name'=>'Test Region','slug'=>'test-region','is_active'=>true,'sort_order'=>1]);
        $dest = Destination::create(['name'=>'Test Dest','slug'=>'test-dest','region_id'=>$region->id,'is_active'=>true,'sort_order'=>1]);
        $cat = Category::create(['name'=>'Trek','slug'=>'trek','is_active'=>true,'sort_order'=>1]);
        $pkg = Package::create([
            'category_id'=>$cat->id,'destination_id'=>$dest->id,'region_id'=>$region->id,
            'title'=>'Test Package','slug'=>'test-package','duration_days'=>5,'duration_nights'=>4,
            'price'=>1000,'status'=>'published','published_at'=>now(),
        ]);
        $dep = PackageDeparture::create([
            'package_id'=>$pkg->id,'departure_date'=>now()->addDays(10)->toDateString(),
            'return_date'=>now()->addDays(15)->toDateString(),'price'=>1000,'seats_total'=>4,'seats_booked'=>0,'status'=>'open'
        ]);
        return [$pkg,$dep];
    }

    public function test_cannot_overbook(): void
    {
        [$pkg,$dep] = $this->createPackageWithDeparture();
        // first booking 3 pax should succeed (4 seats)
        $res1 = $this->postJson('/api/v1/bookings', [
            'package_id'=>$pkg->id,'departure_id'=>$dep->id,'travel_date'=>$dep->departure_date,
            'pax_adult'=>3,'customer_name'=>'A','customer_email'=>'a@test.com','customer_phone'=>'123'
        ]);
        $res1->assertStatus(201);
        // second booking 2 pax should fail (only 1 left)
        $res2 = $this->postJson('/api/v1/bookings', [
            'package_id'=>$pkg->id,'departure_id'=>$dep->id,'travel_date'=>$dep->departure_date,
            'pax_adult'=>2,'customer_name'=>'B','customer_email'=>'b@test.com','customer_phone'=>'456'
        ]);
        $res2->assertStatus(422);
        $this->assertStringContainsString('Not enough seats', $res2->json('message'));
    }

    public function test_payment_verify_requires_pending(): void
    {
        [$pkg,$dep] = $this->createPackageWithDeparture();
        $book = Booking::create([
            'package_id'=>$pkg->id,'travel_date'=>now()->addDays(10)->toDateString(),
            'pax_adult'=>1,'total_amount'=>1000,'customer_name'=>'X','customer_email'=>'x@test.com','customer_phone'=>'123'
        ]);
        $res = $this->postJson('/api/v1/payments/verify', [
            'booking_code'=>$book->booking_code,'gateway'=>'esewa','transaction_id'=>'TEST_OK_1'
        ]);
        $res->assertStatus(422);
        $this->assertStringContainsString('No pending payment', $res->json('message'));
    }

    public function test_payment_mock_verify_succeeds_with_txn(): void
    {
        [$pkg,$dep] = $this->createPackageWithDeparture();
        $book = Booking::create([
            'package_id'=>$pkg->id,'travel_date'=>now()->addDays(10)->toDateString(),
            'pax_adult'=>1,'total_amount'=>1000,'customer_name'=>'Y','customer_email'=>'y@test.com','customer_phone'=>'123'
        ]);
        $init = $this->postJson('/api/v1/payments/initiate', ['booking_code'=>$book->booking_code,'gateway'=>'esewa']);
        $init->assertStatus(201);
        $verify = $this->postJson('/api/v1/payments/verify', ['booking_code'=>$book->booking_code,'gateway'=>'esewa','transaction_id'=>'TEST_OK_123']);
        $verify->assertStatus(200);
        $this->assertTrue($verify->json('success'));
        $this->assertEquals('completed', $verify->json('data.payment.status'));
        $book->refresh();
        $this->assertEquals('paid', $book->payment_status);
    }

    public function test_search_escapes_wildcards(): void
    {
        [$pkg,$dep] = $this->createPackageWithDeparture();
        $res = $this->getJson('/api/v1/search?q=Test%25&type=packages');
        $res->assertStatus(200);
        $res->assertJson(['success'=>true]);
    }
}
