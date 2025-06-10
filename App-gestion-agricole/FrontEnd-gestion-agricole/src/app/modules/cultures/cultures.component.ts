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
import { HttpClient } from '@angular/common/http';
import { SupabaseStorageService } from '../../services/supabase-storage.service';

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
  private http = inject(HttpClient);
  private supabaseStorage = inject(SupabaseStorageService);

  // Types de culture prédéfinis
  readonly typesCulture = [
    'Céréales',
    'Légumineuses',
    'Tubercules',
    'Légumes',
    'Fruits',
    'Plantes aromatiques',
    'Plantes médicinales',
    'Autres'
  ];

  // États avec les signaux
  cultures = signal<Culture[]>([]);
  filteredCultures = signal<Culture[]>([]);
  loading = signal(true);
  searchTerm = signal('');
  selectedType = signal<string>('');

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

  photoFile: File | null = null;
  isLoading = false;

  toggleDescription(id: number) {
    this.expandedDescriptions[id] = !this.expandedDescriptions[id];
  }

  // Formulaire simplifié
  cultureForm = this.fb.group({
    nom: ['', [Validators.required, Validators.minLength(2)]],
    description: [''],
    type_culture: ['', Validators.required],
    conditions_climatiques: [''],
    cout_estime: [undefined as number | undefined]
  });

  constructor() {
    this.loadCultures();

    effect(() => {
      const term = this.searchTerm().toLowerCase();
      const type = this.selectedType();
      
      this.filteredCultures.set(
        this.cultures().filter((c) => {
          const matchesSearch = term
            ? c.nom.toLowerCase().includes(term) ||
              (c.description && c.description.toLowerCase().includes(term))
            : true;
          
          const matchesType = type ? c.type_culture === type : true;
          
          return matchesSearch && matchesType;
        })
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
      cout_estime: culture.cout_estime || undefined
    });
    this.showFormDialog.set(true);
  }

  onPhotoSelected(event: any) {
    this.photoFile = event.target.files[0];
  }

  onSubmit() {
    if (this.cultureForm.invalid) return;
    this.saveCulture();
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

  async saveCulture() {
    if (this.cultureForm.invalid) return;

    this.submitting.set(true);
    const formData = this.cultureForm.value;

    try {
      let photoUrl = this.currentCulture()?.photo || null;
      if (this.photoFile) {
        photoUrl = await this.supabaseStorage.uploadCulturePhoto(
          this.photoFile,
          this.currentCulture()?.id?.toString() || 'new'
        );
      }

      const payload = {
        ...formData,
        photo: photoUrl || undefined,
        nom: formData.nom || '',
        description: formData.description || undefined,
        type_culture: formData.type_culture || '',
        conditions_climatiques: formData.conditions_climatiques || '',
        cout_estime: formData.cout_estime || undefined
      };

      if (this.isEditing() && this.currentCulture()) {
        this.api.updateCulture(this.currentCulture()!.id, payload).subscribe({
          next: (culture) => {
            this.cultures.update((c) =>
              c.map((item) => (item.id === culture.id ? culture : item))
            );
            this.showFormDialog.set(false);
            this.photoFile = null;
            this.submitting.set(false);
          },
          error: (err) => {
            console.error('Erreur:', err);
            this.submitting.set(false);
          }
        });
      } else {
        this.api.createCulture(payload).subscribe({
          next: (culture) => {
            this.cultures.update((c) => [...c, culture]);
            this.showFormDialog.set(false);
            this.photoFile = null;
            this.submitting.set(false);
          },
          error: (err) => {
            console.error('Erreur:', err);
            this.submitting.set(false);
          }
        });
      }
    } catch (error) {
      console.error('Erreur lors de l\'upload de la photo:', error);
      this.submitting.set(false);
    }
  }
}
