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
        Schema::create('trading_infos', function (Blueprint $table) {
            $table->id();
            $table->string('kode_emiten', 10)->index();
            $table->date('date')->comment('Trading date');

            // Price information
            $table->decimal('previous', 15, 2)->nullable()->comment('Previous close price');
            $table->decimal('open_price', 15, 2)->nullable()->comment('Opening price');
            $table->decimal('first_trade', 15, 2)->nullable()->comment('First trade price');
            $table->decimal('high', 15, 2)->nullable()->comment('Highest price');
            $table->decimal('low', 15, 2)->nullable()->comment('Lowest price');
            $table->decimal('close', 15, 2)->nullable()->comment('Closing price');
            $table->decimal('change', 15, 2)->nullable()->comment('Price change');

            // Regular market trading data
            $table->decimal('volume', 20, 2)->nullable()->comment('Trading volume');
            $table->decimal('value', 20, 2)->nullable()->comment('Trading value');
            $table->decimal('frequency', 15, 2)->nullable()->comment('Trading frequency');

            // Index
            $table->decimal('index_individual', 15, 4)->nullable()->comment('Individual stock index');

            // Bid/Offer data
            $table->decimal('offer', 15, 2)->nullable()->comment('Best offer/ask price');
            $table->decimal('offer_volume', 20, 2)->nullable()->comment('Offer volume');
            $table->decimal('bid', 15, 2)->nullable()->comment('Best bid price');
            $table->decimal('bid_volume', 20, 2)->nullable()->comment('Bid volume');

            // Shares information
            $table->decimal('listed_shares', 20, 2)->nullable()->comment('Total listed shares');
            $table->decimal('tradeble_shares', 20, 2)->nullable()->comment('Tradeable shares');
            $table->decimal('weight_for_index', 20, 2)->nullable()->comment('Weight for index calculation');

            // Foreign trading
            $table->decimal('foreign_sell', 20, 2)->nullable()->comment('Foreign sell volume');
            $table->decimal('foreign_buy', 20, 2)->nullable()->comment('Foreign buy volume');

            // Delisting
            $table->date('delisting_date')->nullable()->comment('Delisting date if applicable');

            // Non-regular market data
            $table->decimal('non_regular_volume', 20, 2)->nullable()->comment('Non-regular market volume');
            $table->decimal('non_regular_value', 20, 2)->nullable()->comment('Non-regular market value');
            $table->decimal('non_regular_frequency', 15, 2)->nullable()->comment('Non-regular market frequency');

            $table->timestamps();

            // Indexes
            $table->unique(['kode_emiten', 'date'], 'unique_kode_date');
            $table->index('date');

            // Note: No foreign key constraint to allow importing trading data
            // for stocks not yet in stock_companies table
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trading_infos');
    }
};

