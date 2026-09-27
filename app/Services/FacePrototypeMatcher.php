<?php

namespace App\Services;

class FacePrototypeMatcher
{
    /**
     * @param  list<float>  $descriptor
     * @param  list<array{alias: string, descriptor: list<float>}>  $references
     * @return array{status: string, candidates: list<array{alias: string, distance: float}>, gap: ?float}
     */
    public function match(array $descriptor, array $references, float $threshold, float $minimumGap): array
    {
        $distances = [];
        foreach ($references as $reference) {
            $sum = 0.0;
            foreach ($descriptor as $index => $value) {
                $sum += ($value - $reference['descriptor'][$index]) ** 2;
            }
            $distance = sqrt($sum);
            $alias = $reference['alias'];
            $distances[$alias] = min($distances[$alias] ?? INF, $distance);
        }
        asort($distances, SORT_NUMERIC);
        $candidates = [];
        foreach (array_slice($distances, 0, 2, true) as $alias => $distance) {
            $candidates[] = ['alias' => $alias, 'distance' => $distance];
        }
        $gap = count($candidates) > 1 ? $candidates[1]['distance'] - $candidates[0]['distance'] : null;
        $status = 'unknown';
        if ($candidates !== [] && $candidates[0]['distance'] <= $threshold) {
            $status = $gap !== null && $gap < $minimumGap ? 'ambiguous' : 'candidate';
        }

        return ['status' => $status, 'candidates' => $candidates, 'gap' => $gap];
    }
}
