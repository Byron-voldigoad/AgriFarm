import { CommonModule } from '@angular/common';
import { Component, Input } from '@angular/core';
import { Chart, ChartConfiguration, registerables } from 'chart.js';

@Component({
  selector: 'app-crop-yield-chart',
  standalone: true,
  imports: [CommonModule],
  template: '<canvas id="yieldChart"></canvas>',
  styles: [`
    #yieldChart {
      width: 100% !important;
      height: 100% !important;
    }
  `]
})
export class CropYieldChartComponent {
  @Input() chartData: any;
  private chart: any;

  constructor() {
    Chart.register(...registerables);
  }

  ngOnChanges(): void {
    this.renderChart();
  }

  ngAfterViewInit(): void {
    this.renderChart();
  }

  private renderChart(): void {
    if (!this.chartData) return;

    const ctx = document.getElementById('yieldChart') as HTMLCanvasElement;
    if (!ctx) return;

    if (this.chart) {
      this.chart.destroy();
    }

    const config: ChartConfiguration = {
      type: 'line',
      data: {
        labels: this.chartData.labels,
        datasets: [{
          ...this.chartData.datasets[0],
          borderColor: '#10B981',
          backgroundColor: 'rgba(16, 185, 129, 0.1)',
          tension: 0.3,
          fill: true,
          pointBackgroundColor: '#fff',
          pointBorderColor: '#10B981',
          pointBorderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              boxWidth: 12,
              usePointStyle: true,
              pointStyle: 'circle'
            }
          },
          tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: '#1F2937',
            titleFont: { size: 14 },
            bodyFont: { size: 12 },
            padding: 12,
            cornerRadius: 8
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
              callback: function(value) {
                return value + ' t/ha';
              }
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    };

    this.chart = new Chart(ctx, config);
  }

  ngOnDestroy(): void {
    if (this.chart) {
      this.chart.destroy();
    }
  }
}