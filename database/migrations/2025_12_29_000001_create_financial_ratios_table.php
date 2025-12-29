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
        Schema::create('financial_ratios', function (Blueprint $table) {
            $table->id();

            // Stock identification
            $table->string('code', 10)->comment('Stock code, e.g., AADI');
            $table->string('stock_name')->comment('Company name');
            $table->string('sharia', 5)->nullable()->comment('Sharia compliance flag: S = Sharia');

            // Sector & Industry classification
            $table->string('sector')->nullable();
            $table->string('sub_sector')->nullable();
            $table->string('industry')->nullable();
            $table->string('sub_industry')->nullable();
            $table->string('sector_code', 10)->nullable();
            $table->string('sub_sector_code', 10)->nullable();
            $table->string('industry_code', 10)->nullable();
            $table->string('sub_industry_code', 10)->nullable();
            $table->string('sub_name')->nullable();
            $table->string('sub_code', 10)->nullable();

            // Financial statement period
            $table->date('fs_date')->comment('Financial statement date');
            $table->string('fiscal_year_end', 10)->nullable()->comment('Fiscal year end month, e.g., Dec');

            // Financial figures (in billions IDR)
            $table->decimal('assets', 20, 2)->nullable()->comment('Total assets in billions');
            $table->decimal('liabilities', 20, 2)->nullable()->comment('Total liabilities in billions');
            $table->decimal('equity', 20, 2)->nullable()->comment('Total equity in billions');
            $table->decimal('sales', 20, 2)->nullable()->comment('Total sales/revenue in billions');
            $table->decimal('ebt', 20, 2)->nullable()->comment('Earnings before tax in billions');
            $table->decimal('profit_period', 20, 2)->nullable()->comment('Profit for the period in billions');
            $table->decimal('profit_attr_owner', 20, 2)->nullable()->comment('Profit attributable to owners in billions');

            // Per share data
            $table->decimal('eps', 15, 2)->nullable()->comment('Earnings per share');
            $table->decimal('book_value', 15, 2)->nullable()->comment('Book value per share');

            // Financial ratios (using decimal 15,4 to handle extreme values)
            $table->decimal('per', 15, 4)->nullable()->comment('Price to Earnings Ratio');
            $table->decimal('price_bv', 15, 4)->nullable()->comment('Price to Book Value');
            $table->decimal('de_ratio', 15, 4)->nullable()->comment('Debt to Equity Ratio');
            $table->decimal('roa', 15, 4)->nullable()->comment('Return on Assets (%)');
            $table->decimal('roe', 15, 4)->nullable()->comment('Return on Equity (%)');
            $table->decimal('npm', 15, 4)->nullable()->comment('Net Profit Margin (%)');

            // Audit information
            $table->string('audit', 5)->nullable()->comment('Audit status: A = Audited, U = Unaudited');
            $table->string('opini', 10)->nullable()->comment('Audit opinion: WTM = Wajar Tanpa Modifikasi');

            $table->timestamps();

            // Indexes
            $table->index('code');
            $table->index('sector');
            $table->index('sub_sector');
            $table->index('industry');
            $table->index('fs_date');
            $table->index('sharia');
            $table->unique(['code', 'fs_date'], 'unique_code_fs_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_ratios');
    }
};

