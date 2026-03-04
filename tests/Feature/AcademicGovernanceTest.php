<?php

namespace Tests\Feature;

use App\Support\AcademicRuleSnapshotService;
use App\Support\AcademicStatusMutationService;
use App\Support\FinancialImpactService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AcademicGovernanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_academic_rule_snapshot_is_immutable_for_existing_semester(): void
    {
        $this->skipIfSqlite();
        $this->seed();

        $tahunId = 1;
        $snapshot = AcademicRuleSnapshotService::getOrCreate($tahunId, 1);
        $rulesBefore = json_decode((string) $snapshot->rules_json, true);

        DB::table('academic_settings')->update([
            'grade_a_min' => 95,
            'updated_at' => now(),
        ]);

        $rulesAfter = AcademicRuleSnapshotService::rulesForYear($tahunId);

        $this->assertSame(
            (float) ($rulesBefore['grade_ranges'][0]['min'] ?? 0),
            (float) ($rulesAfter['grade_ranges'][0]['min'] ?? 0)
        );
    }

    public function test_financial_disputed_after_final_krs_forces_suspended_pending_decision(): void
    {
        $this->skipIfSqlite();
        $this->seed();

        $tagihanId = 1;
        DB::table('tagihan_ukt')->where('id', $tagihanId)->update([
            'status' => 'disputed',
            'updated_at' => now(),
        ]);

        FinancialImpactService::applyTagihanStatusImpact($tagihanId, 'disputed', 1, 'test case');

        $latestPeriodStatus = DB::table('mahasiswa_period_status')
            ->where('mahasiswa_id', 1)
            ->where('tahun_akademik_id', 1)
            ->orderByDesc('id')
            ->value('eligibility_status');

        $this->assertSame('suspended_pending_decision', $latestPeriodStatus);

        $decision = DB::table('academic_decisions')
            ->where('mahasiswa_id', 1)
            ->where('tahun_akademik_id', 1)
            ->where('context', 'ukt_disputed_after_krs_final')
            ->first();

        $this->assertNotNull($decision);
    }

    public function test_status_mutation_service_updates_legacy_status_and_logs(): void
    {
        $this->skipIfSqlite();
        $this->seed();

        $changed = AcademicStatusMutationService::mutatePeriodEligibility(
            mahasiswaId: 1,
            tahunAkademikId: 1,
            newStatus: 'suspended',
            source: 'manual',
            note: 'uji mutasi',
            actorId: 1
        );

        $this->assertTrue($changed);
        $this->assertSame('suspended', DB::table('mahasiswa')->where('id', 1)->value('status_akademik'));

        $log = DB::table('mahasiswa_status_logs')
            ->where('mahasiswa_id', 1)
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame('manual', $log->sumber);
    }

    private function skipIfSqlite(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('Governance edge-case test ini dijalankan pada MySQL karena enum status UKT v2.');
        }
    }
}
