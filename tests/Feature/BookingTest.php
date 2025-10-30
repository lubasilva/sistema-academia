<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Plan;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar configurações padrão
        Setting::create(['key' => 'max_capacity_per_class', 'value' => '10']);
        Setting::create(['key' => 'min_booking_hours', 'value' => '2']);
        Setting::create(['key' => 'min_cancel_hours', 'value' => '2']);
    }

    public function test_authenticated_users_can_access_bookings_page(): void
    {
        $user = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($user)->get('/bookings');

        $response->assertStatus(200);
    }

    public function test_guest_users_cannot_access_bookings_page(): void
    {
        $response = $this->get('/bookings');

        $response->assertRedirect('/login');
    }

    public function test_student_with_active_plan_can_create_booking(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $plan = Plan::factory()->create([
            'credits' => 10,
            'duration_days' => 30,
        ]);

        UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'credits_remaining' => 10,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $schedule = Schedule::factory()->create([
            'day_of_week' => now()->addDays(3)->dayOfWeek,
            'time' => '10:00:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/bookings', [
            'date' => now()->addDays(3)->format('Y-m-d'),
            'schedule_id' => $schedule->id,
        ]);

        $response->assertRedirect('/bookings');
        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_student_without_active_plan_cannot_create_booking(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $schedule = Schedule::factory()->create([
            'day_of_week' => now()->addDays(3)->dayOfWeek,
            'time' => '10:00:00',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/bookings', [
            'date' => now()->addDays(3)->format('Y-m-d'),
            'schedule_id' => $schedule->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $user->id,
        ]);
    }

    public function test_booking_deducts_credit_from_user_plan(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $plan = Plan::factory()->create([
            'credits' => 10,
            'duration_days' => 30,
        ]);

        $userPlan = UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'credits_remaining' => 10,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $schedule = Schedule::factory()->create([
            'day_of_week' => now()->addDays(3)->dayOfWeek,
            'time' => '10:00:00',
            'is_active' => true,
        ]);

        $this->actingAs($user)->post('/bookings', [
            'date' => now()->addDays(3)->format('Y-m-d'),
            'schedule_id' => $schedule->id,
        ]);

        $this->assertDatabaseHas('user_plans', [
            'id' => $userPlan->id,
            'credits_remaining' => 9,
        ]);
    }

    public function test_user_can_cancel_booking(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $plan = Plan::factory()->create();

        $userPlan = UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'credits_remaining' => 5,
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addDays(30),
        ]);

        $schedule = Schedule::factory()->create();
        $booking = Booking::create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'date' => now()->addDays(3)->format('Y-m-d'),
            'status' => 'confirmed',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete("/bookings/{$booking->id}");

        $response->assertRedirect('/bookings');
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
        ]);

        // Verificar se crédito foi devolvido
        $this->assertDatabaseHas('user_plans', [
            'id' => $userPlan->id,
            'credits_remaining' => 6,
        ]);
    }
}
