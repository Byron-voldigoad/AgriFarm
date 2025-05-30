import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

interface Task {
  id?: number;
  description: string;
  dateDebut: string;
  dateFin: string | null;
  status: string;
  ouvrier_id: number;
}

interface Worker {
  id: number;
  nom: string;
  type?: string;
}

@Component({
  selector: 'app-taches',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './taches.component.html',
  styleUrls: ['./taches.component.css'],
})
export class TachesComponent implements OnInit {
  private baseUrl = 'http://localhost:8000/api';

  tasks: Task[] = [];
  filteredTasks: Task[] = [];
  workers: Worker[] = [];

  // Filtres
  filterStatus: string = '';
  filterWorker: string = '';
  filterStartDate: string = '';
  filterEndDate: string = '';

  // Pagination
  pageSize: number = 6;
  currentPage: number = 1;

  // Modal
  showModal: boolean = false;
  isEditing: boolean = false;
  currentTask: Task = {
    description: '',
    dateDebut: new Date().toISOString().split('T')[0],
    dateFin: null,
    status: 'A faire',
    ouvrier_id: 0,
  };

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    this.loadTasks();
    this.loadWorkers();
  }

  STATUS_MAP: Record<string, string> = {
    'A faire': 'en_attente',
    'En cours': 'en_cours',
    Terminé: 'terminée',
    Terminée: 'terminée',
  };

  STATUS_MAP_REVERSE: Record<string, string> = {
    en_attente: 'A faire',
    en_cours: 'En cours',
    terminée: 'Terminée',
    annulée: 'Annulée',
  };

  loadTasks(): void {
    this.http.get<any[]>(`${this.baseUrl}/taches`).subscribe(
      (data) => {
        this.tasks = data.map((task) => ({
          ...task,
          dateDebut: task.dateDebut ?? task.dateDébut,
          status: this.STATUS_MAP_REVERSE[task.status] || task.status,
        }));
        this.filterTasks();
      },
      (error) => {
        console.error('Erreur lors du chargement des tâches:', error);
      }
    );
  }

  loadWorkers(): void {
    this.http
      .get<Worker[]>(`${this.baseUrl}/utilisateurs?type=OuvrierAgricole`)
      .subscribe(
        (data) => {
          // Filtrer côté front si jamais l'API retourne plus que les ouvriers
          this.workers = data.filter((w) =>
            (w.type || '').toLowerCase().includes('ouvrier')
          );
          if (this.workers.length > 0 && !this.currentTask.ouvrier_id) {
            this.currentTask.ouvrier_id = this.workers[0].id;
          }
        },
        (error) => {
          console.error('Erreur lors du chargement des ouvriers:', error);
        }
      );
  }

  filterTasks(): void {
    let filtered = this.tasks.filter((task) => {
      // Correction du filtre statut (case-insensitive, accepte "Terminé" ou "terminé")
      const matchesStatus =
        !this.filterStatus ||
        (task.status &&
          task.status.toLowerCase() === this.filterStatus.toLowerCase());
      const matchesWorker =
        !this.filterWorker || task.ouvrier_id.toString() === this.filterWorker;
      const matchesStartDate =
        !this.filterStartDate ||
        new Date(task.dateDebut) >= new Date(this.filterStartDate);
      const matchesEndDate =
        !this.filterEndDate ||
        (task.dateFin
          ? new Date(task.dateFin) <= new Date(this.filterEndDate)
          : true);
      return (
        matchesStatus && matchesWorker && matchesStartDate && matchesEndDate
      );
    });
    this.filteredTasks = filtered;
    // Reset page if out of range
    if (this.currentPage > this.totalPages()) {
      this.currentPage = 1;
    }
  }

  getWorkerName(workerId: number): string {
    const worker = this.workers.find((w) => w.id === workerId);
    return worker ? worker.nom : 'Inconnu';
  }

  // Modal functions
  openAddTaskModal(): void {
    this.isEditing = false;
    this.currentTask = {
      description: '',
      dateDebut: new Date().toISOString().split('T')[0],
      dateFin: null,
      status: '',
      ouvrier_id: this.workers.length > 0 ? this.workers[0].id : 0,
    };
    this.showModal = true;
  }

  editTask(task: Task): void {
    this.isEditing = true;
    this.currentTask = { ...task };
    this.showModal = true;
  }

  closeModal(): void {
    this.showModal = false;
  }

  submitTask(): void {
    // Prépare le payload pour l'API
    const payload: any = {
      description: this.currentTask.description,
      dateDebut: this.currentTask.dateDebut,
      dateFin: this.currentTask.dateFin,
      status: this.STATUS_MAP[this.currentTask.status] || 'en_attente',
      ouvrier_id: this.currentTask.ouvrier_id,
    };

    if (this.isEditing && this.currentTask.id) {
      this.http
        .put(`${this.baseUrl}/taches/${this.currentTask.id}`, payload)
        .subscribe(
          () => {
            this.loadTasks();
            this.closeModal();
          },
          (error) => {
            console.error('Erreur lors de la mise à jour de la tâche:', error);
          }
        );
    } else {
      this.http.post(`${this.baseUrl}/taches`, payload).subscribe(
        () => {
          this.loadTasks();
          this.closeModal();
        },
        (error) => {
          console.error('Erreur lors de la création de la tâche:', error);
        }
      );
    }
  }

  deleteTask(id?: number): void {
    if (!id) return;
    if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
      this.http.delete(`${this.baseUrl}/taches/${id}`).subscribe(
        () => {
          this.loadTasks();
        },
        (error) => {
          console.error('Erreur lors de la suppression de la tâche:', error);
        }
      );
    }
  }

  // Pagination
  paginatedTasks(): Task[] {
    const start = (this.currentPage - 1) * this.pageSize;
    return this.filteredTasks.slice(start, start + this.pageSize);
  }

  totalPages(): number {
    return Math.ceil(this.filteredTasks.length / this.pageSize);
  }

  goToPage(page: number): void {
    if (page < 1 || page > this.totalPages()) return;
    this.currentPage = page;
  }

  getStartIndex(): number {
    if (this.filteredTasks.length === 0) return 0;
    return (this.currentPage - 1) * this.pageSize + 1;
  }

  getEndIndex(): number {
    return Math.min(
      this.currentPage * this.pageSize,
      this.filteredTasks.length
    );
  }
}
