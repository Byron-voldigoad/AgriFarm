import { CommonModule } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { CropYieldChartComponent } from './components/crop-yield-chart.component';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';

interface Card {
  title: string;
  value: string | number;
  trend: string;
  icon: string;
}

interface Task {
  title: string;
  description: string;
  date: string;
  color: string;
  icon: string;
}

interface ChartData {
  labels: string[];
  datasets: {
    label: string;
    data: number[];
  }[];
}

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, CropYieldChartComponent,FormsModule],
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.css']
})
export class DashboardComponent implements OnInit {
  selectedFilter: string = 'this_year';
  cards: Card[] = [];
  tasks: Task[] = [];
  chartData: ChartData = { labels: [], datasets: [] };
  isLoading = true;

  constructor(private http: HttpClient, private router: Router) {}

  ngOnInit(): void {
    this.fetchDashboardData();
  }

  fetchDashboardData(): void {
    this.http.get<any>('http://localhost:8000/api/dashboard').subscribe({
      next: (data) => {
        this.cards = data.cards;
        this.tasks = data.tasks;
        this.chartData = data.chartData;
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Error fetching dashboard data:', error);
        this.isLoading = false;
      }
    });
  }

  getIconClass(icon: string): string {
    const iconMap: { [key: string]: string } = {
      'sprout': 'fas fa-seedling',
      'check-square': 'fas fa-tasks',
      'trending-up': 'fas fa-chart-line',
      'dollar-sign': 'fas fa-money-bill-wave',
      'calendar': 'far fa-calendar-alt'
    };
    return iconMap[icon] || 'fas fa-info-circle';
  }

  getTrendClass(trend: string): string {
    if (trend.includes('+')) return 'text-green-500';
    if (trend.includes('-')) return 'text-red-500';
    return 'text-gray-500';
  }
  onFilterChange() {
    this.http.get<any>(`http://localhost:8000/api/dashboard?filter=${this.selectedFilter}`)
        .subscribe(data => {
            this.chartData = data.chartData;
            this.cards = data.cards;
            this.tasks = data.tasks;
        });
}

navigateToTasks() {
  this.router.navigate(['/taches']);
}
}