<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite recreates the table on alter; views referencing tower_units must
        // be dropped first and recreated after, or the rename step fails.
        $towerViews = [
            'vw_towers_sales_summary',
            'vw_towers_structural_status',
            'vw_towers_finishing_status',
            'vw_towers_facade_status',
        ];

        foreach ($towerViews as $view) {
            DB::statement("DROP VIEW IF EXISTS {$view}");
        }

        Schema::table('tower_units', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('is_sold')
                ->constrained('customers')
                ->nullOnDelete();

            $table->index('customer_id');
        });

        DB::statement('
            CREATE VIEW vw_towers_sales_summary AS
            SELECT td.id as tower_definition_id, td.name as tower_name,
                SUM(CASE WHEN tu.is_sold = 1 THEN 1 ELSE 0 END) as total_sold,
                SUM(CASE WHEN tu.is_sold = 0 THEN 1 ELSE 0 END) as total_unsold,
                COUNT(*) as total
            FROM tower_units tu
            JOIN tower_definitions td ON tu.tower_definition_id = td.id
            WHERE tu.deleted_at IS NULL
            GROUP BY td.id, td.name
        ');

        DB::statement("
            CREATE VIEW vw_towers_structural_status AS
            SELECT so.name as status_name,
                SUM(CASE WHEN td.code_prefix = 'T1' THEN 1 ELSE 0 END) as tower_1,
                SUM(CASE WHEN td.code_prefix = 'T2' THEN 1 ELSE 0 END) as tower_2,
                SUM(CASE WHEN td.code_prefix = 'T3' THEN 1 ELSE 0 END) as tower_3,
                SUM(CASE WHEN td.code_prefix = 'T4' THEN 1 ELSE 0 END) as tower_4,
                SUM(CASE WHEN td.code_prefix = 'T5' THEN 1 ELSE 0 END) as tower_5,
                SUM(CASE WHEN td.code_prefix = 'T6' THEN 1 ELSE 0 END) as tower_6,
                COUNT(*) as total
            FROM tower_units tu
            JOIN status_options so ON tu.structural_status_id = so.id
            JOIN tower_definitions td ON tu.tower_definition_id = td.id
            WHERE tu.deleted_at IS NULL
            GROUP BY so.name
        ");

        DB::statement("
            CREATE VIEW vw_towers_finishing_status AS
            SELECT so.name as status_name,
                SUM(CASE WHEN td.code_prefix = 'T1' THEN 1 ELSE 0 END) as tower_1,
                SUM(CASE WHEN td.code_prefix = 'T2' THEN 1 ELSE 0 END) as tower_2,
                SUM(CASE WHEN td.code_prefix = 'T3' THEN 1 ELSE 0 END) as tower_3,
                SUM(CASE WHEN td.code_prefix = 'T4' THEN 1 ELSE 0 END) as tower_4,
                SUM(CASE WHEN td.code_prefix = 'T5' THEN 1 ELSE 0 END) as tower_5,
                SUM(CASE WHEN td.code_prefix = 'T6' THEN 1 ELSE 0 END) as tower_6,
                COUNT(*) as total
            FROM tower_units tu
            JOIN status_options so ON tu.finishing_status_id = so.id
            JOIN tower_definitions td ON tu.tower_definition_id = td.id
            WHERE tu.deleted_at IS NULL
            GROUP BY so.name
        ");

        DB::statement("
            CREATE VIEW vw_towers_facade_status AS
            SELECT so.name as status_name,
                SUM(CASE WHEN td.code_prefix = 'T1' THEN 1 ELSE 0 END) as tower_1,
                SUM(CASE WHEN td.code_prefix = 'T2' THEN 1 ELSE 0 END) as tower_2,
                SUM(CASE WHEN td.code_prefix = 'T3' THEN 1 ELSE 0 END) as tower_3,
                SUM(CASE WHEN td.code_prefix = 'T4' THEN 1 ELSE 0 END) as tower_4,
                SUM(CASE WHEN td.code_prefix = 'T5' THEN 1 ELSE 0 END) as tower_5,
                SUM(CASE WHEN td.code_prefix = 'T6' THEN 1 ELSE 0 END) as tower_6,
                COUNT(*) as total
            FROM tower_units tu
            JOIN status_options so ON tu.facade_status_id = so.id
            JOIN tower_definitions td ON tu.tower_definition_id = td.id
            WHERE tu.deleted_at IS NULL
            GROUP BY so.name
        ");
    }

    public function down(): void
    {
        $towerViews = [
            'vw_towers_sales_summary',
            'vw_towers_structural_status',
            'vw_towers_finishing_status',
            'vw_towers_facade_status',
        ];

        foreach ($towerViews as $view) {
            DB::statement("DROP VIEW IF EXISTS {$view}");
        }

        Schema::table('tower_units', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropIndex(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
