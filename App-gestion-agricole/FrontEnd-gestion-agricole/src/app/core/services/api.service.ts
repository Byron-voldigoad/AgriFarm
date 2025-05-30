import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Culture } from '../models/culture.model';
import { Parcelle } from '../models/parcelle.model';
import { DashboardData } from '../models/dashboard.model';
import { Observable, throwError } from 'rxjs';
import { catchError, map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root',
})
export class ApiService {
  private baseUrl = 'http://localhost:8000/api';

  constructor(private http: HttpClient) {}

  // Opérations CRUD pour les cultures
  getCultures(): Observable<Culture[]> {
    return this.http.get<Culture[]>(`${this.baseUrl}/cultures`);
  }

  createCulture(culture: Omit<Culture, 'id'>): Observable<Culture> {
    return this.http.post<Culture>(`${this.baseUrl}/cultures`, culture);
  }

  updateCulture(id: number, culture: Partial<Culture>): Observable<Culture> {
    return this.http.put<Culture>(`${this.baseUrl}/cultures/${id}`, culture);
  }

  deleteCulture(id: number): Observable<void> {
    return this.http.delete<void>(`${this.baseUrl}/cultures/${id}`);
  }

  // Opérations CRUD pour les parcelles
  getParcelles(): Observable<Parcelle[]> {
    return this.http.get<Parcelle[]>(`${this.baseUrl}/parcelles`);
  }

  getParcelle(id: number): Observable<Parcelle> {
    return this.http.get<Parcelle>(`${this.baseUrl}/parcelles/${id}`);
  }

  createParcelle(parcelle: Omit<Parcelle, 'id' | 'created_at' | 'updated_at' | 'cultures'> & { culture_ids?: number[] }) {
    return this.http.post<Parcelle>(`${this.baseUrl}/parcelles`, parcelle);
  }

  updateParcelle(id: number, parcelle: any): Observable<Parcelle> {
    return this.http.put<Parcelle>(`${this.baseUrl}/parcelles/${id}`, parcelle);
  }

  deleteParcelle(id: number) {
    return this.http.delete(`${this.baseUrl}/parcelles/${id}`);
  }

  // Récupération des données du dashboard
  getDashboardData(): Observable<DashboardData> {
    return this.http.get<DashboardData>(`${this.baseUrl}/dashboard`).pipe(
      map(data => ({
        cards: data.cards,
        tasks: data.tasks,
        chartData: data.chartData
      }))
    );
  }

  getAgriculteurs(): Observable<any> {
    return this.http.get(`${this.baseUrl}/utilisateurs?type=Agriculteur`);
  }
}
