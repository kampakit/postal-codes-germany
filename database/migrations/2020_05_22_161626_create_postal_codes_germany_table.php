<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostalCodesGermanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postal_codes_germany', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('postal_code')->comment('5-digit German postal code (Postleitzahl)')->index();
            $table->float('longitude')->comment('Center longitude (geographische Länge');
            $table->float('latitude')->comment('Center latitude (geographische Breite)');
            $table->string('city')->comment('City or place (Ort)');
            $table->string('postal_code_description')->comment('Summary of places covered by postal code');
            $table->string('displayed_city')->comment('Place name to display in search results');
            $table->string('landkreis')->comment('Administrative District (Landkreis)');
            $table->string('bundesland')->comment('Federal state (Bundesland)');
            $table->integer('inhabitants')->comment('Number of inhabitants (Einwohnerzahl)');
            $table->float('area_km2')->comment('Area in square kilometres (Fläche in Quadratkilometern)');
            $table->integer('osm_id')->comment('OpenStreetMap ID');
            $table->integer('gemeindeschluessel')->comment('Official Municipality Key (Amtlicher Gemeindeschlüssel');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm;');
            DB::statement('CREATE INDEX IF NOT EXISTS trgm_idx ON postal_codes_germany USING GIN (displayed_city gin_trgm_ops);');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('postal_codes_germany');
    }
}
