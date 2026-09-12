<?php

namespace Tests\Feature;

use App\Models\SourcingRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicSourcingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
    }

    public function test_public_sourcing_form_creates_customer_logs_in_and_redirects_to_portal(): void
    {
        Storage::fake('public');

        $response = $this->withSession(['_token' => csrf_token()])->post(route('sourcing.submit'), [
            '_token' => csrf_token(),
            'name' => 'خریدار آزمایشی',
            'contact' => 'lead@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'title' => 'دستگاه صنعتی',
            'source_url' => 'https://www.alibaba.com/item/demo',
            'estimated_quantity' => 50,
            'technical_specifications' => 'توان ۲۲۰ ولت',
            'sample' => UploadedFile::fake()->image('sample.jpg'),
        ]);

        $user = User::where('email', 'lead@example.com')->firstOrFail();

        $response->assertRedirect('/portal/portal-sourcing-requests');
        $this->assertAuthenticatedAs($user);
        $this->assertSame('customer', $user->role);
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertSame(1, SourcingRequest::where('user_id', $user->id)->count());
        Storage::disk('public')->assertExists(SourcingRequest::first()->attachments[0]['path']);
    }

    public function test_existing_account_requires_its_password(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'role' => 'customer',
            'password' => 'secret-password',
        ]);

        $this->get(route('sourcing.create'));
        $response = $this->from(route('sourcing.create'))->withSession(['_token' => csrf_token()])->post(route('sourcing.submit'), [
            '_token' => csrf_token(),
            'name' => $user->name,
            'contact' => $user->email,
            'title' => 'کالای جدید',
            'technical_specifications' => 'مشخصات',
        ]);

        $response->assertRedirect(route('sourcing.create'));
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
        $this->assertDatabaseCount('sourcing_requests', 0);
    }
}
