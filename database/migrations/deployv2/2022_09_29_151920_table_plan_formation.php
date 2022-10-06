<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PlanFormation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agent_formations', function (Blueprint $table) {
            $table->dropColumn('plan_formation');
            $table->boolean('avis_commission')->default(true);
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agent_formations', function (Blueprint $table) {
            $table->string('plan_formation')->nullable();
            $table->dropColumn('avis_commission');
        });
        
    }
}

