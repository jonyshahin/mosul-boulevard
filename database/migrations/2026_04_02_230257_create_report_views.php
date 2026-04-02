<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_towers_facade_status');
        DB::statement('DROP VIEW IF EXISTS vw_villas_facade_status');
        DB::statement('DROP VIEW IF EXISTS vw_towers_finishing_status');
        DB::statement('DROP VIEW IF EXISTS vw_villas_finishing_status');
        DB::statement('DROP VIEW IF EXISTS vw_towers_structural_status');
        DB::statement('DROP VIEW IF EXISTS vw_villas_structural_status');
        DB::statement('DROP VIEW IF EXISTS vw_towers_sales_summary');
        DB::statement('DROP VIEW IF EXISTS vw_villas_sales_summary');
    }
};
