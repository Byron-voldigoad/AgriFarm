import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class MeteoService {
  constructor(private http: HttpClient) {}

  getCurrentWeather(lat: number, lon: number): Observable<any> {
    // Exemple : Paris (lat=48.85, lon=2.35)
    const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&hourly=temperature_2m,relative_humidity_2m`;
    return this.http.get(url);
  }
}
