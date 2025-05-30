import { Component, OnInit, AfterViewInit, OnDestroy } from '@angular/core';
import { ApiService } from '../../core/services/api.service';
import {
  Parcelle,
  ParcelleWithRelations,
} from '../../core/models/parcelle.model';
import { Culture } from '../../core/models/culture.model';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import {
  LucideAngularModule,
  MapPin,
  Square,
  X,
  Map,
  Edit,
  Trash2,
  Plus,
  LucideIconData,
  Sprout,
  TrendingUp,
} from 'lucide-angular';
import {
  FormBuilder,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
import * as L from 'leaflet';
import { GeoSearchControl, OpenStreetMapProvider } from 'leaflet-geosearch';
import { GeocodingService } from '../../core/services/geocoding.service'; // À créer
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-parcelles',
  standalone: true,
  imports: [
    CommonModule,
    RouterModule,
    ReactiveFormsModule,
    LucideAngularModule,
    HttpClientModule,
    FormsModule, // Ajouté pour ngModel
  ],
  templateUrl: './parcelles.component.html',
  styleUrls: ['./parcelles.component.css'],
})
export class ParcellesComponent implements OnInit, AfterViewInit, OnDestroy {
  parcelles: ParcelleWithRelations[] = [];
  agriculteurs: any[] = [];
  cultures: Culture[] = [];
  isLoading = true;
  errorMessage = '';
  showModal = false;
  isEditing = false;
  currentParcelleId: number | null = null;
  parcelleForm: FormGroup;
  showMapSidebar = false;
  selectedParcelleForMap: ParcelleWithRelations | null = null;
  searchTerm: string = '';
  etatFilter: string = '';
  cultureFilter: string = '';

  pageSize = 6;
  currentPage = 1;

  private map: L.Map | null = null;

  icons = { Map, Edit, Trash2, Plus, Sprout, TrendingUp, MapPin, Square, X };

  constructor(
    private apiService: ApiService,
    private fb: FormBuilder,
    private geocodingService: GeocodingService
  ) {
    this.parcelleForm = this.fb.group({
      nom: ['', Validators.required],
      surface: ['', [Validators.required, Validators.min(0)]],
      localisation: ['', Validators.required],
      etat: ['', Validators.required],
      agriculteur_id: ['', Validators.required],
      culture_id: [''],
      stadeCroissance: [{ value: '', disabled: true }],
    });
  }

  ngOnInit(): void {
    this.loadData();
  }

  ngAfterViewInit(): void {
    this.initMap();
  }

  ngOnDestroy(): void {
    if (this.map) {
      this.map.remove();
      this.map = null;
    }
  }

  openMapSidebar(parcelle: ParcelleWithRelations): void {
    this.selectedParcelleForMap = parcelle;
    this.showMapSidebar = true;

    // Attendre que la sidebar soit totalement visible (animation terminée)
    setTimeout(() => {
      this.initMap();
    }, 400); // 400ms pour laisser le temps à la transition CSS
  }

  closeMapSidebar(): void {
    this.showMapSidebar = false;
    if (this.map) {
      this.map.remove();
      this.map = null;
    }
  }

  initMap(): void {
    if (this.map) {
      this.map.remove();
      this.map = null;
    }

    const mapElement = document.getElementById('parcelle-map');
    if (!mapElement) return;

    // Force la taille du conteneur (important !)
    mapElement.style.height = '300px';
    mapElement.style.width = '100%';
    mapElement.style.minHeight = '300px';
    mapElement.style.display = 'block';
    mapElement.style.position = 'relative';

    this.fixLeafletIcons();

    // Utilise le centre de la parcelle si possible, sinon centre défaut
    let center: [number, number] = [46.2276, 2.2137];
    const loc = this.selectedParcelleForMap?.localisation;
    if (loc && typeof loc === 'string' && loc.includes(',')) {
      const parts = loc.split(',').map((s) => s.trim());
      if (parts.length === 2) {
        const parsedLat = parseFloat(parts[0]);
        const parsedLon = parseFloat(parts[1]);
        if (!isNaN(parsedLat) && !isNaN(parsedLon)) {
          center = [parsedLat, parsedLon];
        }
      }
    }

    this.map = L.map(mapElement, {
      preferCanvas: true,
    }).setView(center, 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 19,
      detectRetina: true,
    }).addTo(this.map);

    // Ajoute le marker si coordonnées valides
    if (center[0] !== 46.2276 || center[1] !== 2.2137) {
      L.marker(center)
        .addTo(this.map)
        .bindPopup(
          `<b>${this.selectedParcelleForMap?.nom}</b><br>${this.selectedParcelleForMap?.localisation}`
        );
    }

    // Invalidate la taille plusieurs fois pour forcer le rendu correct
    this.map.invalidateSize();
    setTimeout(() => this.map?.invalidateSize(), 200);
    setTimeout(() => this.map?.invalidateSize(), 500);
  }

  // Ajoutez cette nouvelle méthode
  private fixLeafletIcons(): void {
    delete (L.Icon.Default.prototype as any)._getIconUrl;

    L.Icon.Default.mergeOptions({
      iconRetinaUrl: 'assets/leaflet/images/marker-icon-2x.png',
      iconUrl: 'assets/leaflet/images/marker-icon.png',
      shadowUrl: 'assets/leaflet/images/marker-shadow.png',
    });
  }

  async centerMapOnParcelle() {
    // Ne rien faire ici, le centrage et le marker sont déjà gérés dans initMap()
  }

  loadData(): void {
    this.isLoading = true;

    this.apiService.getAgriculteurs().subscribe({
      next: (agriculteurs) => {
        this.agriculteurs = agriculteurs.filter(
          (a: any) => a.type === 'Agriculteur'
        );
        this.apiService.getCultures().subscribe({
          next: (cultures) => {
            this.cultures = cultures;
            this.loadParcelles();
          },
          error: (err) => this.handleError('Erreur chargement cultures', err),
        });
      },
      error: (err) => this.handleError('Erreur chargement agriculteurs', err),
    });
  }

  loadParcelles(): void {
    this.apiService.getParcelles().subscribe({
      next: (parcelles: Parcelle[]) => {
        this.parcelles = parcelles.map((p) => ({
          ...p,
          agriculteurNom: this.getAgriculteurName(p.agriculteur_id),
          cultureNom: p.culture_id
            ? this.getCultureName(p.culture_id)
            : undefined,
          etat: p.etat || 'En jachère',
        }));
        this.isLoading = false;
      },
      error: (err) => this.handleError('Erreur chargement parcelles', err),
    });
  }

  getAgriculteurName(id: number): string {
    const agriculteur = this.agriculteurs.find((a) => a.id === id);
    return agriculteur?.nom || 'Inconnu';
  }

  getCultureName(id: number): string {
    const culture = this.cultures.find((c) => c.id === id);
    return culture?.nom || 'Inconnue';
  }

  onCultureChange(event: Event): void {
    const select = event.target as HTMLSelectElement;
    const stadeControl = this.parcelleForm.get('stadeCroissance');

    if (select.value) {
      stadeControl?.enable();
    } else {
      stadeControl?.disable();
      stadeControl?.setValue('');
    }
  }

  openAddModal(): void {
    this.isEditing = false;
    this.currentParcelleId = null;
    this.parcelleForm.reset({
      etat: 'En jachère',
      culture_id: '',
      stadeCroissance: { value: '', disabled: true },
    });
    this.showModal = true;
  }

  openEditModal(parcelle: ParcelleWithRelations): void {
    console.log('Données de la parcelle à modifier:', parcelle);
    this.isEditing = true;
    this.currentParcelleId = parcelle.id;
    this.parcelleForm.patchValue({
      nom: parcelle.nom,
      surface: parcelle.surface,
      localisation: parcelle.localisation,
      etat: parcelle.etat,
      agriculteur_id: parcelle.agriculteur_id,
      culture_id: parcelle.culture_id || null,
      stadeCroissance: parcelle.stadeCroissance || null,
    });

    if (parcelle.culture_id) {
      this.parcelleForm.get('stadeCroissance')?.enable();
    } else {
      this.parcelleForm.get('stadeCroissance')?.disable();
    }

    this.showModal = true;
  }

  submitForm(): void {
    if (this.parcelleForm.invalid) return;

    const formValue = this.parcelleForm.value;
    const parcelleData = {
      nom: formValue.nom,
      surface: formValue.surface,
      localisation: formValue.localisation,
      etat: formValue.etat,
      agriculteur_id: formValue.agriculteur_id,
      culture_id: formValue.culture_id ? Number(formValue.culture_id) : null,
      stadeCroissance: formValue.culture_id ? formValue.stadeCroissance : null,
    };

    console.log('Données envoyées:', parcelleData); // Debug

    if (this.isEditing && this.currentParcelleId) {
      this.apiService
        .updateParcelle(this.currentParcelleId, parcelleData)
        .subscribe({
          next: (updatedParcelle) => {
            console.log('Réponse du serveur:', updatedParcelle); // Debug
            // Mise à jour optimiste de l'affichage
            const index = this.parcelles.findIndex(
              (p) => p.id === this.currentParcelleId
            );
            if (index !== -1) {
              this.parcelles[index] = {
                ...this.parcelles[index],
                ...parcelleData,
                cultureNom: formValue.culture_id
                  ? this.getCultureName(Number(formValue.culture_id))
                  : undefined,
              };
            }
            this.showModal = false;
          },
          error: (err) => {
            console.error('Erreur:', err);
            this.errorMessage =
              err.error?.message || 'Erreur lors de la mise à jour';
            // Recharger les données pour être sûr
            this.loadParcelles();
          },
        });
    } else {
      this.apiService.createParcelle(parcelleData).subscribe({
        next: () => {
          this.loadParcelles();
          this.showModal = false;
        },
        error: (err) => {
          console.error('Erreur création:', err);
          this.errorMessage =
            err.error?.message || 'Erreur lors de la création';
        },
      });
    }
  }

  deleteParcelle(id: number): void {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette parcelle ?')) {
      this.apiService.deleteParcelle(id).subscribe({
        next: () => this.loadParcelles(),
        error: (err) => this.handleError('Erreur suppression', err),
      });
    }
  }

  handleError(context: string, error: any): void {
    console.error(`${context}:`, error);
    this.errorMessage =
      error.error?.message || `Erreur lors de ${context.toLowerCase()}`;
    this.isLoading = false;
  }

  getIcon(iconName: string): LucideIconData {
    return this.icons[iconName as keyof typeof this.icons];
  }

  get filteredParcelles(): ParcelleWithRelations[] {
    let filtered = this.parcelles;

    if (this.searchTerm?.trim()) {
      const term = this.searchTerm.trim().toLowerCase();
      filtered = filtered.filter(
        (p) =>
          p.nom?.toLowerCase().includes(term) ||
          p.localisation?.toLowerCase().includes(term) ||
          p.agriculteurNom?.toLowerCase().includes(term) ||
          (p.cultureNom && p.cultureNom.toLowerCase().includes(term))
      );
    }

    if (this.etatFilter) {
      filtered = filtered.filter((p) => p.etat === this.etatFilter);
    }

    if (this.cultureFilter) {
      filtered = filtered.filter((p) => p.cultureNom === this.cultureFilter);
    }

    return filtered;
  }

  paginatedParcelles() {
    const start = (this.currentPage - 1) * this.pageSize;
    return this.filteredParcelles.slice(start, start + this.pageSize);
  }

  totalPages() {
    return Math.ceil(this.filteredParcelles.length / this.pageSize);
  }

  goToPage(page: number) {
    if (page < 1 || page > this.totalPages()) return;
    this.currentPage = page;
  }
}
