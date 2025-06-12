import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class MeteoService {
  private openMeteoUrl = 'https://api.open-meteo.com/v1/forecast';

  constructor(private http: HttpClient) { }

  getWeatherData(latitude: number, longitude: number): Observable<any> {
    return this.http.get(`${this.openMeteoUrl}`, {
      params: {
        latitude: latitude.toString(),
        longitude: longitude.toString(),
        hourly: 'temperature_2m,relative_humidity_2m,precipitation',
        current_weather: 'true',
        timezone: 'auto'
      }
    });
  }

  sendMeteoData(parcelleId: number, temperature: number, humidite: number, pluie: number): Observable<any> {
    return this.http.post('http://localhost:8000/api/meteo', {
      parcelle_id: parcelleId,
      temperature: temperature,
      humidite: humidite,
      pluie: pluie
    });
  }
}
