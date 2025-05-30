import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { FormsModule } from '@angular/forms';
import { Chart, registerables } from 'chart.js';

@Component({
  selector: 'app-rendement',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './rendement.component.html',
  styleUrls: ['./rendement.component.css'],
})
export class RendementComponent implements OnInit {
  rendements: any[] = [];
  filteredRendements: any[] = [];
  cultures: any[] = [];
  selectedCultureId: string = '';
  chart: any;
  viewMode: 'table' | 'charts' = 'charts';

  // Pagination
  currentPage = 1;
  itemsPerPage = 10;

  constructor(private http: HttpClient) {
    Chart.register(...registerables);
  }

  ngOnInit(): void {
    this.loadRendements();
    this.loadCultures();
  }

  loadRendements() {
    this.http.get<any[]>('http://localhost:8000/api/rendements').subscribe({
      next: (data) => {
        this.rendements = data.map((r) => ({
          ...r,
          quantite: r.quantité ?? r.quantite,
          couts: r.couts ?? r.couts,
        }));
        this.filterRendements();
      },
    });
  }

  loadCultures() {
    this.http.get<any[]>('http://localhost:8000/api/cultures').subscribe({
      next: (data) => (this.cultures = data),
    });
  }

  filterRendements() {
    this.currentPage = 1; // Reset à la première page lors du filtrage
    this.filteredRendements = this.selectedCultureId
      ? this.rendements.filter((r) => r.culture?.id == this.selectedCultureId)
      : [...this.rendements];

    this.updateCharts();
  }

  // Pagination
  get paginatedRendements(): any[] {
    const startIndex = (this.currentPage - 1) * this.itemsPerPage;
    return this.filteredRendements.slice(
      startIndex,
      startIndex + this.itemsPerPage
    );
  }

  get totalPages(): number {
    return Math.ceil(this.filteredRendements.length / this.itemsPerPage);
  }

  goToPage(page: number): void {
    this.currentPage = page;
  }

  getEndIndex() {
    return Math.min(
      this.currentPage * this.itemsPerPage,
      this.filteredRendements.length
    );
  }

  // Graphiques
  updateCharts() {
    if (this.chart) {
      this.chart.destroy();
    }

    if (this.filteredRendements.length === 0) return;

    const labels = this.filteredRendements.map(
      (r) =>
        `${r.culture?.nom || 'Inconnue'} - ${new Date(
          r.date
        ).toLocaleDateString()}`
    );
    const quantites = this.filteredRendements.map((r) => r.quantite);
    const couts = this.filteredRendements.map((r) => r.couts);

    const ctx = document.getElementById('rendementChart') as HTMLCanvasElement;
    this.chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Quantité (tonnes)',
            data: quantites,
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
            yAxisID: 'y',
          },
          {
            label: 'Coûts (FCFA)',
            data: couts,
            backgroundColor: 'rgba(255, 99, 132, 0.6)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1,
            yAxisID: 'y1',
            type: 'line',
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          mode: 'index',
          intersect: false,
        },
        scales: {
          y: {
            type: 'linear',
            display: true,
            position: 'left',
            title: {
              display: true,
              text: 'Quantité (tonnes)',
            },
          },
          y1: {
            type: 'linear',
            display: true,
            position: 'right',
            title: {
              display: true,
              text: 'Coûts (FCFA)',
            },
            grid: {
              drawOnChartArea: false,
            },
          },
        },
      },
    });
  }

  // Calculs
  getTotalQuantite() {
    return this.filteredRendements.reduce(
      (sum, r) => sum + (r.quantite || 0),
      0
    );
  }

  calculateRendement(rendement: any): number {
    return rendement.couts > 0
      ? (rendement.quantite / rendement.couts) * 1000
      : 0;
  }

  // UI
  toggleViewMode() {
    this.viewMode = this.viewMode === 'table' ? 'charts' : 'table';
    if (this.viewMode === 'charts') {
      setTimeout(() => this.updateCharts(), 100);
    }
  }

  openAddRendementDialog() {
    // À implémenter
    alert("Formulaire d'ajout à implémenter");
  }
}
