<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite recreates the table on alter; views referencing villas must be
        // dropped first and recreated after, or the rename step fails.
        $villaViews = [
            'vw_villas_sales_summary',
            'vw_villas_structural_status',
            'vw_villas_finishing_status',
            'vw_villas_facade_status',
        ];

        foreach ($villaViews as $view) {
            DB::statement("DROP VIEW IF EXISTS {$view}");
        }

        Schema::table('villas', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('is_sold')
                ->constrained('customers')
                ->nullOnDelete();

            $table->index('customer_id');
        });

        DB::statement('
            CREATE VIEW vw_villas_sales_summary AS
            SELECT vt.id as villa_type_id, vt.name as villa_type_name,
                SUM(CASE WHEN v.is_sold = 1 THEN 1 ELSE 0 END) as total_sold,
                SUM(CASE WHEN v.is_sold = 0 THEN 1 ELSE 0 END) as total_unsold,
                COUNT(*) as total
            FROM villas v
            JOIN villa_types vt ON v.villa_type_id = vt.id
            WHERE v.deleted_at IS NULL
            GROUP BY vt.id, vt.name
        ');

        DB::statement("
            CREATE VIEW vw_villas_structural_status AS
            SELECT so.name as status_name,
                SUM(CASE WHEN vt.code_prefix = 'A' THEN 1 ELSE 0 END) as type_a_count,
                SUM(CASE WHEN vt.code_prefix = 'B' THEN 1 ELSE 0 END) as type_b_count,
                COUNT(*) as total
            FROM villas v
            JOIN status_options so ON v.structural_status_id = so.id
            JOIN villa_types vt ON v.villa_type_id = vt.id
            WHERE v.deleted_at IS NULL
            GROUP BY so.name
        ");

        DB::statement("
            CREATE VIEW vw_villas_finishing_status AS
            SELECT so.name as status_name,
                SUM(CASE WHEN vt.code_prefix = 'A' THEN 1 ELSE 0 END) as type_a_count,
                SUM(CASE WHEN vt.code_prefix = 'B' THEN 1 ELSE 0 END) as type_b_count,
                COUNT(*) as total
            FROM villas v
            JOIN status_options so ON v.finishing_status_id = so.id
            JOIN villa_types vt ON v.villa_type_id = vt.id
            WHERE v.deleted_at IS NULL
            GROUP BY so.name
        ");

        DB::statement("
            CREATE VIEW vw_villas_facade_status AS
            SELECT so.name as status_name,
                SUM(CASE WHEN vt.code_prefix = 'A' THEN 1 ELSE 0 END) as type_a_count,
                SUM(CASE WHEN vt.code_prefix = 'B' THEN 1 ELSE 0 END) as type_b_count,
                COUNT(*) as total
            FROM villas v
            JOIN status_options so ON v.facade_status_id = so.id
            JOIN villa_types vt ON v.villa_type_id = vt.id
            WHERE v.deleted_at IS NULL
            GROUP BY so.name
        ");
    }

    public function down(): void
    {
        $villaViews = [
            'vw_villas_sales_summary',
            'vw_villas_structural_status',
            'vw_villas_finishing_status',
            'vw_villas_facade_status',
        ];

        foreach ($villaViews as $view) {
            DB::statement("DROP VIEW IF EXISTS {$view}");
        }

        Schema::table('villas', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropIndex(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
