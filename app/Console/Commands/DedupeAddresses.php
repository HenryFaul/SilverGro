<?php

namespace App\Console\Commands;

use App\Models\Address;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Hides the surplus copies of addresses that were duplicated during the data
 * migration, so the address book is usable without anyone having to click Hide
 * roughly eighteen hundred times.
 *
 * Only exact repeats are touched: same party, same street line, same postal
 * code. Anything that differs in any of those is left alone for a human to
 * judge - a handful of the surplus addresses look like real addresses that
 * simply belong to a different party, and no rule here can tell those apart.
 *
 * Nothing is deleted. The keeper is whichever copy the trades already point at,
 * so with --repoint the hidden rows end up referenced by nothing at all.
 */
class DedupeAddresses extends Command
{
    protected $signature = 'addresses:dedupe
        {--dry-run : Report what would change without writing anything}
        {--repoint : Also move trade references from the hidden copies onto the keeper}
        {--party= : Restrict to one party, e.g. "App\\Models\\Supplier:101"}';

    protected $description = 'Hide duplicate addresses left behind by the migration';

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $repoint = (bool) $this->option('repoint');

        if ($dry) {
            $this->warn('DRY RUN - nothing will be written.');
        }

        $groups = $this->duplicateGroups();

        if ($groups->isEmpty()) {
            $this->info('No duplicate address groups found.');
            return self::SUCCESS;
        }

        $hidden = 0;
        $repointed = 0;
        $rows = [];

        foreach ($groups as $g) {
            $copies = Address::query()
                ->where('poly_address_type', $g->poly_address_type)
                ->where('poly_address_id', $g->poly_address_id)
                ->whereRaw('COALESCE(TRIM(line_1), "") = ?', [$g->line_1])
                ->whereRaw('COALESCE(code, "") = ?', [$g->code])
                ->whereNull('hidden_at')
                ->get();

            if ($copies->count() < 2) {
                continue;
            }

            $usage = $this->usageCounts($copies->pluck('id')->all());

            // Keep whichever copy the trades actually lean on. Falling back to the
            // primary flag and then the oldest id keeps the choice deterministic,
            // so a dry run and the real run agree.
            $keeper = $copies->sortByDesc(fn ($a) => [
                $usage[$a->id] ?? 0,
                (int) $a->is_primary,
                -$a->id,
            ])->first();

            $losers = $copies->reject(fn ($a) => $a->id === $keeper->id);

            $movedHere = 0;

            if ($repoint) {
                $loserIds = $losers->pluck('id')->all();

                if (!$dry) {
                    $movedHere += DB::table('transport_loads')
                        ->whereIn('delivery_address_id', $loserIds)
                        ->update(['delivery_address_id' => $keeper->id]);
                    $movedHere += DB::table('transport_loads')
                        ->whereIn('collection_address_id', $loserIds)
                        ->update(['collection_address_id' => $keeper->id]);
                } else {
                    $movedHere += DB::table('transport_loads')->whereIn('delivery_address_id', $loserIds)->count();
                    $movedHere += DB::table('transport_loads')->whereIn('collection_address_id', $loserIds)->count();
                }

                $repointed += $movedHere;
            }

            if (!$dry) {
                Address::whereIn('id', $losers->pluck('id'))->update([
                    'hidden_at' => now(),
                    'hidden_by_id' => null, // system, not a person
                ]);
            }

            $hidden += $losers->count();

            $rows[] = [
                $g->poly_address_type . '#' . $g->poly_address_id,
                mb_strimwidth($g->line_1 === '' ? '(blank)' : $g->line_1, 0, 34, '...'),
                $copies->count(),
                $keeper->id,
                $losers->count(),
                $movedHere ?: '-',
            ];
        }

        usort($rows, fn ($a, $b) => $b[4] <=> $a[4]);

        $this->table(
            ['party', 'address', 'copies', 'keep id', 'hide', 'refs moved'],
            array_slice($rows, 0, 25)
        );

        if (count($rows) > 25) {
            $this->line('  ... and ' . (count($rows) - 25) . ' more groups');
        }

        $this->newLine();
        $this->info(sprintf(
            '%s %d duplicate addresses across %d groups.%s',
            $dry ? 'Would hide' : 'Hid',
            $hidden,
            count($rows),
            $repoint ? sprintf(' %s %d trade references.', $dry ? 'Would move' : 'Moved', $repointed) : ''
        ));

        if (!$repoint) {
            $this->comment('Re-run with --repoint to move trade references onto the keeper.');
        }

        return self::SUCCESS;
    }

    private function duplicateGroups()
    {
        $query = DB::table('addresses')
            ->selectRaw('poly_address_type, poly_address_id, COALESCE(TRIM(line_1), "") as line_1, COALESCE(code, "") as code, COUNT(*) as copies')
            ->whereNull('deleted_at')
            ->whereNull('hidden_at')
            ->whereNotNull('poly_address_id')
            ->groupBy('poly_address_type', 'poly_address_id', 'line_1', 'code')
            ->havingRaw('COUNT(*) > 1');

        if ($party = $this->option('party')) {
            [$type, $id] = explode(':', $party, 2);
            $query->where('poly_address_type', $type)->where('poly_address_id', $id);
        }

        return collect($query->get());
    }

    /**
     * How many trades point at each of these addresses.
     */
    private function usageCounts(array $ids): array
    {
        $counts = [];

        foreach (['delivery_address_id', 'collection_address_id'] as $column) {
            $rows = DB::table('transport_loads')
                ->select($column . ' as address_id', DB::raw('COUNT(*) as n'))
                ->whereIn($column, $ids)
                ->groupBy($column)
                ->get();

            foreach ($rows as $row) {
                $counts[$row->address_id] = ($counts[$row->address_id] ?? 0) + $row->n;
            }
        }

        return $counts;
    }
}
