import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface Recommandation {
  id: number;
  parcelle_id: number;
  titre: string;
  contenu: string;
  type: string;
  priorite: string;
  status: string;
  created_at: string;
  updated_at: string;
  parcelle: {
    id: number;
    nom: string;
    surface: number;
    localisation: string;
    etat: string;
    capteurs: Array<{
      type: string;
      valeur: string;
      dateMesure: string;
    }>;
  };
}

@Injectable({
  providedIn: 'root'
})
export class RecommandationService {
  private apiUrl = 'http://localhost:8000/api/recommandations';

  constructor(private http: HttpClient) { }

  getRecommandations(): Observable<Recommandation[]> {
    const headers = {
      'Authorization': `Bearer ${localStorage.getItem('token')}`
    };
    return this.http.get<Recommandation[]>(this.apiUrl, { headers });
  }
}
