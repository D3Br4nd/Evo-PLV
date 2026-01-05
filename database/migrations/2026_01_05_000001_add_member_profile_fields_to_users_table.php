<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');

            $table->date('birth_date')->nullable()->after('last_name');

            // Birth place
            $table->string('birth_place_type')->nullable()->after('birth_date'); // it|foreign
            $table->string('birth_province_code', 2)->nullable()->after('birth_place_type'); // IT only
            $table->string('birth_city')->nullable()->after('birth_province_code'); // IT city name OR foreign city
            $table->string('birth_country')->nullable()->after('birth_city'); // foreign only

            // Residence
            $table->string('residence_type')->nullable()->after('birth_country'); // it|foreign
            $table->string('residence_street')->nullable()->after('residence_type');
            $table->string('residence_house_number')->nullable()->after('residence_street');
            $table->string('residence_locality')->nullable()->after('residence_house_number'); // frazione
            $table->string('residence_province_code', 2)->nullable()->after('residence_locality'); // IT only
            $table->string('residence_city')->nullable()->after('residence_province_code'); // IT city name OR foreign city
            $table->string('residence_country')->nullable()->after('residence_city'); // foreign only

            // PLV membership dates
            $table->date('plv_joined_at')->nullable()->after('residence_country');
            $table->date('plv_expires_at')->nullable()->after('plv_joined_at');

            $table->string('phone')->nullable()->after('plv_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'birth_date',
                'birth_place_type',
                'birth_province_code',
                'birth_city',
                'birth_country',
                'residence_type',
                'residence_street',
                'residence_house_number',
                'residence_locality',
                'residence_province_code',
                'residence_city',
                'residence_country',
                'plv_joined_at',
                'plv_expires_at',
                'phone',
            ]);
        });
    }
};


