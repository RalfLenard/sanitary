<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('sanitary', function (Blueprint $table) {
            $table->id();
            $table->string('name_of_establishment');
            $table->string('name_of_owner');
            $table->string('contact_number');
            $table->string('barangay')->nullable();
            $table->string('line_of_business')->nullable();
            $table->string('inspector_name')->nullable();
            $table->year('renewal_year');
            $table->string('permit_code')->nullable();
            $table->timestamp('renewed_at')->useCurrent();
            $table->enum('status', ['New', 'Renewed', 'Close', 'Inspected', 'Completed'])->default('New');
            $table->boolean('confirmed')->default(false);
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanitary');
    }
};
