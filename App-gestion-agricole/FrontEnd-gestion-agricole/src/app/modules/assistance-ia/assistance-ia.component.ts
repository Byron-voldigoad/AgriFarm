import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Recommandation, RecommandationService } from './recommandation.service';
import { MeteoService } from './meteo.service';

@Component({
  selector: 'app-assistance-ia',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './assistance-ia.component.html',
})
export class AssistanceIAComponent implements OnInit {

  recommandations: Recommandation[] = [];
  errorMessage: string | null = null;

  constructor(
    private recommandationService: RecommandationService,
    private meteoService: MeteoService
  ) { }

  ngOnInit(): void {
    this.recommandationService.getRecommandations().subscribe(
      (recommandations: Recommandation[]) => {
        this.recommandations = recommandations;
        recommandations.forEach(reco => {
          if (reco.parcelle && reco.parcelle.localisation) {
            const [lat, lon] = reco.parcelle.localisation.split(',').map(Number);
            this.meteoService.getWeatherData(lat, lon).subscribe(
              (data: any) => {
                if (data && data.current_weather) {
                  const temp = data.current_weather.temperature;
                  const hum = data.current_weather.relative_humidity;
                  const prec = data.hourly.precipitation[0];
                  this.meteoService.sendMeteoData(
                    reco.parcelle.id,
                    temp,
                    hum,
                    prec
                  ).subscribe();
                }
              },
              (error: any) => {
                console.error('Erreur météo:', error);
              }
            );
          }
        });
      },
      (error: any) => {
        this.errorMessage = "Impossible de charger les recommandations. Vérifiez que le serveur backend est bien démarré.";
        console.error(error);
      }
    );
  }
}
