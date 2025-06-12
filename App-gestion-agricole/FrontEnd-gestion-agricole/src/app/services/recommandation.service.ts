import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

// Interface pour typer les données d'une parcelle
export interface Parcelle {
  id: number;
  nom: string;
  surface: number;
  localisation: string;
  etat: string;
  // Ajoutez d'autres champs si nécessaire
}

// Interface pour typer les données d'une recommandation
export interface Recommandation {
  id: number;
  parcelle_id: number;
  titre: string;
  contenu: string;
  type: string;
  priorite: 'haute' | 'moyenne' | 'basse';
  status: 'proposée' | 'lue' | 'acceptée';
  created_at: string;
  updated_at: string;
  parcelle: Parcelle; // Relation avec la parcelle
}

@Injectable({
  providedIn: 'root'
})
export class RecommandationService {

  private apiUrl = 'http://localhost:8000/api/recommandations'; // URL de votre API Laravel

  constructor(private http: HttpClient) { }

  getRecommandations(): Observable<Recommandation[]> {
    return this.http.get<Recommandation[]>(this.apiUrl);
  }
}
