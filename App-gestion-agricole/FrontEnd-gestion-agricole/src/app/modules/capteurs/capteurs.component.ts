import { Component, OnInit, LOCALE_ID } from '@angular/core';
import { CommonModule, registerLocaleData } from '@angular/common';
import localeFr from '@angular/common/locales/fr';
import { MeteoService } from '../../services/meteo.service';
import { ApiService } from '../../core/services/api.service';
import {
  LucideAngularModule,
  LucideIconData,
  ThermometerSun,
} from 'lucide-angular';
import L from 'leaflet';
import { FormsModule } from '@angular/forms';

registerLocaleData(localeFr);

interface Parcelle {
  id: number;
  nom: string;
  localisation: string;
  weather?: any;
}

@Component({
  selector: 'app-capteurs',
  standalone: true,
  imports: [CommonModule, LucideAngularModule, FormsModule],
  providers: [{ provide: LOCALE_ID, useValue: 'fr-FR' }],
  templateUrl: './capteurs.component.html',
})
export class CapteursComponent implements OnInit {
  parcelles: Parcelle[] = [];
  searchTerm: string = '';
  filteredParcelles: Parcelle[] = [];
  paginatedParcelles: Parcelle[] = [];
  currentPage: number = 1;
  itemsPerPage: number = 6;
  totalPages: number = 1;
  icons = { ThermometerSun };
  isLoading: boolean = false;
  constructor(
    private meteo: MeteoService,
    private apiService: ApiService // <-- utilise ton service pour charger les parcelles
  ) {}

  ngOnInit() {
    this.isLoading = true;
    this.apiService.getParcelles().subscribe((parcelles: any[]) => {
      // On suppose que chaque parcelle a une propriété localisation: "lat,lon"
      this.parcelles = parcelles.map((p) => ({
        id: p.id,
        nom: p.nom,
        localisation: p.localisation,
      }));

      this.parcelles.forEach((parcelle) => {
        const [lat, lon] = parcelle.localisation
          .split(',')
          .map((v: string) => parseFloat(v.trim()));
        if (!isNaN(lat) && !isNaN(lon)) {
          this.meteo.getCurrentWeather(lat, lon).subscribe((data) => {
            const utcDate = new Date(data.current_weather.time + 'Z');
            parcelle.weather = {
              temperature: data.current_weather.temperature,
              humidity: data.hourly.relative_humidity_2m[0],
              windspeed: data.current_weather.windspeed,
              time: utcDate,
            };
          });
        }
      });
      this.isLoading = false;
      this.filterParcelles();
    });
  }

  filterParcelles() {
    this.filteredParcelles = this.parcelles.filter((p) =>
      p.nom.toLowerCase().includes(this.searchTerm.toLowerCase())
    );
    this.totalPages =
      Math.ceil(this.filteredParcelles.length / this.itemsPerPage) || 1;
    this.currentPage = 1;
    this.updatePaginatedParcelles();
  }

  updatePaginatedParcelles() {
    const start = (this.currentPage - 1) * this.itemsPerPage;
    const end = start + this.itemsPerPage;
    this.paginatedParcelles = this.filteredParcelles.slice(start, end);
  }

  goToPage(page: number) {
    if (page < 1 || page > this.totalPages) return;
    this.currentPage = page;
    this.updatePaginatedParcelles();
  }

  private fixLeafletIcons(): void {
    delete (L.Icon.Default.prototype as any)._getIconUrl;

    L.Icon.Default.mergeOptions({
      iconRetinaUrl: 'assets/leaflet/images/marker-icon-2x.png',
      iconUrl: 'assets/leaflet/images/marker-icon.png',
      shadowUrl: 'assets/leaflet/images/marker-shadow.png',
    });
  }

  getIcon(iconName: string): LucideIconData {
    return this.icons[iconName as keyof typeof this.icons];
  }
}
