// geocoding.service.ts
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { map, catchError } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class GeocodingService {
  private nominatimUrl = 'https://nominatim.openstreetmap.org/search';
  private defaultCoords = { lat: 10.719358, lon: 14.374867 }; // Vos coordonnées par défaut

  constructor(private http: HttpClient) { }

  geocode(address: string): Observable<{lat: number, lon: number}> {
    if (!address) return of(this.defaultCoords);
    
    return this.http.get<any[]>(`${this.nominatimUrl}?format=json&q=${encodeURIComponent(address)}`, {
      headers: { 'Accept-Language': 'fr-FR' } // Pour des résultats en français
    }).pipe(
      map(results => {
        if (results?.length > 0) {
          return {
            lat: parseFloat(results[0].lat),
            lon: parseFloat(results[0].lon)
          };
        }
        return this.defaultCoords;
      }),
      catchError(() => of(this.defaultCoords)) // Retourne les coords par défaut en cas d'erreur
    );
  }
}