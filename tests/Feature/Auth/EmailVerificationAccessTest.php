<?php

use App\Models\User;

test('verified Google users can access account settings without a verified phone', function () {
    $user = User::factory()->create(['phone' => null, 'phone_verified_at' => null, 'google_id' => 'google-123']);

    $this->actingAs($user)->get(route('profile.edit'))->assertOk();

    expect($user->fresh()->phone_verified_at)->toBeNull();
});
