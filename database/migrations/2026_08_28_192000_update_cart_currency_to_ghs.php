<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('carts')->where('currency', 'USD')->update(['currency' => 'GHS']);
    }

    public function down(): void
    {
        DB::table('carts')->where('currency', 'GHS')->update(['currency' => 'USD']);
    }
};
