<?php

namespace App\Http\Controllers;

use App\Models\Culture;
use App\Models\Parcelle;
use App\Models\Tache;
use App\Models\Rendement;
use App\Models\Finances;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = now()->year;
        $filter = $request->input('filter', 'this_year'); // Définition de $filter avec valeur par défaut
        
        // 1. Calcul des cultures actives
        $culturesActives = Culture::count();
        $culturesLastYear = Culture::whereYear('created_at', '<', $currentYear)->count();
        
        // 2. Calcul des tâches
        $tachesEnCours = Tache::where('status', 'en_attente')->count();
        $tachesRecent = Tache::where('status', 'en_attente')
                       ->where('dateDebut', '>=', now()->subMonth())
                       ->count();
        
        // 3. Calcul du rendement
        $rendementData = Rendement::selectRaw('SUM(quantité) as total, YEAR(date) as year')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();

        $rendementTotal = $rendementData->sum('total');
        $rendementLastYear = $rendementData->firstWhere('year', $currentYear - 1)->total ?? 0;
        
        // 4. Calcul des revenus nets
        $revenusNets = Finances::sum('revenu') - Finances::sum('dépenseTotale');
        $revenusLastYear = Finances::whereYear('created_at', $currentYear - 1)
                         ->sum('revenu') - 
                         Finances::whereYear('created_at', $currentYear - 1)
                         ->sum('dépenseTotale');
        
        $cards = [
            [
                'title' => 'Cultures Actives',
                'value' => $culturesActives,
                'trend' => $this->getComparisonTrend($culturesLastYear, $culturesActives),
                'icon' => 'sprout'
            ],
            [
                'title' => 'Tâches en Attente',
                'value' => $tachesEnCours,
                'trend' => $this->getTaskTrend(),
                'icon' => 'check-square'
            ],
            [
                'title' => 'Rendement Total',
                'value' => number_format($rendementTotal, 2) . ' t',
                'trend' => $this->getComparisonTrend($rendementLastYear, $rendementTotal),
                'icon' => 'trending-up'
            ],
            [
                'title' => 'Revenus Nets',
                'value' => number_format($revenusNets) . ' FCFA',
                'trend' => $this->getComparisonTrend($revenusLastYear, $revenusNets),
                'icon' => 'dollar-sign'
            ]
        ];
    
        // Tâches récentes
        $tasks = Tache::where('status', 'en_attente')
            ->orderBy('dateDebut', 'desc')
            ->take(5)
            ->get()
            ->map(function ($task) {
                $dateDebut = is_string($task->dateDebut) 
                    ? Carbon::parse($task->dateDebut)
                    : $task->dateDebut;

                return [
                    'title' => $task->description,
                    'description' => 'Échéance: '.$dateDebut->format('d/m/Y'),
                    'date' => $dateDebut->format('Y-m-d'),
                    'color' => 'blue',
                    'icon' => 'check-square'
                ];
            })->toArray();
    
        // Graphique avec filtre temporel
        $chartData = $this->getChartData($filter);
    
        return response()->json([
            'cards' => $cards,
            'tasks' => $tasks,
            'chartData' => $chartData,
            'currentFilter' => $filter
        ]);
    }

    private function getChartData($filter)
    {
        $now = now();
        $startDate = $now->copy()->startOfYear();
        $endDate = $now;

        switch ($filter) {
            case 'last_3_months':
                $startDate = $now->copy()->subMonths(2)->startOfMonth();
                break;
            case 'last_6_months':
                $startDate = $now->copy()->subMonths(5)->startOfMonth();
                break;
        }

        // Récupération des données
        $monthlyData = Rendement::selectRaw('
                MONTH(date) as month, 
                SUM(quantité) as total
            ')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Construction des données pour Chart.js
        $labels = [];
        $data = [];
        
        $monthsToShow = $filter === 'last_3_months' ? 3 : ($filter === 'last_6_months' ? 6 : 12);
        
        for ($i = $monthsToShow - 1; $i >= 0; $i--) {
            $currentMonth = $now->copy()->subMonths($i);
            $labels[] = $currentMonth->format('M');
            $monthNumber = (int)$currentMonth->format('n');
            $data[] = $monthlyData[$monthNumber] ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Production (t)',
                    'data' => $data,
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#047857'
                ]
            ]
        ];
    }

    private function getComparisonTrend($oldValue, $newValue)
    {
        if ($oldValue == 0) return 'Nouvelle métrique';
        $change = (($newValue - $oldValue)/$oldValue)*100;
        return sprintf('%s%.1f%% depuis l\'an dernier', $change >= 0 ? '+' : '', $change);
    }

    private function getTaskTrend()
    {
        $currentMonthCount = Tache::whereMonth('created_at', now()->month)->count();
        $lastMonthCount = Tache::whereMonth('created_at', now()->subMonth()->month)->count();
        
        if ($lastMonthCount == 0) return 'Nouvelle métrique';
        
        $change = (($currentMonthCount - $lastMonthCount)/$lastMonthCount)*100;
        return sprintf('%s%.1f%% vs mois dernier', $change >= 0 ? '+' : '', $change);
    }
}