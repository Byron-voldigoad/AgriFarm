import { Component, inject, signal, effect } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  FormsModule,
  ReactiveFormsModule,
  FormBuilder,
  Validators,
} from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';
import { Culture } from '../../core/models/culture.model';

@Component({
  selector: 'app-cultures',
  standalone: true,
  imports: [CommonModule, FormsModule, ReactiveFormsModule, RouterModule],
  templateUrl: './cultures.component.html',
  styleUrls: ['./cultures.component.css'],
})
export class CulturesComponent {
  private api = inject(ApiService);
  private fb = inject(FormBuilder);

  // États avec les signaux
  cultures = signal<Culture[]>([]);
  filteredCultures = signal<Culture[]>([]);
  loading = signal(true);
  searchTerm = signal('');

  // Dialogues
  showFormDialog = signal(false);
  showDeleteDialog = signal(false);
  isEditing = signal(false);
  submitting = signal(false);
  currentCulture = signal<Culture | null>(null);
  expandedDescriptions: { [key: number]: boolean } = {};

  // Pagination
  pageSize = 6;
  currentPage = 1;

  toggleDescription(id: number) {
    this.expandedDescriptions[id] = !this.expandedDescriptions[id];
  }

  // Formulaire simplifié
  cultureForm = this.fb.group({
    nom: ['', [Validators.required, Validators.minLength(2)]],
    description: [''],
    photo: [''],
    type_culture: ['', Validators.required],
    conditions_climatiques: [''],
    cout_estime: [
      null as number | null,
      Validators.pattern(/^[0-9]+(\.[0-9]{1,2})?$/),
    ],
  });

  constructor() {
    this.loadCultures();

    effect(() => {
      const term = this.searchTerm().toLowerCase();
      this.filteredCultures.set(
        term
          ? this.cultures().filter(
              (c) =>
                c.nom.toLowerCase().includes(term) ||
                (c.description && c.description.toLowerCase().includes(term))
            )
          : [...this.cultures()]
      );
    });
  }

  loadCultures() {
    this.loading.set(true);
    this.api.getCultures().subscribe({
      next: (cultures) => {
        this.cultures.set(cultures);
        this.loading.set(false);
      },
      error: (err) => {
        console.error('Erreur de chargement:', err);
        this.loading.set(false);
      },
    });
  }

  openAddDialog() {
    this.isEditing.set(false);
    this.currentCulture.set(null);
    this.cultureForm.reset({
      nom: '',
      description: '',
    });
    this.showFormDialog.set(true);
  }

  openEditDialog(culture: Culture) {
    this.isEditing.set(true);
    this.currentCulture.set(culture);
    this.cultureForm.patchValue({
      nom: culture.nom,
      description: culture.description || '',
      type_culture: culture.type_culture || '',
      conditions_climatiques: culture.conditions_climatiques || '',
      cout_estime: culture.cout_estime ?? null,
    });
    this.showFormDialog.set(true);
  }

  onSubmit() {
    if (this.cultureForm.invalid) return;

    this.submitting.set(true);
    const formData = this.cultureForm.value;

    const cultureData: any = {
      nom: formData.nom!,
      description: formData.description || '',
      type_culture: formData.type_culture || '',
      conditions_climatiques: formData.conditions_climatiques || '',
      cout_estime: formData.cout_estime || null,
    };

    if (this.isEditing() && this.currentCulture()) {
      this.api.updateCulture(this.currentCulture()!.id, cultureData).subscribe({
        next: (culture) => {
          this.cultures.update((c) =>
            c.map((item) => (item.id === culture.id ? culture : item))
          );
          this.showFormDialog.set(false);
        },
        error: (err) => {
          console.error('Erreur:', err);
          this.submitting.set(false);
        },
        complete: () => this.submitting.set(false),
      });
    } else {
      this.api.createCulture(cultureData).subscribe({
        next: (culture) => {
          this.cultures.update((c) => [...c, culture]);
          this.showFormDialog.set(false);
        },
        error: (err) => {
          console.error('Erreur:', err);
          this.submitting.set(false);
        },
        complete: () => this.submitting.set(false),
      });
    }
  }

  confirmDelete() {
    const culture = this.currentCulture();
    if (!culture) return;

    this.submitting.set(true);
    this.api.deleteCulture(culture.id).subscribe({
      next: () => {
        this.cultures.update((c) => c.filter((item) => item.id !== culture.id));
        this.showDeleteDialog.set(false);
      },
      error: (err) => {
        console.error('Erreur:', err);
        this.submitting.set(false);
      },
      complete: () => this.submitting.set(false),
    });
  }

  // Utilisez cette méthode à la place de filteredCultures() dans le template
  paginatedCultures() {
    const filtered = this.filteredCultures();
    const start = (this.currentPage - 1) * this.pageSize;
    return filtered.slice(start, start + this.pageSize);
  }

  totalPages() {
    return Math.ceil(this.filteredCultures().length / this.pageSize);
  }

  goToPage(page: number) {
    if (page < 1 || page > this.totalPages()) return;
    this.currentPage = page;
  }
}
