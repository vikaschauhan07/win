<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->text('userid')->nullable();
            $table->text('firstname')->nullable();
            $table->text('lastname')->nullable();
            $table->text('email')->nullable();
            $table->text('gender')->nullable();
            $table->text('locale')->nullable();
            $table->text('picture')->nullable();
            $table->text('excluded_stores')->nullable();
            $table->text('password')->nullable();
            $table->tinyInteger('trial')->nullable();
            $table->text('subscription')->nullable();
            $table->timestamp('subscription_started')->nullable();
            $table->integer('subscription_end_for_migration')->nullable();
            $table->integer('migrated_and_resubscribed')->nullable();
            $table->integer('lemon_squeezy_subscriber')->nullable();
            $table->text('ls_customer_id')->nullable();
            $table->text('subscription_platform')->nullable();
            $table->text('subscription_id')->nullable();
            $table->text('customer_id')->nullable();
            $table->text('charge_id')->nullable();
            $table->text('affiliate')->nullable();
            $table->text('coupon')->nullable();
            $table->integer('free_fb_ads_credits_used')->nullable()->default(0);
            $table->integer('free_tt_ads_credits_used')->nullable()->default(0);
            $table->integer('free_creative_center_credits_used')->nullable()->default(0);
            $table->integer('free_meta_advertisers_credits_used')->nullable()->default(0);
            $table->text('stores')->nullable();
            $table->text('stores_id')->nullable();
            $table->text('ads')->nullable();
            $table->text('tiktokads')->nullable();
            $table->boolean('confirmed')->default(false);
            $table->text('elapsed_at')->nullable();
            $table->string('referral_code', 45)->nullable();
            $table->string('referred_by', 45)->nullable();
            $table->string('remember_me', 45)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
